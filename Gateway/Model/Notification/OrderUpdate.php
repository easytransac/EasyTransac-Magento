<?php
/**
 * Easytransac_Gateway payment method.
 *
 * @category    Easytransac
 * @package     Easytransac_Gateway
 * @author      Easytrasac
 * @copyright   Easytransac (https://www.easytransac.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Easytransac\Gateway\Model\Notification;

use Easytransac\Gateway\Model\EasyTransacClientIdMapper;
use Easytransac\Gateway\Model\Logger\Logger;
use Easytransac\Gateway\Model\SaveCreditCard;
use Magento\Framework\DB\Transaction;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Payment\Transaction\Builder;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\ResourceModel\Order as OrderResource;
use Magento\Sales\Model\Service\InvoiceService;

class OrderUpdate
{
    /**
     * @var EasyTransacClientIdMapper
     */
    private $clientIdMapper;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var SaveCreditCard
     */
    private $saveCreditCard;
    /**
     * @var Builder
     */
    private $transBuilder;
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;
    /**
     * @var InvoiceService
     */
    private $invoiceService;
    /**
     * @var Transaction
     */
    private $transaction;
    /**
     * @var InvoiceSender
     */
    private $invoiceSender;
    /**
     * @var InvoiceRepositoryInterface
     */
    private $invoiceRepository;

    /**
     * @var OrderFactory
     */
    private $orderFactory;

    /**
     * @var OrderResource
     */
    private $orderResource;

    /**
     * Order Update Construct
     *
     * @param EasyTransacClientIdMapper $clientIdMapper
     * @param Logger $logger
     * @param SaveCreditCard $saveCreditCard
     * @param Builder $transBuilder
     * @param OrderRepositoryInterface $orderRepository
     * @param InvoiceService $invoiceService
     * @param Transaction $transaction
     * @param InvoiceSender $invoiceSender
     * @param InvoiceRepositoryInterface $invoiceRepository
     * @param OrderFactory $orderFactory
     * @param OrderResource $orderResource
     */
    public function __construct(
        EasyTransacClientIdMapper $clientIdMapper,
        Logger                    $logger,
        SaveCreditCard            $saveCreditCard,
        Builder $transBuilder,
        OrderRepositoryInterface $orderRepository,
        InvoiceService $invoiceService,
        Transaction $transaction,
        InvoiceSender $invoiceSender,
        InvoiceRepositoryInterface $invoiceRepository,
        OrderFactory $orderFactory,
        OrderResource $orderResource
    ) {
        $this->clientIdMapper = $clientIdMapper;
        $this->logger = $logger;
        $this->saveCreditCard = $saveCreditCard;
        $this->transBuilder = $transBuilder;
        $this->orderRepository = $orderRepository;
        $this->invoiceService = $invoiceService;
        $this->transaction = $transaction;
        $this->invoiceSender = $invoiceSender;
        $this->invoiceRepository = $invoiceRepository;
        $this->orderFactory = $orderFactory;
        $this->orderResource = $orderResource;
    }

    /**
     * Save order data
     *
     * @param array $responseData
     * @return void
     * @throws CouldNotSaveException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function saveOrder($responseData)
    {
        $order = $this->orderFactory->create();
        $this->orderResource->load($order, $responseData['OrderId'], 'quote_id');

        $this->saveCardDetails($order, $responseData);
        $this->saveInvoiceData($order, $responseData);

        $this->logger->info('Invoice generated for order no. : ' . $order->getId());
    }

    /**
     * Save credit card details
     *
     * @param Order $order
     * @param array $responseData
     * @return void
     * @throws CouldNotSaveException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function saveCardDetails(Order $order, array $responseData)
    {
        $additionalInformation = $order->getPayment()->getAdditionalInformation();
        if (isset($additionalInformation['saveCard']) && $additionalInformation['saveCard'] != ''
            && $additionalInformation['saveCard'] == 'yes') {
            /** Save Attribute Value in Magento */
            if (isset($responseData['Client']['Id'])) {
                $easyTransacId = $responseData['Client']['Id'];
                $this->clientIdMapper->saveClientIdForCustomerId($easyTransacId, (int)$order->getCustomerId());
                $this->logger->info("Customer easytransac client id : " . $easyTransacId);

                /** Save Card Details */
                if (isset($responseData['UserId'])) {
                    $this->saveCreditCard->saveCardDetails(
                        (int)$order->getCustomerId(),
                        $responseData
                    );
                    $this->logger->info(sprintf("Stored card for user : %s", $responseData['UserId']));
                }
            }
        }
    }

    /**
     * Generate invoice data
     *
     * @param Order $order
     * @param array $responseData
     * @return void
     * @throws LocalizedException
     */
    public function saveInvoiceData(Order $order, array $responseData)
    {
        if (isset($responseData['OrderId']) && $responseData['OrderId'] != ""
            && isset($responseData['Tid']) && $responseData['Tid'] != ""
            && isset($responseData['Status']) && $responseData['Status'] != ""
        ) {
            $transactionId = $responseData['Tid'];
            $order->setStatus(Order::STATE_PROCESSING);
            if ($order->canInvoice()) {
                $invoice = $this->invoiceService->prepareInvoice($order);
                $invoice->setRequestedCaptureCase(Invoice::CAPTURE_ONLINE)
                    ->setTransactionId($transactionId)
                    ->setState(Invoice::STATE_PAID)
                    ->register()
                    ->pay();
                $transactionSave = $this->transaction
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder());
                $transactionSave->save();

                // Update the order
                $order->setTotalPaid($order->getTotalPaid())
                    ->setExtOrderId($transactionId)
                    ->setBaseTotalPaid($order->getBaseTotalPaid());

                if (isset($responseData['MultiplePayments']) && $responseData['MultiplePayments'] == "yes"
                    && isset($responseData['MultiplePaymentsRepeat']) && $responseData['MultiplePaymentsRepeat'] != ""
                    && isset($responseData['Amount']) && $responseData['Amount'] != ""
                ) {
                    $order->setEasytransacInstallmentAmount($responseData['Amount']);
                }

                $this->invoiceSender->send($invoice);
                $order->addCommentToStatusHistory(
                    __('Invoice generated #%1.', $invoice->getId())
                )
                    ->setIsCustomerNotified(true);
                $this->orderRepository->save($order);
                $this->invoiceRepository->save($invoice);

                $this->updatePaymentData($order, $responseData);
            }
        }
    }

    /**
     * Update payment data
     *
     * @param Order $order
     * @param array $responseData
     * @return void
     */
    public function updatePaymentData(Order $order, array $responseData)
    {
        $transactionId = $responseData['Tid'];
        $payment = $order->getPayment();
        if (isset($responseData['CardMonth']) && isset($responseData['CardYear'])
            && isset($responseData['CardNumber']) && isset($responseData['CardType'])
            && isset($responseData['CardOwner'])
        ) {
            $responseData['CardNumber'] = substr($responseData['CardNumber'], -4);
            $payment->setLastTransId($transactionId)
                ->setCcExpMonth($responseData['CardMonth'])
                ->setCcExpYear($responseData['CardYear'])
                ->setCcLast4($responseData['CardNumber'])
                ->setCcType($responseData['CardType'])
                ->setCcOwner($responseData['CardOwner']);
        }

        //update sales payment transaction data
        $message = __('The authorized amount is %1.', $order->getTotalPaid());
        $transaction = $this->transBuilder->setPayment($payment)
            ->setOrder($order)
            ->setTransactionId($transactionId)
            ->setFailSafe(true)
            ->build(TransactionInterface::TYPE_CAPTURE);
        $payment->addTransactionCommentsToOrder(
            $transaction,
            $message
        );
        $payment->setParentTransactionId(null);
        $this->orderRepository->save($order);
    }
}

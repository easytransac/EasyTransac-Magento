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

namespace Easytransac\Gateway\Model\EasyTransacApi;

use EasyTransac\Entities\Customer;
use EasyTransac\Entities\OneClickTransaction;
use EasyTransac\Entities\PaymentPageTransaction;
use Easytransac\Gateway\Api\CreditCardRepositoryInterface;
use Easytransac\Gateway\Api\Data\CreditCardInterface;
use Easytransac\Gateway\Model\Config;
use Easytransac\Gateway\Model\EasyTransacClientIdMapper;
use Easytransac\Gateway\Model\Logger\Logger;
use EasyTransac\Requests\OneClickPayment;
use EasyTransac\Requests\PaymentPage;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class PaymentPageApi extends AbstractApiCall
{
    /**
     * @var CreditCardRepositoryInterface
     */
    private $creditCardRepository;
    /**
     * @var EasyTransacClientIdMapper
     */
    private $clientIdMapper;

    /**
     * Constructor
     *
     * @param Config $config
     * @param Logger $logger
     * @param CreditCardRepositoryInterface $creditCardRepository
     * @param EasyTransacClientIdMapper $clientIdMapper
     */
    public function __construct(
        Config $config,
        Logger $logger,
        CreditCardRepositoryInterface $creditCardRepository,
        EasyTransacClientIdMapper $clientIdMapper
    ) {
        parent::__construct($config, $logger);
        $this->creditCardRepository = $creditCardRepository;
        $this->clientIdMapper = $clientIdMapper;
    }

    /**
     * Get Payment Page
     *
     * @param array $data
     * @param string|null $multipayment
     * @return array
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getPaymentPageApi(array $data, string $multipayment = null): array
    {

        $this->getApiService();

        $clientId = '';
        $logMessage = '';
        $customerId = $data['Uid'] ?? '';
        if ($customerId) {
            $clientId = $this->clientIdMapper->getEasyTransacClientId($customerId);
        }

        $customer = (new Customer())
            ->setUid($data['Uid'] ?? '')
            ->setCity($data['City'])
            ->setZipCode($data['ZipCode'])
            ->setAddress($data['Address'])
            ->setPhone($data['phone'])
            ->setLastname($data['Lastname'])
            ->setFirstname($data['Firstname'])
            ->setEmail($data['email'])
            ->setCompany($data['company'])
            ->setNationality('')
            ->setBirthDate('')
            ->setCallingCode('')
            ->setClientId($clientId);

        if (isset($data['AliasId']) && $data['AliasId'] != "") {
            $alias = $this->getCardAlias((int)$data['AliasId']);

            if (isset($data['emiMonths']) && $data['emiMonths'] != "") {
                /** Request for Oneclick Multipayment Installments using card alias */

                $transaction = (new OneClickTransaction())
                    ->setAmount($data['Amount'])
                    ->setClientIp($data['ClientIp'])
                    ->setOrderId($data['OrderId']) // Passing QuoteId
                    ->setMultiplePayments($multipayment)
                    ->setMultiplePaymentsRepeat($data['emiMonths'])
                    ->setReturnMethod('GET')
                    ->setCustomer($customer)
                    ->setReturnUrl($this->config->getStoreReturnUrl())
                    ->setAlias($alias->getAlias());
                $logMessage = "Payment page API response for EMI oneclick Multipayment";
            } else {
                /** Request for Oneclick Cardpayment using card alias */
                $transaction = (new OneClickTransaction())
                    ->setAmount($data['Amount'])
                    ->setOrderId($data['OrderId']) // Passing QuoteId
                    ->setClientIp($data['ClientIp'])
                    ->setReturnMethod('GET')
                    ->setCustomer($customer)
                    ->setReturnUrl($this->config->getStoreReturnUrl())
                    ->setAlias($alias->getAlias());
                $logMessage = "Payment page API response for EMI oneclick Cardpayment";
            }

            if ($this->getDebug()) {
                $this->logger->info("Easytrasaction request : ", $transaction->toArray());
            }

            $dp = new OneClickPayment();
            $response = $dp->execute($transaction);
        } else {
            if (isset($data['emiMonths']) && $data['emiMonths'] != "") {
                /** Request for Multipayment Installments */
                $transaction = (new PaymentPageTransaction())
                    ->setAmount($data['Amount'])
                    ->setClientIp($data['ClientIp'])
                    ->setSecure('yes')
                    ->setOrderId($data['OrderId']) // Passing QuoteId
                    ->setMultiplePayments($multipayment)
                    ->setMultiplePaymentsRepeat($data['emiMonths'])
                    ->setReturnMethod('GET')
                    ->setCustomer($customer)
                    ->setReturnUrl($this->config->getStoreReturnUrl());
                $logMessage = "Payment page API response for EMI";
            } else {
                /** Request for Payment Page using Debit Card */
                $transaction = (new PaymentPageTransaction())
                    ->setAmount($data['Amount'])
                    ->setClientIp($data['ClientIp'])
                    ->setSecure('yes')
                    ->setOrderId($data['OrderId']) // Passing QuoteId
                    ->setSaveCard($data['SaveCard'])
                    ->setReturnMethod('GET')
                    ->setCustomer($customer)
                    ->setReturnUrl($this->config->getStoreReturnUrl());
                $logMessage = "Payment page API response for new card";
            }

            if ($this->getDebug()) {
                $this->logger->info("Easytrasaction request : ", $transaction->toArray());
            }

            $dp = new PaymentPage();
            $response = $dp->execute($transaction);
        }
        return $this->getResponseData($response, $logMessage);
    }

    /**
     * Get Payment Page for MultiPayment Method
     *
     * @param array $data
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getMultiPaymentApi(array $data): array
    {
        return $this->getPaymentPageApi($data, 'yes');
    }

    /**
     * Get Card Alias by entity Id
     *
     * @param int $aliasId
     * @return CreditCardInterface
     * @throws LocalizedException
     */
    public function getCardAlias(int $aliasId): CreditCardInterface
    {
        return $this->creditCardRepository->getById($aliasId);
    }
}

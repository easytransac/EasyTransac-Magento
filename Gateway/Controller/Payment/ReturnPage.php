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

namespace Easytransac\Gateway\Controller\Payment;

use Easytransac\Gateway\Model\Logger\Logger;
use Exception;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultInterface;

class ReturnPage implements ActionInterface
{
    /**
     * @var RedirectFactory
     */
    private $redirectFactory;
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * ReturnPage Constructor
     *
     * @param RedirectFactory $redirectFactory
     * @param RequestInterface $request
     * @param Session $checkoutSession
     * @param Logger $logger
     */
    public function __construct(
        RedirectFactory  $redirectFactory,
        RequestInterface $request,
        Session $checkoutSession,
        Logger $logger,
    ) {
        $this->redirectFactory = $redirectFactory;
        $this->request = $request;
        $this->logger = $logger;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Execute action based on request and return result
     *
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        $receivedData = $this->request->getParams();

        $order = $this->checkoutSession->getLastRealOrder();
        $order->setExtOrderId($receivedData['Tid']);

        switch ($receivedData['Status']) {
            // todo : default case
            case Notification::FAILED:
            case Notification::REFUNDED:
            case Notification::PENDING:
                return $this->failedPage();

            case Notification::CAPTURED:
                return $this->acceptedPage();
        }
    }

    /**
     * Redirect to Success Page
     *
     * @return Redirect|void
     */
    public function acceptedPage()
    {
        try {
            return $this->redirectFactory->create()->setPath('checkout/onepage/success');
        } catch (Exception $e) {
            $this->logger->error('EasyTransac Payment exception: ' . $e->getCode() . ' (' . $e->getMessage() . ') ');
        }
    }

    /**
     * Redirect to Cart Page
     *
     * @return Redirect|void
     */
    public function failedPage()
    {
        try {
            return $this->redirectFactory->create()->setPath('checkout/cart');
        } catch (Exception $e) {
            $this->logger->error('EasyTransac Payment exception: ' . $e->getCode() . ' (' . $e->getMessage() . ') ');
        }
    }
}

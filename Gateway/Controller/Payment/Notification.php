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
use Easytransac\Gateway\Model\Notification\OrderUpdate;
use Exception;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;

class Notification implements HttpPostActionInterface, CsrfAwareActionInterface
{
    public const CAPTURED = 'captured';
    public const PENDING = 'pending';
    public const FAILED = 'failed';
    public const REFUNDED = 'refunded';

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var OrderUpdate
     */
    private $orderUpdate;

    /**
     * Constructor
     *
     * @param Logger $logger
     * @param RequestInterface $request
     * @param JsonFactory $resultJsonFactory
     * @param OrderUpdate $orderUpdate
     */
    public function __construct(
        Logger           $logger,
        RequestInterface $request,
        JsonFactory      $resultJsonFactory,
        OrderUpdate      $orderUpdate
    ) {
        $this->logger = $logger;
        $this->request = $request;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->orderUpdate = $orderUpdate;
    }

    /**
     * Order data update after payment api
     *
     * @return ResponseInterface|Json|ResultInterface|void
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        try {
            $responseData = (array)$this->request->getPost();
            $this->logger->info("Easytransac request data : ", $responseData);

            if (isset($responseData['OrderId']) && $responseData['OrderId'] != "") {
                //Save card details if status is captured and save card checkbox is selected
                if (isset($responseData['Status']) && $responseData['Status'] == self::CAPTURED) {
                    $this->orderUpdate->saveOrder($responseData);
                    return $resultJson->setData([
                        'success' => true,
                        'message' => __('Easytransac invoice generated successfully')
                    ]);
                }

                if (isset($responseData['Status']) && $responseData['Status'] == self::REFUNDED) {
                    $this->logger->info('Easytransac refund processed');
                    return $resultJson->setData([
                        'success' => true,
                        'message' => __('Easytransac refund processed')
                    ]);
                }
            }
        } catch (Exception $e) {
            $this->logger->info('Easytransac payment notification: ' . $e->getMessage());
            return $resultJson->setData([
                'success' => false,
                'message' => __($e->getMessage())
            ]);
        }
    }

    /**
     * Create exception in case CSRF validation failed.
     *
     * Return null if default exception will suffice.
     *
     * @param RequestInterface $request
     * @return InvalidRequestException|null
     */
    public function createCsrfValidationException(
        RequestInterface $request
    ): ?InvalidRequestException {
        return null;
    }

    /**
     * Perform custom request validation.
     *
     * Return null if default validation is needed.
     *
     * @param RequestInterface $request
     * @return bool|null
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}

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

namespace Easytransac\Gateway\Gateway\Http\Client;

use Easytransac\Gateway\Model\EasyTransacApi\PaymentPageApi;
use Exception;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;

class TransactionCapture implements ClientInterface
{
    /**
     * @var PaymentPageApi
     */
    private $paymentPageApi;

    /**
     * TransactionCapture Constructor
     *
     * @param PaymentPageApi $paymentPageApi
     */
    public function __construct(
        PaymentPageApi $paymentPageApi
    ) {
        $this->paymentPageApi = $paymentPageApi;
    }

    /**
     * Places request to gateway
     *
     * @param TransferInterface $transferObject
     * @return array
     * @throws CouldNotSaveException
     */
    public function placeRequest(TransferInterface $transferObject): array
    {
        try {
            return $this->paymentPageApi->getPaymentPageApi($transferObject->getBody());
        } catch (Exception $exception) {
            throw new CouldNotSaveException(
                __($exception->getMessage())
            );
        }
    }
}

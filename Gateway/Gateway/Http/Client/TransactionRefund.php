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

use Easytransac\Gateway\Model\EasyTransacApi\RefundApi;
use Exception;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;

class TransactionRefund implements ClientInterface
{
    /**
     * @var RefundApi
     */
    private $refundApi;

    /**
     * TransactionCapture Constructor
     *
     * @param RefundApi $refundApi
     */
    public function __construct(
        RefundApi $refundApi
    ) {
        $this->refundApi = $refundApi;
    }

    /**
     * Place request for order
     *
     * @param TransferInterface $transferObject
     * @return array
     * @throws CouldNotSaveException
     */
    public function placeRequest(TransferInterface $transferObject): array
    {
        try {
            return $this->refundApi->getPaymentRefund($transferObject->getBody());
        } catch (Exception $exception) {
            throw new CouldNotSaveException(
                __($exception->getMessage())
            );
        }
    }
}

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

use EasyTransac\Entities\Refund;
use EasyTransac\Requests\PaymentRefund;
use Magento\Framework\Exception\LocalizedException;

class RefundApi extends AbstractApiCall
{
    /**
     * Get Payment Page
     *
     * @param array $data
     * @return array
     * @throws LocalizedException
     */
    public function getPaymentRefund(array $data): array
    {
        $this->getApiService();

        //Request for Refund
        $transaction = (new Refund())
            ->setAmount($data['Amount'])
            ->setTid($data['Tid']);

        if ($this->getDebug()) {
            $this->logger->info("Refund Request : ", $transaction->toArray());
        }

        $dp = new PaymentRefund();
        $response = $dp->execute($transaction);

        $logMessage = "Refund API Response : - ";
        return $this->getResponseData($response, $logMessage);
    }
}

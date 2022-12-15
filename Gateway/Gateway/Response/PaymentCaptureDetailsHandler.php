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

namespace Easytransac\Gateway\Gateway\Response;

use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Model\Order\Payment;

class PaymentCaptureDetailsHandler implements HandlerInterface
{
    public const CAPTURED = 'captured';

    /**
     * Handles response
     *
     * @param array $handlingSubject
     * @param array $response
     * @return void
     */
    public function handle(array $handlingSubject, array $response)
    {

        $payment = SubjectReader::readPayment($handlingSubject);

        if ($response["response"] === self::CAPTURED) {
            $this->setInvoiceToPending($payment);
        }
    }

    /**
     * Set payment to pending to ensure that the invoice is created in an OPEN state
     *
     * @param Payment $payment
     * @return mixed
     */
    private function setInvoiceToPending($payment)
    {
        $payment->setIsTransactionPending(false);
        // Do not close parent authorisation since order can still be cancelled/refunded
        $payment->setShouldCloseParentTransaction(false);

        return $payment;
    }
}

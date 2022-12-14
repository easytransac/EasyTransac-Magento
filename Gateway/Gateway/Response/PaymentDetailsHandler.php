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
use Magento\Sales\Api\Data\OrderPaymentInterface;

class PaymentDetailsHandler implements HandlerInterface
{
    public const REQUEST_ID = 'RequestId';

    public const OPERATION_TYPE = 'OperationType';

    public const PAYMENT_METHOD = 'PaymentMethod';

    public const AMOUNT = 'Amount';

    /**
     * List of additional details
     *
     * @var array
     */
    protected $additionalInformationMapping = [
        self::REQUEST_ID,
        self::OPERATION_TYPE,
        self::PAYMENT_METHOD,
        self::AMOUNT
    ];

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * Constructor
     *
     * @param SubjectReader $subjectReader
     */
    public function __construct(SubjectReader $subjectReader)
    {
        $this->subjectReader = $subjectReader;
    }

    /**
     * Handles response
     *
     * @param array $handlingSubject
     * @param array $response
     * @return void
     */
    public function handle(array $handlingSubject, array $response)
    {
        $paymentDO = $this->subjectReader->readPayment($handlingSubject);

        /** @var OrderPaymentInterface $payment */
        $payment = $paymentDO->getPayment();

        foreach ($this->additionalInformationMapping as $additionalInformationKey) {
            if (isset($additionalData[$additionalInformationKey])) {
                $payment->setAdditionalInformation(
                    $additionalInformationKey,
                    $additionalData[$additionalInformationKey]
                );
            }
        }
    }
}

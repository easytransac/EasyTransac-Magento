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

namespace Easytransac\Gateway\Gateway\Request;

use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Helper\Formatter;
use Magento\Checkout\Model\Session as CheckoutSession;

class PaymentDataBuilder implements BuilderInterface
{
    use Formatter;

    public const AMOUNT = 'Amount';
    public const ORDER_ID = 'OrderId';

    /**
     * @var SubjectReader
     */
    private $subjectReader;
    /**
     * @var CheckoutSession
     */
    private $session;

    /**
     * Constructor
     *
     * @param SubjectReader $subjectReader
     * @param CheckoutSession $session
     */
    public function __construct(
        SubjectReader $subjectReader,
        CheckoutSession $session
    ) {
        $this->subjectReader = $subjectReader;
        $this->session = $session;
    }

    /**
     * Builds gateway transfer object
     *
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject): array
    {
        $amount = $this->subjectReader->readAmount($buildSubject);

        return [
            self::AMOUNT => $this->formatPrice($amount)  * 100,
            self::ORDER_ID => $this->session->getQuoteId()
        ];
    }
}

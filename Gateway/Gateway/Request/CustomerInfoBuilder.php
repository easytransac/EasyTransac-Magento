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
use Magento\Customer\Model\Session;

class CustomerInfoBuilder implements BuilderInterface
{
    public const CUSTOMER = 'customer';
    public const FIRST_NAME = 'Firstname';
    public const LAST_NAME = 'Lastname';
    public const COMPANY = 'company';
    public const EMAIL = 'email';
    public const PHONE = 'phone';
    public const USER_ID = 'Uid';

    /**
     * @var SubjectReader
     */
    private SubjectReader $subjectReader;
    /**
     * @var Session
     */
    private Session $session;

    /**
     * Constructor
     *
     * @param SubjectReader $subjectReader
     * @param Session $session
     */
    public function __construct(
        SubjectReader $subjectReader,
        Session $session
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
        $paymentDO = $this->subjectReader->readPayment($buildSubject);

        $order = $paymentDO->getOrder();
        $billingAddress = $order->getBillingAddress();
        $custId = $this->session->getCustomerId();
        return [
                self::FIRST_NAME => $billingAddress->getFirstname(),
                self::LAST_NAME => $billingAddress->getLastname(),
                self::COMPANY => $billingAddress->getCompany() ?? '',
                self::PHONE => $billingAddress->getTelephone(),
                self::EMAIL => $billingAddress->getEmail(),
                self::USER_ID => $custId
        ];
    }
}

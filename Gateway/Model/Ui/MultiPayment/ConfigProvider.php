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

namespace Easytransac\Gateway\Model\Ui\MultiPayment;

use Easytransac\Gateway\Model\Config;
use Easytransac\Gateway\Model\ResourceModel\CreditCard;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Customer\Model\Session as CustomerSession;

class ConfigProvider implements ConfigProviderInterface
{
    public const EASYTRANSAC_MULTIPAYMENT = 'easytransac_multipayment';
    /**
     * @var Config
     */
    private Config $config;
    /**
     * @var CreditCard
     */
    private $creditCard;
    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * Multipayment Constructor
     *
     * @param Config $config
     * @param CreditCard $creditCard
     * @param CustomerSession $customerSession
     */
    public function __construct(
        Config $config,
        CreditCard $creditCard,
        CustomerSession $customerSession
    ) {
        $this->config = $config;
        $this->creditCard = $creditCard;
        $this->customerSession = $customerSession;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig(): array
    {
        $customerId = (int)$this->customerSession->getCustomerId();
        return [
            'payment' => [
                self::EASYTRANSAC_MULTIPAYMENT => [
                    'checkouticon' => $this->config->getVisibleIconCheckout(),
                    'monthly_emi' => $this->config->getConfig('payment/easytransac_multipayment/monthly_emi'),
                    'saveCCDataEnabled' => $this->config->getSaveCardCheckoutForMultipayment(),
                    'savedPaymentData' => $this->creditCard->getSavedPaymentData($customerId)
                ]
            ]
        ];
    }
}

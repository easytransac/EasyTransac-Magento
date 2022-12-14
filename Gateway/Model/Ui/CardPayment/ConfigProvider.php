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

namespace Easytransac\Gateway\Model\Ui\CardPayment;

use Easytransac\Gateway\Model\Config;
use Easytransac\Gateway\Model\ResourceModel\CreditCard;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Customer\Model\Session as CustomerSession;

class ConfigProvider implements ConfigProviderInterface
{
    public const EASYTRANSAC_CC = 'card_gateway';

    /**
     * @var Config
     */
    private Config $config;
    /**
     * @var Session
     */
    private $checkoutSession;
    /**
     * @var CreditCard
     */
    private $creditCard;
    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @param Session $checkoutSession
     * @param Config $config
     * @param CreditCard $creditCard
     * @param CustomerSession $customerSession
     */
    public function __construct(
        Session $checkoutSession,
        Config $config,
        CreditCard $creditCard,
        CustomerSession $customerSession
    ) {
        $this->config = $config;
        $this->checkoutSession = $checkoutSession;
        $this->creditCard = $creditCard;
        $this->customerSession = $customerSession;
    }

    /**
     * Get config
     *
     * @return \array[][]
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getConfig(): array
    {
        $customerId = (int)$this->customerSession->getCustomerId();
        return [
            'payment' => [
                self::EASYTRANSAC_CC => [
                    'total' => $this->getQuote()->getGrandTotal(),
                    'checkouticon' => $this->getCheckoutIcon(),
                    'saveCCDataEnabled' => $this->config->getSaveCardCheckout(),
                    'savedPaymentData' => $this->creditCard->getSavedPaymentData($customerId),
                ]
            ]
        ];
    }
    /**
     * Get quote
     *
     * @return CartInterface|Quote
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getQuote()
    {
        return $this->checkoutSession->getQuote();
    }

    /**
     * Value of Display Icon at checkout
     *
     * @return bool
     */
    public function getCheckoutIcon(): bool
    {
        return $this->config->getVisibleIconCheckout();
    }
}

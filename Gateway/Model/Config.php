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

namespace Easytransac\Gateway\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    public const XML_PATH_TO_DEBIG_LOG = 'payment/general/debug';
    public const XML_PATH_TO_API_KEY = 'payment/general/api_key';
    public const XML_PATH_TO_CHECKOUT_ICON = 'payment/general/display_icon';
    public const XML_PATH_TO_SAVE_CARD_STATUS = 'payment/card_gateway/save_card';
    public const XML_PATH_TO_MULTIPAYMENT_SAVE_CARD_STATUS = 'payment/easytransac_multipayment/save_card';
    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;
    /**
     * @var EncryptorInterface
     */
    private EncryptorInterface $encryptor;
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param EncryptorInterface $encryptor
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface $encryptor,
        UrlInterface $urlBuilder
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->encryptor = $encryptor;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Get config value
     *
     * @param string $path
     * @param string|null $scopeCode
     * @param string|null $scopeType
     * @return string|null
     */
    public function getConfig(
        string  $path,
        ?string $scopeCode = null,
        ?string $scopeType = ScopeInterface::SCOPE_STORE
    ): ?string {
        return $this->scopeConfig->getValue($path, $scopeType, $scopeCode);
    }

    /**
     * Get config value of debuger
     *
     * @return bool
     */
    public function getDebugLog(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_TO_DEBIG_LOG);
    }

    /**
     * Get config value of API key
     *
     * @return string|null
     */
    public function getApiKey(): ?string
    {
        $key = $this->getConfig(self::XML_PATH_TO_API_KEY);
        return $this->encryptor->decrypt($key);
    }

    /**
     * Get config value of Display Icon at checkout
     *
     * @return bool
     */
    public function getVisibleIconCheckout(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_TO_CHECKOUT_ICON);
    }

    /**
     * Get config value of Save card at checkout
     *
     * @return bool
     */
    public function getSaveCardCheckout(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_TO_SAVE_CARD_STATUS);
    }

    /**
     * Get config value of Save card at checkout
     *
     * @return bool
     */
    public function getSaveCardCheckoutForMultipayment(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_TO_MULTIPAYMENT_SAVE_CARD_STATUS);
    }

    /**
     * Get store return URL
     *
     * @return string
     */
    public function getStoreReturnUrl(): string
    {
        return $this->urlBuilder->getUrl('easytransac/payment/returnpage');
    }
}

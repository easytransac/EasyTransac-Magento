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

namespace Easytransac\Gateway\Block\Adminhtml\System\Config\Form;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\Helper\SecureHtmlRenderer;

/**
 * Notification URL field info block.
 */
class NotificationUrl extends Field
{
    public const NOTIFICATION_URL = 'easytransac/payment/notification';
    /**
     * Notification Url Constructor
     *
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param array $data
     * @param SecureHtmlRenderer|null $secureRenderer
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        array $data = [],
        ?SecureHtmlRenderer $secureRenderer = null,
    ) {
        parent::__construct($context, $data, $secureRenderer);
        $this->storeManager = $storeManager;
    }

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Prepare global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate('Easytransac_Gateway::config/notificaitonUrl.phtml');
        }
        return $this;
    }

    /**
     * Get html
     *
     * @param AbstractElement $element
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    /**
     * Get Notification URL
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getNotificationUrl(): string
    {
        $baseUrl = $this->storeManager->getStore()->getBaseUrl();
        return $baseUrl . self::NOTIFICATION_URL;
    }
}

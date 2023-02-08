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

namespace Easytransac\Gateway\ViewModel;

use Easytransac\Gateway\Model\ResourceModel\CreditCard;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class SavedCards implements ArgumentInterface
{
    /**
     * @var CreditCard
     */
    private $creditCard;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * SaveCards Constructor
     *
     * @param CustomerSession $customerSession
     * @param CreditCard $creditCard
     */
    public function __construct(
        CustomerSession $customerSession,
        CreditCard $creditCard
    ) {
        $this->creditCard = $creditCard;
        $this->customerSession = $customerSession;
    }

    /**
     * Get Current customers saved card details
     *
     * @return array
     * @throws LocalizedException
     */
    public function getCardData(): array
    {
        $customerId = (int)$this->customerSession->getCustomerId();

        return $this->creditCard->getSavedPaymentData($customerId);
    }
}

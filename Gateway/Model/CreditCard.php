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

use Easytransac\Gateway\Api\Data\CreditCardInterface;
use Magento\Framework\Model\AbstractModel;
use Easytransac\Gateway\Model\ResourceModel\CreditCard as ResourceCreditCard;

class CreditCard extends AbstractModel implements CreditCardInterface
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct() // @codingStandardsIgnoreLine
    {
        $this->_init(ResourceCreditCard::class);
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * Get Customer Id
     *
     * @return int
     */
    public function getCustomerId(): int
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * Set Customer ID
     *
     * @param int $value
     * @return self
     */
    public function setCustomerId(int $value): CreditCardInterface
    {
        return $this->setData(self::ID, $value);
    }

    /**
     * Get CC number
     *
     * @return string
     */
    public function getCcNumber(): string
    {
        return $this->getData(self::CC_NUMBER);
    }

    /**
     * Set CC number
     *
     * @param string $value
     * @return self
     */
    public function setCcNumber(string $value): CreditCardInterface
    {
        return $this->setData(self::ID, $value);
    }

    /**
     * Get CC month
     *
     * @return int
     */
    public function getCcMonth(): int
    {
        return $this->getData(self::CC_MONTH);
    }

    /**
     * Set CC month
     *
     * @param int $value
     * @return self
     */
    public function setCcMonth(int $value): CreditCardInterface
    {
        return $this->setData(self::ID, $value);
    }

    /**
     * Get CC year
     *
     * @return int
     */
    public function getCcYear(): int
    {
        return $this->getData(self::CC_YEAR);
    }

    /**
     * Set CC year
     *
     * @param int $value
     * @return self
     */
    public function setCcYear(int $value): CreditCardInterface
    {
        return $this->setData(self::ID, $value);
    }

    /**
     * Get CC type
     *
     * @return string
     */
    public function getCcType(): string
    {
        return $this->getData(self::CC_TYPE);
    }

    /**
     * Set CC type
     *
     * @param string $value
     * @return self
     */
    public function setCcType(string $value): CreditCardInterface
    {
        return $this->setData(self::ID, $value);
    }

    /**
     * Get CC Alias
     *
     * @return string|null
     */
    public function getAlias(): ?string
    {
        return $this->getData(self::ALIAS);
    }

    /**
     * Set CC Alias
     *
     * @param string $value
     * @return self
     */
    public function setAlias(string $value): CreditCardInterface
    {
        return $this->setData(self::ALIAS, $value);
    }

    /**
     * Get EasyTransac UserId
     *
     * @return string|null
     */
    public function getUserId(): ?string
    {
        return $this->getData(self::USER_ID);
    }

    /**
     * Set EasyTransac UserId
     *
     * @param string $value
     * @return self
     */
    public function setUserId(string $value): CreditCardInterface
    {
        return $this->setData(self::USER_ID, $value);
    }
}

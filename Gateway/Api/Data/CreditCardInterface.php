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

namespace Easytransac\Gateway\Api\Data;

interface CreditCardInterface
{
    public const ID = 'id';
    public const CUSTOMER_ID = 'customer_id';
    public const CC_NUMBER = 'cc_number';
    public const CC_MONTH = 'cc_month';
    public const CC_YEAR = 'cc_year';
    public const CC_TYPE = 'cc_type';
    public const ALIAS = 'alias';
    public const USER_ID = 'user_id';

    /**
     * Get Id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get Customer Id
     *
     * @return int
     */
    public function getCustomerId(): int;

    /**
     * Set Customer Id
     *
     * @param int $value
     * @return self
     */
    public function setCustomerId(int $value): CreditCardInterface;

    /**
     * Get CC number
     *
     * @return string
     */
    public function getCcNumber(): string;

    /**
     * Set CC number
     *
     * @param string $value
     * @return self
     */
    public function setCcNumber(string $value): CreditCardInterface;

    /**
     * Get CC month
     *
     * @return int
     */
    public function getCcMonth(): int;

    /**
     * Set CC month
     *
     * @param int $value
     * @return self
     */
    public function setCcMonth(int $value): CreditCardInterface;

    /**
     * Get CC year
     *
     * @return int
     */
    public function getCcYear(): int;

    /**
     * Set CC year
     *
     * @param int $value
     * @return self
     */
    public function setCcYear(int $value): CreditCardInterface;

    /**
     * Get CC type
     *
     * @return string
     */
    public function getCcType(): string;

    /**
     * Set CC type
     *
     * @param string $value
     * @return self
     */
    public function setCcType(string $value): CreditCardInterface;

    /**
     * Get CC Alias
     *
     * @return string|null
     */
    public function getAlias(): ?string;

    /**
     * Set CC Alias
     *
     * @param string $value
     * @return self
     */
    public function setAlias(string $value): CreditCardInterface;

    /**
     * Get EasyTransac UserId
     *
     * @return string|null
     */
    public function getUserId(): ?string;

    /**
     * Set EasyTransac UserId
     *
     * @param string $value
     * @return self
     */
    public function setUserId(string $value): CreditCardInterface;
}

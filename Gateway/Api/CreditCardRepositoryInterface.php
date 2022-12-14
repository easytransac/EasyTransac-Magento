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

namespace Easytransac\Gateway\Api;

use Easytransac\Gateway\Api\Data\CreditCardInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;

interface CreditCardRepositoryInterface
{
    /**
     * Save credit-card details.
     *
     * @param CreditCardInterface $creditCard
     * @return CreditCardInterface
     * @throws LocalizedException
     */
    public function save(CreditCardInterface $creditCard): CreditCardInterface;

    /**
     * Retrieve credit card.
     *
     * @param int $ccId
     * @return CreditCardInterface
     * @throws LocalizedException
     */
    public function getById(int $ccId): CreditCardInterface;

    /**
     * Retrieve credit card matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return Data\CreditCardSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}

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

use Magento\Framework\Api\SearchResultsInterface;

interface CreditCardSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get items.
     *
     * @return CreditCardInterface[]
     */
    public function getItems();

    /**
     * Set items.
     *
     * @param CreditCardInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

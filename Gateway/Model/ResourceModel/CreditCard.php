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

namespace Easytransac\Gateway\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CreditCard extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init('easytransac_cc_details', 'id');
    }

    /**
     * Get Saved payment details from customer id
     *
     * @param int $customerId
     * @return array
     * @throws LocalizedException
     */
    public function getSavedPaymentData(int $customerId): array
    {
        if (!$customerId) {
            return [];
        }

        $select = $this->getConnection()->select()
            ->from($this->getMainTable())
            ->where("customer_id = :customerId");

        $params = ['customerId' => $customerId];

        return $this->getConnection()->fetchAll($select, $params);
    }
}

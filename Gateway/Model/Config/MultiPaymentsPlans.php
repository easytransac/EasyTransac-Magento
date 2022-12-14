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

namespace Easytransac\Gateway\Model\Config;

use Magento\Framework\Data\OptionSourceInterface;

class MultiPaymentsPlans implements OptionSourceInterface
{
    /**
     * Display Multipayments terns for order place
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $options = [];
        foreach ($this->getOptions() as $value => $label) {
            $options[] = [
                'label' => $label,
                'value' => $value + 3
            ];
        }
        return $options;
    }

    /**
     * Retrieve options for Multipayments
     *
     * @return array
     */
    public function getOptions(): array
    {
        return [3,4,5,6,7,8,9,10,11,12];
    }
}

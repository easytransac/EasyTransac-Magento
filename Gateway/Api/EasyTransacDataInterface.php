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

interface EasyTransacDataInterface
{
    /**
     * Get 3DSecure Link
     *
     * @param int $orderId
     * @return string|null
     */
    public function handleInternalRequest(int $orderId): ?string;
}

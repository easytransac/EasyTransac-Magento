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

namespace Easytransac\Gateway\Model\EasyTransacApi;

use Easytransac\Gateway\Model\Config;
use Easytransac\Gateway\Model\Logger\Logger;
use EasyTransac\Core\Services;
use Exception;
use Magento\Framework\Exception\LocalizedException;

abstract class AbstractApiCall
{
    /**
     * @var Config
     */
    protected $config;
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * Constructor
     *
     * @param Config $config
     * @param Logger $logger
     */
    public function __construct(
        Config $config,
        Logger $logger
    ) {
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * Provide ApiKey
     *
     * @return void
     */
    public function getApiService()
    {
        Services::getInstance()->provideAPIKey($this->getApiKey());
    }

    /**
     * Convert Api Response to Array
     *
     * @param object $response
     * @param string $logMessage
     * @return array
     * @throws LocalizedException
     */
    public function getResponseData(object $response, string $logMessage): array
    {
        try {
            if ($this->getDebug()) {
                $this->logger->info($logMessage, $response->getRealArrayResponse());
            }
            if ($response->isSuccess()) {
                return $response->getContent()->toArray();
            } else {
                $this->logger->info("Easytransac Response : " . $response->getErrorMessage());
                throw new LocalizedException(
                    __($response->getErrorMessage())
                );
            }
        } catch (Exception $e) {
            $this->logger->info("Error Response : " . $e->getMessage());
            throw new LocalizedException(
                __($response->getErrorMessage())
            );
        }
    }

    /**
     * Check Debugger status
     *
     * @return bool
     */
    public function getDebug(): bool
    {
        return $this->config->getDebugLog();
    }

    /**
     * Get Api Key From Store Config
     *
     * @return string|null
     */
    public function getApiKey()
    {
        return $this->config->getApiKey();
    }
}

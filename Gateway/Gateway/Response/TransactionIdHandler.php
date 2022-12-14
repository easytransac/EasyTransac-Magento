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

namespace Easytransac\Gateway\Gateway\Response;

use Easytransac\Gateway\Model\Config;
use Easytransac\Gateway\Model\Logger\Logger;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;

class TransactionIdHandler implements HandlerInterface
{
    /**
     * @var SubjectReader
     */
    private SubjectReader $subjectReader;
    /**
     * @var Logger
     */
    private Logger $logger;
    /**
     * @var Config
     */
    private Config $config;

    /**
     * Constructor
     *
     * @param SubjectReader $subjectReader
     * @param Config $config
     * @param Logger $logger
     */
    public function __construct(
        SubjectReader $subjectReader,
        Config $config,
        Logger $logger
    ) {
        $this->subjectReader = $subjectReader;
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * Handles response
     *
     * @param array $handlingSubject
     * @param array $response
     * @return void
     * @throws CouldNotSaveException
     */
    public function handle(array $handlingSubject, array $response): void
    {
        $debuger = $this->config->getDebugLog();

        if ($debuger) {
            $this->logger->info("TransactionHandler Response :- ", $response);
        }

        $paymentDO = $this->subjectReader->readPayment($handlingSubject);
        $payment = $paymentDO->getPayment();

        try {
            if (array_key_exists('PageUrl', $response)) {
                $payment->setAdditionalInformation('3DSecureUrl', $response['PageUrl']);
            }
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __($response)
            );
        }
    }
}

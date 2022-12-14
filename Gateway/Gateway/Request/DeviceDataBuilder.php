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

namespace Easytransac\Gateway\Gateway\Request;

use Easytransac\Gateway\Model\Config;
use Magento\Framework\HTTP\Header;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

class DeviceDataBuilder implements BuilderInterface
{
    public const CLIENT_IP = 'ClientIp';
    public const USER_AGENT = 'UserAgent';
    public const SAVE_CARD = 'SaveCard';
    public const ALIAS_ID = 'AliasId';
    public const EMI_MONTHS = 'emiMonths';

    /**
     * @var SubjectReader
     */
    private $subjectReader;
    /**
     * @var RemoteAddress
     */
    private $remoteAddress;
    /**
     * @var Header
     */
    private $header;
    /**
     * @var Config
     */
    private $config;

    /**
     * DeviceDataBuilder constructor
     *
     * @param SubjectReader $subjectReader
     * @param RemoteAddress $remoteAddress
     * @param Header $header
     * @param Config $config
     */
    public function __construct(
        SubjectReader $subjectReader,
        RemoteAddress $remoteAddress,
        Header $header,
        Config $config
    ) {
        $this->subjectReader = $subjectReader;
        $this->remoteAddress = $remoteAddress;
        $this->header = $header;
        $this->config = $config;
    }

    /**
     * Builds gateway transfer object
     *
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject): array
    {
        $result = [];
        $paymentDO = $this->subjectReader->readPayment($buildSubject);
        $payment = $paymentDO->getPayment();
        $data = $payment->getAdditionalInformation();

        $clientIp = $this->remoteAddress->getRemoteAddress();
        $userAgent = $this->header->getHttpUserAgent();

        return [
            self::CLIENT_IP => $clientIp,
            self::USER_AGENT => $userAgent,
            self::SAVE_CARD => $data['saveCard'],
            self::ALIAS_ID => $data['aliasId'],
            self::EMI_MONTHS => $data['emiMonths']
        ];
    }
}

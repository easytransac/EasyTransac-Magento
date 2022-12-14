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

use Magento\Directory\Model\CountryFactory;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

class AddressDataBuilder implements BuilderInterface
{
    public const FIRST_NAME = 'Firstname';
    public const LAST_NAME = 'Lastname';
    public const CITY = 'City';
    public const ZIPCODE = 'ZipCode';
    public const EMAIL = 'Email';
    public const PHONE = 'Phone';
    public const ADDRESS = 'Address';
    public const COUNTRY = 'country';

    /**
     * @var SubjectReader
     */
    private SubjectReader $subjectReader;
    /**
     * @var CountryFactory
     */
    private CountryFactory $countryFactory;

    /**
     * Constructor
     *
     * @param SubjectReader $subjectReader
     * @param CountryFactory $countryFactory
     */
    public function __construct(
        SubjectReader $subjectReader,
        CountryFactory $countryFactory
    ) {
        $this->subjectReader = $subjectReader;
        $this->countryFactory = $countryFactory;
    }

    /**
     * Builds gateway transfer object
     *
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject): array
    {
        $paymentDO = $this->subjectReader->readPayment($buildSubject);

        $order = $paymentDO->getOrder();
        $result = [];

        $shippingAddress = $order->getShippingAddress();
        if ($shippingAddress) {
            $street = $shippingAddress->getStreet();
            $address = implode(', ', $street);

            $result = [
                self::FIRST_NAME => $shippingAddress->getFirstname(),
                self::LAST_NAME => $shippingAddress->getLastname(),
                self::CITY => $shippingAddress->getCity(),
                self::ZIPCODE => $shippingAddress->getPostcode(),
                self::EMAIL => $shippingAddress->getEmail(),
                self::PHONE => $shippingAddress->getTelephone(),
                self::COUNTRY => $this->getCountryName($shippingAddress->getCountryId()),
                self::ADDRESS => $address
            ];
        }

        return $result;
    }

    /**
     * Country full name
     *
     * @param string $countryId
     * @return string
     */
    public function getCountryName(string $countryId): string
    {
        $countryName = '';
        $country = $this->countryFactory->create()->loadByCode($countryId);
        if ($country) {
            $countryName = $country->getName();
        }
        return $countryName;
    }
}

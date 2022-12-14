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

namespace Easytransac\Gateway\Model;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Model\Session as CustomerSession;

class EasyTransacClientIdMapper
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;
    /**
     * @var CustomerSession
     */
    private $customerSession;
    /**
     * @var CustomerInterface
     */
    private $customer;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerSession $customerSession
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        CustomerSession $customerSession
    ) {
        $this->customerRepository = $customerRepository;
        $this->customerSession = $customerSession;
    }

    /**
     * Save Client id with customer attribute
     *
     * @param string $clientId EasyTransac ClientId.
     * @param int $customerId CustomerId
     * @return void
     * @throws CouldNotSaveException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function saveClientIdForCustomerId(string $clientId, int $customerId)
    {
        $customer = $this->getCustomer($customerId);
        if ($customer === null) {
            return null;
        }
        $customer->setCustomAttribute('easytransac_clientid', $clientId);
        try {
            $this->customerRepository->save($customer);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
    }

    /**
     * Get EasyTransac Client Id
     *
     * @param string $customerId
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getEasyTransacClientId(string $customerId): string
    {
        $easytransacClientId = "";
        if ($customerId != '' && $this->customerSession->isLoggedIn()) {
            $attribute = $this->getCustomer((int)$customerId)->getCustomAttribute('easytransac_clientid');
            if ($attribute) {
                $easytransacClientId = $attribute->getValue();
            }
        }
        return $easytransacClientId;
    }

    /**
     * Returns logged customer.
     *
     * @param int $customerId
     * @return CustomerInterface|null
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function getCustomer(int $customerId): ?CustomerInterface
    {
        if (!$this->customer) {
            if ($customerId) {
                $this->customer = $this->customerRepository->getById($customerId);
            } else {
                return null;
            }
        }
        return $this->customer;
    }
}

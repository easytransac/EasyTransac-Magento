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

use Easytransac\Gateway\Api\CreditCardRepositoryInterface;
use Easytransac\Gateway\Api\Data\CreditCardInterfaceFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;

class SaveCreditCard
{
    /**
     * @var CreditCardInterfaceFactory
     */
    private $creditCardInterfaceFactory;
    /**
     * @var CreditCardRepositoryInterface
     */
    private $creditCardRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param CreditCardInterfaceFactory $creditCardInterfaceFactory
     * @param CreditCardRepositoryInterface $creditCardRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        CreditCardInterfaceFactory $creditCardInterfaceFactory,
        CreditCardRepositoryInterface $creditCardRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->creditCardInterfaceFactory = $creditCardInterfaceFactory;
        $this->creditCardRepository = $creditCardRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Save Card Details
     *
     * @param int $customerId
     * @param array $responseData
     * @return void
     * @throws LocalizedException
     */
    public function saveCardDetails(
        int $customerId,
        array $responseData
    ) {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('customer_id', $customerId)
            ->addFilter('cc_month', $responseData['CardMonth'])
            ->addFilter('cc_year', $responseData['CardYear'])
            ->addFilter('cc_type', $responseData['CardType'])
            ->addFilter('cc_number', $responseData['CardNumber'])
            ->create();

        $saveCardItems = $this->creditCardRepository->getList($searchCriteria);
        $cardAlreadyExist = $saveCardItems->getTotalCount();

        if ($cardAlreadyExist <= 0) {
            $CcData = $this->creditCardInterfaceFactory->create();
            $cc = [
                'cc_number' => $responseData['CardNumber'],
                'cc_month' => $responseData['CardMonth'],
                'cc_year' => $responseData['CardYear'],
                'cc_type' => $responseData['CardType'],
                'alias' => $responseData['Alias'],
                'customer_id' => $customerId,
                'user_id' => $responseData['UserId']
            ];
            $CcData->setData($cc);
            $this->creditCardRepository->save($CcData);
        }
    }
}

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
use Easytransac\Gateway\Api\Data\CreditCardInterface;
use Easytransac\Gateway\Api\Data\CreditCardSearchResultsInterfaceFactory as SearchResultFactory;
use Easytransac\Gateway\Api\Data\CreditCardSearchResultsInterface;
use Easytransac\Gateway\Model\ResourceModel\CreditCard as ResourceCreditCard;
use Easytransac\Gateway\Model\ResourceModel\CreditCard\CollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class CreditCardRepository implements CreditCardRepositoryInterface
{
    /**
     * @var ResourceCreditCard
     */
    private $resourceCreditCard;
    /**
     * @var CreditCardFactory
     */
    private $creditCardFactory;
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;
    /**
     * @var SearchResultFactory
     */
    private $searchResultsFactory;

    /**
     * Repository Constructor
     *
     * @param ResourceCreditCard $resourceCreditCard
     * @param CreditCardFactory $creditCardFactory
     * @param CollectionFactory $collectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SearchResultFactory $searchResultsFactory
     */
    public function __construct(
        ResourceCreditCard $resourceCreditCard,
        CreditCardFactory  $creditCardFactory,
        CollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        SearchResultFactory $searchResultsFactory
    ) {
        $this->resourceCreditCard = $resourceCreditCard;
        $this->creditCardFactory = $creditCardFactory;
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * Save credit-card details.
     *
     * @param CreditCardInterface $creditCard
     * @return CreditCardInterface
     * @throws CouldNotSaveException
     */
    public function save(CreditCardInterface $creditCard): CreditCardInterface
    {
        try {
            $this->resourceCreditCard->save($creditCard);
        } catch (LocalizedException $exception) {
            throw new CouldNotSaveException(
                __('Could not save the card: %1', $exception->getMessage()),
                $exception
            );
        }
        return $creditCard;
    }

    /**
     * Retrieve credit card.
     *
     * @param int $ccId
     * @return CreditCardInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $ccId): CreditCardInterface
    {
        $creditCard = $this->creditCardFactory->create();
        $this->resourceCreditCard->load($creditCard, $ccId);
        if (!$creditCard->getId()) {
            throw new NoSuchEntityException(__('The CC details with the "%1" ID doesn\'t exist.', $ccId));
        }
        return $creditCard;
    }

    /**
     * Load credit card data collection by given search criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return CreditCardSearchResultsInterface;
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $collection = $this->collectionFactory->create();

        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        $sortOrdersData = $searchCriteria->getSortOrders();
        if ($sortOrdersData) {
            foreach ($sortOrdersData as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $searchResults->setItems($collection->getData());
        return $searchResults;
    }
}

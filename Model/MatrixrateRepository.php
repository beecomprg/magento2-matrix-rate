<?php

namespace Beecom\MatrixRate\Model;

use Beecom\MatrixRate\Api\MatrixrateRepositoryInterface;
use Beecom\MatrixRate\Api\MatrixrateRepositoryInterfaceFactory;
use Beecom\MatrixRate\Api\Data;
use Beecom\MatrixRate\Model\ResourceModel\Carrier\Matrixrate as ResourceBlock;
use Beecom\MatrixRate\Model\ResourceModel\Carrier\Matrixrate\CollectionFactory as BlockCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class MatrixrateRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MatrixrateRepository implements MatrixrateRepositoryInterface
{
    protected $resource;

    /**
     * @var MatrixrateFactory
     */
    protected $matrixrateFactory;

    /**
     * @var MatrixrateCollectionFactory
     */
    protected $matrixrateCollectionFactory;

    protected $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    protected $dataBlockFactory;

    private $storeManager;

    private $collectionProcessor;

    /**
     * MatrixrateRepository constructor.
     * @param ResourceBlock $resource
     * @param MatrixrateFactory $matrixrateFactory
     * @param Data\MatrixrateInterfaceFactory $dataBlockFactory
     * @param BlockCollectionFactory $matrixrateCollectionFactory
     * @param Data\MatrixrateSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface|null $collectionProcessor
     */
    public function __construct(
        ResourceBlock $resource,
        MatrixrateFactory $matrixrateFactory,
        \Beecom\MatrixRate\Api\Data\MatrixrateInterfaceFactory $dataBlockFactory,
        BlockCollectionFactory $matrixrateCollectionFactory,
        Data\MatrixrateSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor = null
    ) {
        $this->resource = $resource;
        $this->matrixrateFactory = $matrixrateFactory;
        $this->matrixrateCollectionFactory = $matrixrateCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataBlockFactory = $dataBlockFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor ?: $this->getCollectionProcessor();
    }

    public function save(Data\MatrixrateInterface $matrixrate)
    {
        if (empty($matrixrate->getStoreId())) {
            $matrixrate->setStoreId($this->storeManager->getStore()->getId());
        }

        try {
            $this->resource->save($matrixrate);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $matrixrate;
    }

    public function getById($matrixrateId)
    {
        $matrixrate = $this->matrixrateFactory->create();
        $this->resource->load($matrixrate, $matrixrateId);
        if (!$matrixrate->getId()) {
            throw new NoSuchEntityException(__('CMS Block with id "%1" does not exist.', $matrixrateId));
        }
        return $matrixrate;
    }

    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        $collection = $this->matrixrateCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    public function delete(Data\MatrixrateInterface $matrixrate)
    {
        try {
            $this->resource->delete($matrixrate);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    public function deleteById($matrixrateId)
    {
        return $this->delete($this->getById($matrixrateId));
    }

    private function getCollectionProcessor()
    {
        if (!$this->collectionProcessor) {
            $this->collectionProcessor = \Magento\Framework\App\ObjectManager::getInstance()->get(
                'Beecom\MatrixRate\Model\Api\SearchCriteria\MatrixrateCollectionProcessor'
            );
        }
        return $this->collectionProcessor;
    }
}

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
 * Class BlockRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MatrixrateRepository implements MatrixrateRepositoryInterface
{
    /**
     * @var ResourceBlock
     */
    protected $resource;

    /**
     * @var BlockFactory
     */
    protected $matrixrateFactory;

    /**
     * @var BlockCollectionFactory
     */
    protected $matrixrateCollectionFactory;

    /**
     * @var Data\BlockSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var \Magento\Cms\Api\Data\BlockInterfaceFactory
     */
    protected $dataBlockFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @param ResourceBlock $resource
     * @param BlockFactory $matrixrateFactory
     * @param Data\MatrixrateInterfaceFactory $dataBlockFactory
     * @param BlockCollectionFactory $matrixrateCollectionFactory
     * @param Data\MatrixrateSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
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

    /**
     * Save Block data
     *
     * @param \Magento\Cms\Api\Data\BlockInterface $matrixrate
     * @return Block
     * @throws CouldNotSaveException
     */
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

    /**
     * Load Block data by given Block Identity
     *
     * @param string $matrixrateId
     * @return Block
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($matrixrateId)
    {
        $matrixrate = $this->matrixrateFactory->create();
        $this->resource->load($matrixrate, $matrixrateId);
        if (!$matrixrate->getId()) {
            throw new NoSuchEntityException(__('CMS Block with id "%1" does not exist.', $matrixrateId));
        }
        return $matrixrate;
    }

    /**
     * Load Block data collection by given search criteria
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Magento\Cms\Api\Data\BlockSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        /** @var \Magento\Cms\Model\ResourceModel\Block\Collection $collection */
        $collection = $this->matrixrateCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        /** @var Data\BlockSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * Delete Block
     *
     * @param \Magento\Cms\Api\Data\BlockInterface $matrixrate
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(Data\MatrixrateInterface $matrixrate)
    {
        try {
            $this->resource->delete($matrixrate);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Delete Block by given Block Identity
     *
     * @param string $matrixrateId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($matrixrateId)
    {
        return $this->delete($this->getById($matrixrateId));
    }

    /**
     * Retrieve collection processor
     *
     * @deprecated 101.1.0
     * @return CollectionProcessorInterface
     */
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

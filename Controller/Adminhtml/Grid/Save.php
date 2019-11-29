<?php

namespace Beecom\MatrixRate\Controller\Adminhtml\Grid;

use Magento\Backend\App\Action\Context;
use Beecom\MatrixRate\Api\MatrixrateRepositoryInterface;
use Beecom\MatrixRate\Model\Matrixrate;
use Beecom\MatrixRate\Model\MatrixrateFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;

class Save extends \Beecom\MatrixRate\Controller\Adminhtml\Matrixrate
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var MatrixrateFactory
     */
    private $matrixrateFactory;

    /**
     * @var MatrixrateRepositoryInterface
     */
    private $matrixrateRepository;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param DataPersistorInterface $dataPersistor
     * @param MatrixrateFactory|null $matrixrateFactory
     * @param MatrixrateRepositoryInterface|null $matrixrateRepository
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        DataPersistorInterface $dataPersistor,
        MatrixrateFactory $matrixrateFactory = null,
        MatrixrateRepositoryInterface $matrixrateRepository = null
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->matrixrateFactory = $matrixrateFactory
            ?: \Magento\Framework\App\ObjectManager::getInstance()->get(MatrixrateFactory::class);
        $this->matrixrateRepository = $matrixrateRepository
            ?: \Magento\Framework\App\ObjectManager::getInstance()->get(MatrixrateRepositoryInterface::class);
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            if (isset($data['is_active']) && $data['is_active'] === 'true') {
                $data['is_active'] = Matrixrate::STATUS_ENABLED;
            }
            if (empty($data['rate_id'])) {
                $data['rate_id'] = null;
            }

            if (empty($data['dest_country_id'])) {
                $data['dest_country_id'] = '*';
            }

            /** @var \Beecom\MatrixRate\Model\Matrixrate $model */
            $model = $this->matrixrateFactory->create();

            $id = $this->getRequest()->getParam('rate_id');
            if ($id) {
                try {
                    $model = $this->matrixrateRepository->getById($id);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage(__('This matrixrate no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }

            $model->setData($data);

            try {
                $this->matrixrateRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the matrixrate.'));
                $this->dataPersistor->clear('beecom_matrixrate_matrixrate');
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['rate_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the matrixrate.'));
            }

            $this->dataPersistor->set('beecom_matrixrate_matrixrate', $data);
            return $resultRedirect->setPath('*/*/edit', ['rate_id' => $this->getRequest()->getParam('rate_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}

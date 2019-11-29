<?php

namespace Beecom\MatrixRate\Controller\Adminhtml\Grid;

use Beecom\MatrixRate\Api\MatrixrateRepositoryInterface;
use Beecom\MatrixRate\Model\MatrixrateFactory;

class Edit extends \Beecom\MatrixRate\Controller\Adminhtml\Matrixrate
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var MatrixrateRepositoryInterface
     */
    private $matrixrateRepository;

    /**
     * @var MatrixrateFactory
     */
    private $matrixrateFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        MatrixrateRepositoryInterface $matrixrateRepository,
        MatrixrateFactory $matrixrateFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->matrixrateRepository = $matrixrateRepository;
        $this->matrixrateFactory = $matrixrateFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Edit Rate
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('rate_id');
        $model = $this->matrixrateFactory->create();

        // 2. Initial checking
        if ($id) {
            $model = $this->matrixrateRepository->getById($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This rate no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $this->_coreRegistry->register('beecom_matrixrate_matrixrate', $model);

        // 5. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Matrixrate') : __('New Matrixrate'),
            $id ? __('Edit Matrixrate') : __('New Matrixrate')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Matrixrate'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? $model->getTitle() : __('New Matrixrate'));
        return $resultPage;
    }
}

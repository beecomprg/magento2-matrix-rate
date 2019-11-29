<?php

namespace Beecom\MatrixRate\Controller\Adminhtml\Grid;

use Beecom\MatrixRate\Api\MatrixrateRepositoryInterface;
use Beecom\MatrixRate\Model\MatrixrateFactory;

class Delete extends \Beecom\MatrixRate\Controller\Adminhtml\Matrixrate
{
    /**
     * @var MatrixrateFactory
     */
    private $matrixrateFactory;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry, MatrixrateFactory $matrixrateFactory)
    {
        $this->matrixrateFactory = $matrixrateFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('rate_id');
        if ($id) {
            $name = "";
            try {
                /** @var \Beecom\MatrixRate\Model\Matrixrate $page */
                $page = $this->matrixrateFactory->create();
                $page->load($id);
                $name = $page->getCost();
                $page->delete();
                $this->messageManager->addSuccess(__('The matrix rate has been deleted.'));
                $this->_eventManager->dispatch(
                    'adminhtml_beecom_matrixrate_rate_on_delete',
                    ['name' => $name, 'status' => 'success']
                );
                $resultRedirect->setPath('beecom_matrixrate/*/');
                return $resultRedirect;
            } catch (\Exception $e) {
                $this->_eventManager->dispatch(
                    'adminhtml_beecom_matrixrate_rate_on_delete',
                    ['name' => $name, 'status' => 'fail']
                );
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                $resultRedirect->setPath('beecom_matrixrate/*/edit', ['rate_id' => $id]);
                return $resultRedirect;
            }
        }
        // display error message
        $this->messageManager->addError(__('Matrix rate to delete was not found.'));
        // go to grid
        $resultRedirect->setPath('beecom_matrixrate/*/');
        return $resultRedirect;
    }
}

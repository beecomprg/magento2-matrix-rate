<?php
namespace Beecom\MatrixRate\Plugin;

use Beecom\MatrixRate\Model\ResourceModel\Carrier\Matrixrate\CollectionFactory as BlockCollectionFactory;


class AfterGetDeliveryMethodsPlugin
{
    protected $collection;
    protected $_carrierFactory;

    public function __construct(
        BlockCollectionFactory $collection,
        \Magento\Shipping\Model\CarrierFactory $carrierFactory
    )
    {
        $this->collection = $collection;
        $this->_carrierFactory = $carrierFactory;
    }

    public function afterGetDeliveryMethods(\Beecom\Core\Block\Adminhtml\Form\Field\ShippingMethods $configModel, $carriers)
    {
        $collection = $this->collection->create();
        if($collection->getSize() > 0){
            foreach ($collection->getItems() as $rate){
                $carrierCode = 'matrixrate_matrixrate_' . $rate->getId();
                $carriers[$carrierCode] = $rate->getShippingMethod();
            }
        }
        return $carriers;
    }
}

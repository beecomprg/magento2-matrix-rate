<?php
namespace Beecom\MatrixRate\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeData implements UpgradeDataInterface
{
    protected $_matrixrateFactory;

    public function __construct(\Beecom\MatrixRate\Model\MatrixrateFactory $matrixrateFactory)
    {
        $this->_matrixrateFactory = $matrixrateFactory;
    }

    public function upgrade( ModuleDataSetupInterface $setup, ModuleContextInterface $context ) {
        if ( version_compare($context->getVersion(), '1.0.1', '<' )) {
            $data = [
                'is_active' => 1,
                'website_id' => 0,
                'dest_country_id' => "*",
                'dest_region_id' => "0",
                'dest_city' => "*",
                'dest_zip' => "*",
                'dest_zip_to' => "*",
                'condition_name' => "package_value",
                'condition_from_value' => "0.0000",
                'condition_to_value' => "99.99",
                'price' => "9.99",
                'cost' => "4.99",
                'shipping_method' => "Delivery",
            ];
            $matrixrate = $this->_matrixrateFactory->create();
            $matrixrate->addData($data)->save();
        }

        if ( version_compare($context->getVersion(), '1.0.2', '<' )) {
            $data = [
                'is_active' => 1,
                'website_id' => 0,
                'dest_country_id' => "*",
                'dest_region_id' => "0",
                'dest_city' => "*",
                'dest_zip' => "*",
                'dest_zip_to' => "*",
                'condition_name' => "package_value",
                'condition_from_value' => "100.0000",
                'condition_to_value' => "999999.9999",
                'price' => "0.00",
                'cost' => "4.99",
                'shipping_method' => "Delivery",
            ];
            $matrixrate = $this->_matrixrateFactory->create();
            $matrixrate->addData($data)->save();
        }
    }
}

<?php
namespace Beecom\MatrixRate\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

class UpgradeData implements UpgradeDataInterface
{
    protected $_matrixrateFactory;
    protected $matrixrateRepository;
    protected $searchCriteriaBuilder;

    public function __construct(
        \Beecom\MatrixRate\Model\MatrixrateFactory $matrixrateFactory,
        \Beecom\MatrixRate\Model\MatrixrateRepository $matrixrateRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    )
    {
        $this->_matrixrateFactory = $matrixrateFactory;
        $this->matrixrateRepository = $matrixrateRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
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

        if ( version_compare($context->getVersion(), '1.0.3', '<' )) {
            $installer = $setup->startSetup();
            $installer->getConnection()->addColumn(
                $installer->getTable('beecom_matrixrate'),
                'sku',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Product sku'
                ]
            );
            $searchCriteria = $this->searchCriteriaBuilder->create();
            $list = $this->matrixrateRepository->getList($searchCriteria)->getItems();
            foreach ($list as $item) {
                if(!$item->getSku() || is_null($item->getSku())){
                    $matrixrate = $this->_matrixrateFactory->create();
                    $matrixrate->addData($item->getData())->setSku('*')->save();
                }
            }

            $installer->getConnection()->modifyColumn(
                $installer->getTable('beecom_matrixrate'),
                'sku',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                ]
            );
            $installer->endSetup();
        }

        if ( version_compare($context->getVersion(), '1.0.4', '<' )) {
            $installer = $setup->startSetup();
            $installer->getConnection()->modifyColumn(
                $installer->getTable('beecom_matrixrate'),
                'dest_region_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 30,
                    'nullable' => false
                ]
            );

            $installer->getConnection()->dropIndex(
                'beecom_matrixrate',
                $installer->getConnection()->getIndexName(
                    'beecom_matrixrate',
                    ['website_id', 'dest_country_id', 'dest_region_id', 'dest_city', 'dest_zip', 'condition_name',
                        'condition_from_value','condition_to_value','shipping_method'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                )
            );

            //FIXME add unique index dropped above

            $installer->endSetup();
        }
    }
}

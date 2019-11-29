<?php

namespace Beecom\MatrixRate\Model\ResourceModel\Carrier\Matrixrate;

/**
 * Shipping table rates collection
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Directory/country table name
     *
     * @var string
     */
    protected $countryTable;

    /**
     * Directory/country_region table name
     *
     * @var string
     */
    protected $regionTable;

    /**
     * Define resource model and item
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Beecom\MatrixRate\Model\Matrixrate::class,
            \Beecom\MatrixRate\Model\ResourceModel\Carrier\Matrixrate::class
        );
        $this->countryTable = $this->getTable('directory_country');
        $this->regionTable = $this->getTable('directory_country_region');
        $this->_map['fields']['rate_id'] = 'main_table.rate_id';
        $this->_map['fields']['store'] = 'store_table.store_id';
    }

    /**
     * Initialize select, add country iso3 code and region name
     *
     * @return void
     */
    public function _initSelect()
    {
        parent::_initSelect();

        $this->_select->joinLeft(
            ['country_table' => $this->countryTable],
            'country_table.country_id = main_table.dest_country_id',
            ['dest_country' => 'iso3_code']
        )->joinLeft(
            ['region_table' => $this->regionTable],
            'region_table.region_id = main_table.dest_region_id',
            ['dest_region' => 'code']
        );

        $this->addOrder('dest_country', self::SORT_ORDER_ASC);
        $this->addOrder('dest_region', self::SORT_ORDER_ASC);
        $this->addOrder('dest_zip', self::SORT_ORDER_ASC);
        $this->addOrder('condition_name', self::SORT_ORDER_ASC);
    }

    /**
     * Add website filter to collection
     *
     * @param int $websiteId
     * @return \Beecom\MatrixRate\Model\ResourceModel\Carrier\Matrixrate\Collection
     */
    public function setWebsiteFilter($websiteId)
    {
        return $this->addFieldToFilter('website_id', $websiteId);
    }

    /**
     * Add condition name (code) filter to collection
     *
     * @param string $conditionName
     * @return \Beecom\MatrixRate\Model\ResourceModel\Carrier\Matrixrate\Collection
     */
    public function setConditionFilter($conditionName)
    {
        return $this->addFieldToFilter('condition_name', $conditionName);
    }

    /**
     * Add country filter to collection
     *
     * @param string $countryId
     * @return \Beecom\MatrixRate\Model\ResourceModel\Carrier\Matrixrate\Collection
     */
    public function setCountryFilter($countryId)
    {
        return $this->addFieldToFilter('dest_country_id', $countryId);
    }
}

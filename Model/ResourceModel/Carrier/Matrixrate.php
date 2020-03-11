<?php

namespace Beecom\MatrixRate\Model\ResourceModel\Carrier;

use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime;
use Magento\Store\Model\StoreManagerInterface;

class Matrixrate extends AbstractDb
{
    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        DateTime $dateTime,
        EntityManager $entityManager,
        MetadataPool $metadataPool,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->_storeManager = $storeManager;
        $this->dateTime = $dateTime;
        $this->entityManager = $entityManager;
        $this->metadataPool = $metadataPool;
    }

    /**
     * Define main table and id field name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('beecom_matrixrate', 'rate_id');
    }

    /**
     * Return table rate array or false by rate request
     *
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @param bool $zipRangeSet
     * @return array|bool
     */
    public function getRate(\Magento\Quote\Model\Quote\Address\RateRequest $request, $zipRangeSet = false)
    {
        $adapter = $this->getConnection();
        $shippingData=[];
        $postcode = $request->getDestPostcode();
        if ($zipRangeSet && is_numeric($postcode)) {
            #  Want to search for postcodes within a range
            $zipSearchString = ' AND :postcode BETWEEN dest_zip AND dest_zip_to ';
        } else {
            $zipSearchString = " AND :postcode LIKE dest_zip ";
        }

        $websites = [0, (int)$request->getWebsiteId()];

        $products = $request->getAllItems();
        $skus = [];

        foreach ($products as $product){
            if($product['type'] == 'simple'){ //start with the items we want
                $skus[] = $product['sku'];
            }
        }

        for ($j=0; $j<8; $j++) {
            $select = $adapter->select()->from(
                $this->getMainTable()
            )->where(
                'website_id IN (?)', $websites
            )->where(
                'is_active = (?)', '1'
            )->order(
                ['dest_country_id DESC', 'dest_region_id DESC', 'dest_zip DESC', 'condition_from_value DESC']
            );

            $zoneWhere='';
            $bind=[];
            switch ($j) {
                case 0: // country, region, city, postcode
                    $zoneWhere =  "dest_country_id = :country_id AND dest_region_id = :region_id AND STRCMP(LOWER(dest_city),LOWER(:city))= 0 " .$zipSearchString;
                    $bind = [
                        ':country_id' => $request->getDestCountryId(),
                        ':region_id' => (int)$request->getDestRegionId(),
                        ':city' => $request->getDestCity(),
                        ':postcode' => $request->getDestPostcode(),
                    ];
                    break;
                case 1: // country, region, no city, postcode
                    $zoneWhere =  "dest_country_id = :country_id AND dest_region_id = :region_id AND dest_city='*' "
                        .$zipSearchString;
                    $bind = [
                        ':country_id' => $request->getDestCountryId(),
                        ':region_id' => (int)$request->getDestRegionId(),
                        ':postcode' => $request->getDestPostcode(),
                    ];
                    break;
                case 2: // country, state, city, no postcode
                    $zoneWhere = "dest_country_id = :country_id AND dest_region_id = :region_id AND STRCMP(LOWER(dest_city),LOWER(:city))= 0 AND dest_zip ='*'";
                    $bind = [
                        ':country_id' => $request->getDestCountryId(),
                        ':region_id' => (int)$request->getDestRegionId(),
                        ':city' => $request->getDestCity(),
                    ];
                    break;
                case 3: //country, city, no region, no postcode
                    $zoneWhere =  "dest_country_id = :country_id AND dest_region_id = '0' AND STRCMP(LOWER(dest_city),LOWER(:city))= 0 AND dest_zip ='*'";
                    $bind = [
                        ':country_id' => $request->getDestCountryId(),
                        ':city' => $request->getDestCity(),
                    ];
                    break;
                case 4: // country, postcode
                    $zoneWhere =  "dest_country_id = :country_id AND dest_region_id = '0' AND dest_city ='*' "
                        .$zipSearchString;
                    $bind = [
                        ':country_id' => $request->getDestCountryId(),
                        ':postcode' => $request->getDestPostcode(),
                    ];
                    break;
                case 5: // country, region
                    $zoneWhere =  "dest_country_id = :country_id AND dest_region_id = :region_id  AND dest_city ='*' AND dest_zip ='*'";
                    $bind = [
                        ':country_id' => $request->getDestCountryId(),
                        ':region_id' => (int)$request->getDestRegionId(),
                    ];
                    break;
                case 6: // country
                    $zoneWhere =  "dest_country_id = :country_id AND dest_region_id = '0' AND dest_city ='*' AND dest_zip ='*'";
                    $bind = [
                        ':country_id' => $request->getDestCountryId(),
                    ];
                    break;
                case 7: // nothing
                    $zoneWhere =  "dest_country_id = '*' AND dest_region_id = '0' AND dest_city ='*' AND dest_zip ='*' AND sku is '*'";
                    break;
            }

            $select->where($zoneWhere);

            $bind[':condition_name'] = $request->getConditionMRName();
            $bind[':condition_value'] = $request->getData($request->getConditionMRName());

            $select->where('condition_name = :condition_name');
            $select->where('condition_from_value <= :condition_value');
            $select->where('condition_to_value >= :condition_value');

            $bind[':skus'] = $condition;
            $select->where('sku IN (:skus) OR sku = "*"');

            $results = $adapter->fetchAll($select, $bind);

            if (!empty($results)) {
                foreach ($results as $data) {
                    $shippingData[]=$data;
                }
                break;
            }
        }

        return $shippingData;
    }

    /**
     * Load directory countries
     *
     * @return \Beecom\MatrixRate\Model\ResourceModel\Carrier\Matrixrate
     */
    protected function _loadDirectoryCountries()
    {
        if ($this->importIso2Countries !== null && $this->importIso3Countries !== null) {
            return $this;
        }

        $this->importIso2Countries = [];
        $this->importIso3Countries = [];

        /** @var $collection \Magento\Directory\Model\ResourceModel\Country\Collection */
        $collection = $this->countryCollectionFactory->create();
        foreach ($collection->getData() as $row) {
            $this->importIso2Countries[$row['iso2_code']] = $row['country_id'];
            $this->importIso3Countries[$row['iso3_code']] = $row['country_id'];
        }

        return $this;
    }

    /**
     * Load directory regions
     *
     * @return \Beecom\MatrixRate\Model\ResourceModel\Carrier\Matrixrate
     */
    protected function _loadDirectoryRegions()
    {
        if ($this->importRegions !== null) {
            return $this;
        }

        $this->importRegions = [];

        /** @var $collection \Magento\Directory\Model\ResourceModel\Region\Collection */
        $collection = $this->regionCollectionFactory->create();
        foreach ($collection->getData() as $row) {
            $this->importRegions[$row['country_id']][$row['code']] = (int)$row['region_id'];
        }

        return $this;
    }

    /**
     * Return import condition full name by condition name code
     *
     * @param string $conditionName
     * @return string
     */
    protected function getConditionFullName($conditionName)
    {
        if (!isset($this->conditionFullNames[$conditionName])) {
            $name = $this->carrierMatrixrate->getCode('condition_name_short', $conditionName);
            $this->conditionFullNames[$conditionName] = $name;
        }

        return $this->conditionFullNames[$conditionName];
    }
}

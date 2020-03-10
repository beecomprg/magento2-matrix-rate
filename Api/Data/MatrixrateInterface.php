<?php

namespace Beecom\MatrixRate\Api\Data;

/**
 * Brands page interface.
 */
interface MatrixrateInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const RATE_ID                  = 'rate_id';
    const IS_ACTIVE               = 'is_active';
    const WEBSITE_ID                    = 'website_id';
    const DEST_COUNTRY_ID         = 'dest_country_id';
    const DEST_REGION_ID                  = 'dest_region_id';
    const DEST_CITY                 = 'dest_city';
    const DEST_ZIP                = 'dest_zip';
    const DEST_ZIP_TO            = 'dest_zip_to';
    const CONDITION_NAME              = 'condition_name';
    const CONDITION_FROM_VALUE              = 'condition_from_value';
    const CONDITION_TO_VALUE              = 'condition_to_value';
    const PRICE              = 'price';
    const COST              = 'cost';
    const SHIPPING_METHOD              = 'shipping_method';
    const SKU              = 'sku';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get ID
     *
     * @return bool|null
     */
    public function getIsActive();

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getWebsiteId();

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getDestCountryId();

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getDestRegionId();

    /**
     * Get ID
     *
     * @return string|null
     */
    public function getDestCity();

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getDestZip();

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getDestZipTo();

    /**
     * Get ID
     *
     * @return string|null
     */
    public function getConditionName();

    /**
     * Get ID
     *
     * @return string|null
     */
    public function getConditionFromValue();

    /**
     * Get ID
     *
     * @return float|null
     */
    public function getPrice();

    /**
     * Get ID
     *
     * @return float|null
     */
    public function getCost();

    /**
     * Get Shipping Method
     *
     * @return string|null
     */
    public function getShippingMethod();

    /**
     * Get Sku
     *
     * @return string|null
     */
    public function getSku();

    /**
     * @param $id
     * @return mixed
     */
    public function setId($id);

    /**
     * @param $isActive
     * @return mixed
     */
    public function setIsActive($isActive);

    /**
     * @param $id
     * @return mixed
     */
    public function setWebsiteId($id);

    /**
     * @param $destCountryId
     * @return mixed
     */
    public function setDestCountryId($destCountryId);

    /**
     * @param $destRegionId
     * @return mixed
     */
    public function setDestRegionId($destRegionId);

    /**
     * @param $destCity
     * @return mixed
     */
    public function setDestCity($destCity);

    /**
     * @param $destZip
     * @return mixed
     */
    public function setDestZip($destZip);

    /**
     * @param $destZipTo
     * @return mixed
     */
    public function setDestZipTo($destZipTo);

    /**
     * @param $conditionName
     * @return mixed
     */
    public function setConditionName($conditionName);

    /**
     * @param $conditionFromValue
     * @return mixed
     */
    public function setConditionFromValue($conditionFromValue);

    /**
     * Get ID
     *
     * @return int|null
     */
    public function setPrice($conditionToValue);

    /**
     * @param $cost
     * @return mixed
     */
    public function setCost($cost);

    /**
     * @param $shippingMethod
     * @return mixed
     */
    public function setShippingMethod($shippingMethod);

    /**
     * @param $sku
     * @return mixed
     */
    public function setSku($sku);


}

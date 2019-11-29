<?php
namespace Beecom\MatrixRate\Model;

use Beecom\MatrixRate\Api\Data\MatrixrateInterface;
use Magento\Framework\DataObject\IdentityInterface;


class Matrixrate extends \Magento\Framework\Model\AbstractModel implements MatrixrateInterface, IdentityInterface
{
    const CACHE_TAG = 'beecom_matrixrate';

    const STATUS_ENABLED = 1;

    const STATUS_DISABLED = 0;

    protected $_cacheTag = 'beecom_matrixrate';

    protected $_eventPrefix = 'beecom_matrixrate';

    protected function _construct()
    {
        $this->_init('Beecom\MatrixRate\Model\ResourceModel\Carrier\Matrixrate');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId(){
        return $this->getData(self::RATE_ID);
    }

    /**
     * Get ID
     *
     * @return bool|null
     */
    public function getIsActive(){
        return (bool) $this->getData(self::IS_ACTIVE);
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getWebsiteId(){
        return $this->getData(self::WEBSITE_ID);
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getDestCountryId(){
        return $this->getData(self::DEST_COUNTRY_ID);
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getDestRegionId(){
        return $this->getData(self::DEST_REGION_ID);
    }

    /**
     * Get ID
     *
     * @return string|null
     */
    public function getDestCity(){
        return $this->getData(self::DEST_CITY);
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getDestZip(){
        return $this->getData(self::DEST_ZIP);
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getDestZipTo(){
        return $this->getData(self::DEST_ZIP_TO);
    }

    /**
     * Get ID
     *
     * @return string|null
     */
    public function getConditionName(){
        return $this->getData(self::CONDITION_NAME);
    }

    /**
     * Get ID
     *
     * @return string|null
     */
    public function getConditionFromValue(){
        return $this->getData(self::CONDITION_FROM_VALUE);
    }

    /**
     * Get ID
     *
     * @return float|null
     */
    public function getPrice(){
        return $this->getData(self::CONDITION_TO_VALUE);
    }

    /**
     * Get ID
     *
     * @return float|null
     */
    public function getCost(){
        return $this->getData(self::COST);
    }

    /**
     * Get ID
     *
     * @return string|null
     */
    public function getShippingMethod(){
        return $this->getData(self::SHIPPING_METHOD);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function setId($id){
        return $this->setData(self::RATE_ID, $id);
    }

    /**
     * @param $isActive
     * @return mixed
     */
    public function setIsActive($isActive){
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function setWebsiteId($id){
        return $this->setData(self::WEBSITE_ID, $id);
    }

    /**
     * @param $destCountryId
     * @return mixed
     */
    public function setDestCountryId($destCountryId){
        return $this->setData(self::DEST_COUNTRY_ID, $destCountryId);
    }

    /**
     * @param $destRegionId
     * @return mixed
     */
    public function setDestRegionId($destRegionId){
        return $this->setData(self::DEST_REGION_ID, $destRegionId);
    }

    /**
     * @param $destCity
     * @return mixed
     */
    public function setDestCity($destCity){
        return $this->setData(self::DEST_CITY, $destCity);
    }

    /**
     * @param $destZip
     * @return mixed
     */
    public function setDestZip($destZip){
        return $this->setData(self::DEST_ZIP, $destZip);
    }

    /**
     * @param $destZipTo
     * @return mixed
     */
    public function setDestZipTo($destZipTo){
        return $this->setData(self::DEST_ZIP_TO, $destZipTo);
    }

    /**
     * @param $conditionName
     * @return mixed
     */
    public function setConditionName($conditionName){
        return $this->setData(self::CONDITION_NAME, $conditionName);
    }

    /**
     * @param $conditionFromValue
     * @return mixed
     */
    public function setConditionFromValue($conditionFromValue){
        return $this->setData(self::CONDITION_FROM_VALUE, $conditionFromValue);
    }

    /**
     * @param $conditionToValue
     * @return Matrixrate|int|null
     */
    public function setPrice($conditionToValue){
        return $this->setData(self::CONDITION_TO_VALUE, $conditionToValue);
    }

    /**
     * @param $cost
     * @return mixed
     */
    public function setCost($cost){
        return $this->setData(self::COST, $cost);
    }

    /**
     * @param $shippingMethod
     * @return mixed
     */
    public function setShippingMethod($shippingMethod){
        return $this->setData(self::SHIPPING_METHOD, $shippingMethod);
    }
}

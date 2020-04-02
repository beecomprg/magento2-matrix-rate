<?php

namespace Beecom\MatrixRate\Model\Matrixrate\Source;

use Magento\Directory\Model\Config\Source\Allregion;

class Regionoption extends Allregion
{
    public function toOptionArray($isMultiselect = false)
    {
        if (!$this->_options) {
            $countriesArray = $this->_countryCollectionFactory->create()->load()->toOptionArray(false);
            $this->_countries = [];
            foreach ($countriesArray as $a) {
                $this->_countries[$a['value']] = $a['label'];
            }

            $countryRegions = [];
            $regionsCollection = $this->_regionCollectionFactory->create()->load();
            foreach ($regionsCollection as $region) {
                $countryRegions[$region->getCountryId()][$region->getCode()] = $region->getDefaultName();
            }
            uksort($countryRegions, [$this, 'sortRegionCountries']);

            $this->_options = [];
            foreach ($countryRegions as $countryId => $regions) {
                $regionOptions = [];
                foreach ($regions as $regionId => $regionName) {
                    $regionOptions[] = ['label' => $regionName, 'value' => $regionId];
                }
                $this->_options[] = ['label' => $this->_countries[$countryId], 'value' => $regionOptions];
            }
        }
        $options = $this->_options;
        if (!$isMultiselect) {
            array_unshift($options, ['value' => '', 'label' => '']);
        }

        $empty = ['value' => '', 'label' => __('--Please Select--')];
        $options[0] = $empty;

        return $options;

    }
}


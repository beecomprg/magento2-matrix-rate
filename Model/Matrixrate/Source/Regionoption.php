<?php

namespace Beecom\MatrixRate\Model\Matrixrate\Source;

use Magento\Directory\Model\Config\Source\Allregion;

class Regionoption extends Allregion
{
    public function toOptionArray($isMultiselect = false)
    {
        $options =  parent::toOptionArray($isMultiselect);
        $empty = ['value' => '', 'label' => __('--Please Select--')];
        $options[0] = $empty;
        return $options;

    }
}


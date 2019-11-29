<?php

namespace Beecom\MatrixRate\Model\Config\Source;

class Matrixrate implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Beecom\MatrixRate\Model\Carrier\Matrixrate
     */
    protected $carrierMatrixrate;

    /**
     * @param \Beecom\MatrixRate\Model\Carrier\Matrixrate $carrierMatrixrate
     */
    public function __construct(\Beecom\MatrixRate\Model\Carrier\Matrixrate $carrierMatrixrate)
    {
        $this->carrierMatrixrate = $carrierMatrixrate;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $arr = [];
        foreach ($this->carrierMatrixrate->getCode('condition_name') as $k => $v) {
            $arr[] = ['value' => $k, 'label' => $v];
        }
        return $arr;
    }
}

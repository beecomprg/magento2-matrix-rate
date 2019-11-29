<?php
/**
 * Beecom
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * Beecom MatrixRate
 *
 * @category Beecom
 * @package Beecom_MatrixRate
 * @copyright Copyright (c) 2014 Zowta LLC (http://www.Beecom.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Beecom Team sales@beecom.com
 *
 */
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
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

<?php
/**
 * {{license text}}
 */

namespace Beecom\MatrixRate\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{

    /**
     * System configuration helper
     *
     * @var \Beecom\Brands\Helper\Config $configHelper
     */
    protected $configHelper;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
    }
}

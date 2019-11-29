<?php
/**
 * {{license text}}
 */

namespace Beecom\MatrixRate\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    const PATH_BRANDS_PAGE_SIZE    = "beecom_brands/general/page_size";
    const PATH_BRANDS_PAGER_LIMIT    = "beecom_brands/general/pager_limit";

    protected $urlbuilder;
    protected $imagehelper;
    protected $storeManager;
    protected $session;
    protected $registry;
    protected $productLoader;

    /**
     * System configuration helper
     *
     * @var \Beecom\Brands\Helper\Config $configHelper
     */
    protected $configHelper;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\UrlInterface $urlbuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        DirectoryList $directory_list,
        \Magento\Framework\Registry $registry,
        \Magento\Checkout\Model\Session $session,
        \Magento\Catalog\Model\ProductFactory $productLoader,
        \Beecom\Brands\Helper\Config $configHelper
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->directory_list = $directory_list;
        $this->urlbuilder = $urlbuilder;
        $this->session = $session;
        $this->registry = $registry;
        $this->productLoader = $productLoader;
        $this->configHelper = $configHelper;
    }

    public function getProductLink($manufacturer)
    {
        // catalogsearch/advanced/result/?manufacturer=46
        return $this->urlbuilder->getUrl('catalogsearch/advanced/result') . '?' . http_build_query(['manufacturer' => [$manufacturer]]);
    }

    public function getIndexUrl($store=null)
    {
        // toto nevim jestli je 100% magento friendly
        // potrebuji udelat url brand index stranky
        // brands/index/index -> /our-brands
        // toto ale nevim jak vyresit
        return $this->urlbuilder->getBaseUrl() . $this->configHelper->getBrandsUrlKey($store);
    }

    public function getBrandIndexTitle($store=null)
    {
        return $this->configHelper->getBrandsTitle($store);
    }

    public function getDetailLink($page, $store=null)
    {
        // load identifier by page_id
        // contruct url by nase-znacky/identifier

        $identifier = $page->getIdentifier();
        return rtrim($this->getIndexUrl($store)) . '/' . $identifier;
        // return $this->urlbuilder->getUrl('brands/index/view', ['brand' => $page_id]);
    }

    public function getSubDir()
    {
        return 'beecom_brands/logo';
    }

    public function getFilePath($name)
    {
        $path = $this->directory_list->getPath('media');
        $filepath=$path . '/' . $this->getSubDir() . '/' . $name;
        return $filepath;
    }

    public function getFileUrl($filename)
    {
        return $this->getMediaUrl() . $filename;
    }

    public function getMime($filepath)
    {
        $mime = mime_content_type($filepath);
        return $mime;
    }

    public function getMediaUrl()
    {
        $mediaUrl = $this->storeManager->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $this->getSubDir() . '/';
        return $mediaUrl;
    }

    public function getBaseUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    // unused
    public function getMimeExtension($filepath)
    {
        $mime = $this->getMime($filepath);

        $map = [
            'image/jpg' => 'jpg',
            'image/jpeg' => 'jpg',
            'image/gif' => 'gif',
            'image/png' => 'png',
        ];

        if (isset($map[$mime])) {
            return $map[$mime];
        } else {
            return "";
        }
    }

    public function getPageSize($store_id = null)
    {
        return $this->scopeConfig->getValue(
            self::PATH_BRANDS_PAGE_SIZE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store_id
        );
    }

    public function getPagerLimit($store_id = null)
    {
        $configValue = $this->scopeConfig->getValue(
            self::PATH_BRANDS_PAGER_LIMIT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store_id
        );
        $explodedValues = explode(",",preg_replace('/\s+/', '', $configValue));
        $ret = [];
        foreach ($explodedValues as $explodedValue){
          $ret[$explodedValue] = $explodedValue;
        }
        return $ret;
    }
}

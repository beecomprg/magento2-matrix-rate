<?php


namespace Beecom\MatrixRate\Plugin\Carrier;

use Magento\Quote\Model\Cart\ShippingMethodConverter;
use Magento\Quote\Api\Data\ShippingMethodInterface;
use Magento\Quote\Api\Data\ShippingMethodExtensionFactory;

class AdditionalComponent
{
    /**
     * @var ShippingMethodExtensionFactory
     */
    protected $extensionFactory;

    protected $scopeConfig;

    /**
     * DeliveryDate constructor.
     *
     * @param ShippingMethodExtensionFactory $extensionFactory
     */
    public function __construct(ShippingMethodExtensionFactory $extensionFactory, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->extensionFactory = $extensionFactory;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Add delivery date information to the carrier data object
     *
     * @param ShippingMethodConverter $subject
     * @param ShippingMethodInterface $result
     * @return ShippingMethodInterface
     */
    public function afterModelToDataObject(ShippingMethodConverter $subject, ShippingMethodInterface $result)
    {
        $extensibleAttribute =  ($result->getExtensionAttributes())
            ? $result->getExtensionAttributes()
            : $this->extensionFactory->create();

        if ($result->getCarrierCode() == 'matrixrate') {
            $mapping = $this->scopeConfig->getValue('balikobot/general/mapping_matrix_rates', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

            if ($mapping) {
                $mappings = json_decode($mapping);

                $balikobotSettings = [];
                foreach ($mappings as $item) {
                    $balikobotSettings[$item->matrixrate] = ['type' => $item->balikobot, 'popup' => (bool) $item->popup];
                }

                if (isset($balikobotSettings[$result->getMethodCode()])) {
                    $extensibleAttribute->setAdditionalComponent($balikobotSettings[$result->getMethodCode()]);
                }
            }
        }

        $result->setExtensionAttributes($extensibleAttribute);

        return $result;
    }
}

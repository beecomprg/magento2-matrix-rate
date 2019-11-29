<?php

namespace Beecom\MatrixRate\Model\Carrier;

use Magento\Framework\DataObject as DataObjectAlias;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Xml\Security;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface as CarrierInterfaceAlias;
use Magento\Shipping\Model\Shipment\Request;
use Beecom\Balikobot\Helper\Client;
use Merinsky\Balikobot\Balikobot;
use Magento\Sales\Model\ResourceModel\Order\Shipment\Track\CollectionFactory as TrackCollectionFactory;
use Magento\Sales\Api\Data\ShipmentTrackInterface;
use Magento\Sales\Model\Order\Shipment;

class Matrixrate extends AbstractCarrier implements CarrierInterfaceAlias
{
    /**
     * @var string
     */
    protected $_code = 'matrixrate';

    /**
     * @var bool
     */
    protected $_isFixed = false;

    /**
     * @var string
     */
    protected $defaultConditionName = 'package_value';

    /**
     * @var array
     */
    protected $conditionNames = [];

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $resultMethodFactory;

    /**
     * @var \Beecom\MatrixRate\Model\ResourceModel\Carrier\MatrixrateFactory
     */
    protected $matrixrateFactory;

    protected $client;

    /**
     * Array of quotes
     *
     * @var array
     */
    protected static $_quotesCache = [];

    /**
     * Flag for check carriers for activity
     *
     * @var string
     */
    protected $_activeFlag = 'active';

    /**
     * Directory data
     *
     * @var \Magento\Directory\Helper\Data
     */
    protected $_directoryData = null;

    /**
     * @var \Magento\Shipping\Model\Simplexml\ElementFactory
     */
    protected $_xmlElFactory;

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $_rateFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * @var \Magento\Shipping\Model\Order\TrackFactory
     */
    protected $_trackFactory;

    /**
     * @var \Magento\Shipping\Model\Tracking\Result\ErrorFactory
     */
    protected $_trackErrorFactory;

    /**
     * @var \Magento\Shipping\Model\Tracking\Result\StatusFactory
     */
    protected $_trackStatusFactory;

    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $_regionFactory;

    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $_countryFactory;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $_currencyFactory;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * Raw rate request data
     *
     * @var \Magento\Framework\DataObject|null
     */
    protected $_rawRequest = null;

    /**
     * The security scanner XML document
     *
     * @var Security
     */
    protected $xmlSecurity;

    /** @var  \Magento\Sales\Model\ResourceModel\Order\Shipment\Track\Collection */
    protected $trackingCollection;

    protected $orderRepository;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
         \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
         \Psr\Log\LoggerInterface $logger,
         Security $xmlSecurity,
         \Magento\Shipping\Model\Simplexml\ElementFactory $xmlElFactory,
         \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
         \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $resultMethodFactory,
         \Magento\Shipping\Model\Order\TrackFactory $trackFactory,
         \Magento\Shipping\Model\Tracking\Result\ErrorFactory $trackErrorFactory,
         \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory,
         \Magento\Directory\Model\RegionFactory $regionFactory,
         \Magento\Directory\Model\CountryFactory $countryFactory,
         \Magento\Directory\Model\CurrencyFactory $currencyFactory,
         \Magento\Directory\Helper\Data $directoryData,
         \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Beecom\MatrixRate\Model\ResourceModel\Carrier\MatrixrateFactory $matrixrateFactory,
        Client $client,
        TrackCollectionFactory $collectionFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        array $data = []
    ) {
        $this->rateResultFactory = $rateResultFactory;
        $this->resultMethodFactory = $resultMethodFactory;
        $this->matrixrateFactory = $matrixrateFactory;
        $this->_trackFactory = $trackFactory;
        $this->client = $client;
        $this->trackingCollection = $collectionFactory->create();
        $this->orderRepository = $orderRepository;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
//        parent::__construct(
//            $scopeConfig,
//            $rateErrorFactory,
//            $logger,
//            $xmlSecurity,
//            $xmlElFactory,
//            $rateResultFactory,
//            $resultMethodFactory,
//            $trackFactory,
//            $trackErrorFactory,
//            $trackStatusFactory,
//            $regionFactory,
//            $countryFactory,
//            $currencyFactory,
//            $directoryData,
//            $stockRegistry,
//            $data
//        );
        foreach ($this->getCode('condition_name') as $k => $v) {
            $this->conditionNames[] = $k;
        }
        $this->client = $client;
    }

    /**
     * @param RateRequest $request
     * @return \Magento\Shipping\Model\Rate\Result
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function collectRates(RateRequest $request)
    {
        $this->_logger->debug('Matrix rate loaded');

        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $this->_logger->debug('Matrix rate loaded');
        $this->_logger->debug(__LINE__);

        // exclude Virtual products price from Package value if pre-configured
        if (!$this->getConfigFlag('include_virtual_price') && $request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                if ($item->getParentItem()) {
                    continue;
                }
                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    foreach ($item->getChildren() as $child) {
                        if ($child->getProduct()->isVirtual()) {
                            $request->setPackageValue($request->getPackageValue() - $child->getBaseRowTotal());
                        }
                    }
                } elseif ($item->getProduct()->isVirtual()) {
                    $request->setPackageValue($request->getPackageValue() - $item->getBaseRowTotal());
                }
            }
        }
        $this->_logger->debug(__LINE__);

        // Free shipping by qty
        $freeQty = 0;
        if ($request->getAllItems()) {
            $freePackageValue = 0;
            foreach ($request->getAllItems() as $item) {
                if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                    continue;
                }

                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    foreach ($item->getChildren() as $child) {
                        if ($child->getFreeShipping() && !$child->getProduct()->isVirtual()) {
                            $freeShipping = is_numeric($child->getFreeShipping()) ? $child->getFreeShipping() : 0;
                            $freeQty += $item->getQty() * ($child->getQty() - $freeShipping);
                        }
                    }
                } elseif ($item->getFreeShipping()) {
                    $freeShipping = is_numeric($item->getFreeShipping()) ? $item->getFreeShipping() : 0;
                    $freeQty += $item->getQty() - $freeShipping;
                    $freePackageValue += $item->getBaseRowTotal();
                }
            }
            $oldValue = $request->getPackageValue();
            $request->setPackageValue($oldValue - $freePackageValue);
        }

        if (!$request->getConditionMRName()) {
            $conditionName = $this->getConfigData('condition_name');
            $request->setConditionMRName($conditionName ? $conditionName : $this->defaultConditionName);
        }
        $this->_logger->debug(__LINE__);

        // Package weight and qty free shipping
        $oldWeight = $request->getPackageWeight();
        $oldQty = $request->getPackageQty();

        $request->setPackageWeight($request->getFreeMethodWeight());
        $request->setPackageQty($oldQty - $freeQty);

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->rateResultFactory->create();
        $zipRange = $this->getConfigData('zip_range');
        $rateArray = $this->getRate($request, $zipRange);

        $request->setPackageWeight($oldWeight);
        $request->setPackageQty($oldQty);

        $foundRates = false;

        $this->_debug($rateArray);

        foreach ($rateArray as $rate) {
            if (!empty($rate) && $rate['price'] >= 0) {
                /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
                $method = $this->resultMethodFactory->create();

                $method->setCarrier('matrixrate');
                $method->setActive($rate['is_active']);
                $method->setCarrierTitle($this->getConfigData('title'));

                $method->setMethod('matrixrate_' . $rate['rate_id']);
                $method->setMethodTitle(__($rate['shipping_method']));

                if ($request->getFreeShipping() === true || $request->getPackageQty() == $freeQty) {
                    $shippingPrice = 0;
                } else {
                    $shippingPrice = $this->getFinalPriceWithHandlingFee($rate['price']);
                }

                $method->setPrice($shippingPrice);
                $method->setCost($rate['cost']);

                $result->append($method);
                $foundRates = true; // have found some valid rates
            }
        }

        $this->_logger->debug(__LINE__);


        if (!$foundRates) {
            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Error $error */
            $this->_logger->debug(__LINE__);

            $error = $this->_rateErrorFactory->create(
                [
                    'data' => [
                        'carrier' => $this->_code,
                        'carrier_title' => $this->getConfigData('title'),
                        'error_message' => $this->getConfigData('specificerrmsg'),
                    ],
                ]
            );
            $this->_logger->debug(__LINE__);

            $result->append($error);
        }
        $this->_logger->debug(__LINE__);

        return $result;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @param bool $zipRange
     * @return array|bool
     */
    public function getRate(\Magento\Quote\Model\Quote\Address\RateRequest $request, $zipRange)
    {
        $this->_logger->debug('Matrix rate loaded');

        return $this->matrixrateFactory->create()->getRate($request, $zipRange);
    }

    /**
     * @param string $type
     * @param string $code
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCode($type, $code = '')
    {
        $codes = [
            'condition_name' => [
                'package_weight' => __('Weight vs. Destination'),
                'package_value' => __('Order Subtotal vs. Destination'),
                'package_qty' => __('# of Items vs. Destination'),
            ],
            'condition_name_short' => [
                'package_weight' => __('Weight'),
                'package_value' => __('Order Subtotal'),
                'package_qty' => __('# of Items'),
            ],
        ];

        if (!isset($codes[$type])) {
            throw new LocalizedException(__('Please correct Matrix Rate code type: %1.', $type));
        }

        if ('' === $code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
            throw new LocalizedException(__('Please correct Matrix Rate code for type %1: %2.', $type, $code));
        }

        return $codes[$type][$code];
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        $this->_logger->debug('Matrix rate loaded');

        return ['matrixrate' => $this->getConfigData('name')];
    }

    /**
     * Check if carrier has shipping label option available
     *
     * @return boolean
     */
    public function isShippingLabelsAvailable()
    {
        //@todo document that this behavior depends on whether balikobot is enabled or not - around plugin
        return false;
    }

    public function isTrackingAvailable()
    {
        return true;
    }

    /**
     * Checks if shipping method can collect rates
     * @return bool
     */
    public function canCollectRates()
    {
        return (bool)$this->getConfigFlag($this->_activeFlag);
    }

    /**
     * Determine whether current carrier enabled for activity
     *
     * @return bool
     */
    public function isActive()
    {
        $active = $this->getConfigData('active');
        return $active == 1 || $active == 'true';
    }

    /**
     * Do request to shipment
     *
     * @param Request $request
     * @return \Magento\Framework\DataObject
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function requestToShipment($request)
    {
        $packages = $request->getPackages();
        if (!is_array($packages) || !$packages) {
            throw new LocalizedException(__('No packages for request'));
        }
        if ($request->getStoreId() != null) {
            $this->setStore($request->getStoreId());
        }
        $data = [];
        $order = $request->getOrderShipment()->getOrder();
        $payment = $order->getPayment();
        $method = $payment->getMethod();
        foreach ($packages as $packageId => &$package) {
            $request->setOrderIncrementId($order->getIncrementId());
            $request->setPackagePrice($order->getGrandTotal());
            $request->setBalikobotType($order->getBalikobotType());
            $request->setBalikobotBranch($order->getBalikobotBranch());
            $request->setPackageId($packageId);
            $request->setPackagingType($package['params']['container']);
            $request->setPackageWeight($package['params']['weight']);
            $request->setPackageParams(new DataObjectAlias($package['params']));
            $request->setPackageItems($package['items']);
            $request->setCurrencyCode($order->getOrderCurrencyCode());
            $request->setPaymentMethod($method);
            if ($order->getBalikobotType()) {
                $result = $this->_doShipmentRequest($request);

                if ($result->hasErrors()) {
                    $response = new DataObjectAlias([]);
                    $response->setErrors($result->getErrors());

                    return $response;
                } else {
                    $data[] = [
                        'tracking_number' => $result->getTrackingNumber(),
                        'label_content' => $result->getShippingLabelContent(),
                    ];
                    $package['carrier_id'] = $result->getTrackingNumber();
                    $package['package_id'] = $result->getPackageId();
                }
                if (!isset($isFirstRequest)) {
                    $request->setMasterTrackingId($result->getTrackingNumber());
                    $isFirstRequest = false;
                }
            }
        }
      //  $request->getOrderShipment()->setPackages($packages)->save();

        $response = new DataObjectAlias(['info' => $data]);

        return $response;
    }

    /**
     * Do shipment request to carrier web service, obtain Print Shipping Labels and process errors in response
     *
     * @param \Magento\Framework\DataObject $request
     * @return \Magento\Framework\DataObject
     */
    protected function _doShipmentRequest(\Magento\Framework\DataObject $request)
    {
        $data = new DataObjectAlias();
        try{
            $client = $this->client->getClient();
            $deliverer = $this->client->getServiceCode($request->getBalikobotType());
            if (is_array($deliverer)) {
                $carrierCode = $deliverer[0];
                $serviceType = $deliverer[1];
            } else {
                $carrierCode = $deliverer;
                $serviceType = null;
            }
            $phoneNumberUtil = \libphonenumber\PhoneNumberUtil::getInstance();
            $phoneNumberObject = $phoneNumberUtil->parse($request->getRecipientContactPhoneNumber(), 'CZ');
            $phoneNumberFormatted = $phoneNumberUtil->format($phoneNumberObject, \libphonenumber\PhoneNumberFormat::E164);
            $response = $client->service($carrierCode, $serviceType, [
                Balikobot::OPTION_ORDER => $request->getOrderIncrementId(),
                Balikobot::OPTION_PRICE => $request->getPackagePrice(),
                Balikobot::OPTION_WEIGHT => $request->getPackageWeight(),
                Balikobot::OPTION_BRANCH => $request->getBalikobotBranch(),
            ]);
            if ($request->getPaymentMethod() == 'cashondelivery') {
                $response = $response->cashOnDelivery(
                    $request->getPackagePrice(),
                    $request->getOrderIncrementId(),
                    $request->getCurrencyCode()
                );
            }
            $response = $response->customer(
                    $request->getRecipientContactPersonFirstName().' '.$request->getRecipientContactPersonLastName(),
                    $request->getRecipientAddressStreet(),
                    $request->getRecipientAddressCity(),
                    str_replace(' ', '', $request->getRecipientAddressPostalCode()),
                    $phoneNumberFormatted,
                    $request->getRecipientEmail()
                )
                ->add();
            $data->setTrackingNumber($response['carrier_id']);
            $data->setPackageId($response['package_id']);
            $data->setShippingLabelContent(file_get_contents($response['label_url']));
            return $data;
        }catch (\Exception $exception){
            $data->hasErrors(true);
            $data->setErrors([$exception->getMessage()]);
            return $data;
        }

    }

    /**
     * Get tracking information
     *
     * @param string $trackingNumber
     * @return string|false
     * @api
     */
    public function getTrackingInfo($trackingNumber)
    {
        $client = $this->client->getClient();
        $this->trackingCollection->addFieldToFilter(ShipmentTrackInterface::TRACK_NUMBER, $trackingNumber);
        /** @var Shipment\Track $tracking */
        $tracking = $this->trackingCollection->getFirstItem();
        $orderId = $tracking->getOrderId();

        $order = $this->orderRepository->get($orderId);
        if ($order->getBalikobotType()) {
            $matrixRateMapping = json_decode($this->_scopeConfig->getValue('balikobot/general/mapping_matrix_rates'));
            $mappingDecoded = [];
            foreach ($matrixRateMapping as $item) {
                $mappingDecoded['matrixrate_'.$item->matrixrate] = $item->balikobot;
            }
            $balikobotMethod = $mappingDecoded[$order->getShippingMethod()];
            $deliverer = $this->client->getServiceCode($balikobotMethod);
            if (is_array($deliverer)) {
                $carrierCode = $deliverer[0];
            } else {
                $carrierCode = $deliverer;
            }

            $result = $client->trackPackage($carrierCode, $trackingNumber);
        } else {
            $result = $this->getTracking($trackingNumber);
        }

        if ($result instanceof \Magento\Shipping\Model\Tracking\Result) {
            $trackings = $result->getAllTrackings();
            if ($trackings) {
                return $trackings[0];
            }
        } elseif (is_string($result) && !empty($result)) {
            return $result;
        } elseif (is_array($result) && !empty($result)) {
            $return['results'] = $result;
            $return['number'] = $trackingNumber;
            return $return;
        }

        return false;
    }

    public function getTracking($trackings){
        $result = "";
        if(!is_array($trackings)){
            $trackings = [$trackings];
        }
        foreach ($trackings as $tracking){
            $this->getTrackingInfo($tracking);
        }
        return $result;
    }

    public function rollBack($data)
    {
        throw new \Exception($data->getErrors());
    }
}

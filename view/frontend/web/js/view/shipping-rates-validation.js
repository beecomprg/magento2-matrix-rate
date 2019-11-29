/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'Magento_Checkout/js/model/shipping-rates-validation-rules',
        '../model/shipping-rates-validator',
        '../model/shipping-rates-validation-rules'
    ],
    function (
        Component,
        defaultShippingRatesValidator,
        defaultShippingRatesValidationRules,
        matrixrateShippingRatesValidator,
        matrixrateShippingRatesValidationRules
    ) {
        "use strict";
        defaultShippingRatesValidator.registerValidator('matrixrate', matrixrateShippingRatesValidator);
        defaultShippingRatesValidationRules.registerRules('matrixrate', matrixrateShippingRatesValidationRules);
        return Component;
    }
);

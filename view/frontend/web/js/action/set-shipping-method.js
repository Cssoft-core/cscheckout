define([
    'CSSoft_Cscheckout/js/model/shipping-method/save-processor'
], function (shippingMethodSaveProcessor) {
    'use strict';

    return function () {
        return shippingMethodSaveProcessor.saveShippingMethod();
    };
});

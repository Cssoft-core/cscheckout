define([
    'CSSoft_Cscheckout/js/model/shipping-address/save-processor'
], function (shippingAddressSaveProcessor) {
    'use strict';

    return function () {
        return shippingAddressSaveProcessor.saveShippingAddress();
    };
});

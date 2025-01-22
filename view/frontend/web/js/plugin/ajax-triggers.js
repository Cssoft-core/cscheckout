define([
    'CSSoft_Cscheckout/js/model/layout',
    'CSSoft_Cscheckout/js/model/storage-sequence',
    'CSSoft_OrderCheckout/js/action/update-shipping-rates'
], function (layout, sequence, updateShippingRates) {
    'use strict';

    return {
        /**
         * Add triggers to specific requests
         */
        init: function () {
            /**
             * Add sequence after coupons request to reload shipping methods
             */
            sequence.add('after', '/coupons', function (result) {
                var isSuccess = true;

                if (!result) {
                    return;
                }

                if (result.status) {
                    isSuccess = result.status >= 200 &&
                                result.status < 300 ||
                                result.status === 304;
                }

                if (isSuccess) {
                    updateShippingRates();
                }
            });
        }
    };
});

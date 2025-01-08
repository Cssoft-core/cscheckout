define([
    'jquery',
    'underscore',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
    'CSSoft_Cscheckout/js/model/region-serializer'
], function ($, _, wrapper, quote, regionSerializer) {
    'use strict';

    var checkoutConfig = window.checkoutConfig,
        oldMethods = [],
        oldTotal,
        newTotal;

    return function (target) {
        if (!checkoutConfig || !checkoutConfig.isCscheckout) {
            return target;
        }

        function canSkipOriginalAction(newMethods) {
            var newTotal = quote.totals()['grand_total'];

            if (oldTotal === newTotal && newMethods.length === oldMethods.length) {
                if (_.every(newMethods, function (method, index) {
                    return _.isMatch(oldMethods[index], method);
                })) {
                    return true;
                }
            }

            oldTotal = newTotal;
            oldMethods = newMethods;

            return false;
        }

        target.setPaymentMethods = wrapper.wrap(
            target.setPaymentMethods,
            _.debounce(function (originalAction, methods) {
                var data;

                if (this.doNotUpdate) {
                    // Do not update payments after place order was pressed
                    //
                    // This method is called after shipping information save:
                    // @see Checkout/view/frontend/web/js/model/shipping-save-processor/default::done
                    return;
                }

                if (canSkipOriginalAction(methods)) {
                    return;
                }

                // Save form values
                data = regionSerializer.serialize($('.payment-method._active'));

                originalAction(methods);

                // Restore form values
                setTimeout(function () {
                    regionSerializer.restore($('.payment-method._active'), data);
                }, 500);
            }, 200)
        );

        return target;
    };
});

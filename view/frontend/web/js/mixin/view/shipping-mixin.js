define([
    'jquery',
    'CSSoft_OrderCheckout/js/scroll-to-error',
    'Magento_Checkout/js/model/quote'
], function ($, scrollToError, quote) {
    'use strict';

    var checkoutConfig = window.checkoutConfig;

    return function (target) {
        if (!checkoutConfig || !checkoutConfig.isCscheckout) {
            return target;
        }

        return target.extend({
            /**
             * @return {Boolean}
             */
            validateShippingInformation: function () {
                var result = this._super(),
                    event = $.Event('fc:validate-shipping-information', {
                        valid: result
                    });

                $('body').trigger(event);

                // try to scroll to third-party message
                setTimeout(scrollToError, 100);

                return event.valid;
            },

            setShippingInformation: function () {
                if (quote.cscheckout) {
                    quote.cscheckout.state.savingShippingStep = true;
                }

                this._super();

                if (quote.cscheckout) {
                    quote.cscheckout.state.savingShippingStep = false;
                }
            }
        });
    };
});

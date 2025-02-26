define([
    'underscore',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote'
], function (_, wrapper, quote) {
    'use strict';

    var checkoutConfig = window.checkoutConfig;

    return function (target) {
        if (!checkoutConfig || !checkoutConfig.isCscheckout) {
            return target;
        }

        target.startLoader = wrapper.wrap(
            target.startLoader,
            function (originalMethod) {
                var err = new Error(),
                    stop = false,
                    stopStrings = [
                        'set-payment-information',
                        '.updateAddress',
                    ];

                if (quote.cscheckout && quote.cscheckout.state.preventLoader) {
                    quote.cscheckout.state.preventLoader = false;
                    return;
                }

                if (typeof err.stack === 'string') {
                    stop = _.some(stopStrings, function (string) {
                        return err.stack.indexOf(string) > -1;
                    });

                    if (stop) {
                        return;
                    }
                }

                return originalMethod();
            }
        );

        return target;
    };
});

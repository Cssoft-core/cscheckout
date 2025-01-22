define([
    'underscore',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
    'CSSoft_Cscheckout/js/model/layout',
    'CSSoft_Cscheckout/js/action/set-shipping-address'
], function (_, wrapper, quote, layout, setShippingAddressAction) {
    'use strict';

    var checkoutConfig = window.checkoutConfig;

    return function (target) {
        if (!checkoutConfig || !checkoutConfig.isCscheckout || layout.isMultistep()) {
            return target;
        }

        /**
         * Reload payment methods when sensitive shipping fields are updated.
         */
        return wrapper.wrap(target, function (originalAction, shippingAddress) {
            var result,
                keysToCompare = ['postcode', 'countryId', 'region', 'regionId'],
                previousValues = false,
                currentValues = false;

            if (quote.cscheckout && quote.cscheckout.state.placeOrderPressed) {
                // third-party payments fix (Adyen)
                return;
            }

            result = originalAction(shippingAddress);

            if (quote.cscheckout && quote.cscheckout.memo.shippingAddress) {
                previousValues = _.pick(quote.cscheckout.memo.shippingAddress, keysToCompare);
                currentValues = _.pick(shippingAddress, keysToCompare);
            }

            // save shipping address to get updated payment methods
            if (-1 !== checkoutConfig.cssoft.cscheckout.dependencies.payment.indexOf('address')) {
                if (previousValues &&
                    !_.isEmpty(previousValues) &&
                    !_.isEqual(currentValues, previousValues)) {

                    if (quote.cscheckout) {
                        quote.cscheckout.state.preventLoader = true;
                    }

                    setShippingAddressAction();
                }
            }

            return result;
        });
    };
});

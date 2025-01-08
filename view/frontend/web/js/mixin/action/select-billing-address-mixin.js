define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/action/create-billing-address'
], function ($, wrapper, quote, checkoutData, createBillingAddress) {
    'use strict';

    var checkoutConfig = window.checkoutConfig;

    return function (target) {
        if (!checkoutConfig || !checkoutConfig.isCscheckout) {
            return target;
        }

        /**
         * Don't set billing address if "Place Order" was just pressed (2.2.8 bugfix)
         */
        return wrapper.wrap(target, function (originalAction, billingAddress) {
            var checkbox = $('#billing-address-same-as-shipping-shared');

            if (quote.cscheckout && quote.cscheckout.state.placeOrderPressed) {
                // 2.2.8 compatiblity to fix equal billing and shipping addresses
                return;
            }

            if (quote.cscheckout && !quote.cscheckout.state.initialPageLoad &&
                checkbox.length && !checkbox.prop('checked') &&
                billingAddress.getCacheKey() === quote.shippingAddress()?.getCacheKey()
            ) {
                return;
            }

            if (quote.cscheckout?.state.initialPageLoad &&
                checkoutData.getSelectedBillingAddress() === 'new-customer-billing-address' &&
                checkoutData.getNewCustomerBillingAddress()
            ) {
                billingAddress = createBillingAddress(checkoutData.getNewCustomerBillingAddress());
            }

            originalAction(billingAddress);
        });
    };
});

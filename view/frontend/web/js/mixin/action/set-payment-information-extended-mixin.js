define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/quote'
], function ($, wrapper, customer, quote) {
    'use strict';

    var checkoutConfig = window.checkoutConfig,
        deferred = $.Deferred();

    deferred.abort = function () {};
    deferred.error = deferred.fail;
    deferred.success = deferred.done;
    deferred.complete = deferred.always;

    /**
     * Prevent 'The shipping address is missing.' error in 'set-payment-information' request
     */
    function waitForAddressAndSetShippingMethod() {
        if (deferred.state() === 'resolved') {
            return deferred;
        }

        if (!quote.shippingAddress()) {
            setTimeout(function () {
                waitForAddressAndSetShippingMethod();
            }, 200);

            return deferred;
        }

        // wait for shipping rates to prevent reset of previously selected method
        if (!quote.shippingMethod() &&
            (!$('#checkout-step-shipping_method').length ||
                // do not move to define to prevent cyclic dependecy
                require('Magento_Checkout/js/model/shipping-service').isLoading())
        ) {
            setTimeout(function () {
                waitForAddressAndSetShippingMethod();
            }, 200);

            return deferred;
        }

        // do not move to define to prevent cyclic dependecy
        require(['CSSoft_Cscheckout/js/action/set-shipping-method'], function (setShippingMethodAction) {
            setShippingMethodAction().always(function () {
                deferred.resolve();
            });
        });

        return deferred;
    }

    return function (target) {
        if (!checkoutConfig || !checkoutConfig.isCscheckout) {
            return target;
        }

        return wrapper.wrap(target, function (originalAction, messageContainer, paymentData, skipBilling) {
            var placeOrderPressed = quote.cscheckout && quote.cscheckout.state.placeOrderPressed,
                placeOrderVisible = $('.action.checkout', '.place-order').is(':visible'),
                initialPageLoad = !quote.cscheckout || quote.cscheckout.state.initialPageLoad;

            if (!customer.isLoggedIn() && !quote.guestEmail) {
                if (initialPageLoad || placeOrderVisible && !placeOrderPressed) {
                    quote.guestEmail = 'mail@example.com'; // Prevent '400 Bad Request' response
                    skipBilling = true;
                }
            }

            if (!initialPageLoad && !placeOrderVisible && quote.guestEmail === 'mail@example.com') {
                quote.guestEmail = '';
            }

            if (!initialPageLoad || quote.isVirtual()) {
                return originalAction(messageContainer, paymentData, skipBilling);
            }

            return waitForAddressAndSetShippingMethod().then(function () {
                originalAction(messageContainer, paymentData, skipBilling);
            });
        });
    };
});

define([
    'jquery',
    'underscore',
    'Magento_Checkout/js/model/quote',
    'CSSoft_Cscheckout/js/model/layout',
    'CSSoft_Cscheckout/js/model/validator',
    'CSSoft_Cscheckout/js/action/sync-billing-address',
    'Magento_Checkout/js/action/set-shipping-information',
    'Magento_Checkout/js/model/payment-service'
], function (
    $,
    _,
    quote,
    layout,
    validator,
    syncBillingAddress,
    setShippingInformationAction,
    paymentService
) {
    'use strict';

    /**
     * @param  {Object} result
     * @return {Object}
     */
    function submitShippingInformationCallback(result) {
        $('body').trigger($.Event('fc:placeOrderSetShippingInformationAfter', {
            response: result
        }));

        delete paymentService.doNotUpdate;

        return result;
    }

    return {
        /**
         * Place Order method
         */
        placeOrder: _.debounce(function () {
            var event;

            syncBillingAddress();

            quote.cscheckout.state.placeOrderPressed = true;

            if (!validator.validate()) {
                quote.cscheckout.state.placeOrderPressed = false;

                return false;
            }

            event = $.Event('fc:placeOrderBefore', {
                cancel: false
            });
            $('body').trigger(event);

            // allow to interrupt the process
            if (event.cancel) {
                quote.cscheckout.state.placeOrderPressed = false;

                return;
            }

            if (layout.isMultistep()) {
                this._place();
            } else {
                $.when(this.submitShippingInformation()).done(this._place);
            }
        }, 200),

        /**
         * Click hidden "Place Order" button in payment section
         */
        _place: function () {
            quote.cscheckout.state.placeOrderPressed = false;

            $(
                [
                    '.actions-toolbar:not([style="display: none;"])',
                    '.action.checkout:not([style="display: none;"])'
                ].join(' '),
                '.payment-method._active'
            ).trigger('click');

            // try to call button click method directly, without .click() emulation

            $('body').trigger('fc:placeOrderAfter');
        },

        /**
         * @return {Deferred|Boolean}
         */
        submitShippingInformation: function () {
            if (!quote.isVirtual() && quote.shippingMethod()) {
                paymentService.doNotUpdate = true;

                $('body').trigger($.Event('fc:placeOrderSetShippingInformationBefore'));

                return setShippingInformationAction()
                    .then(
                        submitShippingInformationCallback,
                        submitShippingInformationCallback
                    );
            }

            return true;
        }
    };
});

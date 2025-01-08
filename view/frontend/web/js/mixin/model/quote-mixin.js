define([
    'jquery',
    'knockout',
    'CSSoft_Cscheckout/js/model/layout'
], function ($, ko, layout) {
    'use strict';

    return function (quote) {
        var billingAddress = quote.billingAddress;

        quote.billingAddress = ko.pureComputed({
            read: function () {
                return billingAddress();
            },

            write: function (value) {
                var billingInsideShipping = $('.checkout-billing-address')
                    .closest('.checkout-shipping-address').length > 0;

                // Do not reset billing address when:
                // 1. Layout is multistep.
                // 2. Billing is moved to shipping step.
                if (value === null &&
                    billingInsideShipping &&
                    layout.isMultistep() &&
                    quote.cscheckout &&
                    quote.cscheckout.state.savingShippingStep
                ) {
                    quote.cscheckout.state.savingShippingStep = false;

                    return;
                }

                billingAddress(value);
            }
        });

        return quote;
    };
});

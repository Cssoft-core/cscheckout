define([
    'underscore',
    'uiRegistry',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/checkout-data-resolver',
    'Magento_Checkout/js/model/address-converter'
], function (_, registry, quote, checkoutDataResolver, addressConverter) {
    'use strict';

    function copyAddressToQuote(shippingAddressData) {
        var shippingAddress = quote.shippingAddress(),
            addressData = addressConverter.formAddressDataToQuoteAddress(shippingAddressData),
            field;

        if (!shippingAddress) {
            return;
        }

        for (field in addressData) {
            if (addressData.hasOwnProperty(field) &&
                shippingAddress.hasOwnProperty(field) &&
                typeof addressData[field] != 'function'
            ) {
                shippingAddress[field] = addressData[field];
            } else if (typeof addressData[field] != 'function' &&
                !_.isEqual(shippingAddress[field], addressData[field])) {
                shippingAddress = addressData;
                break;
            }
        }

        quote.shippingAddress(shippingAddress);
    }

    return function (target) {
        return target.extend({
            selectShipping: function () {
                this._super();

                if (this.isStorePickupAddress(quote.shippingAddress())) {
                    // copied from shipping.js::setShippingInformation()
                    quote.billingAddress(null);
                    checkoutDataResolver.resolveBillingAddress();
                    registry.async('checkoutProvider')(checkoutProvider => {
                        copyAddressToQuote(checkoutProvider.get('shippingAddress'));
                    });
                }
            }
        });
    };
});

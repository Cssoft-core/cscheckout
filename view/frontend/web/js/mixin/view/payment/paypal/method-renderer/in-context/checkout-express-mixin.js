define([
    'jquery',
    'underscore',
    'uiRegistry',
    'Magento_Checkout/js/model/quote',
    'CSSoft_Cscheckout/js/model/validator',
    'CSSoft_Cscheckout/js/model/layout'
], function ($, _, registry, quote, validator, layout) {
    'use strict';

    var checkoutConfig = window.checkoutConfig;

    return function (target) {
        if (!checkoutConfig || !checkoutConfig.isCscheckout) {
            return target;
        }

        return target.extend({
            /**
             * @param {HTMLElement} context
             */
            initListeners: function (context) {
                $('#customer-email').on('change', function () {
                    this.validate();
                }.bind(this));

                setTimeout(this.observeShippingChanges.bind(this), 1000);

                return this._super(context);
            },

            observeShippingChanges: function () {
                var scopes = [
                    '.form-shipping-address input',
                    '.form-shipping-address select',
                    '.form-shipping-address textarea'
                ];

                $(document).on('change paste cut', scopes.join(','), _.debounce(function () {
                    this.validate();
                }.bind(this), 300));
            },

            validate: function () {
                var result = this._super();

                if (this.actions && this.actions.disable) {
                    if (!this.silentlyValidateShippingInformation()) {
                        this.actions.disable();
                    }
                }

                return result;
            },

            onClick: function () {
                this._super();
                validator.validateShippingAddress();
                validator.validateShippingRadios();
            },

            /**
             * [silentlyValidateShippingInformation description]
             */
            silentlyValidateShippingInformation: function () {
                var shipping = registry.get('checkout.steps.shipping-step.shippingAddress'),
                    address = registry.get('checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset');

                if (quote.isVirtual() || layout.isMultistep() || !shipping.isFormInline || !address) {
                    return true;
                }

                return validator.isValidUiComponent(address);
            }
        });
    };
});

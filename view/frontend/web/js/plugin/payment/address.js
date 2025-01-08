define([
    'Magento_Ui/js/lib/view/utils/async',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/checkout-data-resolver',
    'Magento_Customer/js/model/customer',
    'CSSoft_Cscheckout/js/model/layout',
    'CSSoft_Cscheckout/js/utils/move',
    'CSSoft_Cscheckout/js/plugin/payment/address-instant-save',
    'mage/translate'
], function ($, quote, checkoutDataResolver, customer, layout, move, billingAddressInstantSave, $t) {
    'use strict';

    var settings = {
            el: '.checkout-billing-address',
            title: '',
            position: 0
        },
        position = {
            'before-payment-methods': {
                el: '.opc-payment',
                method: 'before'
            },
            'before-shipping-address': {
                title: false,

                /**
                 * @return {Array}
                 */
                el: function () {
                    if (customer.isLoggedIn() || layout.isEmailOnSeparateStep()) {
                        return ['#checkout-step-shipping', 'before'];
                    }

                    return ['#checkout-step-shipping .form-login', 'after'];
                },

                /**
                 * Additional logic to execute after move
                 */
                after: function () {
                    if (layout.isMultistep() &&
                        !quote.billingAddress() &&
                        window.checkoutConfig.customerData?.default_billing
                    ) {
                        checkoutDataResolver.resolveBillingAddress();
                    }

                    $('body').addClass('fc-billing-before-shipping');

                    $.async('.checkout-billing-address', function (el) {
                        $(el).append('<div class="step-title">' + $t('Shipping Address') + '</div>');
                    });

                    // rename top title "Shipping Address"
                    $.async('#shipping > .step-title', function (el) {
                        $(el).text(settings.title ? settings.title : $t('Address'));
                    });
                }
            },
            'after-shipping-address': {
                el: '#checkout-step-shipping',
                method: 'after'
            }
        };

    return {
        /**
         * Plugin initialization
         */
        init: function (config, positionRules) {
            settings = $.extend({}, settings, config);
            position = $.extend({}, position, positionRules);

            this.addTitle();
            this.move();
        },

        /**
         * Add title above billign address
         */
        addTitle: function () {
            var rule = this.getRule();

            if (!settings.title || rule && rule.title === false) {
                return;
            }

            $.async(settings.el, function (el) {
                var title = '<div class="step-title">' + settings.title + '</div>';

                if ($(el).closest('.payment-method-content').length) {
                    return;
                }

                if (settings.position === 'after-shipping-address') {
                    $(el).find('.billing-address-same-as-shipping-block').after(title);
                } else {
                    $(el).prepend(title);
                }
            });
        },

        /**
         * Move billing address as configured
         */
        move: function () {
            var rule = this.getRule();

            if (!rule || rule.disabled) {
                return;
            }

            if (typeof rule.method === 'function') {
                rule.method = rule.method();
            }

            if (typeof rule.el === 'function') {
                rule.el = rule.el();
            }

            if (typeof rule.el === 'object') {
                rule.method = rule.el[1];
                rule.el = rule.el[0];
            }

            if (settings.position.includes('shipping')) {
                $(document).on('fc:validate-shipping-information', function (event) {
                    if (!$('body').hasClass('fc-billing-instant-save')) {
                        return;
                    }
                    if (!billingAddressInstantSave.validate()) {
                        event.valid = false;
                    }
                });
            }

            move(settings.el)[rule.method](rule.el).done(rule.after);
        },

        /**
         * Get position rule
         *
         * @return {Object}
         */
        getRule: function () {
            var rule = position[settings.position];

            if (quote.isVirtual() && settings.position.indexOf('shipping') !== -1) {
                rule = position['before-payment-methods'];
            }

            return rule;
        }
    };
});

/*global ga dataLayer*/

define([
    'jquery',
    'underscore',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/model/customer',
    'CSSoft_Cscheckout/js/utils/form-field/watcher',
    'mage/validation'
], function ($, _, quote, customer, watcher) {
    'use strict';

    // These steps must be in sync with GA ecommerce funnel steps.
    var steps = {
            checkout: 1,
            email: 2,
            shippingMethod: 3,
            paymentMethod: 4,
            placeOrder: 5
        },
        events = {
            checkout: 'begin_checkout',
            email: 'email_address',
            shippingMethod: 'shipping_method',
            paymentMethod: 'payment_method',
            placeOrder: 'purchase'
        },
        currentStep,
        pendingSteps = {},
        pendingStepsTmp = {};

    /**
     * @param {String} email
     * @return {String}
     */
    function getMailDomain(email) {
        if (!email || email.indexOf('@') === -1) {
            return '';
        }

        return email.split('@')[1];
    }

    /**
     * Init ga.ec script.
     */
    function unitGoogleAnalytics() {
        //jscs:disable requireCamelCaseOrUpperCaseIdentifiers
        ga('require', 'ec', 'ec.js');
        ga('set', 'currencyCode', quote.totals().base_currency_code);
        //jscs:enable requireCamelCaseOrUpperCaseIdentifiers
    }

    function getEcommerceData() {
        var totals = quote.totals();

        return {
            currency: totals.base_currency_code,
            value: totals.base_grand_total - totals.base_shipping_amount + quote.shippingMethod()?.amount || 0,
            items: quote.getItems().map(item => {
                return {
                    item_id: item.sku,
                    item_name: item.name,
                    price: item.price,
                    quantity: item.qty
                };
            })
        };
    }

    function toGtmData(code, data) {
        var result = {},
            codeToValueProperty = {
                shippingMethod: 'shipping_tier',
                paymentMethod: 'payment_type'
            };

        if (codeToValueProperty[code]) {
            result[codeToValueProperty[code]] = data.option;
        }

        return result;
    }

    /**
     * Process pending steps
     */
    function processPendingSteps() {
        _.each(pendingStepsTmp, function (stepData) {
            step(stepData.code, stepData.option);
        });
        pendingStepsTmp = {};
    }

    /**
     * Send step action to GA
     *
     * @param {String} code
     */
    function step(code, option) {
        var data = {
            step: steps[code]
        };

        if (!data.step) {
            return;
        }

        if (option) {
            data.option = option;
        }

        // fix for proper dropoff calculation
        if (data.step < steps.placeOrder && data.step > currentStep + 1) {
            pendingSteps[code] = {
                code: code,
                step: data.step,
                option: data.option
            };

            return;
        }

        pendingStepsTmp = _.sortBy(pendingSteps, 'step');
        pendingSteps = {};

        if (data.step === steps.placeOrder) {
            processPendingSteps();
        }

        if (typeof ga === 'function') {
            if (data.step !== currentStep) {
                ga('ec:setAction', 'checkout', data);
            } else {
                ga('ec:setAction', 'checkout_option', data);
            }
            ga('send', 'event', 'checkout', 'option');
        }

        if (typeof dataLayer === 'object') {
            dataLayer.push({ ecommerce: null });
            dataLayer.push(_.extend({}, toGtmData(code, data), {
                event: events[code],
                ecommerce: getEcommerceData()
            }));
        }

        currentStep = data.step;

        if (data.step !== steps.placeOrder) {
            processPendingSteps();
        }
    }

    /**
     * Report checkout page load
     */
    function reportCheckoutPage() {
        if (typeof ga === 'function') {
            $.each(quote.getItems(), function (key, item) {
                ga('ec:addProduct', {
                    id: item.sku,
                    name: item.name,
                    price: item.price,
                    quantity: item.qty
                });
            });
            ga('ec:setAction', 'checkout', {
                step: steps.checkout,
                option: customer.isLoggedIn() ? 'Logged In' : 'Guest'
            });
            ga('send', 'pageview');
        }

        currentStep = steps.checkout;

        if (typeof dataLayer === 'object') {
            dataLayer.push({ ecommerce: null });
            dataLayer.push({
                event: 'begin_checkout',
                ecommerce: getEcommerceData()
            });
        }

        if (customer.isLoggedIn()) {
            step('email', customer.customerData ? getMailDomain(customer.customerData.email) : '');
        }
    }

    /**
     * Attach event listeners to catch all ecommerce information
     */
    function addObservers() {
        var validateEmail = $.validator.methods['validate-email'];

        // Ecommerce Funnel Steps
        watcher('#customer-email', _.debounce(function () {
            var email = $('#customer-email').val();

            if (validateEmail(email)) {
                step('email', getMailDomain(email));
            }
        }, 200));

        if (!quote.isVirtual()) {
            quote.shippingMethod.subscribe(function (method) {
                var option = false;

                //jscs:disable requireCamelCaseOrUpperCaseIdentifiers
                if (method && method.carrier_code) {
                    option = method.carrier_code;

                    if (method.method_code) {
                        option += ': ' + method.method_code;
                    }
                }
                //jscs:enable requireCamelCaseOrUpperCaseIdentifiers

                step('shippingMethod', option);
            });
        } else {
            step('shippingMethod', 'not required');
        }

        quote.paymentMethod.subscribe(function (method) {
            var option = false;

            if (method && method.method) {
                option = method.method;
            }

            step('paymentMethod', option);
        });

        $(document.body).on('fc:placeOrderAfter', function () {
            step('placeOrder');
        });
    }

    return {
        /**
         * Init google ecommerce integration
         */
        init: function () {
            if (typeof ga !== 'function' && typeof dataLayer !== 'object') {
                return setTimeout(this.init.bind(this), 5000);
            }

            if (typeof ga === 'function') {
                unitGoogleAnalytics();
            }

            reportCheckoutPage();

            addObservers();
        }
    };
});

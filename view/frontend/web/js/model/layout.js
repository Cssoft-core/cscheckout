define([], function () {
    'use strict';

    return {
        /**
         * Get current cscheckout layout mode
         * @returns {String}
         */
        getLayout: function () {
            return window.checkoutConfig.cssoft.cscheckout.layout;
        },

        /**
         * Checks if current layout is a onecolumn expanded layout
         * @returns {Boolean}
         */
        isOneColumnExpanded: function () {
            return this.getLayout() === 'cscheckout-col1-set expanded';
        },

        /**
         * Checks if current layout is a multistep layout
         * @return {Boolean}
         */
        isMultistep: function () {
            return this.getLayout() === 'cscheckout-col1-set';
        },

        /**
         * Checks if email field moved to separate step
         * @return {Boolean}
         */
        isEmailOnSeparateStep: function () {
            return window.checkoutConfig.cssoft.cscheckout.emailOnSeparateStep;
        },

        /**
         * @return {Boolean}
         */
        isBillingBeforeShipping: function () {
            return window.checkoutConfig.cssoft.cscheckout.payment.billingBeforeShipping;
        }
    };
});

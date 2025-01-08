define([
    'jquery',
    'mage/utils/wrapper'
], function ($, wrapper) {
    'use strict';

    var checkoutConfig = window.checkoutConfig;

    return function (target) {
        if (!checkoutConfig || !checkoutConfig.isCscheckout) {
            return target;
        }

        target.set = wrapper.wrap(
            target.set,
            function (originalMethod, sectionName, sectionData) {
                try {
                    return originalMethod(sectionName, sectionData);
                } catch {
                    //
                }
            }
        );

        return target;
    };
});

define([
    'jquery'
], function ($) {
    'use strict';

    return {
        /**
         * Con los terroristas
         * Do the Harlem Shake
         *
         * @param {Element|jQuery} el
         */
        shake: function (el) {
            $(el).addClass('cscheckout-shake')
                .one(
                    'cssoftAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend',
                    function () {
                        $(this).removeClass('cscheckout-shake');
                    }
                );
        }
    };
});

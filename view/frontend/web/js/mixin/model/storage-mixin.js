define([
    'jquery',
    'underscore',
    'mage/utils/wrapper',
    'CSSoft_Cscheckout/js/model/storage-sequence'
], function ($, _, wrapper, sequence) {
    'use strict';

    var checkoutConfig = window.checkoutConfig;

    /**
     * @param  {Object} target
     * @return {Object}
     */
    return function (target) {
        var requests = {
            get: {},
            post: {},
            put: {},
            delete: {}
        };

        if (!checkoutConfig || !checkoutConfig.isCscheckout) {
            return target;
        }

        /**
         * Add global=false param to prevent redundant sections update requests
         *
         * @param {Function} o
         * @param {String} url
         */
        function _postParamsProxy(o, url) {
            var args = Array.prototype.slice.call(arguments, 1),
                pattern = /\/shipping-information|\/shipping-method|\/billing-address|\/gift-message/;

            if (args.length < 3 && url.match(pattern)) {
                args[2] = false;
            }

            return o.apply(target, args);
        }

        target.post = wrapper.wrap(target.post, _postParamsProxy);

        /**
         * Prevents multiple requests to the same url using same method at a time
         *
         * @param {Object} memo
         * @return {Function}
         */
        function _debounceProxy(memo) {
            var index = 0;

            return function (original, url) {
                var args = Array.prototype.slice.call(arguments, 1),
                    abort,
                    callback,
                    deferred;

                /** [callback description] */
                callback = function (result, i, method) {
                    memo[url][i].deferred[method](result);
                    memo[url][i] = false;
                };

                /** [abort description] */
                abort = function () {
                    var i = this.index;

                    if (!memo[url][i] || !memo[url][i].xhr) {
                        return;
                    }

                    memo[url][i].xhr.abort();
                };

                if (!memo[url] || !memo[url][index] || memo[url][index].xhr) {
                    deferred = $.Deferred();
                    deferred.abort = abort.bind({ index: index });
                    deferred.error = deferred.fail;
                    deferred.success = deferred.done;
                    deferred.complete = deferred.always;

                    if (!memo[url]) {
                        memo[url] = {};
                    } else if (memo[url][index] && memo[url][index].xhr) {
                        index++;
                    }

                    memo[url][index] = {
                        deferred: deferred,
                        debounced: _.debounce(function () {
                            var i = this.index;

                            memo[url][i].xhr = original.apply(target, memo[url][i].args);
                            memo[url][i].xhr.then(
                                function (response) {
                                    callback(response, i, 'resolve');
                                },
                                function (response) {
                                    callback(response, i, 'reject');
                                }
                            );
                        }.bind({ index: index }), 100)
                    };
                }

                memo[url][index].args = args;
                memo[url][index].debounced();

                return memo[url][index].deferred;
            };
        }

        target.get    = wrapper.wrap(target.get, _debounceProxy(requests.get));
        target.post   = wrapper.wrap(target.post, _debounceProxy(requests.post));
        target.put    = wrapper.wrap(target.put, _debounceProxy(requests.put));
        target.delete = wrapper.wrap(target.delete, _debounceProxy(requests.delete));

        /**
         * Runs sequence before and after original action
         *
         * @param  {Function} o
         * @param  {String} url
         */
        function _proxy(o, url) {
            var args = arguments,
                wrappedOriginal,
                wrappedAfter;

            if (sequence.has(url)) {
                /**
                 * Wrapped original function to use in `then` chain
                 * @return {Promise}
                 */
                wrappedOriginal = function () {
                    return o.apply(
                        target,
                        Array.prototype.slice.call(args, 1)
                    );
                };

                /**
                 * Wrapped sequence.run to use in `then` chain
                 * @param  {Mixed} result
                 * @return {Promise}
                 */
                wrappedAfter = function (result) {
                    sequence.run(url, 'after', result);

                    return result;
                };

                return $.when.apply($, sequence.run(url, 'before'))
                    .then(wrappedOriginal, wrappedOriginal)
                    .then(wrappedAfter, wrappedAfter);
            }

            return o.apply(
                target,
                Array.prototype.slice.call(args, 1)
            );
        }

        // Wrap all methods into _proxy call
        target.get    = wrapper.wrap(target.get, _proxy);
        target.post   = wrapper.wrap(target.post, _proxy);
        target.put    = wrapper.wrap(target.put, _proxy);
        target.delete = wrapper.wrap(target.delete, _proxy);

        return target;
    };
});

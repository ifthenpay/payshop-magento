/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'Magento_Checkout/js/view/payment/default',
    ],
    function (Component) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Ifthenpay_Payshop/payment/form',
                phoneNumber: ''
            },

            getCode: function () {
                return 'ifthenpay_payshop';
            },

            getData: function () {
                return {
                    'method': this.item.method,
                };
            },
        });
    }
);
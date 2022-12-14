define(
    [
        'jquery',
        'underscore',
        'Magento_Checkout/js/model/url-builder',
        'mage/storage',
        'ko'
    ],
    function(
        $,
        _,
        urlBuilder,
        storage,
        ko
    ) {
        'use strict';
        return {
            paymentMethods: ko.observable({}),

            getSecurePaymentLink: function(orderId) {
                let serviceUrl = urlBuilder.createUrl('/easytransac/orders/paymentstatus', {});
                let payload = {
                    orderId: orderId
                };
                return storage.post(
                    serviceUrl,
                    JSON.stringify(payload),
                    true
                );
            },
        };
    }
);

/**
 * Easytransac_Gateway payment method.
 *
 * @category    Easytransac
 * @package     Easytransac_Gateway
 * @author      Easytrasac
 * @copyright   Easytransac (https://www.easytransac.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
define(
    [
        'jquery',
        'mage/translate',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/action/place-order',
        'Magento_Checkout/js/model/url-builder',
        'mage/storage',
        'Magento_Customer/js/model/customer',
        'Easytransac_Gateway/js/model/easytransac-payment-service',
        'Magento_Checkout/js/action/redirect-on-success'
    ],
    function (
        $,
        $t,
        Component,
        fullScreenLoader,
        placeOrderAction,
        urlBuilder,
        storage,
        customer,
        easytransacPaymentService,
        redirectOnSuccessAction
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Easytransac_Gateway/payment/easytransac-card-form',
                code: 'card_gateway'
            },

            initObservable: function () {
                this._super()
                return this;
            },

            initialize: function () {
                this._super();
            },

            /**
             * Check if payment is active
             *
             * @returns {Boolean}
             */
            isActive: function () {
                return true;
            },

            /**
             * Get payment code
             *
             * @returns {String}
             */
            getCode: function () {
                return 'card_gateway';
            },

            /**
             * Validate payment method
             *
             * @return {Boolean}
             */
            validate: function () {
                return true;
            },

            /**
             * Hide save card option for oneclick
             */
            isSelectCard: function() {
                $('#save_card_option').hide();
                $(document).find('input[name="card_payment_new_card"]').prop('checked', false);
                return true;
            },

            /**
             * Display save card option for oneclick
             */
            isNewCard: function() {
                $('#save_card_option').show();
                $(document).find('input[name="oneclick_saved_data"]').prop('checked', false);
                return true;
            },

            /**
             * @return {Object}
             */
            getData: function () {
                let saveCard = $('input[name="easytransac[is_enabled]"]').prop("checked") ? 'yes' : 'no';
                let alias = [];
                $.each($("input[name='oneclick_saved_data']:checked"), function () {
                    alias.push($(this).val());
                });

                return {
                    'method': this.getCode(),
                    'additional_data': {
                        'saveCard': saveCard,
                        'aliasId': alias.toString()
                    }
                };
            },

            /**
             * Order Placed
             *
             * @returns {boolean}
             */
            beforePlaceOrder: function () {
                let self = this;

                if (this.validate()) {
                    fullScreenLoader.startLoader();
                    self.isPlaceOrderActionAllowed(false);

                    self.getPlaceOrderDeferredObject().fail(
                        function () {
                            fullScreenLoader.stopLoader();
                            self.isPlaceOrderActionAllowed(true);
                        }
                    ).done(
                        function (orderId) {
                            self.afterPlaceOrder();
                            self.orderId = orderId;
                            easytransacPaymentService.getSecurePaymentLink(orderId).done(function (responseJSON) {
                                if (responseJSON) {
                                    window.location.replace(responseJSON);
                                } else {
                                    redirectOnSuccessAction.execute();
                                }
                            });
                        }
                    );
                }
                return false;
            },

            getPlaceOrderDeferredObject: function () {
                return $.when(placeOrderAction(this.getData()));
            },

            /**
             * Payment method title
             *
             * @returns {string}
             */
            getTitle: function () {
                return $t("Card Payment With Easytransac");
            },

            /**
             * Display easytransac logo at checkout
             *
             * @returns {*}
             */
            displayCheckoutIcon: function () {
                return window.checkoutConfig.payment[this.getCode()].checkouticon;
            },

            /**
             * Save cards for later
             *
             * @returns {*}
             */
            isSaveDataEnabled: function () {
                if (!customer.isLoggedIn()) {
                    return false;
                }
                return window.checkoutConfig.payment[this.getCode()].saveCCDataEnabled;
            },

            /**
             * Get Saved payment details from customer id
             *
             * @returns {array}
             */
            getSavedPaymentData: function () {
                return window.checkoutConfig.payment[this.getCode()].savedPaymentData;
            },

            /**
             * Save cards enable and retrieve more than 0 card details will show
             *
             * @returns {boolean}
             */
            useSaveDataMode: function () {
                return !!(this.isSaveDataEnabled() && this.getSavedPaymentData().length > 0);
            },
        });
    }
);

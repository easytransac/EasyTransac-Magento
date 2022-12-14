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
        'Magento_Checkout/js/model/payment/additional-validators',
        'Easytransac_Gateway/js/model/easytransac-payment-service',
        'Magento_Checkout/js/action/place-order',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/action/redirect-on-success',
        'ko'
    ],
    function (
        $,
        $t,
        Component,
        fullScreenLoader,
        additionalValidators,
        easytransacPaymentService,
        placeOrderAction,
        customer,
        redirectOnSuccessAction,
        ko
    ) {
        'use strict';

        return Component.extend({
            redirectAfterPlaceOrder: false,
            defaults: {
                template: 'Easytransac_Gateway/payment/easytransac-multipayment',
                code: 'easytransac_multipayment',
                selectedEmi: ko.observable(true)
            },

            initObservable: function () {
                this._super()
                return this;
            },

            initialize: function () {
                this._super();
            },

            /**
             * @returns {string}
             */
            getCode: function () {
                return 'easytransac_multipayment';
            },

            /**
             * Method Title
             * @returns {*}
             */
            getTitle: function () {
                return $t("Pay in Several Instalments with Easytransac");
            },

            /**
             * Display Easytransac Logo at checkout
             *
             * @returns {*}
             */
            displayCheckoutIcon: function () {
                return window.checkoutConfig.payment[this.getCode()].checkouticon;
            },

            /**
             * Hide save card option for oneclick
             */
            isSelectCard: function() {
                $('#save_card_option_multipayment').hide();
                $(document).find('input[name="multi_card_payment_new_card"]').prop('checked', false);
                return true;
            },

            /**
             * Display save card option for oneclick
             */
            isNewCard: function() {
                $('#save_card_option_multipayment').show();
                $(document).find('input[name="oneclick_saved_data"]').prop('checked', false);
                return true;
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

            /**
             * Get EMI months Value
             * @returns {*}
             */
            emi_months: function () {
                return window.checkoutConfig.payment[this.getCode()].monthly_emi;
            },

            /**
             * Get Monthly Installments
             * @returns {*[]}
             */
            getMonthlyInstallments: function () {
                const months = [];
                for (let i = 2; i <= this.emi_months(); i++) {
                    months.push(i);
                }
                return months;
            },

            /**
             * Get Payment additional Data
             *
             * @return {Object}
             */
            getData: function () {

                let saveCard = $('input[name="easymultitransac[is_enabled]"]').prop("checked") ? 'yes' : 'no';
                let month =$("input[name='monthly_installments']:checked").val();
                let alias = [];
                $.each($("input[name='oneclick_saved_data']:checked"), function () {
                    alias.push($(this).val());
                });

                return {
                    'method': this.getCode(),
                    'additional_data': {
                        'saveCard': saveCard,
                        'emiMonths': month,
                        'aliasId': alias.toString()
                    }
                };
            },

            /**
             * Custom place order function
             *
             * @returns {boolean}
             */
            continueWithMultiPayments: function () {
                let self = this;
                if (additionalValidators.validate()) {
                    fullScreenLoader.startLoader();
                    self.isPlaceOrderActionAllowed(false);

                    self.getPlaceOrderDeferredObject().fail(
                        function () {
                            fullScreenLoader.stopLoader();
                            self.isPlaceOrderActionAllowed(true);
                        }
                    ).done(
                        function (orderId) {
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

            /**
             *
             * @returns {*|jQuery}
             */
            getPlaceOrderDeferredObject: function () {
                return $.when(placeOrderAction(this.getData()));
            },
        });
    }
);

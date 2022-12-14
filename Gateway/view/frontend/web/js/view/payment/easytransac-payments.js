/**
 * Easytransac_Gateway payment method.
 *
 * @category    Easytransac
 * @package     Easytransac_Gateway
 * @author      Easytrasac
 * @copyright   Easytransac (https://www.easytransac.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';

        rendererList.push(
            {
                type: 'easytransac_multipayment',
                component: 'Easytransac_Gateway/js/view/payment/method-renderer/easytransac_multipayment'
            },
            {
                type: 'card_gateway',
                component: 'Easytransac_Gateway/js/view/payment/method-renderer/card_gateway'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);

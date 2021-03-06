/**
 * FatZebra_Gateway Magento JS component
 *
 * @category    Fat Zebra
 * @package     FatZebra_Gateway
 * @copyright   Fat Zebra (https://www.fatzebra.com)
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
                type: 'fatzebra_gateway',
                component: 'FatZebra_Gateway/js/view/payment/method-renderer/fatzebra-method'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);

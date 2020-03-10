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
      'Magento_Payment/js/view/payment/cc-form',
      'jquery',
      'Magento_Payment/js/model/credit-card-validation/validator',
      'Magento_Ui/js/model/messageList',
      'Magento_Checkout/js/model/full-screen-loader',
      'Magento_Vault/js/view/payment/vault-enabler'
  ],
  function (Component, $, validator, messageList, fullScreenLoader, VaultEnabler) {
        'use strict';

        window.fatzebraGateway.messageList = messageList;
        window.fatzebraGateway.fullScreenLoader = fullScreenLoader;

        return Component.extend({
            defaults: {
                template: 'FatZebra_Gateway/payment/fatzebra-form'
            },

            initialize: function() {
                this._super();
                this.vaultEnabler = new VaultEnabler();
                this.vaultEnabler.setPaymentCode(this.getVaultCode());
            },

            /**
             * @returns {String}
             */
            getVaultCode: function () {
                return window.checkoutConfig.payment['fatzebraGateway'].ccVaultCode;
            },

            getCode: function() {
                return 'fatzebra_gateway';
            },

            isActive: function() {
                return true;
            },

            getIframeUrl: function() {
              return window.checkoutConfig.payment.fatzebraGateway.iframeSrc;
            },

            canSaveCard: function() {
              return window.checkoutConfig.payment.fatzebraGateway.canSaveCard;
            },

            customerHasSavedCC: function() {
              return window.checkoutConfig.payment.fatzebraGateway.customerHasSavedCC;
            },

            fatzebraPlaceOrder: function() {
                window.fatzebraGateway.processIframeOrder();
            },

            getData: function() {
                var data = {
                    'method': this.item.method,
                    'additional_data': {
                        "cc_token": jQuery('#fatzebra_gateway-token').val(),
                        "cc_save": jQuery("#fatzebra_gateway_cc_save").is(':checked')
                    }
                };

                this.vaultEnabler.visitAdditionalData(data);
                return data;
            }
        });
    }
);

setTimeout(function() {
  var s = document.createElement( 'script' );
  s.setAttribute( 'src', window.checkoutConfig.payment.fatzebraGateway.fraudFingerprintSrc );
  document.body.appendChild( s );
}, 1000);


window.fatzebraGateway = {
  attachEvents: function() {
    // Clear existing events...
    window.removeEventListener ? window.removeEventListener("message", window.fatzebraGateway.receiveMessage, false) : window.detatchEvent("onmessage", window.fatzebraGateway.receiveMessage);
    // And add...
    window.addEventListener ? window.addEventListener("message", window.fatzebraGateway.receiveMessage, false) : window.attachEvent("onmessage", window.fatzebraGateway.receiveMessage);
  },
  processIframeOrder: function() {
    // PostMessage
    var postMessageStrings = false;
    try{ window.postMessage({toString: function(){ postMessageStrings = true; }},"*") ;} catch(e){}

    // Trigger the iframe
    var iframe = document.getElementById("checkout-iframe");
    window.fatzebraGateway.fullScreenLoader.startLoader();
    iframe.contentWindow.postMessage('doCheckout', '*');
  },

  receiveMessage: function(event) {
      if (event.origin.indexOf("paynow") === -1)  return;
      window.fatzebraGateway.fullScreenLoader.stopLoader();

      var payload = event.data;
      if (typeof event.data == 'string') {
          if (/\[object/i.test(event.data)) {
              window.fatzebraGateway.messageList.addErrorMessage({message: "Sorry, it looks like there has been a problem communicating with your browsers..."});
          }
          var pairs = payload.split("&");
          payload = {};
          for (var i = 0; i < pairs.length; i++) {
              var element = pairs[i];
              var kv = element.split("=");
              payload[kv[0]] = kv[1];
          }
      }

      if ('data' in payload) {
          if (payload.data.message == "form.invalid") {
              window.fatzebraGateway.messageList.addErrorMessage({message: "Validation error: " + payload.data.errors});
              return;
          }
          // Modern browser
          // Use payload.data.x
          window.fatzebraGateway.fillInPaymentForm(payload.data);
      } else {
          // Old browser don't use payload.data.x
          if (payload.message == "form.invalid") {
              window.fatzebraGateway.messageList.addErrorMessage({message: "Validation error: " + payload.errors});
              return;
          }
          window.fatzebraGateway.fillInPaymentForm(payload);
      }
  },
  fillInPaymentForm: function(data) {
    jQuery("#fatzebra_gateway-token").val(data.token);
    jQuery('#fatzebra-place-token-order').click();
  },
  messageList: null,
  fullScreenLoader: null
};

window.fatzebraGateway.attachEvents();

/**
 * @file
 * A JavaScript file for the theme.
 *
 * In order for this JavaScript to be loaded on pages, see the instructions in
 * the README.txt next to this file.
 */

// JavaScript should be made compatible with libraries other than jQuery by
// wrapping it with an "anonymous closure". See:
// - http://drupal.org/node/1446420
// - http://www.adequatelygood.com/2010/3/JavaScript-Module-Pattern-In-Depth
(function ($, Drupal, window, document, undefined) {

    Drupal.behaviors.quick_checkout = {
        attach: function (context, settings) {

            $.unblockUI();

            var $quick_checkout_credit_card = $('#edit-panes-quick-checkout-saved-credit-card', context);
            var $quick_checkout_billing_address_select = $('#edit-panes-quick-checkout-saved-billing-info', context);
            var $copy_address_input = $('#edit-panes-billing-copy-address', context);

            if($(context).attr('id') == 'billing-address-pane' && typeof Cookies.get('uc_checkout_billing_id') !== 'undefined'){
                $('#edit-panes-billing-select-address').val(Cookies.get('uc_checkout_billing_id')).trigger('change');
                Cookies.remove('uc_checkout_billing_id');
            }

            $('#edit-panes-quick-checkout-saved-shipping-info', context).on('change', function () {
                Drupal.yogatuneup.block_ui();
                $('#edit-panes-delivery-select-address').val($(this).val()).trigger('change');
            });

            $quick_checkout_billing_address_select.on('change', function () {
                Drupal.yogatuneup.block_ui();
                Cookies.set('uc_checkout_billing_id', $(this).val());

                if($(this).val() === ''){
                    $copy_address_input.prop('checked', 'checked').trigger('change');
                }
                else{
                    if($copy_address_input.filter(':checked').length > 0){
                        $copy_address_input.prop('checked', false).trigger('change');
                    }
                    else{
                        $('#edit-panes-billing-select-address').val($(this).val()).trigger('change');
                    }
                }
            });

            $copy_address_input.bind('change', function(){
                if($(this).filter(':checked').length > 0){
                    $quick_checkout_billing_address_select.val('');
                }
            });

            select_credit_card_radio($quick_checkout_credit_card.val());
            $quick_checkout_credit_card.on('change', function () {
                select_credit_card_radio($(this).val());
            });

            $(".form-item-panes-payment-details-cc-select select", context).on('change', function () {
                $('#edit-panes-quick-checkout-saved-credit-card').val($(this).val());
            });

            function select_credit_card_radio(pid){
                $(".form-item-panes-payment-details-cc-select select").val(pid).trigger('change');
            }
        }

    };


})(jQuery, Drupal, this, this.document);

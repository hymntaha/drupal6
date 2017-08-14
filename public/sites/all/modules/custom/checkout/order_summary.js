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

    Drupal.behaviors.order_summary = {
        attach: function (context, settings) {

            var $order_summary_pane = $('#order_summary-pane', context);
            var $coupon_pane = $('#coupon-pane', context);

            $('.order-summary-remove', context).bind('click', function (e) {
                e.preventDefault();
                var code = $(this).data('coupon');
                $(this).hide();
                $('.ajax-progress', $(this).parent()).css('display', 'inline-block');
                $('div[id^="edit-panes-order-summary-coupons"] input[value="' + code + '"]').attr('checked', 'checked').trigger('change');
            });

            $('.coupon-code-link', context).on('click', function (e) {
                e.preventDefault();
                $coupon_pane.removeClass('hidden');
                Cookies.set('uc_coupon_visible', true);
                window.location.hash = '#coupon-pane';
                
            });

            $('.coupon-cancel', context).on('click', function (e) {
                e.preventDefault();
                $coupon_pane.addClass('hidden');
                $('.form-item-panes-coupon-code input[type="text"]').val('');
                $('#coupon-messages').children().remove();
                Cookies.remove('uc_coupon_visible');
                window.location.hash = '';
            });

            $('#edit-panes-delivery-create-form', context).on('change', function (e) {
                $('#customer-pane').slideToggle();
                $('#edit-panes-customer-first-name').val($('#edit-panes-delivery-address-delivery-first-name').val());
                $('#edit-panes-customer-last-name').val($('#edit-panes-delivery-address-delivery-last-name').val());
            });

            $('#edit-panes-payment-details-cc-save', context).on('click', function () {
                if (!this.checked) {
                    return;
                }

                if($('.form-item-panes-customer-password-pass2 input').val() == '') {
                    if ($('#customer-pane:visible').length > 0) {
                        $('#edit-panes-customer-first-name').val($('#edit-panes-delivery-address-delivery-first-name').val());
                        $('#edit-panes-customer-last-name').val($('#edit-panes-delivery-address-delivery-last-name').val());
                    }
                    else {
                        $('#edit-panes-delivery-create-form').trigger('click');
                    }

                    window.location.hash = '#customer-pane';
                }
            });

            // Edit email address in place
            $('.email-address-editable + a', context).on('click', function (e) {
                e.preventDefault();
                $('.account-email').toggle();
                if ($(this).text() == '(edit)') {
                    $(this).text('(cancel)');
                }
                else {
                    $(this).text('(edit)')
                    $('.form-item-panes-customer-primary-email-confirm input,.form-item-panes-customer-primary-email input').val($('.email-address-editable').text())
                }
            });

            $('.trigger-checkout-submit', context).bind('click', function(e){
                e.preventDefault();
                $('#edit-continue').trigger('click');
            });

            set_order_summary_pane_width();
            $(window).resize(function(){
               set_order_summary_pane_width();
            });

            $order_summary_pane.affix({
                offset: {
                    top: $('.main-container').offset().top + $('#admin-menu').outerHeight(),
                    bottom: $('footer').outerHeight() + $('.checkout-buttons-box', $order_summary_pane).height() + 120,
                }
            });

            function set_order_summary_pane_width(){
                $order_summary_pane.css('position', 'static');
                $order_summary_pane.css('width', 'auto');
                $order_summary_pane.css('width', $order_summary_pane.width());
                $order_summary_pane.css('position', '');
                $('.checkout-buttons-box', $order_summary_pane).css('width', $order_summary_pane.width());
            }
        }

    };


})(jQuery, Drupal, this, this.document);

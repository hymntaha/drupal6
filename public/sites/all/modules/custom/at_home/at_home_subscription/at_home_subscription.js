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

    Drupal.behaviors.at_home_subscription = {
        attach: function (context, settings) {

            $('.info-wrapper .tabs li', context).bind('click', function () {
                $('.info-wrapper .tabs li').removeClass('active');
                $(this).addClass('active');
            });

            $('#edit-shipping-options input[type="radio"]', context).bind('change', function () {
                if ($(this).val() != 'hold') {
                    change_next_shipment($(this).val());
                }
                else {
                    if ($('#edit-hold-options').find(':selected').val() !== '') {
                        change_next_shipment($('#edit-hold-options').find(':selected').val());
                    }
                }
            });

            $('#edit-hold-options', context).bind('change', function () {
                value = $(this).find(':selected').val();
                if (value !== '') {
                    change_next_shipment(value);
                }
            });

            $('.shipping-next .row .skip', context).bind('click', function () {
                if ($('.shipping-next .row').length == 1) {
                    alert('There must be at least one item shipping next.');
                    return false;
                }
                var key = $(this).parents('.row').attr('data-product-key');
                $('#at-home-subscription-queue-form input[type="checkbox"]:checked').removeAttr('checked');
                $('#edit-' + key).attr('checked', 'checked');
                $('#edit-skip-unskip').trigger('click');
            });

            $('.shipping-next .row .remove', context).bind('click', function () {
                if ($('.shipping-next .row').length == 1) {
                    alert('There must be at least one item shipping next.');
                    return false;
                }
                var key = $(this).parents('.row').attr('data-product-key');
                $('#at-home-subscription-queue-form input[type="checkbox"]:checked').removeAttr('checked');
                $('#edit-' + key).attr('checked', 'checked');
                $('#edit-remove').trigger('click');
            });

            $('#edit-add-to-next, #edit-skip-unskip, #edit-remove, #edit-shipped', context).bind('click', function () {
                $.blockUI({
                    timeout: 0
                });
            });

            $('#edit-trigger-ship-now', context).bind('click', function (e) {
                e.preventDefault();
                $('#edit-submit-ship-now').click();
            });

            $('#edit-trigger-submit', context).bind('click', function (e) {
                e.preventDefault();
                $('#edit-submit').click();
            });

            $('#edit-submit, #edit-submit-ship-now', context).bind('click', function () {
                $.blockUI({
                    timeout: 0
                });
            });

            function change_next_shipment(value) {
                $('#next-shipment-date').text(settings.at_home_subscription.dates[value]);
                $('#shipping-option').text(settings.at_home_subscription.shipping_options[value]);
                $('#at-home-subscription-queue-form input[name="shipping_option"]').val(value);
            }

        }
    };


})(jQuery, Drupal, this, this.document);

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

    Drupal.behaviors.mobile_ubercart = {
        attach: function (context, settings) {
            $('.mobile-cart-remove', context).bind('click', function (e) {
                e.preventDefault();
                $('input[type="submit"]', $(this).parent()).click();
            });

            $('#mobile-cart-form-pane input.error').each(function(){
                $('#cart-form-pane input[name="'+$(this).attr('name')+'"]').addClass('error');
            });
        }
    };


})(jQuery, Drupal, this, this.document);

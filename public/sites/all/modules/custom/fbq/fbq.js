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

    Drupal.behaviors.fbq = {
        attach: function (context, settings) {
            $(document).bind('ubercart:addToCart', function(e, data){
                var $elem = $('#'+URI("?"+data).query(true).form_id.replace(/_/g, '-'));
                fbq('track', 'AddToCart', {
                    content_name: $elem.data('title'),
                    content_ids: [$elem.data('model')],
                    content_type: $elem.data('type'),
                    value: $elem.data['price'],
                    currency: 'USD'
                });
            });
        }
    };


})(jQuery, Drupal, this, this.document);

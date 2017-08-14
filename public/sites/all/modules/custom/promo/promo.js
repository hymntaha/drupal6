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

  Drupal.behaviors.promo = {
    attach: function (context, settings) {
      $('body',context).prepend(settings.promo.content);
      var left = ($(window).width() - $('#promo-popup').width()) / 2;
      $('#promo-popup').css('left',left);
      $('#promo-popup').slideDown();
      $('#promo-close').click(function(){
      	$('#promo-popup').slideUp();
      });
    }
  };


})(jQuery, Drupal, this, this.document);

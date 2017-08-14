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

  Drupal.behaviors.product = {
    attach: function (context, settings) {
      $('.product-type-selector input').change(function(){
        $('.add-to-cart-wrapper[data-nid="'+$(this).attr('data-nid')+'"]').removeClass('active');
        $('.add-to-cart-wrapper[data-nid="'+$(this).attr('data-nid')+'"][data-option="'+$(this).val()+'"]').addClass('active');
        var $product = $(this).parents('.node-product.node-teaser');
        if($product.length > 0){
        	var $href = URI($('header a',$product).attr('href')).removeSearch('type').addSearch('type',$(this).val());
        	$('header a, .field-name-uc-product-image a',$product).attr('href',$href);
        }
      });
    }
  };


})(jQuery, Drupal, this, this.document);

/**
 * Created by antonina on 12/15/15.
 */
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

    Drupal.behaviors.product_landing = {
        attach: function (context, settings) {

            $(window).on('load', function() {
                //Make all grid boxes even height
                function info_box() {
                    var elementHeights = $('.info-box > h2').map(function () {
                        return $(this).height();
                    }).get();

                    var maxHeight = Math.max.apply(null, elementHeights);
                    $info = $('.info-box .sell-price .uc-price').height();
                    $mheight = maxHeight + $info;

                    $('.info-box').height($mheight);
                }

                //Make all grid boxes even height
                function product_box() {
                    var elementHeights = $('.node-product.node-teaser.grid-view .image, .node-gift-card.node-teaser.grid-view .image').map(function () {
                        return $(this).height();
                    }).get();

                    var maxHeight = Math.max.apply(null, elementHeights);

                    $('.node-product.node-teaser.grid-view .image, .node-gift-card.node-teaser.grid-view .image').height(maxHeight);

                    image_align();
                }

                // Align product image vertically
                function image_align() {
                    /*
                    $img = $('.node-product.node-teaser .field-name-uc-product-image .field-item > a img');
                    $img.each(function () {
                        $h = $(this).parent().height();
                        $hi = $(this).height();
                        $hn = ($h - $hi) / 2;
                        $(this).css({
                            'margin-top': $hn
                        });
                    });
                    */
                }

                // Grid/List Switch
                function grid_view() {
                    $('.node-product.node-teaser.list-view').addClass('hidden');
                    $('.node-gift-card.node-teaser.list-view').addClass('hidden');
                    $('.node-product.node-teaser.grid-view').fadeIn('slow').removeClass('hidden');
                    $('.node-gift-card.node-teaser.grid-view').fadeIn('slow').removeClass('hidden');

                    active_switch('fa-th-list', 'fa-th');
                }

                function list_view() {
                    $('.node-product.node-teaser.grid-view').addClass('hidden');
                    $('.node-gift-card.node-teaser.grid-view').addClass('hidden');
                    $('.node-product.node-teaser.list-view').fadeIn('slow').removeClass('hidden');
                    $('.node-gift-card.node-teaser.list-view').fadeIn('slow').removeClass('hidden');

                    active_switch('fa-th', 'fa-th-list');
                    image_align();
                }

                function active_switch($remove, $add) {
                    $('.grid-switch .' + $remove).removeClass('active');
                    $('.grid-switch .' + $add).addClass('active');
                }


                if ($('.node-product-landing').hasClass('grid-view')) {
                    grid_view();
                    product_box();
                    info_box();
                }

                if ($('.node-product-landing').hasClass('list-view')) {
                    list_view();
                }

                $('.grid-switch .fa-th-list').click(function () {
                    $('.node-product-landing').removeClass('grid-view').addClass('list-view');
                    list_view();
                    Cookies.set('layout_mode', 'list-view');
                });

                $('.grid-switch .fa-th').click(function () {
                    $('.node-product-landing').removeClass('list-view').addClass('grid-view');
                    grid_view();
                    product_box();
                    info_box();
                    Cookies.set('layout_mode', 'grid-view');
                });

                // Mobile Default to Grid View
                if ($('html').hasClass('touch') && $(window).width() < 768) {
                    grid_view();
                    $('.info-box .uc-price').css({
                        position: 'relative',
                        bottom: 0
                    });
                }

                // Double click overlay on touch devices in grid view
                if ($('html').hasClass('touch') && $(window).width() < 992) {
                    $('.node-product.node-teaser.grid-view').click(function (e) {
                        e.preventDefault();
                        $(this).find('.overlay').css('visibility', 'visible');
                        $(this).find('.prod-link').click(function () {
                            window.location = $(this).attr('href');
                        });
                    });
                }


                // Add to cart btn
                $('.node-teaser.list-view .ajax-cart-submit-form-button').attr('value', 'Add to Cart');
            });



        }
    };


})(jQuery, Drupal, this, this.document);

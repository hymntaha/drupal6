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

    Drupal.behaviors.yogatuneup = {
        attach: function (context, settings) {

            var uri = new URI();

            // Fix Modal Box vertical alignment
            $('.ctools-modal-content.modal-forms-modal-content').css({position: 'fixed'});
            $('.ctools-modal-content.modal-forms-modal-content').offset(
                {
                    top: Math.floor((jQuery(window).height() - $('.ctools-modal-content.modal-forms-modal-content').outerHeight()) / 2)
                })

            if ($('#admin-menu').length) {
                $('html').css('margin-top', 30);
            }

            if ($('.youtube-colorbox', context).length) {
                $('.youtube-colorbox', context).colorbox({
                    'iframe': true,
                    'width': 640,
                    'height': 480,
                    // Always center in current window
                    'fixed': true,
                    // Prevent scrollbars from appearing
                    'scrolling': false,
                });
            }

            // Make Shop a link
            if (!$('html').hasClass('touch')) {
                $('#block-system-main-menu ul.menu.nav li a').click(function () {
                    window.location.href = $(this).attr('href');
                });
            }

            // Accordion Toggle
            $('.vocabulary-ytu-cert-levels', context).each(function () {
                var $id = '';
                for (var i = 0; i < $('.vocabulary-ytu-cert-levels').length; i++) {
                    $id = '#content-' + i;
                    $(this).children('.category-name').attr('data-toggle', 'collapse').attr('data-target', $id);
                    $(this).children('.content').attr('id', $id).addClass('collapse').addClass('in');
                    var $thisContent = $(this).children('.content');
                    $thisContent.collapse('hide');

                    $(this).children('h2').click(function (e) {
                        e.preventDefault();
                        $($thisContent).collapse('toggle');
                        $(this).toggleClass('tab-open');
                    });
                }
            });

            $('#teacher-search-form #edit-teacher', context).bind('change', function () {
                $('#teacher-search-form > div').append('<div class="ajax-progress"><div class="throbber">&nbsp;</div></div>');
                $.blockUI({
                    timeout: 0
                });
                $('#teacher-search-form').submit();
            });

            $('#teacher-search-zip-radius-form', context).bind('submit', function () {
                $('input[type="submit"]', this).attr('disabled', 'disabled');
                $.blockUI({
                    message: '<h2>Searching for teachers. Please wait...</h2>',
                    timeout: 0
                });
                $('#teacher-search-zip-radius-form > div').append('<div class="ajax-progress"><div class="throbber">&nbsp;</div></div>');
            });

            $('.vocabulary-ytu-cert-levels h2 span', context).bind('click', function () {
                $parent = $(this).parents('.vocabulary-ytu-cert-levels');
                if (($parent).hasClass('open')) {
                    $('.vocabulary-ytu-cert-levels').removeClass('open');
                }
                else {
                    $('.vocabulary-ytu-cert-levels').removeClass('open');
                    $parent.addClass('open');
                }
            });


            $('.category-name span', context).bind('click', function () {
                $parent = $(this).parents('.faq-category');
                if (($parent).hasClass('open')) {
                    $('.faq-category').removeClass('open');
                }
                else {
                    $('.faq-category').removeClass('open');
                    $parent.addClass('open');
                }
            });

            if ($('.field-name-field-videos header', context).length > 1) {
                var max_width = 0;
                $('.field-name-field-videos header', context).each(function () {
                    if ($(this).width() > max_width) {
                        max_width = $(this).width();
                    }
                });
                $('.field-name-field-videos header', context).width(max_width);
            }

            $('#group-coaching-call-archives-form #edit-archives', context).bind('change', function () {
                $parent = $(this).parents('#group-coaching-call-archives-form');
                if ($(this).val() !== '') {
                    $parent.submit();
                }
            });

            $('.ytu-tab-content iframe', context).each(function () {
                var src = $(this).attr('src');
                src = URI(src).query({
                    wmode: 'transparent'
                });
                $(this).attr('src', src);
                $(this).attr('data-src', src);
            });

            $('#ytu-tabs li', context).each(function () {
                $(this).bind('click', function () {
                    $('.ytu-tab-content iframe').each(function () {
                        $(this).attr('src', '');
                        $(this).attr('src', $(this).attr('data-src'));
                    });
                    $('#ytu-tabs li, #ytu-tabs li a, .ytu-tab-content').removeClass('active');
                    $(this).addClass('active');
                    $('a', this).addClass('active');
                    $('#tab-content-' + $(this).attr('id')).addClass('active');
                });
            });

            $('#ytu-tabs li a', context).bind('click', function (e) {
                e.preventDefault();
            });

            if ($('#ytu-tabs').length && window.location.hash) {
                $(window.location.hash).trigger('click');
            }

            // Tabs on Dashboard Home Program
            $('.program-manager ul.tabs li .tab-content').hide();
            $('.program-manager ul.tabs li.active .tab-content').show();
            $('.program-manager ul.tabs li').click(function () {
                $('.program-manager ul.tabs li .tab-content').hide();
                $('.program-manager ul.tabs li.active .tab-content').show();
            });


            $('.user-dashboard-row .node-renewal .right-content .display-price').detach().appendTo('.user-dashboard-row .node-renewal .right-content .add-to-cart-wrapper');

            $('.page-cart-checkout #edit-continue').bind('click', function(e){
               Drupal.yogatuneup.block_ui();
            });

            if($('#block-upsell-product-upsell').length > 0){
                if(Cookies.get('sawUpsellModal') == null){
                    Cookies.set('sawUpsellModal', true);
                    $('#block-upsell-product-upsell').modal();
                }
            }

            if($('body').hasClass('page-faqs')){
                var nid = uri.fragment();
                if(nid){
                    var $faq_node = $('article.node-'+nid);
                    var $faq_panel = $('.panel-heading a', $faq_node.parents('.faq-category'));
                    $faq_panel.click();
                    $('html, body').animate({
                        scrollTop: $faq_panel.offset().top + 'px'
                    }, 'fast');
                }
            }

        }

    };

    Drupal.behaviors.homepage = {
        attach: function (context, settings) {

            function sizeFooterAds() {
                $('.footer-ad-links .row .col-xs-3').each(function (n, i) {
                    $(i).css('padding-top', ($('.footer-ad-links').height() - $(i).height()) / 2)
                    $(i).css('padding-bottom', ($('.footer-ad-links').height() - $(i).height()) / 2)
                })

            }

            $(window).on('load resize', function () {
                sizeFooterAds();
            });

            $('.blog-description').dotdotdot();

            $('.meet-all-teachers').removeClass('active');
        }

    };

    Drupal.yogatuneup = {
        block_ui: function(message){
            if(typeof message === 'undefined'){
                message = '<p class="lead"><strong>Please wait...</strong></p>';
            }
            $.blockUI({
                message: message,
                timeout: 0
            });
        }
    }

})(jQuery, Drupal, this, this.document);

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

            if ($('#admin-menu').length) {
                $('html').css('margin-top', 30);
            }

            if ($('.youtube-colorbox', context).length) {
                $('.youtube-colorbox', context).colorbox({
                    'iframe': true,
                    'width': 640,
                    'height': 480
                });
            }

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


        }
    };

    Drupal.behaviors.homepage = {
        attach: function (context, settings) {
            if (settings.hasOwnProperty('homepage') && settings.homepage.body_bg !== "") {
                $('.field-name-body').css('background-image', 'url("' + settings.homepage.body_bg + '")');
            }

            $('.blog-item h3').dotdotdot();

        }
    };


})(jQuery, Drupal, this, this.document);

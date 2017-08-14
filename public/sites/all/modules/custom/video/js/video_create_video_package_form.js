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

    Drupal.behaviors.video_create_video_package_form = {
        attach: function (context, settings) {
            var $add_all_to_package = $('#add-all-to-package', context);
            var $add_to_package = $('.add-to-package', context);
            var $all_added = $('#all-added', context);

            $add_to_package.click(function(e){
                e.preventDefault();

                var nid = $(this).parents('.node-video-segment-product').attr('id').substr(5);

                $('input[name="videos['+nid+']"]').prop('checked', true).trigger('change');

                $(this).prop('disabled', true).addClass('disabled').html('Added');

                $('#create-package-'+nid).removeClass('hidden');

                $('#no-videos').addClass('hidden');

                $('.package-cost, .discount-info').removeClass('hidden');

                update_count();
            });

            $('.create-package-remove', context).click(function(e){
                e.preventDefault();

                var nid = $(this).parents('tr').attr('id').substr(15);

                $('input[name="videos['+nid+']"]').prop('checked', false).trigger('change');

                $('#node-'+nid+' .add-to-package').prop('disabled', false).removeClass('disabled').html('Add To Package');

                $('#create-package-'+nid).addClass('hidden');

                if($('#create-video-package-table tr').length == $('#create-video-package-table tr.hidden').length){
                    $('#no-videos').removeClass('hidden');
                    $('.package-cost, .discount-info').addClass('hidden');
                }

                update_count();
            });

            $('#clear-videos', context).click(function(e){
               $('.create-package-remove').click();
            });

            $add_all_to_package.click(function(e){
                e.preventDefault();

                $add_to_package.not('.disabled').filter(':visible').click();
                $('#back-top a').click();
            });

            $('#video-segment-filter-form', context).on('video_filter', function(){
               update_count();
               update_extra_info();
            });

            $('#create-packages-extra-info .close', context).click(function(){
                $(this).parent().addClass('hidden');
            });

            function update_count(){
                var $selected_videos = $('#create-video-package-table tr').not('.hidden');
                var count = $add_to_package.filter(':visible').not('.disabled').length;
                var added_videos_count = $selected_videos.length;
                var package_total = 0;
                var discounted_package_total = 0;
                var current_discount_level = get_current_discount_level($selected_videos.length);
                var next_discount_level = get_next_discount_level($selected_videos.length);

                if(count == 0){
                    $all_added.removeClass('hidden');
                    $add_all_to_package.addClass('hidden');
                }
                else{
                    $all_added.addClass('hidden');
                    $add_all_to_package.removeClass('hidden');
                }

                if(added_videos_count == 1){
                    $('#num-selected-videos').html(added_videos_count + ' video');
                }
                else{
                    $('#num-selected-videos').html(added_videos_count + ' videos');
                }

                $selected_videos.each(function(){
                    package_total += parseFloat($('.uc-price', this).attr('data-price'));
                });

                if(current_discount_level != []){
                    discounted_package_total = package_total - (package_total * parseInt(current_discount_level.amount,10)/100);
                }

                if(discounted_package_total > 0){
                    $('#package-cost-price').html(Math.round10(discounted_package_total,-2) + ' <span class="strikethrough">$'+Math.round10(package_total)+'</span>');
                }
                else{
                    $('#package-cost-price').html(Math.round10(package_total,-2));
                }

                if(next_discount_level == []){
                    $('.discount-info').addClass('hidden');
                }
                else{
                    $('#num-to-next-discount').html(parseInt(next_discount_level.breakpoint,10) - $selected_videos.length);
                    $('#discount-amount').html(next_discount_level.amount);
                }
            }

            function update_extra_info(){
                var $extra_info = $('#create-packages-extra-info');
                var $video_segment_filter_form_select = $('#video-segment-filter-form select');
                var tid = '';
                var one_selected = true;
                var none_selected = true;
                var description = '';

                $video_segment_filter_form_select.each(function(){
                    var selected = $(this).val() !== '';
                    if(selected){
                        none_selected = false;
                        if(tid !== ''){
                            one_selected = false;
                        }
                        tid = $(this).val();
                    }
                });

                if(tid === ''){
                    one_selected = false;
                }

                if(one_selected){
                    $.ajax('/ajax/video/term-description/'+tid, {
                        success: function(data){
                            description = data.description;
                            if(description === ''){
                                $extra_info.addClass('hidden');
                            }
                            else{
                                $('.content', $extra_info).html(description);
                                $extra_info.removeClass('hidden');
                            }
                        }
                    });
                }
                else if(none_selected){
                    description = Drupal.settings.video.no_selections_extra_info;
                    if(description === ''){
                        $extra_info.addClass('hidden');
                    }
                    else{
                        $('.content', $extra_info).html(description);
                        $extra_info.removeClass('hidden');
                    }
                }
                else{
                    $extra_info.addClass('hidden');
                }
            }

            function get_current_discount_level(num_selected){
                var discount = [];
                var max_discount_level = 0;

                Drupal.settings.video.video_discount.forEach(function(currentValue, index, array){
                    var breakpoint = parseInt(currentValue.breakpoint,10);
                    if(num_selected >= breakpoint){
                        if(breakpoint > max_discount_level){
                            discount = currentValue;
                            max_discount_level = breakpoint;
                        }
                    }
                });

                return discount;

            }

            function get_next_discount_level(num_selected){
                var discount = [];
                var min_discount_level = 0;

                Drupal.settings.video.video_discount.forEach(function(currentValue, index, array){
                    var breakpoint = parseInt(currentValue.breakpoint,10);
                    if(num_selected < breakpoint){
                        if(min_discount_level == 0 || min_discount_level > breakpoint){
                            discount = currentValue;
                            min_discount_level = breakpoint;
                        }
                    }
                });

                return discount;
            }

            function decimalAdjust(type, value, exp) {
                // If the exp is undefined or zero...
                if (typeof exp === 'undefined' || +exp === 0) {
                    return Math[type](value);
                }
                value = +value;
                exp = +exp;
                // If the value is not a number or the exp is not an integer...
                if (isNaN(value) || !(typeof exp === 'number' && exp % 1 === 0)) {
                    return NaN;
                }
                // Shift
                value = value.toString().split('e');
                value = Math[type](+(value[0] + 'e' + (value[1] ? (+value[1] - exp) : -exp)));
                // Shift back
                value = value.toString().split('e');
                return +(value[0] + 'e' + (value[1] ? (+value[1] + exp) : exp));
            }

            // Decimal round
            if (!Math.round10) {
                Math.round10 = function(value, exp) {
                    return decimalAdjust('round', value, exp);
                };
            }
        }
    }

})(jQuery, Drupal, this, this.document);
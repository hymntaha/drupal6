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

    Drupal.behaviors.video_segment_filter = {
        attach: function (context, settings) {
            var $video_segment_filter_form_select = $('#video-segment-filter-form select', context);
            var $videos = $('.node-video, .node-video-segment-product', context);
            var $favorite_checkbox = $('input[name="favorite"]');
            var $reset_filters = $('#playlist-reset-filters');
            var uri = new URI(), query = uri.query(true);

            $video_segment_filter_form_select.change(function () {
                filter_videos();
            });

            set_default();
            $('.video-count').html($videos.filter(':visible').length);

            if($favorite_checkbox){
                $favorite_checkbox.change(function(){
                    filter_videos();
                });
            }

            if($reset_filters){
                $reset_filters.click(function(e){
                   e.preventDefault();
                   $video_segment_filter_form_select.val('');

                   filter_videos();
                });
            }

            $(window).bind('pageshow', function(){
                set_default();
                filter_videos();
            });

            function set_default(){
                var $option = null;
                var $select = null;
                var category_map = [
                    {
                        'query': 'category',
                        'name': 'video_categories'
                    },
                    {
                        'query': 'body_focus',
                        'name': 'video_body_focus'
                    },
                    {
                        'query': 'activity',
                        'name': 'video_activity'
                    },
                    {
                        'query': 'series',
                        'name': 'video_series'
                    }
                ];

                $video_segment_filter_form_select.val('');

                category_map.forEach(function(currentValue, index, array){
                    if($select == null && query.hasOwnProperty(currentValue.query)){
                        $('select[name="'+currentValue.name+'"] option').each(function(){
                            if($(this).html().toLowerCase() == query[currentValue.query]){
                                $option = $(this);
                                $select = $(this).parents('select');
                            }
                        });
                    }
                });

                if($select && $option){
                    $select.val($option.val()).trigger('change');
                }

                $('#create-packages-videos').removeClass('hidden');
            }

            function filter_videos() {
                $videos.removeClass('hidden');

                $videos.each(function () {
                    var nid = $(this).attr('id').substr(5);
                    var show = true;

                    $video_segment_filter_form_select.each(function () {
                        if ($(this).val()) {
                            show = show && settings.video.video_map[$(this).attr('name')][$(this).val()].includes(nid);
                        }
                    });

                    if($favorite_checkbox.length > 0){
                        if($favorite_checkbox.prop('checked')){
                            show = show && $('#node-'+nid+' .toggle-favorite').attr('data-is-favorite') == 1;
                        }
                    }

                    if(show === false){
                        $(this).addClass('hidden');
                    }

                });

                $('.video-count').html($videos.filter(':visible').length);

                $('#video-segment-filter-form').trigger('video_filter');
            }
        }
    }

})(jQuery, Drupal, this, this.document);
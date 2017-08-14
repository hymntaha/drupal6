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

    Drupal.behaviors.playlist_add_edit_form = {
        attach: function (context, settings) {

            $('#edit-videos input:checked', context).each(function(){
               var nid = $(this).val();
                $('#node-'+nid+' .add-to-playlist').prop('disabled', true).addClass('disabled').html('Added');
            });

            $('.add-to-playlist', context).click(function(e){
                e.preventDefault();

                var nid = $(this).parents('.node-video').attr('id').substr(5);

                $('input[name="videos['+nid+']"]').prop('checked', true).trigger('change');

                $(this).prop('disabled', true).addClass('disabled').html('Added');

                $('#playlist-sort-'+nid).removeClass('hidden');

                $('#no-videos').addClass('hidden');
            });

            $('.playlist-remove', context).click(function(e){
                e.preventDefault();

                var nid = $(this).parents('tr').attr('id').substr(14);

                $('input[name="videos['+nid+']"]').prop('checked', false).trigger('change');

                $('#node-'+nid+' .add-to-playlist').prop('disabled', false).removeClass('disabled').html('Add To Playlist');

                $('#playlist-sort-'+nid).addClass('hidden');

                if($('#playlist-sort tr').length == $('#playlist-sort tr.hidden').length){
                    $('#no-videos').removeClass('hidden');
                }
            });
        }
    }

})(jQuery, Drupal, this, this.document);
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

    Drupal.behaviors.video_favorite = {
        attach: function (context, settings) {
            $('.toggle-favorite').click(function(e){
                e.preventDefault();

                var nid = $(this).parents('.node-video').attr('id').substr(5);
                var $self = $(this);

                $.post('/ajax/video/favorite/'+Drupal.settings.video_favorite.uid+'/'+nid, function(data){
                    $self.attr('data-is-favorite', data.favorite);
                });
            })
        }
    }

})(jQuery, Drupal, this, this.document);
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

    Drupal.behaviors.playlist = {
        attach: function (context, settings) {
            var mediaplayer_key = 'mediaplayer-'+Drupal.settings.playlist.playlist_id;
            var jwplayer_container = jwplayer(mediaplayer_key);

            jwplayer_container.setup({
                playlist: Drupal.settings.playlist.playlist_json,
                width: "100%",
                aspectratio: "16:9",
                ga: {}
            });

            $('.playlist-video',context).bind('click',function(e){
                e.preventDefault();

                if(e.target.nodeName != 'I'){
                    var index = parseInt($(this).data('playlist-id'),10);
                    jwplayer_container.playlistItem(index);
                }
            });

            jwplayer_container.on('playlistItem', function(e){
                $('.playlist-video').removeClass('active');
                $('.playlist-video[data-playlist-id="'+ e.index +'"]').addClass('active');
            })
        }
    }

})(jQuery, Drupal, this, this.document);
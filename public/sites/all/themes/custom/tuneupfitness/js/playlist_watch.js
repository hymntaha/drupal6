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

    Drupal.behaviors.playlist_watch = {
        attach: function (context, settings) {;
            var jwplayer_container = jwplayer($('.media-player').attr('id'));
            var $player = $('.video-playlist-watch .player', context);
            var $playlistVideos = $('.video-playlist-watch .playlist-videos-adjust', context);
            var $playlistTitle = $('#in-this-playlist-title');
            var resizeTimer;

            $(window).resize(function(e){
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(playlist_height_adjust, 250);
            });

            jwplayer_container.on('ready', function(e){
                playlist_height_adjust();
            });

            $('#playlist-videos-collapse .playlist-video').bind('click', function(e){
                $('html, body').animate({
                    scrollTop: 0
                }, 'fast');
            });

            function playlist_height_adjust(){
                $playlistVideos.height($player.height() - $playlistTitle.outerHeight());
                $playlistVideos.show();
            }
        }
    };


})(jQuery, Drupal, this, this.document);

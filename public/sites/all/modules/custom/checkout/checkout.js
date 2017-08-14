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

    Drupal.behaviors.checkout = {
        attach: function (context, settings) {
            var $cc_select = $(".form-item-panes-payment-details-cc-select select", context);

            set_cc_update_link($cc_select.val());

            $cc_select.on('change', function () {
                var pid = $(this).val();
                set_cc_update_link(pid)
            });

            function set_cc_update_link(pid){
                var $cc_update_link = $('#cc-update-link');

                $cc_update_link.removeClass('alert-danger');
                $('#card-expired-message').hide();

                if(pid == 'new' || pid == ''){
                    $cc_update_link.hide();
                }
                else{
                    $cc_update_link.attr('href', '/user/' + $cc_update_link.data('uid') + '/billing/' + pid + '/edit?destination=cart');
                    $cc_update_link.show();
                    if($('div[data-pid="'+pid+'"]').attr('data-expired') == '1'){
                        $cc_update_link.removeClass('alert');

                        var modal = '<div id="cc-exp-modal" class="modal fade"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="modal-title">Credit Card Expired</h4></div><div class="modal-body">'+$cc_update_link[0].outerHTML+'</div></div></div></div>';

                        $('body').prepend(modal);
                        var $modal = $('#cc-exp-modal');

                        $modal.modal();

                        $modal.on('hidden.bs.modal', function(){
                           $modal.remove();
                        });

                        $cc_update_link.addClass('alert');
                        $cc_update_link.addClass('alert-danger');
                        $('#card-expired-message').show();
                    }
                }
            }
        }

    };


})(jQuery, Drupal, this, this.document);

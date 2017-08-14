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

  Drupal.behaviors.at_home = {
    attach: function (context, settings) {
        $('#webform-component-do-you-have-any-physical-ailments-or-pains--paop-grid-1 thead tr', context).before('<tr class="select-one-row"><th></th><th colspan="2">Select One</th></tr>');
        $('#webform-component-do-you-have-any-physical-ailments-or-pains--paop-grid-2 thead tr', context).before('<tr class="select-one-row"><th colspan="3">Select One</th></tr>');

      //At Home Program Finder Webform Grid Functionality
      $.each(settings.at_home.paop_options, function(i, val) {
        var markup = '<input type="checkbox" id="checkbox-'+i+'" value="'+i+'" class="grid-row-checkbox" />';
        var $associated_inputs = $('input[name="submitted[do_you_have_any_physical_ailments_or_pains][paop_grid_1]['+i+']"], input[name="submitted[do_you_have_any_physical_ailments_or_pains][paop_grid_2]['+i+']"]');
        $('#webform-component-do-you-have-any-physical-ailments-or-pains--paop-grid-1 .webform-grid-question:contains("'+val+'")').prepend(markup);
        $('#checkbox-'+i).bind('click',function(){
          if(!$(this).is(':checked')){
            $associated_inputs.attr('checked','');
          }
        });
        if($associated_inputs.filter(':checked').length){
          $('#checkbox-'+i).attr('checked','checked');
        }
      });

      $('#webform-component-do-you-have-any-physical-ailments-or-pains input[type="radio"]').bind('click',function(){
        var name = $(this).attr('name');
        name = name.split(/((?!^)\[.*?\])/);
        name = name[5].substr(0,name[5].length-1);
        name = name.substr(1);
        $('#webform-component-do-you-have-any-physical-ailments-or-pains--paop-grid-1 #checkbox-'+name).attr('checked','checked');
      });

    }
  };


})(jQuery, Drupal, this, this.document);

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

  Drupal.behaviors.class = {
    attach: function (context, settings) {

      if(all_checked()){
        $('#check-all-categories').attr('checked',true);
      }

      $('#check-all-categories').live('change',function(){
        if($(this).is(':checked')){
          $('.form-item-field-categories-und input[type="checkbox"]').attr('checked',true);
        }
        else{
          $('.form-item-field-categories-und input[type="checkbox"]').removeAttr('checked');
        }
      });
      $('.form-item-field-categories-und input[type="checkbox"]').live('change',function(){
        if(!$(this).is(':checked')){
          $('#check-all-categories').removeAttr('checked');
        }
        else{
          if(all_checked()){
            $('#check-all-categories').attr('checked',true);
          }
        }
      });

      $('#edit-field-single-date .field-add-more-submit').val('Add another date');

      function all_checked(){
        return $('.form-item-field-categories-und input[type="checkbox"]').length == $('.form-item-field-categories-und input[type="checkbox"]:checked').length;
      }
    }
  };


})(jQuery, Drupal, this, this.document);

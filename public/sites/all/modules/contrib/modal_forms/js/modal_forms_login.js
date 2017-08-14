(function ($) {

Drupal.behaviors.initModalFormsLogin = {
  attach: function (context, settings) {
    $("a[href*='/user/login'], a[href*='?q=user/login']", context).once('init-modal-forms-login', function () {
        if(!$(this).hasClass('ignore-modal')){
            this.href = this.href.replace(/user\/login/,'modal_forms/nojs/login');
            $(this).addClass('ctools-use-modal ctools-modal-modal-popup-small');
        }
    });
  }
};

})(jQuery);

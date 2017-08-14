<?php
/**
 * @file
 * page.vars.php
 */

/**
 * Implements hook_preprocess_page().
 *
 * @see page.tpl.php
 */
function yogatuneup_responsive_preprocess_page(&$variables) {
  $request_path = request_path();

  $variables['title_attributes_array'] = array();
  $variables['title_classes_array']    = array('page-header', 'other-stuff');

  $variables['navbar_classes_array']   = '';
  $variables['navbar_classes_array'][] = 'navbar navbar-default';

  $variables['main_container_classes_array'] = array('main-container', 'container');

  switch ($request_path) {
    case 'user/login':
      $variables['tabs'] = array();
      break;
  }

  if (strpos($request_path, 'watch/') !== FALSE) {
    drupal_add_js(drupal_get_path('theme', 'yogatuneup_responsive') . '/js/playlist_watch.js');
  }

  if (module_exists('adroll')) {
    $order_subtotal = '';
    if (isset($variables['page']['content']['system_main']['#theme']) && $variables['page']['content']['system_main']['#theme'] == 'uc_cart_complete_sale') {
      if (isset($variables['page']['content']['system_main']['#order'])) {
        $order_subtotal = order_get_product_coupon_subtotal($variables['page']['content']['system_main']['#order']);
      }
    }
    $variables['adroll_tracking_code'] = array(
      '#theme'          => 'adroll_tracking_code',
      '#adv_id'         => variable_get('adv_id', ''),
      '#pix_id'         => variable_get('pix_id', ''),
      '#order_subtotal' => $order_subtotal,
    );
  }

  foreach ($variables['title_attributes_array'] as $attrib => $val) {
    $variables['title_attributes'][$attrib] = $val;
  }

  if(isset($variables['node'])){
    if($variables['node']->type == 'teacher'){
      $account = user_load($variables['node']->uid);
      $title_label = '';
      $is_integrated = account_is_integrated_teacher($account);
      $is_trainer = account_is_trainer($account);
      $is_role_model = account_is_role_model_practitioner($account);

      if($is_integrated){
        $title_label = 'Integrated Teacher';
      }

      if($is_trainer){
        $title_label = 'Teacher Trainer';
      }

      if($is_integrated && $is_trainer){
        $title_label = 'Integrated Teacher Trainer';
      }

      if($is_role_model){
        if(!empty($title_label)){
          $title_label .= '<br />';
        }

        $title_label .= 'Roll Model Method Practitioner';
      }

      if(!empty($title_label)){
        $variables['title_suffix'] = '<div class="text-uppercase teacher-label">'.$title_label.'</div>';
      }
    }

    if($variables['node']->type == 'blank_page'){
      $variables['title'] = '';
      foreach ($variables['main_container_classes_array'] as $key => $value){
        if($value == 'container'){
          unset($variables['main_container_classes_array'][$key]);
        }
      }
      $variables['main_container_classes_array'][] = 'container-fluid';
    }
  }

  $variables['title_classes'] = implode(' ', $variables['title_classes_array']);
  $variables['main_container_classes'] = implode(' ', $variables['main_container_classes_array']);
}

<?php

function product_admin_settings_form($form, &$form_state){
  $form['featured_product_page_nid'] = array(
    '#type' => 'textfield',
    '#title' => 'Featured Product Page node id',
    '#description' => 'Proved the node id for the page to pull products from',
    '#default_value' => variable_get('featured_product_page_nid',''),
  );

  return system_settings_form($form);
}
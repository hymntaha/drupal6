<?php

function uc_cart_limit_qty_product_form($form, &$form_state, $product){
  /** @var UcCartLimitQty $quantity_limit */

  $default = '';
  if(isset($product->uc_cart_limit_qty)){
    $quantity_limit = $product->uc_cart_limit_qty;
    $default = $quantity_limit->getQtyLimit();
  }

  $form['qty_limit'] = array(
    '#type' => 'textfield',
    '#title' => t('Quantity Limit'),
    '#default_value' => $default,
  );

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Submit'),
  );

  return $form;
}

function uc_cart_limit_qty_product_form_validate($form, &$form_state){
  if(!empty($form_state['values']['qty_limit']) &&
     (!is_numeric($form_state['values']['qty_limit']) || $form_state['values']['qty_limit'] < 1)){
    form_set_error('qty_limit',t('Invalid quantity limit.'));
  }
}

function uc_cart_limit_qty_product_form_submit($form, &$form_state){
  $product = $form_state['build_info']['args'][0];
  $delete = FALSE;

  /** @var UcCartLimitQty $quantity_limit */
  if(isset($product->uc_cart_limit_qty)){
    $quantity_limit = $product->uc_cart_limit_qty;
    if(empty($form_state['values']['qty_limit'])){
      $delete = TRUE;
    }
  }
  else{
    $quantity_limit = new UcCartLimitQty();
  }

  if($delete){
   $quantity_limit->delete();
    drupal_set_message('Quantity limit removed.');
  }
  else {
    if(!empty($form_state['values']['qty_limit'])) {
      $quantity_limit->setNid($product->nid);
      $quantity_limit->setQtyLimit($form_state['values']['qty_limit']);
      $quantity_limit->save();

      drupal_set_message('Quantity limit saved.');
    }
  }

}
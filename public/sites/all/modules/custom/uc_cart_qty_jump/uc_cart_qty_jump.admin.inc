<?php

function uc_cart_qty_jump_product_form($form, &$form_state, $product){
  $form['qty_jump'] = array(
    '#type' => 'textfield',
    '#title' => t('Quantity Jump'),
    '#default_value' => isset($product->uc_cart_qty_jump) ? $product->uc_cart_qty_jump->getQtyJump(): '',
  );

  $form['qty_jump_start'] = array(
    '#type' => 'textfield',
    '#title' => t('Quantity Jump Start'),
    '#description' => 'Enter the quantity to start jumping at',
    '#default_value' => isset($product->uc_cart_qty_jump) ? $product->uc_cart_qty_jump->getQtyJumpStart(): '',
  );

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Submit'),
  );

  return $form;
}

function uc_cart_qty_jump_product_form_validate($form, &$form_state){
  if((empty($form_state['values']['qty_jump']) && !empty($form_state['values']['qty_jump_start'])) ||
     (!empty($form_state['values']['qty_jump']) && empty($form_state['values']['qty_jump_start']))){
    form_set_error('',t('Must fill both values.'));
  }
  else{
    if(!empty($form_state['values']['qty_jump']) &&
       (!is_numeric($form_state['values']['qty_jump']) || $form_state['values']['qty_jump'] < 1)){
      form_set_error('qty_jump',t('Invalid quantity jump. Must be numeric and greater than 1.'));
    }

    if(!empty($form_state['values']['qty_jump_start']) && (!is_numeric($form_state['values']['qty_jump_start']))){
      form_set_error('qty_jump',t('Invalid quantity jump start. Must be numeric.'));
    }
  }
}

function uc_cart_qty_jump_product_form_submit($form, &$form_state){
  $product = $form_state['build_info']['args'][0];
  $delete = FALSE;

  /** @var UcCartQtyJump() $quantity_jump */
  if(isset($product->uc_cart_qty_jump)){
    $quantity_jump = $product->uc_cart_qty_jump;
    if(empty($form_state['values']['qty_jump'])){
      $delete = TRUE;
    }
  }
  else{
    $quantity_jump = new UcCartQtyJump();
  }

  if($delete){
    $quantity_jump->delete();
    drupal_set_message('Quantity limit removed.');
  }
  else {
    $quantity_jump->setNid($product->nid);
    $quantity_jump->setQtyJump($form_state['values']['qty_jump']);
    $quantity_jump->setQtyJumpStart($form_state['values']['qty_jump_start']);
    $quantity_jump->save();

    drupal_set_message('Quantity jump saved.');
  }

}
<?php

function uc_cart_conditions_product_form($form, &$form_state, $product) {
  $conditions = array();

  if(isset($product->uc_cart_conditions)){
    $cart_conditions = $product->uc_cart_conditions;

    /** @var UcCartConditions $cart_conditions */
    foreach($cart_conditions->getConditions() as $sku => $qty){
      $conditions[] = $sku.':'.$qty;
    }
  }

  $form['conditions'] = array(
    '#type'        => 'textarea',
    '#title'       => t('Cart Conditions'),
    '#description' => t('Enter the conditions for this product to be in the cart in the form of lines matching the format SKU:QTY'),
    '#default_value' => implode("\n",$conditions),
  );

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => 'Submit',
  );

  return $form;
}

function uc_cart_conditions_product_form_validate($form, &$form_state) {
  $items = array();
  $lines = explode("\n", $form_state['values']['conditions']);
  foreach ($lines as $line) {
    if(empty($line)){
      continue;
    }
    $pieces = explode(":", $line);
    if (count($pieces) == 2) {
      $sku = preg_replace('/^[\pZ\pC]+|[\pZ\pC]+$/u', '', $pieces[0]);
      $qty = preg_replace('/^[\pZ\pC]+|[\pZ\pC]+$/u', '', $pieces[1]);

      $sku_exists = db_query("SELECT nid FROM {uc_products} WHERE (model = :sku)", array(':sku' => $sku))->fetchField();
      if(!$sku_exists){
        form_set_error('conditions',t('Invalid SKU: :sku',array(':sku' => $sku)));
      }

      if(!is_numeric($qty) || $qty < 1){
        form_set_error('conditions',t('Invalid quantity in conditions.'));
      }

      $items[$sku] = $qty;
    }
    else {
      form_set_error('conditions', t('Invalid condition format.'));
    }
  }

  if(!form_get_errors()){
    $form_state['values']['conditions_array'] = $items;
  }
}

function uc_cart_conditions_product_form_submit($form, &$form_state) {
  $product = $form_state['build_info']['args'][0];

  if(isset($product->uc_cart_conditions)){
    $cart_conditions = $product->uc_cart_conditions;
  }
  else{
    $cart_conditions = new UcCartConditions();
  }

  $cart_conditions->setNid($product->nid);
  $cart_conditions->setConditions($form_state['values']['conditions_array']);
  $cart_conditions->save();

  drupal_set_message('Cart conditions saved.');
}
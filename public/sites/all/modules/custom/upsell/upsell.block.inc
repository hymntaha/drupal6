<?php

/**
 * Implements hook_block_info().
 */
function upsell_block_info() {
  $blocks['account_upsell'] = array(
    'info' => t('Account Upsell'),
    'cache' => DRUPAL_NO_CACHE
  );

  $blocks['product_upsell'] = array(
    'info' => t('Product Upsell'),
    'cache' => DRUPAL_NO_CACHE,
  );

  return $blocks;
}

/**
 * Implements hook_block_view().
 */
function upsell_block_view($delta = '') {
  $block = array();

  switch ($delta) {
    case 'account_upsell':
      $block['subject'] = '';

  	  $account = menu_get_object('user');
  	  if(!$account){
  	  	global $user;
        $account = user_load($user->uid);
  	  }
      $block['content'] = '';
      if(isset($account) && $account->uid){
        $upsells = upsell_get_account_upsells($account->uid);
        if($upsells){
          $block['content'] = array(
            'upsells' => array('#prefix' => '<p>'._mm('upsell_cart_callout','',TRUE).'</p>', 'upsells' => $upsells),
          );
        }
      }
      break;
    case 'product_upsell':
      $block['subject'] = '';
      $block['content'] = '';
      $upsells = upsell_get_upsells_by_cart();
      if($upsells){
        $block['content'] = array(
          'upsells' => array('upsells' => $upsells),
        );
      }
      break;
  }
  return $block;
}
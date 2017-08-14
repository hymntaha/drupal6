<?php

/**
 * Implements hook_preprocess_block().
 */
function yogatuneup_responsive_preprocess_block(&$variables) {
  //dpm($variables);
  switch ($variables['block_html_id']) {
    case 'block-menu-menu-above-nav':
      $variables['classes_array'][] = 'col-sm-8';
      break;
    case 'block-upsell-product-upsell':
      $variables['classes_array'][] = 'modal';
      $variables['classes_array'][] = 'fade';
      break;
  }
}
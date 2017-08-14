<?php

/**
 * Implements hook_preprocess_block().
 */
function tuneupfitness_preprocess_block(&$variables) {
  $variables['prefix'] = '';
  $variables['suffix'] = '';

  switch ($variables['block_html_id']) {
    case 'block-menu-menu-above-nav':
      $variables['classes_array'][] = 'col-sm-8';
      break;
    case 'block-upsell-product-upsell':
      $variables['classes_array'][] = 'modal';
      $variables['classes_array'][] = 'fade';
      break;
    case 'block-block-3':
      $variables['content'] = str_replace('%year',date('Y'),$variables['content']);
      break;
    case 'block-search-form':
      $variables['classes_array'][] = 'col-md-4';
      break;
    case 'block-account-account-links':
      $variables['prefix'] = '<div class="col-md-4 visible-md visible-lg"><div class="text-center"><a href="/"><img class="logo" src="/'.drupal_get_path('theme','tuneupfitness').'/images/logo.png" alt="Tune Up Fitness" /></a></div></div><div class="col-md-4">';
      break;
    case 'block-uc-ajax-cart-delta-0':
      $variables['suffix'] = '</div>';
      break;
  }
}
<?php

function yogatuneup_responsive_preprocess_node(&$variables, $hook) {
  $variables['classes_array'][] = 'view-mode-' . $variables['view_mode'];

  if ($variables['view_mode'] == 'video') {
    $variables['classes_array'][] = 'single-video';
  }

  if ($variables['view_mode'] == 'above_content') {
    $variables['page'] = TRUE;
  }

  if ($variables['view_mode'] == 'playlist_segment') {
    $variables['theme_hook_suggestions'][] = 'node__' . $variables['view_mode'];
  }

  $variables['theme_hook_suggestions'][] = 'node__' . $variables['node']->type . '__' . $variables['view_mode'];
  $variables['theme_hook_suggestions'][] = 'node__' . $variables['node']->nid . '__' . $variables['view_mode'];

  $variables['unpublished'] = (!$variables['status']) ? TRUE : FALSE;

  if (uc_product_is_product($variables)) {

    $variables['title_suffix']['rdf_meta_title']['#metadata'][0]['itemprop'] = "name";
    $variables['attributes_array']['itemscope']                              = '';
    $variables['attributes_array']['itemtype']                               = "http://schema.org/Product";
    $variables['classes_array'][]                                            = 'node-product';

    $variables['content']['add_to_cart']['#form']['#attributes']['data-model'] = $variables['node']->model;
    $variables['content']['add_to_cart']['#form']['#attributes']['data-title'] = $variables['node']->title;
    $variables['content']['add_to_cart']['#form']['#attributes']['data-type'] = $variables['node']->type;
    $variables['content']['add_to_cart']['#form']['#attributes']['data-price'] = round($variables['node']->sell_price,2);
  };

  $function = __FUNCTION__ . '_' . $variables['node']->type;
  if (function_exists($function)) {
    $function($variables, $hook);
  }
}


function yogatuneup_responsive_preprocess_node_product(&$variables, $hook) {
  if ($variables['view_mode'] == 'teaser') {
    $variables['content']['trimmed']                = $variables['content']['body'];
    $variables['content']['trimmed']['#field_name'] = 'body-trimmed';

    $alter = array(
      'max_length'    => 140,
      'ellipsis'      => TRUE,
      'word_boundary' => TRUE,
      'html'          => FALSE,
    );

    $value                                         = strip_tags($variables['content']['body'][0]['#markup']);
    $variables['content']['trimmed'][0]['#markup'] = '<a href="' . $variables['node_url'] . '" class="prod-link">' . views_trim_text($alter, $value) . '</a>';

    $variables['content']['add_to_cart']['#form']['actions']['submit']['#value'] = decode_entities('&#xf218;');
    if (isset($variables['content']['video_playlist']['add_to_cart'])) {
      $variables['content']['video_playlist']['add_to_cart']['#form']['actions']['submit']['#value'] = decode_entities('&#xf218;');
    }

    $variables['display_second_teaser'] = TRUE;
    if (strpos(request_path(), 'at-home-program') !== FALSE) {
      $variables['display_second_teaser'] = FALSE;
    }

  }

  // Structured data parameters for all sell price elements
  $variables['content']['sell_price']['#attributes']['itemprop']  = "offers";
  $variables['content']['sell_price']['#attributes']['itemscope'] = '';
  $variables['content']['sell_price']['#attributes']['itemtype']  = "http://schema.org/Offer";
}

function yogatuneup_responsive_preprocess_node_gift_card(&$variables, $hook) {
  if ($variables['view_mode'] == 'teaser') {
    $variables['content']['add_to_cart']['#form']['actions']['submit']['#value'] = decode_entities('&#xf218;');
  }
}

function yogatuneup_responsive_preprocess_node_product_landing(&$variables, $hook) {
  drupal_add_js(drupal_get_path('theme', 'yogatuneup_responsive') . '/js/product_landing.js');
  $variables['layout_mode'] = 'grid-view';
  if (isset($_COOKIE['layout_mode'])) {
    $variables['layout_mode'] = $_COOKIE['layout_mode'];
  }
}

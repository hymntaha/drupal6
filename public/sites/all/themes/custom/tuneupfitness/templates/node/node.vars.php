<?php

function tuneupfitness_preprocess_node(&$variables, $hook) {
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

    if($variables['view_mode'] == 'teaser'){
      if($node = menu_get_object()){
        if($node->type == 'product_landing'){
          $columns = 3;
          if(isset($node->field_columns[LANGUAGE_NONE][0]['value'])){
            $columns = $node->field_columns[LANGUAGE_NONE][0]['value'];
          }

          if($columns == 4){
            $variables['md_columns'] = 3;
          }
          else{
            $variables['md_columns'] = 4;
          }
        }
      }
    }
  };

  $function = __FUNCTION__ . '_' . $variables['node']->type;
  if (function_exists($function)) {
    $function($variables, $hook);
  }
}


function tuneupfitness_preprocess_node_product(&$variables, $hook) {
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

  if($variables['view_mode'] == 'right_block'){
    $variables['title'] = 'Featured Product';
  }

  // Structured data parameters for all sell price elements
  $variables['content']['sell_price']['#attributes']['itemprop']  = "offers";
  $variables['content']['sell_price']['#attributes']['itemscope'] = '';
  $variables['content']['sell_price']['#attributes']['itemtype']  = "http://schema.org/Offer";
}

function tuneupfitness_preprocess_node_gift_card(&$variables, $hook) {
  if ($variables['view_mode'] == 'teaser') {
    $variables['content']['add_to_cart']['#form']['actions']['submit']['#value'] = decode_entities('&#xf218;');
  }
}

function tuneupfitness_preprocess_node_product_landing(&$variables, $hook) {
  drupal_add_js(drupal_get_path('theme', 'tuneupfitness') . '/js/product_landing.js');
  $variables['layout_mode'] = 'grid-view';
  if (isset($_COOKIE['layout_mode'])) {
    $variables['layout_mode'] = $_COOKIE['layout_mode'];
  }
}

function tuneupfitness_preprocess_node_class(&$variables, $hook) {
  if($variables['view_mode'] == 'right_block'){
    if(isset($variables['content']['field_image'][0]['#item'])){
      $variables['content']['field_image'][0]['#item']['attributes']['class'][] = 'media-object';
    }
  }
}

function tuneupfitness_preprocess_node_exercise(&$variables, $hook) {
  if($variables['view_mode'] == 'right_block'){
    $variables['title'] = 'Weekly Roll Out <br class="visible-xs visible-sm" /><small>('.$variables['title'].')</small>';
    $variables['node_url'] = '/'.WEEKLY_EXERCISE_URL;
    $variables['content']['field_video_thumbnail'][0]['#path']['path'] = WEEKLY_EXERCISE_URL;
  }
}

function tuneupfitness_preprocess_node_pose(&$variables, $hook) {
  if($variables['view_mode'] == 'right_block'){
    $variables['title'] = 'Pose Of The Week <br class="visible-xs visible-sm" /><small>('.$variables['title'].')</small>';
    $variables['node_url'] = '/'.WEEKLY_POSE_URL;
    $variables['content']['field_video_thumbnail'][0]['#path']['path'] = WEEKLY_POSE_URL;
  }
}

function tuneupfitness_preprocess_node_playlist(&$variables, $hook){
  if($variables['view_mode'] == 'full'){

    $unpurchased_segments = video_get_playlist_unpurchased_segments($variables['node']);

    if(count($unpurchased_segments) == 0 || count($unpurchased_segments) == count($variables['node']->field_playlist_videos[LANGUAGE_NONE])){
      $price_suffix = isset($variables['content']['field_price_suffix'][0]['#markup']) ? ' '.$variables['content']['field_price_suffix'][0]['#markup'] : '';
      if($price_suffix){
        $variables['content']['sell_price']['#suffixes'] = array($price_suffix);
      }

      $variables['content']['add_to_cart']['#form']['actions']['submit']['#attributes']['class'][] = 'btn';
      $variables['content']['add_to_cart']['#form']['actions']['submit']['#attributes']['class'][] = 'btn-primary';
      $variables['content']['add_to_cart']['#form']['actions']['submit']['#attributes']['class'][] = 'btn-lg';
      $variables['content']['add_to_cart']['#form']['actions']['submit']['#attributes']['class'][] = 'btn-block';
    }
    else{
      $price = 0;
      foreach($unpurchased_segments as $unpurchased_segment){
        $unpurchased_segment_product = video_get_video_segment_product_from_video_segment($unpurchased_segment);
        $price += $unpurchased_segment_product->price;
      }

      $variables['content']['sell_price']['#value'] = $price;
      $variables['content']['add_to_cart']['submit']['#attributes']['class'][] = 'btn';
      $variables['content']['add_to_cart']['submit']['#attributes']['class'][] = 'btn-primary';
      $variables['content']['add_to_cart']['submit']['#attributes']['class'][] = 'btn-lg';
      $variables['content']['add_to_cart']['submit']['#attributes']['class'][] = 'btn-block';
    }
  }
}

function tuneupfitness_preprocess_node_video_segment_product(&$variables, $hook){

}
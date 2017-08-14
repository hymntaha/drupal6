<?php

   function tuneupfitness_preprocess_field(&$variables, $hook){
      if($variables['element']['#field_name'] == "field_feature_block"){
         $variables['classes_array'][] = 'products-featured';
      }
      $function = __FUNCTION__ . '_' . $variables['element']['#field_name'];
      if (function_exists($function)) {
         $function($variables, $hook);
      }
   }
   function tuneupfitness_preprocess_field_field_image(&$var, $hook){
      if (in_array($var['element']['#bundle'],array('field_feature_block','field_image_link'))) {

         $link = field_get_items('field_collection_item',$var['element']['#object'],'field_link');
         $link = $link[0]['url'];
         $var['items'][0]['#path']['path'] = $link;


         $var['items'][0]['#attributes'] = array('class' => 'col-sm-4');


      }
   }
   function tuneupfitness_preprocess_field_field_title(&$var, $hook){
      if ($var['element']['#bundle'] == 'field_feature_block'){
         $link = field_get_items('field_collection_item',$var['element']['#object'],'field_link');
         $link = $link[0]['url'];
         $var['items'][0]['#markup'] = l($var['element']['#items'][0]['value'],$link);
         $var['classes_array'][] = 'title';
      }
   }
   function tuneupfitness_preprocess_field_field_feature_image(&$var, $hook){
      if(drupal_is_front_page()){
         $link = $var['element']['#object']->field_feature_image_link;
         $link = $link['und'][0]['url'];
         $var['items'][0]['#path']['path'] = $link;
      }
   }

   function tuneupfitness_preprocess_field_uc_product_image(&$var, $hook){

   // Structured data parameters for product image elements
      array_walk($var['items'], function(&$image) {  $image['#item']['attributes']['itemprop'][] = 'image'; });

   }

   function tuneupfitness_preprocess_field_body(&$var, $hook){

   // Structured data parameters for product descriptions
      if ($var['element']['#bundle'] == 'product') {
         $var['items'][0]['#prefix'] = '<div itemprop="description">';
            $var['items'][0]['#suffix'] = '</div>';
      }


   }

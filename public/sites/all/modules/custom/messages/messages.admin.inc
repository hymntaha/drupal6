<?php

function messages_admin($group = ""){

  if($group == ""){
		if (function_exists("dpm")){
			dpm(messages_config());
		}

  }


    $group = strtolower($group);

 return drupal_get_form("messages_admin_form", $group);

}


function messages_key_edit($group, $key){
	return drupal_get_form("messages_key_edit_form", $group, $key);
}

function messages_key_edit_form($form, $form_state, $group, $key){

	$key_info = messages_get_key_info($key);

	$form['group'] = array(
		'#type'=>"hidden",
		'#default_value'=>$group,
	);
	$default_value = messages_get($key);

	$element = _messages_get_element($key_info,$default_value);



		$form[$key] = $element;

	$form['submit'] = array(
		'#type'=>"submit",
		'#default_value'=>"Save"
	);
	$form['#submit'] = array("messages_admin_form_submit");

	return $form;

}


function messages_admin_form($form, $form_state, $group = ""){

  $group = strtolower($group);

  messages_preload_group($group);

$form['group'] = array(
  '#type'=>"hidden",
  '#default_value'=>$group,
);

$items = messages_get_by_group($group);

  foreach($items as $key => $item){
		$default_value = messages_get($key);
		$element = _messages_get_element($item,$default_value);

    if(!empty($item['fieldset'])){
      $form[$item['fieldset']][$key] = $element;

      $form[$item['fieldset']]['#type'] = 'fieldset';
      $form[$item['fieldset']]['#title'] =$item['fieldset'];
      $form[$item['fieldset']]['#collapsible'] = true;
      $form[$item['fieldset']]['#collapsed'] = true;
    }else{
      $form[$key] = $element;
    }
  }

 // $form['#submit'][] = "messages_"

  $form['submit'] = array(
    '#type'=>"submit",
    '#default_value'=>"Save"
  );

  return $form;
}

function messages_admin_form_submit($form, $form_state){

  $values = $form_state['values'];

  $group = $values['group'];

  $group = strtolower($group);

  $items = messages_get_by_group($group);

  foreach($items as $key => $item){

		if(!isset($values[$key])) continue;

    $key_val = $values[$key];

    if($item['type'] == "file" && $key_val != 0){
      $file = file_load($key_val);
      $file->status = FILE_STATUS_PERMANENT;
      file_save($file);
      file_usage_add($file, "messages", "messages", 1);
    }

		if($item['type'] == "image_link"){

				if($values[$key]['image'] != 0){
					$file = file_load($values[$key]['image']);
					$file->status = FILE_STATUS_PERMANENT;
					file_save($file);
					file_usage_add($file, "messages", "messages", 1);
				}
		}


    messages_set($key, $key_val);

  }

  drupal_set_message("Your values have been saved.");


}


function _messages_get_element($item, $value){
  $element = array();

  $element['#title'] = $item['title'];
  if(!empty($item['description'])){
    $element['#description'] = $item['description'];
  }

	if (is_array($value) && $item['type'] != "image_link") {
		$default_value = $value['value'];
	}	else {
		$default_value = $value;
	}

	$element["#default_value"] = $default_value;

	if($item['tokens'] == true){

		$element['#suffix'] = theme("token_tree", array("token_types"=>array("user")));
	}


  if($item['type'] == "file"){
    $element['#type'] = "managed_file";
    $element['#progress_indicator']="bar";
    $element['#upload_location']='public://messages/';
  }else if($item['type'] == "textarea" || $item['type'] == "textfield" ){
    $element['#type'] = $item['type'];
  }else if($item['type'] == "path"){
    $element['#type'] = "textfield";
    $element['#element_validate'][] = "_messages_validate_path";
  }else if($item['type'] == "richtext"){
    $element['#type'] = "text_format";
    $element['#format'] = NULL;
  }else if($item['type'] == "image_link"){

			$element['#type'] = "fieldset";
			$element['#tree'] = true;
		unset($element['#default_value']);

		$element['image']['#title'] = "Image";
		$element['image']['#type'] = "managed_file";
		$element['image']['#progress_indicator']="bar";
		$element['image']['#upload_location']='public://messages/';
		$element['image']['#default_value'] = $value['image'];

		$element['link']['#title'] = "Link";
		$element['link']['#type'] = "textfield";
		$element['link']['#default_value'] = $value['link'];

	}

  return $element;
}

function _messages_validate_path($element, &$form_state, $form){

    if (!drupal_valid_path($element['#value'], true)) {
      form_error($element, t('Not a valid drupal path.'));
    }

}
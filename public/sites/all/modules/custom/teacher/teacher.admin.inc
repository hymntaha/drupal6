<?php

function teacher_admin_renewal_settings_form($form, &$form_state){
	$form['teacher_renewal_product_id'] = array(
		'#title' => 'Teacher Renewal Product ID',
		'#type' => 'textfield',
		'#default_value' => variable_get('teacher_renewal_product_id', ''),
	);

	return system_settings_form($form);
}

function teacher_admin_renewal_settings_form_validate($form, &$form_state){
	if(!empty($form_state['values']['teacher_renewal_product_id'])){
		$node = node_load($form_state['values']['teacher_renewal_product_id']);
		if($node->type != 'renewal'){
			form_set_error('teacher_renewal_product_id',t('This is not a valid renewal product id.'));
		}
	}
}

function teacher_renewal_info_import_form($form, &$form_state){
	$form['renewal_import_csv'] = array(
		'#type' => 'textarea',
		'#title' => 'Import CSV',
		'#required' => true,
	);

	$form['submit'] = array(
		'#type' => 'submit',
		'#value' => 'Import',
	);

	return $form;
}

function teacher_renewal_info_import_form_submit($form, &$form_state){
	set_time_limit(0);
	
	$rows = explode("\n",$form_state['values']['renewal_import_csv']);
	foreach($rows as $row){
		$row = str_getcsv($row);
		if(!empty($row[0])){
			$teacher = node_load($row[0]);
			if($teacher && $teacher->type == 'teacher'){
				$account = user_load($teacher->uid);
				
				$edit = array();
				if($row[1]){
					$edit['field_license_date'][LANGUAGE_NONE][0]['value'] = $row[1];
				}

				if($row[2]){
					$edit['field_license_renewal_date'][LANGUAGE_NONE][0]['value'] = $row[2];
				}

				if($row[3]){
					$edit['field_training_id'][LANGUAGE_NONE][0]['value'] = $row[3];
				}

				if($edit){
					user_save($account,$edit);
				}
			}
		}
	}
	drupal_set_message(t('Import Complete'));
}

function teacher_admin_rebuild($form, &$form_state){
	$form['rebuild'] = array(
		'#type' => 'submit',
		'#value' => 'Rebuild Teachers',
	);

	return $form;
}

function teacher_admin_rebuild_submit($form, &$form_state){
	set_time_limit(0);

	$teachers = teacher_get_teachers();
	foreach($teachers as $nid => $title){
		try{
			node_save(node_load($nid));
		}
		catch (Exception $e) {
			watchdog('teacher', 'Teacher Rebuild Node @nid Error: @message', array('@nid' => $nid,'@message' => $e->getMessage()));
		}
	}

	drupal_set_message(t('Teacher Data Rebuild Complete'));
}

function teacher_rmm_admin_rebuild($form, &$form_state){
  $form['rebuild'] = array(
    '#type' => 'submit',
    '#value' => 'Rebuild RMMs',
  );

  return $form;
}

function teacher_rmm_admin_rebuild_submit($form, &$form_state){
  set_time_limit(0);

  $teachers = teacher_get_roll_model_practitioners();
  foreach($teachers as $uid => $title){
    try{
      user_save(user_load($uid));
    }
    catch (Exception $e) {
      watchdog('teacher', 'RMM Rebuild User @uid Error: @message', array('@uid' => $uid,'@message' => $e->getMessage()));
    }
  }

  drupal_set_message(t('RMM Data Rebuild Complete'));
}
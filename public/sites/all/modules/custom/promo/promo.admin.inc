<?php

function promo_admin_setting_form($form, &$form_state){
	$form['promo_enable'] = array(
		'#title' => 'Enable Promo?',
		'#type' => 'checkbox',
		'#default_value' => variable_get('promo_enable', 0),
	);

	$form['promo_id'] = array(
		'#title' => 'Promo ID',
		'#type' => 'textfield',
		'#default_value' => variable_get('promo_id', ''),
	);

	$form['promo_content'] = array(
		'#title' => 'Promo Content',
		'#type' => 'text_format',
		'#format' => 'rich_text_admin',
		'#default_value' => variable_get('promo_content', ''),
	);

	$form['submit'] = array(
		'#type' => 'submit',
		'#value' => 'Submit',
	);

	return $form;
}

function promo_admin_setting_form_validate($form, &$form_state){
	if($form_state['values']['promo_enable']){
		if(!$form_state['values']['promo_id']){
			form_set_error('promo_id','Promo ID Required.');
		}
		if(!$form_state['values']['promo_content']['value']){
			form_set_error('promo_content','Promo Content Required.');
		}
	}
}

function promo_admin_setting_form_submit($form, &$form_state){
	variable_set('promo_enable', $form_state['values']['promo_enable']);
	variable_set('promo_id', $form_state['values']['promo_id']);
	variable_set('promo_content', $form_state['values']['promo_content']['value']);
	drupal_set_message(t('Settings Saved'));
}
<?php

function zoho_admin_settings_form($form,&$form_state){
	$form['zoho_auth_token'] = array(
		'#type' => 'textfield',
		'#title' => 'Zoho Auth Token',
		'#default_value' => variable_get('zoho_auth_token', ''),
	);

	$form['zoho_application_name'] = array(
		'#type' => 'textfield',
		'#title' => 'Zoho Application Name',
		'#default_value' => variable_get('zoho_application_name', ''),
	);

	$form['zoho_owner_name'] = array(
		'#type' => 'textfield',
		'#title' => 'Zoho Owner Name',
		'#default_value' => variable_get('zoho_owner_name', ''),
	);

	return system_settings_form($form);
}
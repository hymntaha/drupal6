<?php

function infusionsoft_admin_settings_form($form, &$form_state){

	$form['infusionsoft_connection_name'] = array(
		'#type' => 'textfield',
		'#title' => t('Connection Name'),
		'#default_value' => variable_get('infusionsoft_connection_name', 'Infusionsoft Drupal Module'),
	);

	$form['infusionsoft_account_mode'] = array(
		'#type' => 'radios',
		'#title' => t('Account Mode'),
		'#options' => array('sandbox' => 'Sandbox', 'live' => 'Live'),
		'#default_value' => variable_get('infusionsoft_account_mode', 'sandbox'),
	);

	$form['sandbox'] = array(
		'#type' => 'fieldset',
		'#title' => t('Sandbox Settings'),
	);

	$form['sandbox']['sandbox_application_name'] = array(
		'#type' => 'textfield',
		'#title' => t('Application Name'),
		'#default_value' => variable_get('sandbox_application_name', ''),
	);

	$form['sandbox']['sandbox_api_key'] = array(
		'#type' => 'textfield',
		'#title' => t('API Key'),
		'#default_value' => variable_get('sandbox_api_key', ''),
	);

	$form['live'] = array(
		'#type' => 'fieldset',
		'#title' => t('Live Settings'),
	);

	$form['live']['live_application_name'] = array(
		'#type' => 'textfield',
		'#title' => t('Application Name'),
		'#default_value' => variable_get('live_application_name', ''),
	);

	$form['live']['live_api_key'] = array(
		'#type' => 'textfield',
		'#title' => t('API Key'),
		'#default_value' => variable_get('live_api_key', ''),
	);

	$form['infusionsoft_tracking_code'] = array(
		'#type' => 'textarea',
		'#title' => 'Pixel Tracking Code',
		'#default_value' => variable_get('infusionsoft_tracking_code', ''),
	);

	return system_settings_form($form);
}
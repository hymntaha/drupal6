<?php

function account_admin_settings_form($form, &$form_state){
	$form['thank_you_page_id'] = array(
		'#title' => t('Thank You For Registering Page ID'),
		'#default_value' => variable_get('thank_you_page_id',''),
		'#type' => 'textfield',
	);
	return system_settings_form($form);
}
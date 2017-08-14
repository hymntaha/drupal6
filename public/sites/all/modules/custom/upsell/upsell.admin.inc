<?php

function upsell_admin_settings_form($form, &$form_state){
	$form['upsell_discount_enabled'] = array(
		'#type' => 'checkbox',
		'#title' => t('Enable upsell discounts'),
		'#default_value' => variable_get('upsell_discount_enabled',0),
	);
	return system_settings_form($form);
}
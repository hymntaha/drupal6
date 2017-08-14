<?php

function testdotcom_admin_settings_form($form, &$form_state){
  $form['testdotcom_webservice_url'] = array(
    '#type' => 'textfield',
    '#title' => 'Webservice URL',
    '#default_value' => variable_get('testdotcom_webservice_url', ''),
  );

  $form['testdotcom_login_code'] = array(
    '#type' => 'textfield',
    '#title' => 'Login Code',
    '#default_value' => variable_get('testdotcom_login_code', ''),
  );

  $form['testdotcom_xml_data_access_code'] = array(
    '#type' => 'textfield',
    '#title' => 'XML Data Access Code',
    '#default_value' => variable_get('testdotcom_xml_data_access_code', ''),
  );

  $form['testdotcom_account_id_special_code'] = array(
    '#type' => 'textfield',
    '#title' => 'Account ID Special Code ',
    '#default_value' => variable_get('testdotcom_account_id_special_code', ''),
  );

  return system_settings_form($form);
}
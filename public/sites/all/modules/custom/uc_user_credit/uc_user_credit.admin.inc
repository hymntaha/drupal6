<?php

function uc_user_credit_admin_form($form, &$form_state, $account) {
  $form['credit_history'] = array(
    '#type' => 'fieldset',
    '#title' => 'Credit History',
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );

  $form['credit_history']['data'] = array(
    '#markup' => theme('uc_user_credit_history',array('uid' => $account->uid)),
  );

  $form['total_credit'] = array(
    '#markup' => '<p><strong>Current total credit for '.$account->mail.':</strong> '.uc_currency_format(uc_user_credit_get_credit_for_user($account->uid)),
  );

  $form['credit'] = array(
    '#type' => 'textfield',
    '#title' => t('User Credit'),
    '#required' => TRUE,
  );

  $form['memo'] = array(
    '#type' => 'textarea',
    '#title' => t('Memo'),
  );

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => 'Submit',
  );

  return $form;
}

function uc_user_credit_admin_form_validate($form, &$form_state) {
  $account = $form_state['build_info']['args'][0];
  $credit = $form_state['values']['credit'];

  $full_credit = uc_user_credit_get_credit_for_user($account->uid);

  if (!is_numeric($credit)){
    form_set_error('credit', t('Credit must be numeric.'));
  }
  else{
    if($full_credit + $credit < 0){
      form_set_error('credit',t('Can\'t set credit less than zero.'));
    }
  }
}

function uc_user_credit_admin_form_submit($form, &$form_state) {
  $account = $form_state['build_info']['args'][0];
  $credit = round($form_state['values']['credit'], 2);

  $params = array(
    'uid' => $account->uid,
    'credit' => $credit,
    'memo' => $form_state['values']['memo'],
  );

  uc_user_credit_save($params);

  $full_credit = uc_user_credit_get_credit_for_user($account->uid);

  module_invoke_all('uc_user_credit', $account, $credit, $full_credit);

  drupal_set_message(t(':credit credit added to user :account', array(
    ':account' => $account->mail,
    ':credit' => uc_currency_format($credit)
  )));
}

function uc_user_credit_credit_history_export($account){
  $credit_history = uc_user_credit_load($account->uid);

  $header = array(
    'Adjustment',
    'Type',
    'Timestamp',
  );

  $rows = array();

  foreach($credit_history as $row){
    $logged_account = user_load($row->logged_uid);

    $rows[] = array(
      uc_currency_format($row->credit),
      $row->order_id ? 'Order #'.$row->order_id : 'Admin adjustment by '.$logged_account->mail,
      date('m/d/Y h:ia',$row->timestamp),
    );
  }

  data_export_csv_export($rows,$header,'uc_user_credit_history');
}
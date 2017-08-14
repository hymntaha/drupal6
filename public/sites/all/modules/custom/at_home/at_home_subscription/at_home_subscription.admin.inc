<?php

function at_home_subscription_settings_form($form, &$form_state) {
  foreach (at_home_subscription_get_types() as $type => $label) {
    $form['type_' . $type] = array(
      '#type'  => 'fieldset',
      '#title' => $label,
    );

    $products = at_home_subscription_get_product_info($type);

    for ($level = 1; $level <= AH_NUM_LEVELS; $level++) {

      $form['type_' . $type]['level_' . $level] = array(
        '#type'        => 'fieldset',
        '#title'       => t('Level ' . $level),
        '#collapsible' => TRUE,
        '#collapsed'   => TRUE,
      );

      for ($month = 1; $month <= AH_NUM_MONTHS; $month++) {
        $form['type_' . $type]['level_' . $level][$type . '_level_' . $level . '_month_' . $month] = array(
          '#type'              => 'textfield',
          '#title'             => t('Month ' . $month),
          '#autocomplete_path' => 'ajax/at-home-settings-autocomplete/'.$type,
          '#default_value'     => isset($products['l' . $level . 'm' . $month]->nid) ? $products['l' . $level . 'm' . $month]->nid : '',
        );
      }
    }
  }

  $form['submit'] = array(
    '#type'  => 'submit',
    '#value' => 'Submit',
  );

  return $form;
}

function at_home_subscription_settings_form_validate($form, &$form_state) {
  foreach (at_home_subscription_get_types() as $type => $label) {
    for ($level = 1; $level <= AH_NUM_LEVELS; $level++) {
      for ($month = 1; $month <= AH_NUM_MONTHS; $month++) {
        $key = $type . '_level_' . $level . '_month_' . $month;
        if (!empty($form_state['values'][$key])) {
          if (!node_load($form_state['values'][$key])) {
            form_set_error($key, 'Invalid value for Level ' . $level . ' Month ' . $month . '.');
          }
        }
      }
    }
  }
}

function at_home_subscription_settings_form_submit($form, &$form_state) {
  foreach (at_home_subscription_get_types() as $type => $label) {
    for ($level = 1; $level <= AH_NUM_LEVELS; $level++) {
      for ($month = 1; $month <= AH_NUM_MONTHS; $month++) {
        $key = $type . '_level_' . $level . '_month_' . $month;
        if (!empty($form_state['values'][$key])) {
          db_merge('at_home_products')
            ->key(array('level' => $level, 'month' => $month, 'type' => $type))
            ->fields(array(
              'level' => $level,
              'month' => $month,
              'nid'   => $form_state['values'][$key],
              'type'  => $type,
            ))
            ->execute();
        }
      }
    }
  }

  drupal_set_message(t('At Home Settings have been updated.'));
}

function at_home_subscription_at_home_queue_callback($account) {
  $account_info = at_home_subscription_account_info_load($account);
  if (isset($account_info['queue'])) {
    drupal_goto('user/' . $account->uid . '/program-manager');
  }
  else {
    if (!empty($account_info['account']->address) && !empty($account_info['account']->cc_info)) {
      $render[]['#markup'] = '<p>This user does not have a queue. Click the Create Queue button below to build an empty queue.</p>';
      $render[]            = drupal_get_form('at_home_subscription_create_queue_form', $account);
    }
    else {
      $links = '';
      if (empty($account_info['account']->address)) {
        $links .= l('default address', 'user/' . $account->uid . '/addresses');
      }
      if (empty($account_info['account']->cc_info)) {
        $cc_link = l('default billing info', 'user/' . $account->uid . '/billing');
        if (empty($links)) {
          $links .= $cc_link;
        }
        else {
          $links .= ' and ' . $cc_link;
        }
      }
      $render[]['#markup'] = '<p>Queue can not be created.</p><p>Please enter the ' . $links . ' for this user.</p>';
    }
    return $render;
  }
}

function at_home_subscription_create_queue_form($form, &$form_state, $account) {
  $form['uid'] = array(
    '#type'  => 'hidden',
    '#value' => $account->uid,
  );

  $form['submit'] = array(
    '#type'  => 'submit',
    '#value' => 'Create Queue',
  );
  return $form;
}

function at_home_subscription_create_queue_form_submit($form, &$form_state) {
  $account = user_load($form_state['values']['uid']);
  at_home_subscription_create_program(NULL, $account, TRUE);
  drupal_goto('user/' . $account->uid . '/program-manager');
}

function at_home_subscription_exports_form($form, &$form_state) {
  $form['start_date'] = array(
    '#type'          => 'date_popup',
    '#title'         => 'Last Shipment Start Date',
    '#date_format'   => 'Y-m-d',
    '#default_value' => isset($_SESSION['at_home_export_start_date']) ? $_SESSION['at_home_export_start_date'] : '',
  );

  $form['end_date'] = array(
    '#type'          => 'date_popup',
    '#title'         => 'Last Shipment End Date',
    '#date_format'   => 'Y-m-d',
    '#default_value' => isset($_SESSION['at_home_export_end_date']) ? $_SESSION['at_home_export_end_date'] : '',
  );

  $form['type'] = array(
    '#type'          => 'radios',
    '#title'         => 'Export Type',
    '#options'       => array(
      'full'       => 'Full Customer Data',
      'cc_failure' => 'Customers with CC Failure',
      'cc_expired' => 'Customers with CC Expired',
    ),
    '#default_value' => isset($_SESSION['at_home_export_type']) ? $_SESSION['at_home_export_type'] : 'full',
  );

  $form['submit'] = array(
    '#type'  => 'submit',
    '#value' => 'Submit',
  );

  return $form;
}

function at_home_subscription_exports_form_validate($form, &$form_state) {
  if (!empty($form_state['values']['start_date']) && !empty($form_state['values']['end_date'])) {
    if (strtotime($form_state['values']['start_date']) > strtotime($form_state['values']['end_date'])) {
      form_set_error('end_date', 'End Date can not be less than Start Date');
    }
  }
}

function at_home_subscription_exports_form_submit($form, &$form_state) {
  set_time_limit(0);

  $_SESSION['at_home_export_start_date'] = '';
  $_SESSION['at_home_export_end_date']   = '';
  $_SESSION['at_home_export_type']       = $form_state['values']['type'];

  if (module_exists('data_export')) {
    $query = db_select('users_at_home', 'uah')->fields('uah', array('uid'));
    if (!empty($form_state['values']['start_date'])) {
      $_SESSION['at_home_export_start_date'] = $form_state['values']['start_date'];
      $query->condition('last_shipment', strtotime($form_state['values']['start_date']), '>=');
    }
    if (!empty($form_state['values']['end_date'])) {
      $_SESSION['at_home_export_end_date'] = $form_state['values']['end_date'];
      $query->condition('last_shipment', strtotime($form_state['values']['end_date']), '<=');
    }
    if ($form_state['values']['type'] == 'cc_failure') {
      $query->condition('display_cc_problem', 1);
    }

    $headers = array(
      'Name',
      'Email',
      'Initial Program Purchase Date',
      'Initial Purchase Type',
      'Status',
      'Last Shipment',
      'Next Shipment',
      'Frequency',
      'CC Problem',
      'CC Expired',
      'Shipped DVDs',
    );
    $data    = array();

    $results = $query->execute();
    foreach ($results as $row) {
      $account      = user_load($row->uid);
      $account_info = at_home_subscription_account_info_load($account);
      if ($form_state['values']['type'] == 'cc_expired' && !at_home_subscription_cc_expired($account_info)) {
        continue;
      }

      $format        = 'Y-m-d';
      $shipped_queue = array();
      foreach ($account_info['queue'] as $queue_key => $queue) {
        if ($queue->status == AH_QUEUE_SHIPPED) {
          $shipped_queue[] = $queue_key;
        }
      }

      $source  = '';
      $product = at_home_subscription_get_user_at_home_program_product($account->uid);
      if (empty($product)) {
        $source = 'Admin Created';
      }
      else {
        $source = $product->title;
      }

      $data[] = array(
        account_get_full_name($account),
        $account->mail,
        date($format, $account_info['account']->joined),
        $source,
        $account_info['account']->status,
        date($format, $account_info['account']->last_shipment),
        !empty($account_info['account']->next_shipment) ? date($format, $account_info['account']->next_shipment) : '',
        $account_info['account']->shipping_option,
        $account_info['account']->display_cc_problem ? 1 : 0,
        at_home_subscription_cc_expired($account_info) ? 1 : 0,
        implode(' ', $shipped_queue),
      );
    }

    if (count($data)) {
      data_export_csv_export($data, $headers, 'at_home_users_export');
    }
    else {
      drupal_set_message('There are no results for the specified date range / export type.', 'warning');
    }
  }
  else {
    drupal_set_message(t('Data Export module not enabled!'), 'warning');
  }
}
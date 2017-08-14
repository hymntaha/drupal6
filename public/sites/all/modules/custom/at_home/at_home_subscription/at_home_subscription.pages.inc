<?php

function at_home_subscription_program_manager_callback($account) {
  global $user;

  //Only admins can view/edit other users program managers
  if ($user->uid != $account->uid && !account_is_admin()) {
    drupal_goto('user');
  }

  //If the user is not an at home user then redirect back to dashboard, admins can view queues even for users not in the program
  if (!account_is_at_home($account) && !account_is_admin()) {
    drupal_goto('user');
  }

  $account_info     = at_home_subscription_account_info_load($account);
  $shipping_options = at_home_subscription_get_shipping_options();
  $hold_options     = at_home_subscription_get_hold_options();
  $shipping_next    = at_home_subscription_account_info_get_shipping_next($account_info);
  $date_changes     = at_home_subscription_get_date_changes($account_info['account']->last_shipment, TRUE);

  drupal_add_js(drupal_get_path('module', 'at_home_subscription') . '/at_home_subscription.js', 'file');
  drupal_add_js(array(
    'at_home_subscription' => array(
      'dates'            => $date_changes,
      'shipping_options' => array_merge($shipping_options, $hold_options)
    )
  ), 'setting');

  $next_shipment_info = array(
    '#theme'              => 'next_shipment',
    '#next_shipment_date' => !empty($account_info['account']->next_shipment) ? date('m/d/Y', $account_info['account']->next_shipment) : 'N/A',
    '#shipping_option'    => isset($shipping_options[$account_info['account']->shipping_option]) ?
      $shipping_options[$account_info['account']->shipping_option] : $hold_options[$account_info['account']->shipping_option],
    '#shipping_next'      => $shipping_next,
    '#total_calc'         => at_home_subscription_calculate_total_cost($account, $shipping_next),
  );

  $shipping_address = array(
    '#theme'       => 'address_info',
    '#type'        => 'Shipping',
    '#first_name'  => $account_info['account']->address['shipping']['formatted']['first_name'],
    '#last_name'   => $account_info['account']->address['shipping']['formatted']['last_name'],
    '#street1'     => $account_info['account']->address['shipping']['formatted']['street1'],
    '#street2'     => $account_info['account']->address['shipping']['formatted']['street2'],
    '#city'        => $account_info['account']->address['shipping']['formatted']['city'],
    '#zone'        => $account_info['account']->address['shipping']['formatted']['zone'],
    '#postal_code' => $account_info['account']->address['shipping']['formatted']['postal_code'],
    '#country'     => $account_info['account']->address['shipping']['formatted']['country'],
    '#edit_link'   => l(
      'Edit my shipping address',
      'user/' . $account->uid . '/addresses/' . $account_info['account']->address['shipping']['formatted']['aid'] . '/edit',
      array('query' => array('destination' => 'user/' . $account->uid . '/program-manager'))
    ),
  );

  if ($account_info['account']->cc_info) {
    $billing_address = array(
      '#theme'       => 'address_info',
      '#type'        => 'Billing',
      '#first_name'  => $account_info['account']->cc_info['billTo']->firstName,
      '#last_name'   => $account_info['account']->cc_info['billTo']->lastName,
      '#street1'     => $account_info['account']->cc_info['billTo']->address,
      '#street2'     => '',
      '#city'        => $account_info['account']->cc_info['billTo']->city,
      '#zone'        => $account_info['account']->cc_info['billTo']->state,
      '#postal_code' => $account_info['account']->cc_info['billTo']->zip,
      '#country'     => $account_info['account']->cc_info['billTo']->country,
      '#edit_link'   => l(
        'Edit my billing address',
        'user/' . $account->uid . '/billing/' . $account_info['account']->cc_info['profile']['pid'] . '/edit',
        array('query' => array('destination' => 'user/' . $account->uid . '/program-manager'))
      ),
    );

    $credit_card_info = array(
      '#theme'     => 'cc_info',
      '#last_four' => $account_info['account']->cc_info['profile']['cc_last_four'],
      '#exp_month' => $account_info['account']->cc_info['profile']['cc_exp_month'],
      '#exp_year'  => $account_info['account']->cc_info['profile']['cc_exp_year'],
      '#edit_link' => l(
        'Edit my credit card information',
        'user/' . $account->uid . '/billing/' . $account_info['account']->cc_info['profile']['pid'] . '/edit',
        array('query' => array('destination' => 'user/' . $account->uid . '/program-manager'))
      ),
    );
  }
  else {
    $billing_address = array(
      '#theme'       => 'address_info',
      '#type'        => 'Billing',
      '#first_name'  => '',
      '#last_name'   => '',
      '#street1'     => '',
      '#street2'     => '',
      '#city'        => '',
      '#zone'        => '',
      '#postal_code' => '',
      '#country'     => '',
      '#edit_link'   => l(
        'Add billing information',
        'user/' . $account->uid . '/billing/add',
        array('query' => array('destination' => 'user/' . $account->uid . '/program-manager'))
      ),
    );

    $credit_card_info = array(
      '#theme'     => 'cc_info',
      '#last_four' => '',
      '#exp_month' => '',
      '#exp_year'  => '',
      '#edit_link' => l(
        'Add credit card information',
        'user/' . $account->uid . '/billing/add',
        array('query' => array('destination' => 'user/' . $account->uid . '/program-manager'))
      ),
    );
  }

  $string_type = 'DVD';
  if ($account_info['account']->type == AH_TYPE_PLAYLIST) {
    $string_type = 'Online Video';
  }

  $render[] = array(
    '#theme'              => 'program_manager',
    '#header'             => _mm('ah_pm_header'),
    '#name'               => account_get_full_name($account),
    '#last_shipment'      => date('m/d/Y', $account_info['account']->last_shipment),
    '#status'             => $account_info['account']->status,
    '#joined'             => date('m/d/Y', $account_info['account']->joined),
    '#cancel_link'        => l('Cancel Subscription', 'user/' . $account->uid . '/at-home-cancel'),
    '#next_shipment_info' => $next_shipment_info,
    '#shipping_options'   => drupal_get_form('at_home_subscription_shipping_options_form', $account_info),
    '#shipping_address'   => $shipping_address,
    '#billing_address'    => $billing_address,
    '#credit_card_info'   => $credit_card_info,
    '#queue_header'       => str_replace('%type', $string_type, _mm('ah_pm_q_header')),
    '#queue_form'         => drupal_get_form('at_home_subscription_queue_form', $account_info),
    '#account_info'       => $account_info,
  );

  return $render;
}

function at_home_subscription_shipping_options_form($form, &$form_state, $account_info) {
  $form['shipping_options'] = array(
    '#type'          => 'radios',
    '#title'         => t('Subscription Options !description', array('!description' => '<div class="shipping-options-description">' . _mm('ah_pm_shipping_options') . '</div>')),
    '#options'       => at_home_subscription_get_shipping_options(),
    '#default_value' => strpos($account_info['account']->shipping_option, 'hold') !== FALSE ? 'hold' : $account_info['account']->shipping_option,
  );

  $form['hold_options'] = array(
    '#type'          => 'select',
    '#options'       => at_home_subscription_get_hold_options(),
    '#default_value' => strpos($account_info['account']->shipping_option, 'hold') !== FALSE ? $account_info['account']->shipping_option : '',
    '#states'        => array(
      'visible' => array(
        ':input[name="shipping_options"]' => array('value' => 'hold'),
      ),
    ),
  );

  //Drupal 7 FAPI doesn't have support for regular buttons...
  $form['trigger_submit'] = array(
    '#markup' => '<button id="edit-trigger-submit" class="red-button">Save Changes</button>',
  );

  return $form;
}

function at_home_subscription_queue_form($form, &$form_state, $account_info) {
  for ($level = 1; $level <= AH_NUM_LEVELS; $level++) {
    $form['level_' . $level] = array(
      '#type'   => 'fieldset',
      '#prefix' => '<div class="level"><h3>Level ' . $level . '</h3>',
      '#suffix' => '</div>',
      '#weight' => $level + 1,
    );
  }

  foreach ($account_info['queue'] as $queue) {
    $form['level_' . $queue->level]['l' . $queue->level . 'm' . $queue->month] = array(
      '#type'  => 'checkbox',
      '#title' => t('!title !status',
        array(
          '!title'  => '<span class="title ' . str_replace(' ', '-', strtolower($queue->status)) . '">' . 'Month ' . $queue->month . '</span>',
          '!status' => '<span class="status ' . str_replace(' ', '-', strtolower($queue->status)) . '">' . $queue->status . '</span>',
        )
      ),
    );
    if ($queue->status == AH_QUEUE_SHIPPED && !account_is_admin()) {
      $form['level_' . $queue->level]['l' . $queue->level . 'm' . $queue->month]['#disabled'] = TRUE;
    }
  }

  $form['shipping_option'] = array(
    '#type'          => 'hidden',
    '#default_value' => $account_info['account']->shipping_option,
  );

  $form['actions'] = array(
    '#type'   => 'actions',
    '#weight' => 10,
    '#prefix' => '<div class="form-actions">',
    '#suffix' => '</div>',
  );

  $form['actions']['add_to_next'] = array(
    '#type'       => 'submit',
    '#value'      => 'Add Selected to Next Shipment',
    '#attributes' => array('class' => array('link-button')),
  );

  $form['actions']['skip_unskip'] = array(
    '#type'       => 'submit',
    '#value'      => 'Skip or Unskip Selected',
    '#attributes' => array('class' => array('link-button', 'hidden')),
  );

  $form['actions']['remove'] = array(
    '#type'       => 'submit',
    '#value'      => 'Clear Status of Selected',
    '#attributes' => array('class' => array('hidden')),
  );

  $form['actions']['shipped'] = array(
    '#type'       => 'submit',
    '#value'      => 'Set Selected to Shipped',
    '#attributes' => array('class' => array('link-button', 'hidden')),
  );

  if (account_is_admin()) {
    $form['actions']['skip_unskip']['#attributes']['class'] = array('link-button');

    $form['actions']['remove']['#attributes'] = array('class' => array('link-button'));
    $form['actions']['remove']['#prefix'] = ' | ';

    $form['actions']['shipped']['#attributes'] = array('class' => array('link-button'));
    $form['actions']['shipped']['#prefix'] = ' | ';
  }

  $form['actions']['submit'] = array(
    '#type'       => 'submit',
    '#value'      => 'Save Changes',
    '#attributes' => array('class' => array('red-button', 'hidden')),
    '#prefix'     => '',
  );

  $form['actions']['submit_ship_now'] = array(
    '#type'       => 'submit',
    '#value'      => 'Ship Order Now',
    '#attributes' => array('class' => array('red-button', 'hidden')),
    '#prefix'     => '',
  );

  return $form;
}

function at_home_subscription_queue_form_validate($form, &$form_state) {
  if ($form_state['triggering_element']['#value'] == $form['actions']['submit']['#value']) {
    $account_info = $form_state['build_info']['args'][0];
    if (at_home_subscription_cc_expired($account_info)) {
      if ($form_state['values']['shipping_option'] != 'hold_indefinate') {
        form_set_error('', 'Credit Card Information needs to be updated before program can be re-activated.');
      }
    }
  }
}

function at_home_subscription_queue_form_submit($form, &$form_state) {
  $ship_now = $form_state['triggering_element']['#value'] == $form['actions']['submit_ship_now']['#value'];
  $save_sub_options = $form_state['triggering_element']['#value'] == $form['actions']['submit']['#value'];

  $account_info = $form_state['build_info']['args'][0];
  $account = user_load($account_info['account']->uid);

  $updated_info = array(
    'uid' => $account_info['account']->uid,
  );

  if($ship_now){
      $ship_now_products = array();

      foreach($account_info['queue'] as $key => $queue){
        if($queue->status == AH_QUEUE_SHIPPING_NEXT){
          $ship_now_products[] = array(
            'nid' => $queue->nid,
            'ahp_id' => $queue->ahp_id,
          );
        }
      }

      if(!empty($ship_now_products)){
        $form_state['redirect'] = 'user/'.$account_info['account']->uid.'/at-home-ship-now';
      }
  }
  else{
    if ($save_sub_options) {
      $account_info['account']->shipping_option = $form_state['values']['shipping_option'];
      $date_changes                             = at_home_subscription_get_date_changes($account_info['account']->last_shipment);

      $updated_info = array(
        'uid'             => $account_info['account']->uid,
        'status'          => $account_info['account']->shipping_option == 'hold_indefinate' ? AH_STATUS_HOLD : AH_STATUS_ACTIVE,
        'next_shipment'   => $account_info['account']->shipping_option == 'hold_indefinate' ? NULL : $date_changes[$account_info['account']->shipping_option],
        'shipping_option' => $account_info['account']->shipping_option,
        'queue_info'      => array(),
      );

      if ($updated_info['shipping_option'] != 'hold_indefinate') {
        $updated_info['display_cc_problem'] = 0;
      }

      drupal_set_message(t('Your program queue settings have been updated.'));
    }
    else{
      switch($form_state['triggering_element']['#value']){
        case $form['actions']['add_to_next']['#value']:
          $status = AH_QUEUE_SHIPPING_NEXT;
          break;
        case $form['actions']['shipped']['#value']:
          $status = AH_QUEUE_SHIPPED;
          break;
        case $form['actions']['skip_unskip']['#value']:
          $status = AH_QUEUE_SKIPPED;
          break;
        case $form['actions']['remove']['#value']:
        default:
          $status = '';
          break;
      }
      foreach($account_info['queue'] as $key => $queue){
        if(!empty($form_state['values'][$key])){
          $updated_info['queue_info'][$key] = array(
            'ahp_id' => $queue->ahp_id,
            'status' => $status == AH_QUEUE_SKIPPED ? ($queue->status == AH_QUEUE_SKIPPED ? '' : $status) : $status,
          );
        }
      }

      drupal_set_message(t('Your program queue has been updated.'));
    }

    at_home_subscription_account_info_save($updated_info);

    /*
    if (_mm('ah_pm_update_email_from', '', TRUE)) {
      at_home_subscription_send_update_email($account);
    }
    */

    if (account_is_admin() && !account_is_at_home($account)) {
      $account_info  = at_home_subscription_account_info_load($account);
      $shipping_next = at_home_subscription_account_info_get_shipping_next($account_info);
      if (!empty($shipping_next)) {
        user_multiple_role_edit(array($account->uid), 'add_role', USER_AT_HOME_RID);
      }
    }
  }
}

function at_home_subscription_get_shipping_options() {
  return array(
    'monthly'   => 'Monthly Subscription',
    'bi_weekly' => 'Twice a Month Subscription',
    'weekly'    => 'Weekly Subscription',
    'hold'      => 'Hold Subscription',
  );
}

function at_home_subscription_get_hold_options() {
  return array(
    'hold_month'        => 'Hold for a Month',
    'hold_three_months' => 'Hold for Three Months',
    'hold_six_months'   => 'Hold for Six Months',
    'hold_indefinate'   => 'Hold Indefinitely',
  );
}

function at_home_subscription_cancel_form($form, &$form_state, $account) {
  $form['message']['#markup'] = '<div class="cancel-message">' . _mm('ah_pm_cancel_confirm_message', '') . '</div>';

  $form['uid'] = array(
    '#type'  => 'hidden',
    '#value' => $account->uid,
  );

  $form['actions'] = array(
    '#type'   => 'actions',
    'confirm' => array(
      '#type'       => 'submit',
      '#value'      => 'Cancel My Program',
      '#attributes' => array('class' => array('blue-button')),
    ),
    'go_back' => array('#markup' => l('I do not want to cancel at this time.', 'user/' . $account->uid)),
  );
  return $form;
}

function at_home_subscription_cancel_form_submit($form, &$form_state) {
  $account = user_load($form_state['values']['uid']);
  at_home_subscription_set_canceled($account);
  drupal_set_message(t(_mm('ah_pm_cancel_message', '')));
  drupal_goto('user/' . $account->uid);
}

function at_home_subscription_subscription_type_form($form, &$form_state, $account) {
  global $user;

  if ($user->uid != $account->uid && !account_is_admin()) {
    drupal_goto('user');
  }

  $account_info   = at_home_subscription_account_info_load($account);
  $switch_to_type = $account_info['account']->type == AH_TYPE_DVD ? 'Online Video' : 'DVD';

  $form['message']['#markup'] = '<div class="cancel-message">Are you sure you want to switch your subscription to ' . $switch_to_type . 's</div>';

  $form['uid'] = array(
    '#type'  => 'hidden',
    '#value' => $account->uid,
  );

  $form['switch_to_type'] = array(
    '#type'  => 'hidden',
    '#value' => $account_info['account']->type == AH_TYPE_DVD ? AH_TYPE_PLAYLIST : AH_TYPE_DVD,
  );

  $form['actions'] = array(
    '#type'   => 'actions',
    'confirm' => array(
      '#type'       => 'submit',
      '#value'      => 'Switch My Program to ' . $switch_to_type . 's',
      '#attributes' => array('class' => array('blue-button')),
    ),
    'go_back' => array('#markup' => l('I do not want to switch at this time.', 'user/' . $account->uid . '/program-manager')),
  );
  return $form;
}

function at_home_subscription_subscription_type_form_submit($form, &$form_state) {
  at_home_subscription_switch_type($form_state['values']['uid'], $form_state['values']['switch_to_type']);

  $string_type = 'DVDs';
  switch ($form_state['values']['switch_to_type']) {
    case AH_TYPE_PLAYLIST:
      $string_type = 'Online Videos';
      break;
  }

  drupal_set_message('Your subscription type has been set to ' . $string_type);
  $form_state['redirect'] = 'user/' . $form_state['values']['uid'] . '/program-manager';
}

function at_home_subscription_videos_callback($account) {
  global $user;
  $render = array();

  if (!account_is_admin() && $user->uid != $account->uid) {
    return $render;
  }

  $playlists = at_home_subscription_get_at_home_videos($account);
  if (!empty($playlists)) {
    foreach ($playlists as $playlist_nid) {
      $render['videos'][] = array('#markup' => video_playlist_get_link($playlist_nid, 'private'));
    }
  }

  return $render;
}

function at_home_subscription_ship_now_callback($account){
  global $user;

  if ($user->uid != $account->uid && !account_is_admin()) {
    drupal_goto('user');
  }

  $account_info = at_home_subscription_account_info_load($account);

  $build = array(
    '#theme' => 'at_home_subscription_uc_cart_checkout_review',
    '#account_info' => $account_info,
    '#form' => drupal_get_form('at_home_subscription_at_home_ship_now_form', $account),
  );

  return $build;
}

function at_home_subscription_at_home_ship_now_form($form, &$form_state, $account){

  $form['#attached']['js'][] = drupal_get_path('module', 'at_home_subscription').'/at_home_subscription_ship_now.js';

  $form['actions'] = array(
    '#type'   => 'actions',
    'go_back' => array('#markup' => l('I do not want to place an order at this time.', 'user/' . $account->uid . '/program-manager')),
    'submit' => array(
      '#type'       => 'submit',
      '#value'      => 'Complete My At Home Order',
    ),
  );

  return $form;
}

function at_home_subscription_at_home_ship_now_form_submit($form, &$form_state) {
  set_time_limit(0);

  $account_info = at_home_subscription_account_info_load($form_state['build_info']['args'][0]);
  at_home_subscription_process_automatic_order($account_info);

  drupal_set_message('Your order has been placed!');
  $form_state['redirect'] = 'user/' . $account_info['account']->uid . '/program-manager';
}
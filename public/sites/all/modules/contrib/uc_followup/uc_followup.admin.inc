<?php

/**
 * @file
 * UC Follow-up admin forms and settings.
 */

/**
 * List all follow-ups.
 */
function uc_followup_list() {
  $query = db_select('uc_followup', 'f');
  $query->fields('f', array('followup_id', 'order_status', 'effective_date', 'hours_past', 'name', 'status', 'repeat_after', 'repeat_max'));
  $query->orderBy('f.status', 'DESC');
  $result = $query->execute();

  $header = array(t('Name'), t('Triggers on'), t('Effective date'), t('Status'), '');
  $rows = array();
  while ($followup = $result->fetchAssoc()) {
    $row = array();
    $row['name'] = check_plain($followup['name']);
    $row['triggers'] = t('%hours after order became %status', array('%hours' => format_plural($followup['hours_past'], '1 hour', '@count hours'), '%status' => uc_order_status_data($followup['order_status'], 'title')));
    $row['triggers'] .= $followup['repeat_after'] ? ' (' . t('repeat every %hours %times', array('%hours' => format_plural($followup['repeat_after'], '1 hour', '@count hours'), '%times' => $followup['repeat_max'] ? format_plural($followup['repeat_max'], '1 time', '@count times') : '')) . ')' : '';
    $row['effective'] = $followup['effective_date'] ? date('m/d/Y', $followup['effective_date']) : '';
    $row['status'] = variable_get('uc_followup_auto_send', TRUE) ? $followup['status'] ? t('Auto') : t('Manual') : t('Manual (global)');
    $row['operations'] = l(t('Edit'), 'admin/store/follow-up/edit/' . $followup['followup_id']) . ' &nbsp; &nbsp; &nbsp ' . l(t('Delete'), 'admin/store/follow-up/delete/' . $followup['followup_id']);
    $rows[] = $row;
  }

  if (!empty($rows)) {
    $output = theme('table', array('header' => $header, 'rows' => $rows));
  }
  else {
    $output = t('No follow-ups were created yet. <a href="!create-url">Create one?</a>', array('!create-url' => url('admin/store/follow-up/add')));
  }

  return $output;
}

/**
 * Follow-up compose/update form.
 */
function uc_followup_form($form, &$form_state, $followup = NULL) {
  if (isset($followup)) {
    if (isset($form_state['values'])) {
      $edit = $form_state['values'];
    }
    else {
      $edit = $followup;
    }

    $form['followup_id'] = array(
      '#type' => 'hidden',
      '#value' => $edit['followup_id'],
    );
    $btn = t('Update');
  }
  else {
    $btn = t('Create');
  }

  $form['name'] = array(
    '#type' => 'textfield',
    '#title' => t('Name'),
    '#default_value' => isset($edit['name']) ? $edit['name'] : NULL,
    '#description' => t("A brief description of your follow-up."),
    '#required' => TRUE,
  );

  foreach (uc_order_status_list() as $status) {
    $statuses[$status['id']] = $status['title'];
  }
  $form['order_status'] = array(
    '#type' => 'select',
    '#title' => t('Order status'),
    '#multiple' => FALSE,
    '#options' => $statuses,
    '#default_value' => isset($edit['order_status']) ? $edit['order_status'] : NULL,
    '#required' => TRUE,
  );

  // Effective date
  $effective_date = isset($edit['effective_date']) ? $edit['effective_date'] : REQUEST_TIME;
  $form['effective_date'] = array(
    '#type' => 'textfield',
    '#title' => t('Effective date'),
    '#description' => t('When should this followup start sending emails?'),
    '#default_value' => date('Y-m-d H:i:s', $effective_date),
    '#required' => TRUE,
  );

  // Use the date_popup module if it's available.
  if (module_exists('date_popup')) {
    $form['effective_date']['#type'] = 'date_popup';
    $form['effective_date'] += array(
      '#date_format' => 'Y-m-d H:i:s',
      '#date_type' => DATE_UNIX,
      '#date_timezone' => date_default_timezone(),
      '#date_year_range' => '-3:+3',
    );
  }

  $form['hours_past'] = array(
    '#type' => 'textfield',
    '#title' => t('Hours past'),
    '#default_value' => isset($edit['hours_past']) ? $edit['hours_past'] : 24,
    '#description' => t("Amount of hours needed to pass starting from the moment when order got status selected above in order to trigger follow-up."),
    '#required' => TRUE,
  );
  $form['repeat'] = array(
    '#type' => 'fieldset',
    '#title' => t('Repeat'),
    '#collapsible' => TRUE,
    '#collapsed' => !isset($edit['repeat_after']) || !$edit['repeat_after'],
    '#description' => t("<b>Warning!</b> If you will send too many follow-ups you will probably get banned by SPAM filters! So, do not overdo with repeats!"),
  );
  $form['repeat']['repeat_after'] = array(
    '#type' => 'textfield',
    '#title' => t('Repeat every N hours'),
    '#default_value' => isset($edit['repeat_after']) ? $edit['repeat_after'] : 0,
    '#description' => t("Amount of hours from last follow-up after which new follow-up will be send again. Zero or blank if you don't want to send any repeat."),
  );
  $form['repeat']['repeat_max'] = array(
    '#type' => 'textfield',
    '#title' => t('...but only X times'),
    '#default_value' => isset($edit['repeat_max']) ? $edit['repeat_max'] : 0,
    '#description' => t("Maximum amount of follow-ups to send (not includes initial one). Zero means unlimited."),
  );

  // Option for limiting the followup to the customer's latest order.
  $form['last_order'] = array(
    '#type' => 'checkbox',
    '#title' => t('Only send a followup for the customer\'s most recent order that matches these criteria'),
    '#description' => t('If this is disabled, customers will receive this followup for all orders that belong to them. It it\'s enabled, only the customer\'s most recent order will trigger a followup. This is helpful if you want to avoid sending multiple followups to a customer that may have multiple abandoned orders (for example).'),
    '#default_value' => isset($edit['last_order']) ? $edit['last_order'] : 0,
  );

  $form['message'] = array(
    '#type' => 'fieldset',
    '#title' => t('Letter content'),
  );
  $form['message']['sender'] = array(
    '#type' => 'textfield',
    '#title' => t('From'),
    '#default_value' => isset($edit['sender']) ? $edit['sender'] : NULL,
    '#description' => t('The "From" e-mail address. Example: <em>John Smith &lt;john@smith.com&gt;</em>. If blank, store defaults (%default) will be used.', array('%default' => uc_store_email_from())),
  );
  $form['message']['subject'] = array(
    '#type' => 'textfield',
    '#title' => t('Subject'),
    '#default_value' => isset($edit['subject']) ? $edit['subject'] : NULL,
    '#required' => TRUE,
  );
  $form['message']['body'] = array(
    '#type' => 'text_format',
    '#title' => t('Body'),
    '#default_value' => isset($edit['body']) ? $edit['body'] : NULL,
    '#format' => isset($edit['body_format']) ? $edit['body_format'] : NULL,
    '#required' => TRUE,
    '#rows' => 20,
  );

  if (module_exists('token')) {
    $form['message']['tokens'] = array(
      '#type' => 'fieldset',
      '#title' => t('Replacement patterns'),
      '#description' => t('You can make use of the replacement patterns in the e-mail from and recipients fields, the subject, and the message body.'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    );
    $form['message']['tokens']['token_tree'] = array(
      '#theme' => 'token_tree',
      '#token_types' => array('uc_order'),
    );
  }
  else {
    $form['message']['tokens'] = array(
      '#markup' => '<p>' . t('Enable the <a href="@drupal-token">Token module</a> to view the available token browser.', array('@drupal-token' => 'http://drupal.org/project/token')) . '</p>',
    );
  }

  $form['status'] = array(
    '#type' => 'checkbox',
    '#title' => t('Active'),
    '#default_value' => isset($edit['status']) ? $edit['status'] : TRUE,
    '#required' => TRUE,
  );

  $form['actions'] = array('#type' => 'actions');
  $form['actions']['submit'] = array(
    '#type' => 'submit',
    '#value' => $btn,
  );

  return $form;
}

/**
 * Validate follow-up.
 */
function uc_followup_form_validate($form, &$form_state) {

  // Make sure the user has access to the selected text format.
  $formats = filter_formats();
  if (!filter_access($formats[$form_state['values']['body']['format']])) {
    form_set_error('body', 'You do not have permission to use the selected text format. Please choose one that you are allowed to use.');
  }

  // Convert the timestamp to unix-style and validate
  $form_state['values']['effective_date'] = strtotime($form_state['values']['effective_date']);
  if ($form_state['values']['effective_date'] === FALSE) {
    form_set_error('effective_date', 'Invalid timestamp.');
  }
}

/**
 * Save a follow-up to database.
 */
function uc_followup_form_submit($form, &$form_state) {

  // Copy the form values to a new followup object.
  $followup = $form_state['values'];

  // Move the the body and format values so they are saved correctly.
  $followup['body_format'] = $followup['body']['format'];
  $followup['body'] = $followup['body']['value'];

  // If we're saving an existing followup, update it.
  if (isset($followup['followup_id'])) {
    drupal_write_record('uc_followup', $followup, 'followup_id');
    drupal_set_message(t('Follow-up updated.'));
  }

  // Or, if we're saving a new one, insert it.
  else {
    drupal_write_record('uc_followup', $followup);
    drupal_set_message(t('Follow-up created.'));
  }

  // Redirect back to the followup page.
  $form_state['redirect'] = 'admin/store/follow-up';
}

/**
 * Follow-up removal confirmation.
 */
function uc_followup_delete_confirm($form, &$form_state, $followup) {
  $form['followup_id'] = array(
    '#type' => 'value',
    '#value' => $followup['followup_id'],
  );

  return confirm_form($form, t('Are you sure you want to delete test @name?', array('@name' => $followup['name'])), 'admin/store/follow-up', NULL, t('Delete'));
}

/**
 * Submit detelete confirm — remove a follow-up from database.
 */
function uc_followup_delete_confirm_submit($form, &$form_state) {
  db_delete('uc_followup')->condition('followup_id', $form_state['values']['followup_id'])->execute();
  db_delete('uc_followup_sent')->condition('followup_id', $form_state['values']['followup_id'])->execute();

  drupal_set_message(t('Follow-up removed.'));
  $form_state['redirect'] = 'admin/store/follow-up';
}

/**
 * Compose message form.
 */
function uc_followup_customer($order) {
  $output = '';

  // Select pending followups for this order.
  $select = array(
    array('f', 'name'),  // Followup name
    array('f', 'status'),  // Followup status
    array('f', 'hours_past'),  // Followup hours past
    array('f', 'repeat_after'),  // Followup repeat after
    array('f', 'repeat_max'),  // Followup repeat max
    array('o', 'order_status'),  // Order status
  );
  $where = array(
    array('o.order_id', $order->order_id),  // Only return results matching this order.
  );
  $result = uc_followup_query_helper($select, $where);

  while ($data = $result->fetchAssoc()) {
    $pending[$data['followup_id']] = $data;
  }
  // Render pending follow-ups
  $output .= '<h2>' . t('Pending follow-ups') . '</h2>';
  if (!empty($pending)) {
    $header = array(t('Follow-up'), t('Triggers on'), t('Status'), '');
    $rows = array();
    foreach ($pending as $followup) {
      $row = array();
      $row['name'] = check_plain($followup['name']);
      $row['triggers'] = t('%hours after order became %status', array('%hours' => format_plural($followup['hours_past'], '1 hour', '@count hours'), '%status' => uc_order_status_data($followup['order_status'], 'title')));
      $row['triggers'] .= $followup['repeat_after'] ? ' (' . t('repeat every %hours %times', array('%hours' => format_plural($followup['repeat_after'], '1 hour', '@count hours'), '%times' => $followup['repeat_max'] ? format_plural($followup['repeat_max'], '1 time', '@count times') : '')) . ')' : '';
      $row['status'] = variable_get('uc_followup_auto_send', TRUE) ? $followup['status'] ? t('Auto') : t('Manual') : t('Manual (global)');
      $row['operations'] = l(t('Preview & send'), 'admin/store/orders/' . $order->order_id . '/follow-up/send/' . $followup['followup_id']) . ' &nbsp; &nbsp; &nbsp ' . l(t('Skip'), 'admin/store/orders/' . $order->order_id . '/follow-up/skip/' . $followup['followup_id']);
      $rows[] = $row;
    }
    $output .= theme('table', array('header' => $header, 'rows' => $rows));
  }
  else {
    $output .= '<p>' . t('No follow-ups were scheduled for this stage.') . '</p>';
  }

  // Load sent follow-ups
  $query = db_select('uc_followup_sent', 'fs');
  $query->join('uc_followup', 'f');
  $query->fields('fs', array('date', 'email', 'follow_id'));
  $query->fields('f', array('name', 'status'));
  $query->condition('fs.order_id', $order->order_id);
  $query->orderBy('fs.date');
  $query->orderBy('f.order_status');
  $query->orderBy('f.status');
  $result = $query->execute();
  while ($data = $result->fetchAssoc()) {
    $processed[] = $data;
  }
  // Render sent follow-ups
  $output .= '<h2>' . t('Processed follow-ups') . '</h2>';
  if (!empty($processed)) {
    $header = array(t('Date'), t('Follow-up'), t('Status'), '');
    $rows = array();
    foreach ($processed as $followup) {
      $row = array();
      $row['date'] = format_date($followup['date']);
      $row['name'] = check_plain($followup['name']);
      $row['status'] = $followup['email'] ? t('Sent') : t('Skipped');
      $row['operations'] = l(t('Details'), 'admin/store/orders/' . $order->order_id . '/follow-up/details/' . $followup['follow_id']);
      $rows[] = $row;
    }
    $output .= theme('table', array('header' => $header, 'rows' =>$rows));
  }
  else {
    $output .= '<p>' . t('No follow-ups were sent on this stage yet.') . '</p>';
  }

  $replacements = uc_followup_get_token_replacements($order);
  drupal_set_title(t('Follow-ups for #@order (@customer)', array('@order' => $order->order_id, '@customer' => token_replace('[uc_order:first-name] [uc_order:last-name]', $replacements))), PASS_THROUGH);

  return $output;
}

/**
 * Global module's settings form.
 */
function uc_followup_settings() {
  $form = array();

  $form['uc_followup_auto_send'] = array(
    '#type' => 'checkbox',
    '#title' => t('Send follow-ups automatically (via cron run)'),
    '#description' => t('If disabled, follow-ups could only be send manually through the order admin interface. Usefull when full control over the messaging with customers needed.'),
    '#default_value' => variable_get('uc_followup_auto_send', TRUE),
  );
  $form['uc_followup_send_limit'] = array(
    '#type' => 'textfield',
    '#title' => t('Maximum number of letters sent by one cron run'),
    '#default_value' => variable_get('uc_followup_send_limit', 100),
    '#description' => t('Large amount of simultaneous sendings can be threated as SPAM by some of email services.'),
  );
  $form['uc_followup_bcc'] = array(
    '#type' => 'textfield',
    '#title' => t('Bcc email adress'),
    '#description' => t('Blind carbon copy is the e-mail address which will receive the copy of letter without giving beign tracked by original recepient.'),
    '#default_value' => variable_get('uc_followup_bcc', ''),
  );

  return system_settings_form($form);
}

/**
 * Validate global settings form.
 */
function uc_followup_settings_validate($form, &$form_state) {
  if (!is_numeric($form_state['values']['uc_followup_send_limit']) || $form_state['values']['uc_followup_send_limit'] < 0) {
    form_set_error('uc_followup_send_limit', t('Should be positive number or zero.'));
  }
  if (!empty($form_state['values']['uc_followup_bcc']) && !valid_email_address($form_state['values']['uc_followup_bcc'])) {
    form_set_error('uc_followup_bcc', t('The e-mail address %mail is not valid.', array('%mail' => $form_state['values']['uc_followup_bcc'])));
  }
}

/**
 * Review & send follow-up to customer.
 */
function uc_followup_send($form, &$form_state, $order, $followup = NULL, $follow_id = NULL) {
  if (isset($form_state['values'])) {
    $edit = $form_state['values'];
  }
  else {
    // New follow-up message.
    if (!empty($followup)) {
      $edit = $followup;
    }
    // Sent follow-up details.
    elseif ($follow_id) {
      $edit = uc_followup_sent_details($follow_id);
    }
  }
  $form['#order'] = $order;
  $form['#followup'] = $followup;

  $replacements = uc_followup_get_token_replacements($order);

  // Apply token replacements.
  $edit['sender'] = token_replace($edit['sender'], $replacements);
  $edit['subject'] = token_replace($edit['subject'], $replacements);
  $edit['body'] = token_replace($edit['body'], $replacements);
  $edit['order-email'] = token_replace('[uc_order:email]', $replacements);

  // Filter the body text with the selected text format.
  $format = isset($edit['body_format']) ? $edit['body_format'] : NULL;
  $edit['body'] = check_markup($edit['body'], $format);

  if (isset($edit['date'])) {
    if (isset($edit['email'])) {
      $form['email_sent'] = array(
        '#type' => 'item',
        '#title' => t('Sent to'),
        '#default_value' => $edit['email'],
      );
    }
    else {
      $form['email_sent'] = array(
        '#type' => 'item',
        '#title' => t('Sent to'),
        '#markup' => t('This follow-up was skipped.'),
      );
    }
  }
  else {
    $form['email'] = array(
      '#type' => 'textfield',
      '#title' => t('Send to'),
      '#default_value' => $edit['order-email'],
    );
  }

  $form['sender'] = array(
    '#type' => 'textfield',
    '#title' => t('From'),
    '#default_value' => !empty($edit['sender']) ? $edit['sender'] : uc_store_email_from(),
    '#description' => t('The "From" e-mail address. Example: <em>John Smith &lt;john@smith.com&gt;</em>. If blank, store defaults (%default) will be used.', array('%default' => uc_store_email_from())),
  );
  $form['subject'] = array(
    '#type' => 'textfield',
    '#title' => t('Subject'),
    '#default_value' => $edit['subject'],
    '#required' => TRUE,
  );
  $form['body'] = array(
    '#type' => 'textarea',
    '#title' => t('Body'),
    '#default_value' => $edit['body'],
    '#required' => TRUE,
    '#rows' => 20,
  );

  if (isset($edit['date'])) {
    $form['forward'] = array(
      '#type' => 'fieldset',
      '#title' => t('Forward'),
      '#description' => '<p>' . t('You may edit the message before forwarding it to e-mail listed below.') . '</p>',
      '#prefix' => '<div class="container-inline">',
      '#suffix' => '</div>',
    );
    $form['forward']['email'] = array(
      '#type' => 'textfield',
      '#title' => t('Forward to'),
      '#default_value' => isset($edit['email']) ? $edit['email'] : $edit['order-email'],
    );
  }

  // Form actions
  $form['actions'] = array('#type' => 'actions');
  $form['actions']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Send'),
  );

  if (!isset($edit['date'])) {
    $form['actions']['back'] = array(
      '#markup' => l(t('Cancel'), 'admin/store/orders/' . $order->order_id . '/follow-up'),
    );
  }

  return $form;
}

/**
 * Validate follow-up.
 */
function uc_followup_send_validate($form, &$form_state) {
  if (isset($form_state['values']['email']) && !valid_email_address($form_state['values']['email'])) {
    form_set_error('emal', t('The e-mail address %mail is not valid.', array('%mail' => $form_state['values']['emal'])));
  }
}

/**
 * Send follow-up.
 */
function uc_followup_send_submit($form, &$form_state) {
  $order = $form['#order'];
  $followup = $form['#followup'];
  $replacements = uc_followup_get_token_replacements($order);

  $settings = $form_state['values'];

  $sent = drupal_mail('uc_followup', '', $settings['email'], '', $settings, empty($settings['sender']) ? uc_store_email_from() : $settings['sender']);
  if (!$sent['result']) {
    watchdog('ca', 'Attempt to e-mail invoice for order @order_id to @email failed.', array('@email' => $settings['email'], '@order_id' => $order->order_id), WATCHDOG_ERROR);
  }
  else {
    // If follow-up object is empty, than we're sending forward.
    if (empty($followup)) {
      drupal_set_message(t('Message sent.'));
    }
    // else we should save data about this message
    else {
      $settings['date'] = REQUEST_TIME;
      $settings['followup_id'] = $followup['followup_id'];
      $settings['order_id'] = $order->order_id;
      $settings['manual'] = TRUE;
      drupal_write_record('uc_followup_sent', $settings);
      drupal_set_message(t('Follow-up was sent successfully.'));
    }
    $form_state['redirect'] = 'admin/store/orders/' . $order->order_id . '/follow-up';
  }
}

/**
 * Follow-up skip confirmation.
 */
function uc_followup_skip_confirm($form, &$form_state, $order, $followup) {
  $replacements = uc_followup_get_token_replacements($order);

  // Apply token replacements to from and recipient e-mail addressses.
  $form['#sender'] = token_replace($followup['sender'], $replacements);
  $form['#subject'] = token_replace($followup['subject'], $replacements);
  $form['#body'] = token_replace($followup['body'], $replacements);

  // Filter the body text with the selected text format.
  $form['#body'] = check_markup($form['#body'], $followup['body_format']);

  $form['#order'] = $order;
  $form['#followup'] = $followup;
  return confirm_form($form, t('Are you sure you want to skip followup @name for order #@order (@customer)?', array('@name' => $followup['name'], '@order' => $order->order_id, '@customer' => token_replace('[uc_order:first-name] [uc_order:last-name]', $replacements))), 'admin/store/follow-up', NULL, t('Skip follow-up'));
}

/**
 * Skip a follow-up.
 */
function uc_followup_skip_confirm_submit($form, &$form_state) {
  $order = $form['#order'];
  $followup = $form['#followup'];

  $data = array(
    'order_id' => $order->order_id,
    'followup_id' => $followup['followup_id'],
    'manual' => 1,
    'date' => REQUEST_TIME,
    'sender' => $form['#sender'],
    'subject' => $form['#subject'],
    'body' => $form['#body'],
  );
  drupal_write_record('uc_followup_sent', $data);
  drupal_set_message(t('Follow-up skipped.'));

  $form_state['redirect'] = 'admin/store/orders/' . $order->order_id . '/follow-up';
}
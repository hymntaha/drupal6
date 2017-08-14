<?php

function video_admin_settings_form($form, &$form_state) {

  $form['amazon_public_key'] = array(
    '#title' => t('Amazon Public Key'),
    '#type' => 'textfield',
    '#default_value' => variable_get('ytu_amazon_public_key', ''),
  );

  $form['amazon_secret_key'] = array(
    '#title' => t('Amazon Secret Key'),
    '#type' => 'textfield',
    '#default_value' => variable_get('ytu_amazon_secret_key', ''),
  );

  $form['amazon_private_stream'] = array(
    '#title' => t('Amazon Private Stream URL'),
    '#type' => 'textfield',
    '#default_value' => variable_get('ytu_amazon_private_stream_url', ''),
  );

  $form['amazon_private_download'] = array(
    '#title' => t('Amazon Private Download URL'),
    '#type' => 'textfield',
    '#default_value' => variable_get('ytu_amazon_private_download_url', ''),
  );

  $form['amazon_public_stream'] = array(
    '#title' => t('Amazon Public Stream URL'),
    '#type' => 'textfield',
    '#default_value' => variable_get('ytu_amazon_public_stream_url', ''),
  );

  $form['amazon_public_download'] = array(
    '#title' => t('Amazon Public Download URL'),
    '#type' => 'textfield',
    '#default_value' => variable_get('ytu_amazon_public_download_url', ''),
  );

  $form['amazon_private_key_filename'] = array(
    '#title' => t('Amazon Private Key Filename'),
    '#type' => 'textfield',
    '#default_value' => variable_get('ytu_amazon_private_key_filename', ''),
  );

  $form['amazon_key_pair_id'] = array(
    '#title' => t('Amazon Key Pair ID'),
    '#type' => 'textfield',
    '#default_value' => variable_get('ytu_amazon_key_pair_id', ''),
  );

  $form['quickfix_intro_video_nid'] = array(
    '#title' => t('Quickfix video intro nid'),
    '#type' => 'textfield',
    '#default_value' => variable_get('quickfix_intro_video_nid', ''),
  );

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => 'Submit',
  );

  return $form;
}

function video_admin_settings_form_submit($form, &$form_state) {
  drupal_set_message(t('Video Settings Updated'));
  $form_state['values'] = array_map('trim', $form_state['values']);
  variable_set('ytu_amazon_public_key', $form_state['values']['amazon_public_key']);
  variable_set('ytu_amazon_secret_key', $form_state['values']['amazon_secret_key']);
  variable_set('ytu_amazon_private_stream_url', $form_state['values']['amazon_private_stream']);
  variable_set('ytu_amazon_private_download_url', $form_state['values']['amazon_private_download']);
  variable_set('ytu_amazon_public_stream_url', $form_state['values']['amazon_public_stream']);
  variable_set('ytu_amazon_public_download_url', $form_state['values']['amazon_public_download']);
  variable_set('ytu_amazon_private_key_filename', $form_state['values']['amazon_private_key_filename']);
  variable_set('ytu_amazon_key_pair_id', $form_state['values']['amazon_key_pair_id']);
  variable_set('quickfix_intro_video_nid', $form_state['values']['quickfix_intro_video_nid']);
}

function video_user_settings_form($form, &$form_state, $user) {

  $userVideoManager = new UserVideoManager($user->uid);

  $defaults = array();
  foreach ($userVideoManager->getVideos() as $userVideo) {
    $defaults[$userVideo->getVideoNid()] = $userVideo->getVideoNid();
  }

  $form['uid'] = array(
    '#type' => 'hidden',
    '#value' => $user->uid,
  );

  $video_defaults = array();

  foreach ($userVideoManager->getVideos() as $userVideo) {
    $video_defaults[] = $userVideo->getVideoNid();
  }

  $form['videos'] = array(
    '#type' => 'checkboxes',
    '#title' => t('User Videos'),
    '#options' => video_get_video_options($user->uid),
    '#default_value' => $video_defaults,
  );

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Update User Videos'),
  );

  return $form;
}

function video_user_settings_form_submit($form, &$form_state) {
  $account = user_load($form_state['values']['uid']);
  drupal_set_message(t('Videos for ' . $account->field_first_name[LANGUAGE_NONE][0]['safe_value'] . ' (' . $account->mail . ') have been updated.'));

  $userVideoManager = new UserVideoManager($account->uid);

  foreach ($form_state['values']['videos'] as $video_nid => $checked) {
    if ($checked) {
      $userVideoManager->addVideo($video_nid);
    }
    else {
      $userVideoManager->deleteVideo($video_nid);
    }
  }
}

function video_user_playlist_settings_form($form, &$form_state, $user) {

  $userVideoManager = new UserVideoManager($user->uid);

  $defaults = array();
  foreach ($userVideoManager->getVideos() as $userVideo) {
    $defaults[$userVideo->getVideoNid()] = $userVideo->getVideoNid();
  }

  $form['uid'] = array(
    '#type' => 'hidden',
    '#value' => $user->uid,
  );

  $playlists = array();

  foreach ($userVideoManager->getPlaylists() as $userPlaylist) {
    $playlists[] = $userPlaylist->getPlaylistNid();
  }

  $form['playlists'] = array(
    '#type' => 'checkboxes',
    '#title' => t('User Playlists'),
    '#options' => video_get_playlist_options(),
    '#default_value' => $playlists,
  );

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Update User Playlists'),
  );

  return $form;
}

function video_user_playlist_settings_form_submit($form, &$form_state) {
  $account = user_load($form_state['values']['uid']);
  drupal_set_message(t('Playlists for ' . $account->field_first_name[LANGUAGE_NONE][0]['safe_value'] . ' (' . $account->mail . ') have been updated.'));

  $userVideoManager = new UserVideoManager($account->uid);

  foreach ($form_state['values']['playlists'] as $playlist_nid => $checked) {
    if ($checked) {
      if (!$userVideoManager->hasPlaylist($playlist_nid)) {
        $playlist = node_load($playlist_nid);
        $userVideoManager->addPlaylistFromNode($playlist);
      }
    }
    else {
      foreach ($userVideoManager->getPlaylists() as $userPlaylist) {
        if ($userPlaylist->getPlaylistNid() == $playlist_nid) {
          $userPlaylist->delete(TRUE);
        }
      }
    }
  }
}

function video_get_video_options($uid = '') {
  $videos = array();

  $query = db_select('node', 'n')
    ->fields('n', array('nid'))
    ->condition('n.type', 'video')
    ->condition('n.status', NODE_PUBLISHED);

  $video_segments = $query->execute();

  foreach ($video_segments as $row) {
    $video_segment = node_load($row->nid);

    if($video_segment->title == 'About Yoga Tune Up'){
      continue;
    }

    $series = '';
    if($field = field_get_items('node', $video_segment, 'field_video_series')){
      $term = taxonomy_term_load($field[0]['tid']);
      $series = $term->name;
    }

    $videos[$video_segment->nid] = $series ? '(' . $series . ') - ' . $video_segment->title : $video_segment->title;
  }

  asort($videos);

  /* If the video was purchased, display the order ID next to the video */
  if (!empty($uid)) {
    foreach ($videos as $nid => $title) {
      $order_id = db_select('users_video_segments', 'uv')
        ->fields('uv', array('order_id'))
        ->condition('uv.uid', $uid)
        ->condition('uv.video_nid', $nid)
        ->execute()->fetchField();

      if (!empty($order_id)) {
        $videos[$nid] .= ' (Order #' . l($order_id, 'admin/store/orders/' . $order_id) . ')';
      }
    }
  }

  return $videos;
}

function video_get_playlist_options() {
  $playlists = array();

  $query = db_select('node', 'n')
    ->fields('n', array('nid', 'title'))
    ->condition('n.type', 'playlist')
    ->condition('n.status', NODE_PUBLISHED)
    ->orderBy('title', 'ASC');

  $result = $query->execute();

  foreach ($result as $row) {
    $playlists[$row->nid] = $row->title;
  }

  return $playlists;
}

function video_admin_clean_up_form($form, &$form_state){
  $form['submit'] = array(
    '#value' => 'Run Video Cleanup',
    '#type' => 'submit',
  );

  return $form;
}

function video_admin_clean_up_form_submit($form, &$form_state){
  video_clean_up_duplicates();
  drupal_set_message('Video cleanup complete');
}

function video_admin_migrate_data_form($form, &$form_submit) {
  $form['submit'] = array(
    '#value' => 'Run User Video Migration',
    '#type' => 'submit',
  );

  return $form;
}

function video_admin_migrate_data_form_submit($form, &$form_submit) {
  set_time_limit(0);

  $qfix_map = array();

  $results = db_select('users_videos', 'v')
    ->fields('v')
    ->execute();

  foreach ($results as $row) {
    $userVideoManager = new UserVideoManager($row->uid);

    $video = node_load($row->video_nid);
    $video_segment_nid = '';

    switch ($video->type) {
      case 'product':
        if (!isset($qfix_map[$video->nid])) {
          $query = new EntityFieldQuery();
          $query->entityCondition('entity_type', 'node')
            ->entityCondition('bundle', 'video')
            ->fieldCondition('field_video_file_name', 'value', $video->field_video_file_name[LANGUAGE_NONE][0]['value']);
          $video_segment = $query->execute();

          if (!empty($video_segment['node'])) {
            $video_segment = reset($video_segment['node']);
            $qfix_map[$video->nid] = $video_segment->nid;
          }
        }

        if (isset($qfix_map[$video->nid])) {
          $video_segment_nid = $qfix_map[$video->nid];
        }

        break;
      case 'video':
        $video_segment_nid = $video->nid;
        break;
    }

    if ($video_segment_nid) {
      if (!$userVideoManager->hasVideo($video_segment_nid)) {
        $userVideoManager->addVideo($video_segment_nid, $row->order_id);
      }
    }
  }

  $results = db_select('users_video_playlists', 'p')
    ->fields('p')
    ->execute();

  foreach ($results as $row) {
    $userVideoManager = new UserVideoManager($row->uid);
    if ($playlist = node_load($row->playlist_nid)) {
      if (!$userVideoManager->hasPlaylist($playlist->nid)) {
        $userVideoManager->addPlaylistFromNode($playlist, $row->order_id);
      }
    }
  }

  drupal_set_message('User video migration complete.');
}

function video_discount_admin_settings_form($form, &$form_state) {
  for ($i = 1; $i <= VIDEO_DISCOUNT_BREAKPOINTS; $i++) {
    $form['video_discount_' . $i] = array(
      '#type' => 'fieldset',
    );

    $form['video_discount_' . $i]['video_discount_breakpoint_' . $i] = array(
      '#type' => 'textfield',
      '#title' => 'Breakpoint',
      '#default_value' => variable_get('video_discount_breakpoint_' . $i, ''),
    );

    $form['video_discount_' . $i]['video_discount_amount_' . $i] = array(
      '#type' => 'textfield',
      '#title' => 'Amount (%)',
      '#default_value' => variable_get('video_discount_amount_' . $i, ''),
    );
  }

  return system_settings_form($form);
}

function video_discount_admin_settings_form_validate($form, &$form_state) {
  for ($i = 1; $i <= VIDEO_DISCOUNT_BREAKPOINTS; $i++) {
    if (!empty($form_state['values']['video_discount_breakpoint_' . $i]) && !is_numeric($form_state['values']['video_discount_breakpoint_' . $i])) {
      form_set_error('video_discount_breakpoint_' . $i, 'Please enter a numeric value.');
    }
    if (!empty($form_state['values']['video_discount_amount_' . $i]) && !is_numeric($form_state['values']['video_discount_amount_' . $i])) {
      form_set_error('video_discount_amount_' . $i, 'Please enter a numeric value.');
    }
  }
}

function video_import_admin_form($form, &$form_state) {
  $form['csv'] = array(
    '#type' => 'managed_file',
    '#title' => t('Upload a CSV file'),
    '#required' => TRUE,
    '#upload_location' => 'public://video_import',
    '#upload_validators' => array(
      'file_validate_extensions' => array('csv'),
    ),
  );

  $form['count_only'] = array(
    '#type' => 'checkbox',
    '#title' => t('Count Only?'),
    '#default_value' => TRUE,
  );

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Submit'),
  );

  return $form;
}

function video_import_admin_form_validate($form, &$form_state) {
  if (form_get_errors()) {
    return;
  }

  $file = file_load($form_state['values']['csv']);
  $csv = array_map('str_getcsv', file($file->uri));
  if ($file) {
    $validate = '';

    foreach ($csv as $num_row => $cols) {
      if (count($cols) != 14) {
        $validate .= format_string('Row !row: Invalid number of columns<br />', array('!row' => $num_row + 1));
      }

      if (empty($cols[3])) {
        $validate .= format_string('Row !row: Missing Image<br />', array('!row' => $num_row + 1));
      }

      if (!empty($cols[9]) && $cols[10] === '') {
        $validate .= format_string('Row !row: Missing Price<br />', array('!row' => $num_row + 1));
      }
    }

    if ($validate !== '') {
      form_set_error('csv', $validate);
    }
  }
  else {
    form_set_error('csv', 'Error uploading file.');
  }
}

function video_import_admin_form_submit($form, &$form_state) {
  set_time_limit(0);

  global $user;

  $video_add_count = 0;
  $video_update_count = 0;

  $file = file_load($form_state['values']['csv']);

  $terms['category'] = taxonomy_get_tree(taxonomy_vocabulary_machine_name_load('video_categories')->vid);
  $terms['activity'] = taxonomy_get_tree(taxonomy_vocabulary_machine_name_load('video_activity')->vid);
  $terms['body_focus'] = taxonomy_get_tree(taxonomy_vocabulary_machine_name_load('video_body_focus')->vid);
  $terms['series'] = taxonomy_get_tree(taxonomy_vocabulary_machine_name_load('video_series')->vid);

  if ($file) {
    $csv = array_map('str_getcsv', file($file->uri));
    file_delete($file);

    foreach ($csv as $num_row => $cols) {

      $query = new EntityFieldQuery();
      $query->entityCondition('entity_type', 'node')
        ->entityCondition('bundle', 'video')
        ->propertyCondition('title', $cols[0])
        ->fieldCondition('field_video_file_name', 'value', $cols[2])
        ->propertyOrderBy('nid', 'ASC');
      $video_segments = $query->execute();

      if (!empty($video_segments)) {
        foreach($video_segments['node'] as $video_segment){
          $video_update_count++;
          if ($form_state['values']['count_only']) {
            continue;
          }

          $video_segment = node_load($video_segment->nid);
          video_update_existing_video_from_csv_columns($video_segment, $terms, $cols);
        }
      }
      else {
        $video_add_count++;

        if ($form_state['values']['count_only']) {
          continue;
        }

        $values = array(
          'type' => 'video',
          'uid' => $user->uid,
          'status' => NODE_PUBLISHED,
          'comment' => COMMENT_NODE_HIDDEN,
          'promote' => NODE_NOT_PROMOTED,
        );

        $entity = entity_create('node', $values);
        $ewrapper = entity_metadata_wrapper('node', $entity);

        $ewrapper->title->set($cols[0]);
        $ewrapper->field_video_file_name->set($cols[2]);

        if (!empty($cols['11'])) {
          $ewrapper->field_video_sd_file_name->set($cols['11']);
        }

        $image_file = file_save_data(file_get_contents('http://' . AMAZON_PUBLIC_DOWNLOAD_URL . '/' . $cols[3]), 'public://' . $cols['3'], FILE_EXISTS_RENAME);
        $ewrapper->field_video_thumbnail->set(array('fid' => $image_file->fid));

        $taxonomy_map = array(
          4 => 'category',
          5 => 'body_focus',
          6 => 'activity',
          7 => 'series',
        );

        foreach ($taxonomy_map as $delta => $term_machine_name) {
          if (!empty($cols[$delta])) {
            $names = explode('|', $cols[$delta]);
            $to_add = array();
            foreach ($terms[$term_machine_name] as $term) {
              foreach ($names as $name) {
                $name = trim($name);
                if ($term->name == $name) {
                  $to_add[] = intval($term->tid);
                }
              }
            }

            if (!empty($to_add)) {
              $ewrapper->{'field_video_' . $term_machine_name}->set($to_add);
            }
          }
        }

        $ewrapper->field_run_time->set($cols['8']);

        $ewrapper->save();

        //Create video segment product if a SKU is provided
        if (!empty($cols['9'])) {
          $video_segment_nid = $ewrapper->getIdentifier();

          $values = array(
            'type' => 'video_segment_product',
            'uid' => $user->uid,
            'status' => NODE_PUBLISHED,
            'comment' => COMMENT_NODE_HIDDEN,
            'promote' => NODE_NOT_PROMOTED,
          );

          $entity = entity_create('node', $values);
          $ewrapper = entity_metadata_wrapper('node', $entity);

          $ewrapper->title->set($cols[0]);
          $ewrapper->body->set(array('value' => $cols[1]));
          $ewrapper->model->set($cols['9']);
          $ewrapper->sell_price->set($cols['10']);
          $ewrapper->field_video_segment->set($video_segment_nid);
          $ewrapper->shippable->set(0);
          $ewrapper->field_infusionsoft_product_id->set($cols['12']);

          $ewrapper->save();
        }

      }
    }

  }

  if ($form_state['values']['count_only']) {
    drupal_set_message($video_add_count . ' Videos will be created');
    drupal_set_message($video_update_count . ' Videos will be updated');
  }
  else {
    drupal_set_message($video_add_count . ' Videos Created');
    drupal_set_message($video_update_count . ' Videos Updated');
  }
}
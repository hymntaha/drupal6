<?php

/**
 * @param UserPlaylist $playlist
 * @return array
 */
function video_watch_page_callback($playlist){
  global $user;

  $userVideoManager = new UserVideoManager($user->uid);

  $render = array();

  $other_videos = array();

  foreach($userVideoManager->getPlaylists() as $user_playlist){
    if($user_playlist->getId() !== $playlist->getId()){
      $other_videos[] = array('#markup' => video_playlist_get_link($user_playlist));
    }
  }

  drupal_set_title($playlist->getName());

  $playlist_video_nids = array();
  foreach($playlist->getVideos() as $playlistVideo){
    $playlist_video_nids[] =$playlistVideo->getVideoNid();
  }
  $playlist_videos = node_load_multiple($playlist_video_nids);
  foreach($playlist_videos as $delta => $playlist_video){
    if(!$playlist->getEditable()){
      if($playlist_video->title == 'About YTU' || $playlist_video->title == 'About Yoga Tune Up'){
        unset($playlist_videos[$delta]);
      }
    }
  }

  if(!$playlist->getEditable() && $about_ytu = video_get_latest_about_ytu_video_segment()){
    if(strpos($playlist->getName(), 'At Home Program') === FALSE){
      $videos = array($about_ytu->nid => $about_ytu);

      foreach($playlist_videos as $playlist_video){
        $videos[$playlist_video->nid] = $playlist_video;
      }

      $playlist_videos = $videos;
    }
  }

  $playlist_videos_view = node_view_multiple($playlist_videos, 'playlist_segment');

  $playlist_array = video_get_playlist_embed($playlist);

  drupal_add_js(drupal_get_path('module', 'video').'/js/playlist.js');
  drupal_add_js(array('playlist' => array('playlist_id' => $playlist->getId())), 'setting');
  drupal_add_js(array('playlist' => array('playlist_json' => $playlist_array['playlist_json'])), 'setting');

  $render['watch'] = array(
    '#theme' => 'video_watch_playlist',
    '#embed' => array('#markup' => $playlist_array['markup']),
    '#videos' => $playlist_videos_view,
    '#extra_content' => array('#markup' => _mm('video_playlist_watch_extra_content', '')),
    '#other_videos' => $other_videos,
    '#playlist' => $playlist,
  );

  return $render;
}

function video_watch_video_page_callback($video_segment){
  $render = array();

  drupal_set_title('Video - '.$video_segment->title);

  $back_url = '/your-videos';
  if(isset($_GET['destination'])){
    $back_url = $_GET['destination'];
  }

  $render['back']['#markup'] = '<p><a href="'.$back_url.'">&lt; Back to Videos</a></p>';
  $render['embed'] = video_get_embed($video_segment->nid, 'private');

  return $render;
}

function video_watch_all_videos_page_callback(){
  global $user;

  $userVideoManager = new UserVideoManager($user->uid);
  $render = array();

  $render['form'] = drupal_get_form('video_segment_filter_form', $userVideoManager);

  foreach($userVideoManager->getVideos() as $userVideo){
    $video_segment = node_load($userVideo->getVideoNid());
    if($video_segment->title == 'About Yoga Tune Up'){
      continue;
    }
    $render['videos'][] = node_view($video_segment, 'video');
  }

  return $render;
}

function video_create_playlist($account){
  $userVideoManager = new UserVideoManager($account->uid);
  $videos = array();

  foreach($userVideoManager->getVideos() as $userVideo){
    $video_segment = node_load($userVideo->getVideoNid());
    if($video_segment->title == 'About Yoga Tune Up'){
      continue;
    }
    $videos[] = node_view($video_segment, 'teaser');
  }

  return array(
    '#theme' => 'video_playlist_manage',
    '#filter_form' => drupal_get_form('video_segment_filter_form', $userVideoManager),
    '#videos' => $videos,
    '#form' => drupal_get_form('video_custom_playlist_form', $userVideoManager),
  );
}

/**
 * @param UserPlaylist $playlist
 * @return array
 */
function video_edit_playlist($playlist){
  if(!$playlist->getEditable()){
    return array(
      '#markup' => '<p>This playlist is not editable.</p>',
    );
  }

  $userVideoManager = new UserVideoManager($playlist->getUid());
  $videos = array();

  foreach($userVideoManager->getVideos() as $userVideo){
    $video_segment = node_load($userVideo->getVideoNid());
    $videos[] = node_view($video_segment, 'teaser');
  }

  return array(
    '#theme' => 'video_playlist_manage',
    '#filter_form' => drupal_get_form('video_segment_filter_form', $userVideoManager),
    '#videos' => $videos,
    '#form' => drupal_get_form('video_custom_playlist_form', $userVideoManager, $playlist),
  );
}

/**
 * @param $form
 * @param $form_state
 * @param UserVideoManager $userVideoManager
 * @param UserPlaylist $playlist
 * @return mixed
 */
function video_custom_playlist_form($form, &$form_state, $userVideoManager, $playlist = NULL){
  $video_options = array();

  $form['#attached']['js'][] = array(
    'data' => drupal_get_path('module', 'video') . '/js/playlist_add_edit_form.js',
    'type' => 'file',
  );

  $form['playlist_sort']['#tree'] = TRUE;

  foreach($userVideoManager->getVideos() as $userVideo){
    $video_segment = node_load($userVideo->getVideoNid());
    $video_options[$video_segment->nid] = $video_segment->title;

    $form['playlist_sort'][$video_segment->nid]['name'] = array(
      '#markup' => $video_segment->title,
    );

    $form['playlist_sort'][$video_segment->nid]['weight'] = array(
      '#type' => 'weight',
      '#delta' => 25,
      '#attributes' => array('class' => array('weight')),
    );
  }

  $form['playlist_id'] = array(
    '#type' => 'hidden',
    '#value' => $playlist ? $playlist->getId() : '',
  );

  $default_videos = array();

  if($playlist){
    foreach ($playlist->getVideos() as $userPlaylistVideo){
      $default_videos[] = $userPlaylistVideo->getVideoNid();
      if(isset($form['playlist_sort'][$userPlaylistVideo->getVideoNid()]['weight'])){
        $form['playlist_sort'][$userPlaylistVideo->getVideoNid()]['weight']['#default_value'] = $userPlaylistVideo->getWeight();
      }
    }
  }

  $form['videos'] = array(
    '#title' => 'Videos',
    '#type' => 'checkboxes',
    '#options' => $video_options,
    '#default_value' => $default_videos,
  );

  $form['name'] = array(
    '#title' => 'Playlist Name',
    '#type' => 'textfield',
    '#default_value' => $playlist ? $playlist->getName() : '',
    '#required' => TRUE,
  );

  $form['actions'] = array(
    '#type' => 'actions',
  );

  $form['actions']['submit'] = array(
    '#type' => 'submit',
    '#value' => 'Save',
  );

  if($playlist){
    $form['actions']['delete'] = array(
      '#type' => 'submit',
      '#value' => 'Delete',
    );
  }

  return $form;
}

function video_custom_playlist_form_validate($form, &$form_state){
  $no_videos = TRUE;
  foreach($form_state['values']['videos'] as $nid => $value){
    if($value){
      $no_videos = FALSE;
      break;
    }
  }

  if($no_videos){
    form_set_error('', 'You must add at least one video.');
  }
}

function video_custom_playlist_form_submit($form, &$form_state){
  /** @var UserVideoManager $userVideoManager */
  $userVideoManager = $form_state['build_info']['args'][0];

  /** @var UserPlaylist|null $playlist */
  $playlist = isset($form_state['build_info']['args'][1]) ? $form_state['build_info']['args'][1] : NULL;

  if(is_null($playlist)){
    $playlist = new UserPlaylist($userVideoManager->getUid(), $form_state['values']['name'], 1);
    $playlist->save();
  }

  if($form_state['values']['op'] == 'Save'){
    foreach($playlist->getVideos() as $userPlaylistVideo){
      $userPlaylistVideo->delete();
    }

    foreach($form_state['values']['videos'] as $nid){
      if($nid){
        $userPlaylistVideo = new UserPlaylistVideo($playlist->getId(), $nid, $form_state['values']['playlist_sort'][$nid]['weight']);
        $userPlaylistVideo->save();
      }
    }

    if($playlist){
      drupal_set_message('Your playlist has been updated.');
    }
    else{
      drupal_set_message('Your playlist has been created.');
    }
  }
  else if($form_state['values']['op'] == 'Delete'){
    $playlist->delete();
    drupal_set_message('Your playlist has been deleted.');
  }

  $form_state['redirect'] = 'user';
}

/**
 * @param $account
 * @param $video_segment
 * @return string
 */
function video_favorite_video_ajax_callback($account, $video_segment){
  $userVideoManager = new UserVideoManager($account->uid);

  if($userVideoManager->hasVideo($video_segment->nid)){
    $userVideo = UserVideo::load($account->uid, $video_segment->nid);

    if($userVideo->getFavorite()){
      $userVideo->setFavorite(0);
    }
    else{
      $userVideo->setFavorite(1);
    }

    $userVideo->save();

    drupal_json_output($userVideo->toArray());
  }

  drupal_exit();
}

function video_create_video_package_callback(){
  global $user;

  $userVideoManager = new UserVideoManager($user->uid);
  $params = drupal_get_query_parameters();
  $extra_info = '';
  $video_count = 0;

  $vocabs = array(
    'video_categories' => 'category',
    'video_body_focus' => 'body_focus',
    'video_activity' => 'activity',
    'video_series' => 'series',
  );

  foreach($vocabs as $machine_name => $delta){
    if(!empty($extra_info)){
      continue;
    }

    if(isset($params[$delta])){
      $vocabulary = taxonomy_vocabulary_machine_name_load($machine_name);
      $terms = taxonomy_get_tree($vocabulary->vid);

      foreach($terms as $term){
        if(strtolower($params[$delta]) == strtolower($term->name)){
          $extra_info = $term->description;
          break;
        }
      }
    }
    else{
      $extra_info = _mm('video_create_package_no_selections','', TRUE);
    }
  }

  drupal_add_js(array('video' => array('no_selections_extra_info' => _mm('video_create_package_no_selections','', TRUE))), 'setting');

  $render = array(
    '#theme' => 'video_create_package',
    '#body' => _mm('video_create_package_body',''),
    '#extra_info' => $extra_info,
  );

  $nodes = video_get_video_segments_products();
  $items = uc_cart_get_contents();

  foreach($nodes as $node){
    $video_segment = node_load($node->field_video_segment[LANGUAGE_NONE][0]['target_id']);
    if(!$userVideoManager->hasVideo($video_segment->nid)){
      $in_cart = FALSE;
      foreach($items as $item){
        if($item->nid == $node->nid){
          $in_cart = TRUE;
        }
      }

      if(!$in_cart){
        $video_count++;
      }
    }
  }

  $render['#video_count'] = $video_count;

  $render['#filter_form'] = drupal_get_form('video_segment_filter_form', NULL, FALSE);

  foreach ($nodes as $node){
    $render['#videos'][] = node_view($node, 'teaser');
  }

  $render['#form'] = drupal_get_form('video_create_video_package_form');

  return $render;
}

function video_create_video_package_form($form, &$form_state){
  global $user;

  $userVideoManager = new UserVideoManager($user->uid);
  $video_options = array();
  $form['create_package_videos']['#tree'] = TRUE;

  $video_discount = video_get_video_discounts();

  $form['#attached']['js'][] = array(
    'data' => array('video' => array('video_discount' => $video_discount)),
    'type' => 'setting',
  );

  $form['#attached']['js'][] = array(
    'data' => drupal_get_path('module', 'video') . '/js/video_create_video_package_form.js',
    'type' => 'file',
  );

  foreach(video_get_video_segments_products() as $video_segment_product){
    $video_segment_nid = $video_segment_product->field_video_segment[LANGUAGE_NONE][0]['target_id'];

    if($userVideoManager->hasVideo($video_segment_nid)){
      continue;
    }

    $video_options[$video_segment_product->nid] = $video_segment_product->title;

    $form['create_package_videos'][$video_segment_product->nid]['name'] = array(
      '#markup' => $video_segment_product->title,
    );

    $form['create_package_videos'][$video_segment_product->nid]['price'] = array(
      '#markup' => theme('uc_price', array('price' => $video_segment_product->sell_price)),
    );
  }

  $form['videos'] = array(
    '#type' => 'checkboxes',
    '#title' => 'Videos',
    '#options' => $video_options,
  );

  $form['package_cost'] = array(
    '#markup' => '<div class="package-cost hidden">Package cost (<span id="num-selected-videos"></span>): <strong>$<span id="package-cost-price"></span></strong></div>',
  );

  $form['discount_info'] = array(
    '#markup' => '<div class="discount-info hidden">Add <span id="num-to-next-discount"></span> more video(s) for a <span id="discount-amount"></span>% discount</div>'
  );

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => 'Order Selected Videos',
  );

  $form['#validate'][] = 'video_custom_playlist_form_validate';

  return $form;
}

function video_create_video_package_form_submit($form, &$form_state){
  $num_videos = 0;

  foreach($form_state['values']['videos'] as $nid){
    if($nid){
      $num_videos++;
    }
  }

  foreach($form_state['values']['videos'] as $nid){
    if($nid){

      $data = NULL;
      if($discount = video_get_video_discount_level($num_videos)){
        $item = node_load($nid);
        $discount_price = (string) round($item->price - ($item->price * $discount['amount']/100), 2);

        $data['video_discount'][] = array(
          'original_price' => $item->price,
          'price' => $discount_price,
        );
      }

      $form_state['redirect'] = uc_cart_add_item($nid, 1, $data, NULL, variable_get('uc_cart_add_item_msg', TRUE));
    }
  }

}

function video_ajax_taxonomy_term_description_callback($term){
  drupal_json_output(array('description' => $term->description));
}
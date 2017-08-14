<?php

define('POSE_HISTORY_PAGINATION',25);

function pose_daily_admin_settings_page_callback(){
  return array(
    '#markup' => '<p>'.l('Exercise Management','admin/ytu-settings/daily-management/exercise').'</p>'.
      '<p>'.l('Pose Management','admin/ytu-settings/daily-management/pose').'</p>',
  );
}

function pose_admin_settings_page_callback(){
	$render['filter_form'] = drupal_get_form('pose_admin_filter_form');
	$render['results'] = pose_admin_settings_results_table();

	return $render;
}

function pose_exercise_admin_settings_page_callback(){
  $render['filter_form'] = drupal_get_form('pose_exercise_admin_filter_form');
  $render['results'] = pose_exercise_admin_settings_results_table();

  return $render;
}

function pose_admin_filter_form($form,&$form_state){
	$form['filter'] = array(
		'#type' => 'select',
		'#title' => t('View Options'),
		'#options' => array(
			'all' => 'View All Scheduled Poses',
			'past' => 'View Past Scheduled Poses',
			'future' => 'View Current/Future Scheduled Poses',
			'unscheduled' => 'View Unscheduled Poses',
		),
		'#default_value' => isset($_SESSION['pose_admin_filter_options']) ? key($_SESSION['pose_admin_filter_options']) : '',
	);

	$form['submit'] = array(
		'#type' => 'submit',
		'#value' => 'Submit',
	);

	return $form;
}

function pose_admin_filter_form_submit($form,&$form_state){
	$_SESSION['pose_admin_filter_options'] = array(
		$form_state['values']['filter'] => true,
	);
}

function pose_exercise_admin_filter_form($form,&$form_state){
  $form['filter'] = array(
    '#type' => 'select',
    '#title' => t('View Options'),
    '#options' => array(
      'all' => 'View All Scheduled Exercises',
      'past' => 'View Past Scheduled Exercises',
      'future' => 'View Current/Future Scheduled Exercises',
      'unscheduled' => 'View Unscheduled Exercises',
    ),
    '#default_value' => isset($_SESSION['pose_exercise_admin_filter_options']) ? key($_SESSION['pose_exercise_admin_filter_options']) : '',
  );

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => 'Submit',
  );

  return $form;
}

function pose_exercis_admin_filter_form_submit($form,&$form_state){
  $_SESSION['pose_exercise_admin_filter_options'] = array(
    $form_state['values']['filter'] => true,
  );
}

function pose_admin_settings_results_table($options = array()){

	if(isset($_SESSION['pose_admin_filter_options'])){
		$options = $_SESSION['pose_admin_filter_options'];
	}

	$options['header'] = array(
		'Pose' => array('data' => t('Pose'), 'field' => 'n.title'),
		'Start' => array('data' => t('Start'), 'field' => 'p.start','sort' => 'desc'),
		'End' => array('data' => t('End'), 'field' => 'p.end'),
		'View' => array('data' => t('View')),
		'Edit' => array('data' => t('Edit')),
	);

	$render['header'] = array(
		'pager'=>array(
			'#theme'=>'pager',
			'#quantity'=>5
		),
	);

	$render['poses'] = array(
		'#theme' => 'table',
		'#header' => $options['header'],
		'#rows' => pose_admin_settings_query($options),
		'#empty' => t('No Poses.'),
	);

	$render['footer'] = array(
		'pager'=>array(
			'#theme'=>'pager',
			'#quantity'=>5
		),
	);

	$render['#prefix'] = '<div id="poses">';
	$render['#suffix'] = '</div>';

	return $render;
}

function pose_exercise_admin_settings_results_table($options = array()){

  if(isset($_SESSION['pose_exercise_admin_filter_options'])){
    $options = $_SESSION['pose_exercise_admin_filter_options'];
  }

  $options['header'] = array(
    'Exercise' => array('data' => t('Exercise'), 'field' => 'n.title'),
    'Start' => array('data' => t('Start'), 'field' => 'p.start','sort' => 'desc'),
    'End' => array('data' => t('End'), 'field' => 'p.end'),
    'View' => array('data' => t('View')),
    'Edit' => array('data' => t('Edit')),
  );

  $render['header'] = array(
    'pager'=>array(
      '#theme'=>'pager',
      '#quantity'=>5
    ),
  );

  $render['poses'] = array(
    '#theme' => 'table',
    '#header' => $options['header'],
    '#rows' => pose_exercise_admin_settings_query($options),
    '#empty' => t('No Exercises.'),
  );

  $render['footer'] = array(
    'pager'=>array(
      '#theme'=>'pager',
      '#quantity'=>5
    ),
  );

  $render['#prefix'] = '<div id="poses">';
  $render['#suffix'] = '</div>';

  return $render;
}

function pose_admin_settings_query($options = array()){
	$rows = array();

	$destination = drupal_get_destination();

	if(!empty($options['unscheduled'])){
		$scheduled_nids = db_select('pose_dates','p')->fields('p',array('id','nid'))->isNull('end')->execute()->fetchAllKeyed();
		$query = db_select('node','n')->extend('PagerDefault')->limit(POSE_HISTORY_PAGINATION)->extend('TableSort')
				->fields('n',array('nid','title'))
				->condition('type','pose')
				->condition('nid',$scheduled_nids,'NOT IN');
	}
	else{
		$query = db_select('pose_dates','p')->extend('PagerDefault')->limit(POSE_HISTORY_PAGINATION)->extend('TableSort')->fields('p');
		$query->innerJoin('node','n','p.nid = n.nid');

		if(!empty($options['past'])){
			$query->isNotNull('p.end');
		}

		if(!empty($options['future'])){
			$query->isNull('p.end');
		}

		$query->fields('n',array('title'))->orderByHeader($options['header']);
	}

	$result = $query->execute();
	$old_row = array('pose' => '', 'start' => 0, 'end' => 0);

	foreach($result as $row){
		if($old_row['pose'] == $row->title){
			if($old_row['start'] == date('m/d/Y',$row->start) && $old_row['end'] == !empty($row->end) ? date('m/d/Y',$row->end) : ''){
				continue;
			}
		}
		if(!isset($row->id)){
			$row->id = $row->nid;
			$row->start = '';
			$row->end = '';
		}
		$rows[$row->id] = array(
			'pose' => $row->title,
			'start' => !empty($row->start) ? date('m/d/Y',$row->start) : '',
			'end' => !empty($row->end) ? date('m/d/Y',$row->end) : '',
		    'view' => array(
		      'data' => array(
		        '#type' => 'link',
		        '#title' => t('view'),
		        '#href' => "node/$row->nid",
		        '#options' => array('query' => $destination),
		      ),
		    ),
		    'edit' => array(
		      'data' => array(
		        '#type' => 'link',
		        '#title' => t('edit'),
		        '#href' => "node/$row->nid/edit",
		        '#options' => array('query' => $destination),
		      ),
		    ),
		);
		$old_row = $rows[$row->id];
	}

	return $rows;
}

function pose_exercise_admin_settings_query($options = array()){
  $rows = array();

  $destination = drupal_get_destination();

  if(!empty($options['unscheduled'])){
    $scheduled_nids = db_select('exercise_dates','p')->fields('p',array('id','nid'))->isNull('end')->execute()->fetchAllKeyed();
    $query = db_select('node','n')->extend('PagerDefault')->limit(POSE_HISTORY_PAGINATION)->extend('TableSort')
      ->fields('n',array('nid','title'))
      ->condition('type','exercise')
      ->condition('nid',$scheduled_nids,'NOT IN');
  }
  else{
    $query = db_select('exercise_dates','p')->extend('PagerDefault')->limit(POSE_HISTORY_PAGINATION)->extend('TableSort')->fields('p');
    $query->innerJoin('node','n','p.nid = n.nid');

    if(!empty($options['past'])){
      $query->isNotNull('p.end');
    }

    if(!empty($options['future'])){
      $query->isNull('p.end');
    }

    $query->fields('n',array('title'))->orderByHeader($options['header']);
  }

  $result = $query->execute();
  $old_row = array('pose' => '', 'start' => 0, 'end' => 0);

  foreach($result as $row){
    if($old_row['pose'] == $row->title){
      if($old_row['start'] == date('m/d/Y',$row->start) && $old_row['end'] == !empty($row->end) ? date('m/d/Y',$row->end) : ''){
        continue;
      }
    }
    if(!isset($row->id)){
      $row->id = $row->nid;
      $row->start = '';
      $row->end = '';
    }
    $rows[$row->id] = array(
      'pose' => $row->title,
      'start' => !empty($row->start) ? date('m/d/Y',$row->start) : '',
      'end' => !empty($row->end) ? date('m/d/Y',$row->end) : '',
      'view' => array(
        'data' => array(
          '#type' => 'link',
          '#title' => t('view'),
          '#href' => "node/$row->nid",
          '#options' => array('query' => $destination),
        ),
      ),
      'edit' => array(
        'data' => array(
          '#type' => 'link',
          '#title' => t('edit'),
          '#href' => "node/$row->nid/edit",
          '#options' => array('query' => $destination),
        ),
      ),
    );
    $old_row = $rows[$row->id];
  }

  return $rows;
}
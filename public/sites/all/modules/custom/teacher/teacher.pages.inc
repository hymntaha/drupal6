<?php

function teacher_roll_model_practitioners_page_callback($uid = null){
  $rows = array();

  foreach(teacher_get_roll_model_practitioners(TRUE, array('uid' => $uid,'limit' => 50)) as $uid => $rmm){
    $account = user_load($uid);
    if($nid = teacher_get_teacher_page_nid($account)){
      $teacher = node_load($nid);
      $name = $teacher->title;
      $email = field_get_items('node', $teacher, 'field_email');
      $email = $email[0]['value'];
      $address = field_get_items('node', $teacher, 'field_address');
    }
    else{
      $name = account_get_full_name($account);
      $address = field_get_items('user', $account, 'field_location');
      $email = $account->mail;
    }

    $address_formatted = $address[0]['locality'].', '.$address[0]['administrative_area'].' ';
    if($address[0]['country'] == 'US'){
      $address_formatted .= 'USA';
    }
    else{
      $country_list = _addressfield_country_options_list();
      if(isset($country_list[$address[0]['country']])){
        $address_formatted .= $country_list[$address[0]['country']];
      }
    }

    $rows[] = array(
      $name,
      $address_formatted,
      $email,
    );
  }


  $build['table'] = array(
    '#theme' => 'table',
    '#header' => array(
      'Name',
      'Location',
      'Email',
    ),
    '#rows' => $rows,
  );

  $build['pager'] = array('#theme' => 'pager');

  return $build;
}
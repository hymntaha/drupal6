<?php

function teacher_renewal_cron_callback(){
	set_time_limit(0);

	teacher_renewal_30_day_email();
	teacher_renewal_7_day_email();
	teacher_renewal_0_day_email();
	teacher_renewal_p23_day_email();
	teacher_renewal_p30_day_email();
}

function teacher_renewal_30_day_email(){
	$result = teacher_get_teachers_by_license_renewal(date('Y-m-d',strtotime('+ 30 days')));
	foreach($result as $row){
		$account = user_load($row->uid);
	    drupal_mail(
	      "teacher",
	      "teacher_cert_email",
	      $account->mail,
	      language_default(),
	      array(
	      	'teacher_name'=> account_get_first_name($account),
	      	'subject' => _mm('teacher_cert_email_subject','YTU Teacher License Renewal',true),
	      	'date' => date('m/d/Y',strtotime($row->field_license_renewal_date_value)),
	      ),
	      _mm('teacher_cert_email_from','annie@yogatuneup.com',true)
	    );
	}
}

function teacher_renewal_7_day_email(){
	$result = teacher_get_teachers_by_license_renewal(date('Y-m-d',strtotime('+ 7 days')));
	foreach($result as $row){
		$account = user_load($row->uid);
	    drupal_mail(
	      "teacher",
	      "teacher_cert_email_7",
	      $account->mail,
	      language_default(),
	      array(
	      	'teacher_name'=> account_get_first_name($account),
	      	'subject' => _mm('teacher_cert_email_7_subject','YTU Teacher License Renewal',true),
	      	'date' => date('m/d/Y',strtotime($row->field_license_renewal_date_value)),
	      ),
	      _mm('teacher_cert_email_7_from','annie@yogatuneup.com',true)
	    );
	}
}

function teacher_renewal_0_day_email(){
	$result = teacher_get_teachers_by_license_renewal(date('Y-m-d'));
	foreach($result as $row){
		$account = user_load($row->uid);
	    drupal_mail(
	      "teacher",
	      "teacher_cert_email_0t",
	      $account->mail,
	      language_default(),
	      array(
	      	'teacher_name'=> account_get_first_name($account),
	      	'subject' => _mm('teacher_cert_email_0t_subject','YTU Teacher License Renewal',true),
	      	'date' => date('m/d/Y',strtotime($row->field_license_renewal_date_value)),
	      ),
	      _mm('teacher_cert_email_0t_from','annie@yogatuneup.com',true)
	    );

	    drupal_mail(
	      "teacher",
	      "teacher_cert_email_0a",
	      _mm('teacher_cert_email_0a_to',variable_get('site_mail', ini_get('sendmail_from')),TRUE),
	      language_default(),
	      array(
	      	'teacher_name'=> account_get_full_name($account),
	      	'subject' => _mm('teacher_cert_email_0a_subject','YTU Teacher License Renewal',true),
	      	'date' => date('m/d/Y',strtotime($row->field_license_renewal_date_value)),
	      	'teacher_email' => $account->mail,
	      ),
	      _mm('teacher_cert_email_0a_from','annie@yogatuneup.com',true)
	    );
	}
}

function teacher_renewal_p23_day_email(){
	$result = teacher_get_teachers_by_license_renewal(date('Y-m-d',strtotime('- 23 days')));
	foreach($result as $row){
		$account = user_load($row->uid);
	    drupal_mail(
	      "teacher",
	      "teacher_cert_email_p23",
	      $account->mail,
	      language_default(),
	      array(
	      	'teacher_name'=> account_get_first_name($account),
	      	'subject' => _mm('teacher_cert_email_p23_subject','YTU Teacher License Renewal',true),
	      	'date' => date('m/d/Y',strtotime($row->field_license_renewal_date_value)),
	      ),
	      _mm('teacher_cert_email_p23_from','annie@yogatuneup.com',true)
	    );
	}
}

function teacher_renewal_p30_day_email(){
	$uids = array();
	$result = teacher_get_expired_teachers();
	foreach($result as $row){
		$account = user_load($row->uid);
		$uids[] = $row->uid;
	    drupal_mail(
	      "teacher",
	      "teacher_cert_email_p30a",
	      _mm('teacher_cert_email_p30a_to',variable_get('site_mail', ini_get('sendmail_from')),TRUE),
	      language_default(),
	      array(
	      	'teacher_name'=> account_get_first_name($account),
	      	'teacher_email' => $account->mail,
	      	'subject' => _mm('teacher_cert_email_p30a_subject','YTU Teacher License Renewal',true),
	      	'date' => date('m/d/Y',strtotime($row->field_license_renewal_date_value)),
	      ),
	      _mm('teacher_cert_email_p30a_from','annie@yogatuneup.com',true)
	    );
	}

	if($uids){
		user_multiple_role_edit($uids,'remove_role',TEACHER_RID);
	}
}
<?php

define('TEACHER_RID',5);

abstract class YtuUserMigration extends Migration {
  public function __construct($arguments) {
    parent::__construct($arguments);
  }
}

class YtuUserBaseMigration extends YtuUserMigration {
  public function __construct($arguments) {

    parent::__construct($arguments);
    $this->description = t('Yoga Tune Up User Migration');
    $query = Database::getConnection('default', 'for_migration')
    		->select('users', 'u')
            ->fields('u', array('UserID', 'Email', 'Password', 'Fname', 'Lname', 'CreateDate', 'LoginDate','RegisterAs'))
            ->orderBy('u.UserID','DESC');
    $this->source = new MigrateSourceSQL($query);
    $this->destination = new MigrateDestinationUser();
    $this->map = new MigrateSQLMap($this->machineName,
        array('UserID' => array(
              'type' => 'int',
              'not null' => TRUE,
              'description' => 'User ID.'
              )
        ),
        MigrateDestinationUser::getKeySchema()
    );

    $this->addFieldMapping('status')
         ->defaultValue(1);

    $this->addFieldMapping('signature_format')
         ->defaultValue('filtered_html');

    $this->addFieldMapping('roles','RegisterAs')
    	->callbacks(array($this, 'check_teacher_role'));

    $this->addFieldMapping('name', 'Email')->dedupe('users', 'name');
    $this->addFieldMapping('mail', 'Email')->dedupe('users', 'mail');
    $this->addFieldMapping('init', 'Email');
    $this->addFieldMapping('pass', 'Password');

    $this->addFieldMapping('created', 'CreateDate');
    $this->addFieldMapping('access', 'CreateDate');
    $this->addFieldMapping('login', 'LoginDate')
          ->callbacks(array($this,'check_date'));

    $this->addFieldMapping('field_first_name', 'Fname');
    $this->addFieldMapping('field_last_name', 'Lname');

  }

  protected function check_teacher_role($value){
  	$return_value = DRUPAL_AUTHENTICATED_RID;
  	if($value == 'teacher'){
  		$return_value = TEACHER_RID;
  	}
  	return $return_value;
  }

  protected function check_date($value){
    if($value == '0000-00-00'){
      $value = 0;
    }
    return $value;
  }
}

class YtuUserFreeVideoMigration extends YtuUserMigration {
  public function __construct($arguments) {

    parent::__construct($arguments);
    $table_name = 'users_videos';
    $valid_video_ids = array_keys(ytu_migrate_free_video_map());

    $this->description = t('Yoga Tune Up User Free Video Migration');
    $query = Database::getConnection('default', 'for_migration')
        ->select('users', 'u')
            ->fields('u', array('UserID','VideoID'))
            ->condition('u.WatchQuickfix','yes')
            ->condition('u.VideoID',$valid_video_ids,'IN')
            ->orderBy('u.UserID','DESC');
    $this->source = new MigrateSourceSQL($query);
    $this->destination = new MigrateDestinationTable($table_name);
    $this->map = new MigrateSQLMap($this->machineName,
        array(
          'UserID' => array(
            'type' => 'int',
            'not null' => TRUE,
            'description' => 'User ID.'
            ),
          'VideoID' => array(
            'type' => 'int',
            'not null' => TRUE,
            'description' => 'Video ID.',
          ),
        ),
        MigrateDestinationTable::getKeySchema($table_name)
    );

    $this->addFieldMapping('uid', 'UserID')
         ->sourceMigration('YtuUserBase');

    $this->addFieldMapping('video_nid','VideoID')
         ->callbacks(array($this, 'convert_free_video_id'));

  }

  protected function convert_free_video_id($value){
    $video_map = ytu_migrate_free_video_map();
    return $video_map[$value]['small'];
  }
}

class YtuUserVideoMigration extends YtuUserMigration {
  public function __construct($arguments) {

    parent::__construct($arguments);
    $table_name = 'users_videos';
    $valid_video_ids = array_keys(ytu_migrate_video_map());

    $this->description = t('Yoga Tune Up User Video Migration');
    $query = Database::getConnection('default', 'for_migration')
        ->select('tbl_purchased_video', 'v')
            ->fields('v', array('UserID','VideoID','VideoSize'))
            ->condition('v.VideoSize','','<>')
            ->condition('v.VideoID',$valid_video_ids,'IN')
            ->distinct()
            ->orderBy('v.UserID','DESC');
    $this->source = new MigrateSourceSQL($query,array('v_id' => 'Drupal Video Node ID'));
    $this->destination = new MigrateDestinationTable($table_name);
    $this->map = new MigrateSQLMap($this->machineName,
        array(
          'UserID' => array(
            'type' => 'int',
            'not null' => TRUE,
            'description' => 'User ID.'
            ),
          'VideoID' => array(
            'type' => 'int',
            'not null' => TRUE,
            'description' => 'Video ID.',
          ),
          'VideoSize' => array(
            'type' => 'varchar',
            'length' => 255,
            'not null' => TRUE,
            'description' => 'Video Size',
          ),
        ),
        MigrateDestinationTable::getKeySchema($table_name)
    );

    $this->addFieldMapping('uid', 'UserID')
         ->sourceMigration('YtuUserBase');

    $this->addFieldMapping('video_nid','v_id');
  }

  public function prepareRow($row) {
    // Always include this fragment at the beginning of every prepareRow()
    // implementation, so parent classes can ignore rows.
    if (parent::prepareRow($row) === FALSE) {
      return FALSE;
    }

    $uid = ytu_migrate_get_uid_from_old_user_id($row->UserID);
    if(!$uid){
      return FALSE;
    }

    $video_map = ytu_migrate_video_map();
    if(isset($video_map[$row->VideoID][strtolower($row->VideoSize)])){
      $row->v_id = $video_map[$row->VideoID][strtolower($row->VideoSize)];
      $is_dup = db_select('users_videos','uv')->fields('uv',array('uid'))->condition('uid',$uid)->condition('video_nid',$row->v_id)->execute()->fetchField();
      
      if($is_dup){
        return FALSE;
      }
      else{
        return TRUE;
      }
    }
    else{
      return FALSE;
    }
  }
}

class YtuUserAddressMigration extends YtuUserMigration {
  public function __construct($arguments) {

    parent::__construct($arguments);
    $table_name = 'uc_addresses';

    $this->description = t('Yoga Tune Up User Address Migration');
    $query = Database::getConnection('default', 'for_migration')
        ->select('users', 'u')
            ->fields('u', array('UserID','FName','LName','PhoneNo','ShipBillingAddress','ShipCity','ShipZip'))
            ->condition('PhoneNo','','<>')
            ->condition('ShipBillingAddress','','<>')
            ->condition('ShipCity','','<>')
            ->condition('ShipZip','','<>');
    $query->innerJoin('country_master','c','u.ShipCountry = c.country_id');
    $query->innerJoin('states','s','u.ShipState = s.StateID');
    $query->fields('c',array('iso'))->fields('s',array('State'));
    $this->source = new MigrateSourceSQL($query,array('country_id' => 'Ubercart Country ID', 'zone_id' => 'Ubercart Zone ID'));
    $this->destination = new MigrateDestinationTable($table_name);
    $this->map = new MigrateSQLMap($this->machineName,
        array(
          'UserID' => array(
            'type' => 'int',
            'not null' => TRUE,
            'description' => 'User ID.'
            ),
        ),
        MigrateDestinationTable::getKeySchema($table_name)
    );

    $this->addFieldMapping('uid', 'UserID')
         ->sourceMigration('YtuUserBase');

    $this->addFieldMapping('first_name','FName');
    $this->addFieldMapping('last_name','LName');
    $this->addFieldMapping('phone','PhoneNo');
    $this->addFieldMapping('street1','ShipBillingAddress');
    $this->addFieldMapping('city','ShipCity');
    $this->addFieldMapping('zone','zone_id');
    $this->addFieldMapping('postal_code','ShipZip');
    $this->addFieldMapping('country','country_id');

    $this->addFieldMapping('address_name')
          ->defaultValue('');

    $this->addFieldMapping('default_shipping')
          ->defaultValue(1);

    $this->addFieldMapping('default_billing')
          ->defaultValue(0);
  }

  public function prepareRow($row) {
    // Always include this fragment at the beginning of every prepareRow()
    // implementation, so parent classes can ignore rows.
    if (parent::prepareRow($row) === FALSE) {
      return FALSE;
    }

    $uid = ytu_migrate_get_uid_from_old_user_id($row->UserID);
    if(!$uid){
      return FALSE;
    }

    $country_id = ytu_migrate_get_country_id_from_old_country_iso($row->iso);
    if(!$country_id){
      return FALSE;
    }
    else{
      $row->country_id = $country_id;
    }

    $zone_id = ytu_migrate_get_zone_from_old_state_name($row->State,$country_id);
    if(!$zone_id){
      return FALSE;
    }
    else{
      $row->zone_id = $zone_id;
    }

  }
}

class YtuUserInfusionsoftMigration extends YtuUserMigration {
  public function __construct($arguments) {

    parent::__construct($arguments);
    
    ini_set('auto_detect_line_endings', TRUE);
    $table_name = 'users_infusionsoft';
    $columns = array(
        0 => array('id', 'Infusionsoft Contact ID'),
        1 => array('email', 'Infusionsoft Email Address'),
    );
    $options = array();
    $fields = array(
      'user_id' => 'Drupal User ID',
    );

    $this->description = t('Yoga Tune Up User Infusionsoft Migration');
    $this->source = new MigrateSourceCSV('/home/web/yogatuneup/migrate/ytu_user_infusionsoft.csv',$columns,$options,$fields);
    $this->destination = new MigrateDestinationTable($table_name);
    $this->map = new MigrateSQLMap($this->machineName,
        array(
          'id' => array(
            'type' => 'int',
            'not null' => TRUE,
            'description' => 'User Infusionsoft ID.'
            ),
        ),
        MigrateDestinationTable::getKeySchema($table_name)
    );

    $this->addFieldMapping('uid','user_id');
    $this->addFieldMapping('cid','id');
  }

  public function prepareRow($row) {
    // Always include this fragment at the beginning of every prepareRow()
    // implementation, so parent classes can ignore rows.
    if (parent::prepareRow($row) === FALSE) {
      return FALSE;
    }

    $uid = ytu_migrate_get_uid_from_email($row->email);
    // Skip the row if the user is not in the users table
    if (!$uid) {
      return FALSE;
    }

    $is_dup = db_select('users_infusionsoft','i')->fields('i',array('uid'))->condition('uid',$uid)->execute()->fetchField();
    if($is_dup){
      return FALSE;
    }
    
    $row->user_id = $uid;
    return TRUE;
  }
}
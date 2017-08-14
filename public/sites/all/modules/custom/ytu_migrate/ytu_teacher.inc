<?php

abstract class YtuTeacherMigration extends Migration {
  public function __construct($arguments) {
    parent::__construct($arguments);
  }
}

class YtuTeacherBaseMigration extends YtuTeacherMigration {
  public function __construct($arguments) {

    parent::__construct($arguments);
    $this->description = t('Yoga Tune Up Teacher Migration');

    $node_options = MigrateDestinationNode::options(LANGUAGE_NONE, 'rich_text_teacher');

    $fields = array(
      'full_name' => 'Full Name of Teacher',
      'country_iso_code_2' => 'Country Two Character ISO Code',
      'state_code' => 'State Code',
      'photos' => 'Teacher Photos',
    );

    $query = Database::getConnection('default', 'for_migration')
        ->select('users', 'u')
            ->fields('u', array('UserID', 'Email', 'RegisterAs', 'CityName', 'Zip', 'URL', 'Experience', 'TeacherImage'))
            ->condition('RegisterAs','teacher')
            ->orderBy('u.UserID','ASC');
    $query->innerJoin('country_master','c','u.CountryID = c.country_id');
    $query->innerJoin('states','s','u.StateID = s.StateID');
    $query->fields('c',array('iso'))->fields('s',array('State'));
    $this->source = new MigrateSourceSQL($query,$fields);
    $this->destination = new MigrateDestinationNode('teacher',$node_options);
    $this->map = new MigrateSQLMap($this->machineName,
        array('UserID' => array(
              'type' => 'int',
              'not null' => TRUE,
              'description' => 'User ID.'
              )
        ),
        MigrateDestinationNode::getKeySchema()
    );

    $this->addFieldMapping('title','full_name');
    $this->addFieldMapping('uid','UserID')
          ->sourceMigration('YtuUserBase');
    $this->addFieldMapping('status')
          ->defaultValue(1);
    $this->addFieldMapping('promote')
          ->defaultValue(0);
    $this->addFieldMapping('sticky')
          ->defaultValue(0);

    $this->addFieldMapping('field_email','Email');

    $this->addFieldMapping('field_address','country_iso_code_2');
    $this->addFieldMapping('field_address:locality', 'CityName');
    $this->addFieldMapping('field_address:administrative_area', 'state_code');
    $this->addFieldMapping('field_address:postal_code', 'Zip');

    $this->addFieldMapping('field_website','URL');

    $this->addFieldMapping('body','Experience')
          ->callbacks(array($this,'text_html_decode'));
    $this->addFieldMapping('body:format')
          ->defaultValue('rich_text_teacher');

    $this->addFieldMapping('field_photos','photos')
          ->separator('|');
    $this->addFieldMapping('field_photos:source_dir')
     ->defaultValue('/home/web/yogatuneup/backups/sitedumplive/upload/users');

  }

  protected function text_html_decode($value){
    return ytu_migrate_strip_word_html($value);
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
    $account = user_load($uid);
    $row->full_name = ucwords(account_get_full_name($account));

    $iso_code_2 = ytu_migrate_get_country_iso_2_from_old_country_iso($row->iso);
    if(!$iso_code_2){
      return FALSE;
    }
    $row->country_iso_code_2 = $iso_code_2;

    $country_id = ytu_migrate_get_country_id_from_old_country_iso($row->iso);
    if(!$country_id){
      return FALSE;
    }

    $state_code = ytu_migrate_get_state_code_from_old_state_name($row->State,$country_id);
    if(!$state_code){
      return FALSE;
    }
    $row->state_code = $state_code;

    $photos[] = $row->TeacherImage;
    $result = Database::getConnection('default', 'for_migration')
                ->select('teacherImage', 'ti')
                ->fields('ti',array('TeacherImage'))
                ->condition('TeacherID',$row->UserID)
                ->condition('TeacherImage','','<>')
                ->execute();
    foreach($result as $photo_row){
      $photos[] = $photo_row->TeacherImage;
    }
    $row->photos = implode('|',$photos);

  }
}

class YtuTeacherCertMigration extends YtuTeacherMigration {
  public function __construct($arguments) {

    parent::__construct($arguments);
    $this->description = t('Yoga Tune Up Teacher Cert Migration');

    $fields = array(
      'level_name' => 'Cert Level Taxonomy Name',
    );

    $query = Database::getConnection('default', 'for_migration')
        ->select('teacherCertification', 'tc')
            ->fields('tc')
            ->isNotNull('TeacherID')
            ->condition('CertDate','2008-01-01','>=');
    $this->source = new MigrateSourceSQL($query,$fields);
    $this->destination = new MigrateDestinationFieldCollection('field_ytu_certification_level',array('host_entity_type' => 'node'));
    $this->map = new MigrateSQLMap($this->machineName,
        array('ID' => array(
              'type' => 'int',
              'not null' => TRUE,
              'description' => 'Cert Info ID.'
              )
        ),
        MigrateDestinationFieldCollection::getKeySchema()
    );

    $this->addFieldMapping('host_entity_id', 'TeacherID')
      ->sourceMigration('YtuTeacherBase');

    $this->addFieldMapping('field_cert_date','CertDate');
    $this->addFieldMapping('field_cert_level','level_name');

  }

  public function prepareRow($row) {
    // Always include this fragment at the beginning of every prepareRow()
    // implementation, so parent classes can ignore rows.
    if (parent::prepareRow($row) === FALSE) {
      return FALSE;
    }

    $uid = ytu_migrate_get_uid_from_old_user_id($row->TeacherID);
    if(!$uid){
      return FALSE;
    }

    $teacher_nid = ytu_migrate_get_teacher_nid_from_old_user_id($row->TeacherID);
    if(!$teacher_nid){
      return FALSE;
    }

    $cert_level_map = ytu_migrate_cert_level_map();
    if(!isset($cert_level_map[$row->Level])){
      return FALSE;
    }
    $row->level_name = $cert_level_map[$row->Level];
  }
}
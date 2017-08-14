<?php

abstract class YtuClassMigration extends Migration {
  public function __construct($arguments) {
    parent::__construct($arguments);
  }
}

class YtuClassBaseMigration extends YtuClassMigration {
  public function __construct($arguments) {

    parent::__construct($arguments);
    $this->description = t('Yoga Tune Up Class Migration');

    $node_options = MigrateDestinationNode::options(LANGUAGE_NONE, 'rich_text_teacher');

    $fields = array(
      'country_iso_code_2' => 'Country Two Character ISO Code',
      'state_code' => 'State Code',
      'single_start_dates' => 'Single Start Dates',
      'single_end_dates' => 'Single End Dates',
      'recurring_date' => 'Recurring Date',
    );

    $query = Database::getConnection('default', 'for_migration')
        ->select('classes', 'cl')
            ->fields('cl',array('ClassID', 'Name','Description','Phone','URL','Type','ContactPerson','VName','Area','Image','TeacherID','CountryID','StateID','City','Location','TeacherLevel','CreateDate'));
    $query->innerJoin('country_master','c','cl.CountryID = c.country_id');
    $query->innerJoin('states','s','cl.StateID = s.StateID');
    $query->fields('c',array('iso'))->fields('s',array('State'));
    $this->source = new MigrateSourceSQL($query,$fields);
    $this->destination = new MigrateDestinationNode('class',$node_options);
    $this->map = new MigrateSQLMap($this->machineName,
        array('ClassID' => array(
              'type' => 'int',
              'not null' => TRUE,
              'description' => 'Class ID.'
              )
        ),
        MigrateDestinationNode::getKeySchema()
    );

    $this->addFieldMapping('title','Name')
          ->callbacks(array($this,'text_html_decode'));
    $this->addFieldMapping('uid','TeacherID')
          ->sourceMigration('YtuUserBase');
    $this->addFieldMapping('status')
          ->defaultValue(1);
    $this->addFieldMapping('promote')
          ->defaultValue(0);
    $this->addFieldMapping('sticky')
          ->defaultValue(0);
    $this->addFieldMapping('created','CreateDate')
          ->callbacks(array($this,'check_date'));

    $this->addFieldMapping('field_image','Image');
    $this->addFieldMapping('field_image:source_dir')
     ->defaultValue('/home/web/yogatuneup/backups/sitedumplive/upload/workshop');

    $this->addFieldMapping('field_class_type','Type');

    $this->addFieldMapping('field_ytu_certification_training','TeacherLevel')
          ->callbacks(array($this,'convert_teacher_level'));

    $this->addFieldMapping('field_single_date','single_start_dates');
    $this->addFieldMapping('field_single_date:to','single_end_dates');
    $this->addFieldMapping('field_single_date:timezone')
          ->defaultValue('America/New_York');

    $this->addFieldMapping('field_recurring_date_range','recurring_date');

    $this->addFieldMapping('field_teacher','TeacherID')
          ->sourceMigration('YtuTeacherBase');

    $this->addFieldMapping('field_contact','ContactPerson');

    $this->addFieldMapping('field_venue','VName');

    $this->addFieldMapping('field_address','country_iso_code_2');
    $this->addFieldMapping('field_address:thoroughfare','Location');
    $this->addFieldMapping('field_address:locality', 'City');
    $this->addFieldMapping('field_address:administrative_area', 'state_code');

    $this->addFieldMapping('field_phone','Phone');

    $this->addFieldMapping('field_website','URL');

    $this->addFieldMapping('body','Description')
          ->callbacks(array($this,'text_html_decode'));
    $this->addFieldMapping('body:format')
          ->defaultValue('rich_text_teacher');

    $this->addFieldMapping('field_categories','Area')
          ->separator(',');

  }

  protected function check_date($value){
    if($value == '0000-00-00'){
      $value = 0;
    }
    return $value;
  }

  protected function convert_teacher_level($value){
    switch ($value) {
      case 'Integrated Embodied Anatomy':
      case 'Yoga Tune Up&reg; Integrated Embodied Anatomy':
      case 'Yoga Tune Up® Integrated Embodied Anatomy':
        $value = 'YTU Integrated Embodied Anatomy';
        break;
      case 'Level 2 - Shoulders':
        $value = 'Shoulders Immersion';
        break;
      case 'Level 2':
        $value = 'Trainer';
        break;
    }
    return $value;
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

    $teacher_nid = ytu_migrate_get_teacher_nid_from_old_user_id($row->TeacherID);
    if(!$teacher_nid){
      return FALSE;
    }

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

    $row->single_start_dates = array();
    $row->single_end_dates = array();
    $query = Database::getConnection('default', 'for_migration')
        ->select('tbl_workshopClass_DateTime', 'dt')
            ->fields('dt',array('ClassWorkshopID','Date','EndDate','StartTime','EndTime','StartAMPM','EndAMPM'))
            ->condition('ClassWorkshopID',$row->ClassID)
            ->condition('ScheduleType','recuring','<>')
            ->orderBy('Date');
    $result = $query->execute();
    foreach($result as $date_row){
      $start = (string) $date_row->Date;
      $end = (string) $date_row->EndDate;

      if($date_row->StartTime != '00:00:00'){
        $start .= ' '. (string) $date_row->StartTime . (string) $date_row->StartAMPM;
      }
      if($end && $date_row->EndTime != '00:00:00'){
        $end .= ' '. (string) $date_row->EndTime . (string) $date_row->EndAMPM;
      }
      if(!$end && $date_row->EndTime != '00:00:00'){
        $end = (string) $date_row->Date .' '. (string) $date_row->EndTime . (string) $date_row->EndAMPM;
      }

      $row->single_start_dates[] = $start;
      $row->single_end_dates[] = $end;
    }

    $row->recurring_date = '';
    $query = Database::getConnection('default', 'for_migration')
        ->select('tbl_workshopClass_DateTime', 'dt')
            ->fields('dt',array('ClassWorkshopID','Date','EndDate','Day','StartTime','EndTime','StartAMPM','EndAMPM'))
            ->condition('ClassWorkshopID',$row->ClassID)
            ->condition('ScheduleType','recuring')
            ->groupBy('ClassWorkshopID');
    $result = $query->execute();
    foreach($result as $date_row){
      $row->recurring_date = $row->CreateDate;
    }
  }

  public function complete($entity, stdClass $row){
      $query = Database::getConnection('default', 'for_migration')
      ->select('tbl_workshopClass_DateTime', 'dt')
          ->fields('dt',array('ClassWorkshopID','Date','EndDate','Day','StartTime','EndTime','StartAMPM','EndAMPM'))
          ->condition('ScheduleType','recuring')
          ->condition('ClassWorkshopID',$row->ClassID)
          ->orderBy('DateTimeID');
      $result = $query->execute();
      $i = 0;
      $fields = array(
        'entity_type' => 'node',
        'bundle' => 'class',
        'entity_id' => '',
        'revision_id' => '',
        'language' => 'und',
        'delta' => $i,
        'field_recurring_schedule_day' => '',
        'field_recurring_schedule_starthours' => '',
        'field_recurring_schedule_endhours' => '',
      );
      foreach($result as $sched_row){
        $fields['entity_id'] = $entity->nid;
        $fields['revision_id'] = $entity->nid;
        $fields['delta'] = $i;
        $fields['field_recurring_schedule_day'] = date('w',strtotime($sched_row->Day));
        $fields['field_recurring_schedule_starthours'] = date('Hi',strtotime($sched_row->StartTime.$sched_row->StartAMPM));
        $fields['field_recurring_schedule_endhours'] = date('Hi',strtotime($sched_row->EndTime.$sched_row->EndAMPM));
        db_insert('field_data_field_recurring_schedule')->fields($fields)->execute();
        db_insert('field_revision_field_recurring_schedule')->fields($fields)->execute();
        $i++;
      }
  }
}
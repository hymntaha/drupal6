<?php

abstract class YtuGroupMigration extends Migration {
  public function __construct($arguments) {
    parent::__construct($arguments);
  }
}

class YtuGroupCoachingCallMigration extends YtuGroupMigration {
  public function __construct($arguments) {

    parent::__construct($arguments);
    $this->description = t('Yoga Tune Up Coaching Call Migration');

    $fields = array();

    $query = Database::getConnection('default', 'for_migration')
        ->select('teleconferenceTeacher', 't')
            ->fields('t', array('ID','ConfDate','ConfSubject','DocumentName','DateModified'))
            ->condition('Status',1)
            ->orderBy('ConfDate','ASC');
    $this->source = new MigrateSourceSQL($query,$fields);
    $this->destination = new MigrateDestinationNode('coaching_call');
    $this->map = new MigrateSQLMap($this->machineName,
        array('ID' => array(
              'type' => 'int',
              'not null' => TRUE,
              'description' => 'Call ID.'
              )
        ),
        MigrateDestinationNode::getKeySchema()
    );

    $this->addFieldMapping('title','ConfSubject');
    $this->addFieldMapping('uid')
          ->defaultValue(1);
    $this->addFieldMapping('status')
          ->defaultValue(1);
    $this->addFieldMapping('promote')
          ->defaultValue(0);
    $this->addFieldMapping('sticky')
          ->defaultValue(0);
    $this->addFieldMapping('created','DateModified');
    $this->addFieldMapping('changed','DateModified');

    $this->addFieldMapping('field_call_date','ConfDate');
    $this->addFieldMapping('field_call_date:timezone')
          ->defaultValue('America/New_York');

    $this->addFieldMapping('field_mp3','DocumentName');
    $this->addFieldMapping('field_mp3:source_dir')
     ->defaultValue('/home/web/yogatuneup/backups/sitedumplive/upload/teacherteleconf');

  }

  public function prepareRow($row) {
    // Always include this fragment at the beginning of every prepareRow()
    // implementation, so parent classes can ignore rows.
    if (parent::prepareRow($row) === FALSE) {
      return FALSE;
    }

  }
}
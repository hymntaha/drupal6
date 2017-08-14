<?php

function ytu_migrate_migrate_api() {

  $api = array(
    'api' => 2,

    'groups' => array(
      'ytu_user' => array(
        'title' => t('User Imports'),
      ),
      'ytu_teacher' => array(
        'title' => t('Teacher Imports'),
      ),
      'ytu_class' => array(
        'title' => t('Class Imports'),
      ),
      'ytu_group' => array(
        'title' => t('Group Imports')
      ),
    ),

    'migrations' => array(
      'YtuUserBase' => array(
        'class_name' => 'YtuUserBaseMigration',
        'group_name' => 'ytu_user',
      ),
      'YtuUserFreeVideo' => array(
        'class_name' => 'YtuUserFreeVideoMigration',
        'group_name' => 'ytu_user',
        'dependencies' => array(
          'YtuUserBase',
        ),
      ),
      'YtuUserVideo' => array(
        'class_name' => 'YtuUserVideoMigration',
        'group_name' => 'ytu_user',
        'dependencies' => array(
          'YtuUserBase',
        ),
      ),
      'YtuUserAddress' => array(
        'class_name' => 'YtuUserAddressMigration',
        'group_name' => 'ytu_user',
        'dependencies' => array(
          'YtuUserBase',
        ),
      ),
      'YtuUserInfusionsoft' => array(
        'class_name' => 'YtuUserInfusionsoftMigration',
        'group_name' => 'ytu_user',
        'dependencies' => array(
          'YtuUserBase',
        ),
      ),
      'YtuTeacherBase' => array(
        'class_name' => 'YtuTeacherBaseMigration',
        'group_name' => 'ytu_teacher',
      ),
      'YtuTeacherCert' => array(
        'class_name' => 'YtuTeacherCertMigration',
        'group_name' => 'ytu_teacher',
        'dependencies' => array(
          'YtuTeacherBase',
        ),
      ),
      'YtuClassBase' => array(
        'class_name' => 'YtuClassBaseMigration',
        'group_name' => 'ytu_class',
        'dependencies' => array(
          'YtuTeacherBase',
        ),
      ),
      'YtuGroupCoachingCall' => array(
        'class_name' => 'YtuGroupCoachingCallMigration',
        'group_name' => 'ytu_group',
      ),
    ),
  );
  return $api;
}

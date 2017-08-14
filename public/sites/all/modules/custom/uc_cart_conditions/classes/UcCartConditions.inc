<?php

class UcCartConditions {
  public $id;
  public $nid;
  public $conditions;

  function __construct($id = 0, $nid = 0, $conditions = array()) {
    $this->id         = $id;
    $this->nid        = $nid;
    $this->conditions = $conditions;
  }

  /**
   * @return int
   */
  public function getId() {
    return $this->id;
  }

  /**
   * @param int $id
   */
  public function setId($id) {
    $this->id = $id;
  }

  /**
   * @return int
   */
  public function getNid() {
    return $this->nid;
  }

  /**
   * @param int $nid
   */
  public function setNid($nid) {
    $this->nid = $nid;
  }

  /**
   * @return array
   */
  public function getConditions() {
    return $this->conditions;
  }

  /**
   * @param array $conditions
   */
  public function setConditions($conditions) {
    $this->conditions = $conditions;
  }

  public function save(){
    $update = array();

    if($this->id){
      $update = array('id');
    }

    drupal_write_record('uc_cart_conditions',$this,$update);
  }

}
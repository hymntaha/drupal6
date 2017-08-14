<?php

class UcCartQtyJump {
  public $id;
  public $nid;
  public $qty_jump;
  public $qty_jump_start;

  function __construct($id = 0, $nid = 0, $qty_jump = 0, $qty_jump_start = 0) {
    $this->setId($id);
    $this->setNid($nid);
    $this->setQtyJump($qty_jump);
    $this->setQtyJumpStart($qty_jump_start);
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
  private function setId($id) {
    $this->id = $id;
  }

  /**
   * @return mixed
   */
  public function getNid() {
    return $this->nid;
  }

  /**
   * @param mixed $nid
   */
  public function setNid($nid) {
    $this->nid = $nid;
  }

  /**
   * @return mixed
   */
  public function getQtyJump() {
    return $this->qty_jump;
  }

  /**
   * @param mixed $qty_jump
   */
  public function setQtyJump($qty_jump) {
    $this->qty_jump = $qty_jump;
  }

  /**
   * @return mixed
   */
  public function getQtyJumpStart() {
    return $this->qty_jump_start;
  }

  /**
   * @param mixed $qty_jump
   */
  public function setQtyJumpStart($qty_jump_start) {
    $this->qty_jump_start = $qty_jump_start;
  }

  public function save(){
    $update = array();

    if($this->id){
      $update = array('id');
    }

    drupal_write_record('uc_cart_qty_jump',$this,$update);
  }

  public function delete(){
    if(isset($this->id)){
      db_delete('uc_cart_qty_jump')
        ->condition('id',$this->id)
        ->execute();
    }
  }

}
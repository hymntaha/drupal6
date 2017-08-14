<?php

class UcCartLimitQty {
  public $id;
  public $nid;
  public $qty_limit;

  function __construct($id = 0, $nid = 0, $qty_limit = 0) {
    $this->setId($id);
    $this->setNid($nid);
    $this->setQtyLimit($qty_limit);
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
   * @return int
   */
  public function getQtyLimit() {
    return $this->qty_limit;
  }

  /**
   * @param int $qty_limit
   */
  public function setQtyLimit($qty_limit) {
    $this->qty_limit = $qty_limit;
  }

  public function save(){
    $update = array();

    if($this->id){
      $update = array('id');
    }

    drupal_write_record('uc_cart_limit_qty',$this,$update);
  }

  public function delete(){
    if(isset($this->id)){
      db_delete('uc_cart_limit_qty')
        ->condition('id',$this->id)
        ->execute();
    }
  }

}
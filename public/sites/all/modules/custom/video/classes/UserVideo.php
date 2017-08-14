<?php

class UserVideo {
  private $uid;
  private $video_nid;
  private $order_id;
  private $favorite;

  public function __construct($uid, $video_nid, $order_id, $favorite) {
    $this->setUid($uid);
    $this->setVideoNid($video_nid);
    $this->setOrderId($order_id);
    $this->setFavorite($favorite);
  }

  /**
   * @return mixed
   */
  public function getUid() {
    return $this->uid;
  }

  /**
   * @param mixed $uid
   */
  public function setUid($uid) {
    $this->uid = $uid;
  }

  /**
   * @return mixed
   */
  public function getVideoNid() {
    return $this->video_nid;
  }

  /**
   * @param mixed $video_nid
   */
  public function setVideoNid($video_nid) {
    $this->video_nid = $video_nid;
  }

  /**
   * @return mixed
   */
  public function getOrderId() {
    return $this->order_id;
  }

  /**
   * @param mixed $order_id
   */
  public function setOrderId($order_id) {
    $this->order_id = $order_id;
  }

  /**
   * @return mixed
   */
  public function getFavorite() {
    return $this->favorite;
  }

  /**
   * @param mixed $favorite
   */
  public function setFavorite($favorite) {
    $this->favorite = $favorite;
  }

  public function save() {
    db_merge('users_video_segments')
      ->key(array('uid' => $this->getUid(), 'video_nid' => $this->getVideoNid()))
      ->fields(array(
        'uid' => $this->getUid(),
        'video_nid' => $this->getVideoNid(),
        'order_id' => $this->getOrderId(),
        'favorite' => $this->getFavorite(),
      ))
      ->execute();
  }

  public function delete(){
    $userVideoManager = new UserVideoManager($this->getUid());

    db_delete('users_video_segments')
      ->condition('uid', $this->getUid())
      ->condition('video_nid', $this->getVideoNid())
      ->execute();

    foreach($userVideoManager->getPlaylists() as $userPlaylist){
      foreach($userPlaylist->getVideos() as $userPlaylistVideo){
        if($userPlaylistVideo->getVideoNid() == $this->getVideoNid()){
          $userPlaylistVideo->delete();
        }
      }
    }
  }

  public static function load($uid, $video_nid){
    $result = db_select('users_video_segments','v')
      ->fields('v')
      ->condition('uid', $uid)
      ->condition('video_nid', $video_nid)
      ->execute();

    foreach($result as $row){
      return new UserVideo($row->uid, $row->video_nid, $row->order_id, $row->favorite);
    }

    return FALSE;
  }

  public function toArray(){
    return array(
      'uid' => $this->getUid(),
      'video_nid' => $this->getVideoNid(),
      'order_id' => $this->getOrderId(),
      'favorite' => $this->getFavorite(),
    );
  }

}
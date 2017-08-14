<?php

class UserPlaylist {
  private $id;
  private $uid;
  private $name;
  private $editable;
  private $playlist_nid;
  private $videos;

  /**
   * UserPlaylist constructor.
   * @param $uid
   * @param $name
   * @param $editable
   * @param null|int $playlist_nid
   */
  public function __construct($uid, $name, $editable, $playlist_nid = null) {
    $this->uid = $uid;
    $this->name = $name;
    $this->editable = $editable;
    $this->playlist_nid = $playlist_nid;
  }

  /**
   * @return mixed
   */
  public function getId() {
    return $this->id;
  }

  /**
   * @param mixed $id
   */
  public function setId($id) {
    $this->id = $id;
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
  public function getName() {
    return $this->name;
  }

  /**
   * @param mixed $name
   */
  public function setName($name) {
    $this->name = $name;
  }

  /**
   * @return mixed
   */
  public function getEditable() {
    return $this->editable;
  }

  /**
   * @param mixed $editable
   */
  public function setEditable($editable) {
    $this->editable = $editable;
  }

  /**
   * @return int|null
   */
  public function getPlaylistNid() {
    return $this->playlist_nid;
  }

  /**
   * @return UserPlaylistVideo[]
   */
  public function getVideos() {
    return $this->videos;
  }

  /**
   * @param UserPlaylistVideo[] $videos
   */
  public function setVideos($videos) {
    $this->videos = $videos;
  }

  public function hasVideo($video_nid){
    /** @var \UserPlaylistVideo $userPlaylistVideo */
    foreach($this->videos as $userPlaylistVideo){
      if($userPlaylistVideo->getVideoNid() == $video_nid){
        return true;
      }
    }

    return false;
  }

  public function save(){
    $record = new stdClass();

    $record->uid = $this->getUid();
    $record->name = $this->getName();
    $record->editable = $this->getEditable();
    $record->playlist_nid = $this->getPlaylistNid();

    $primary_keys = array();

    if($this->getId()){
      $record->id = $this->getId();
      $primary_keys = array('id');
    }

    drupal_write_record('users_video_custom_playlists', $record, $primary_keys);

    $this->setId($record->id);
  }

  public function delete($delete_segments = FALSE){
    foreach($this->getVideos() as $userPlaylistVideo){
      if($delete_segments){
        $userVideoSegment = UserVideo::load($this->getUid(), $userPlaylistVideo->getVideoNid());
        $userVideoSegment->delete();
      }
      else{
        $userPlaylistVideo->delete();
      }
    }

    db_delete('users_video_custom_playlists')
      ->condition('id', $this->getId())
      ->execute();
  }

  public static function load($id){
    $result = db_select('users_video_custom_playlists', 'p')
      ->fields('p')
      ->condition('id', $id)
      ->execute();

    foreach($result as $row){
      $userPlaylist = new UserPlaylist($row->uid, $row->name, $row->editable, $row->playlist_nid);
      $userPlaylist->setId($row->id);
      $userPlaylist->loadVideosFromDB();

      if(!$userPlaylist->getEditable() && $userPlaylist->getPlaylistNid()){
        if($node = node_load($userPlaylist->getPlaylistNid())){
          $userPlaylist->setName($node->title);
        }
      }

      return $userPlaylist;
    }

    return FALSE;
  }

  private function loadVideosFromDB(){
    $videos = array();

    if($this->getId()){
      $results = db_select('users_video_custom_playlists_segments', 'ps')
        ->fields('ps')
        ->condition('custom_playlist_id', $this->getId())
        ->orderBy('weight', 'ASC')
        ->execute();

      foreach($results as $row){
        $userPlaylistVideo = new UserPlaylistVideo($row->custom_playlist_id, $row->video_nid, $row->weight);
        $userPlaylistVideo->setId($row->id);

        $videos[] = $userPlaylistVideo;
      }
    }

    $this->setVideos($videos);
  }
}
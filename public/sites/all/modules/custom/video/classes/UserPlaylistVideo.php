<?php

class UserPlaylistVideo {
  private $id;
  private $custom_playlist_id;
  private $video_nid;
  private $weight;

  /**
   * UserPlaylistVideo constructor.
   * @param $custom_playlist_id
   * @param $video_nid
   * @param $weight
   */
  public function __construct($custom_playlist_id, $video_nid, $weight) {
    $this->custom_playlist_id = $custom_playlist_id;
    $this->video_nid = $video_nid;
    $this->weight = $weight;
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
  public function getCustomPlaylistId() {
    return $this->custom_playlist_id;
  }

  /**
   * @param mixed $custom_playlist_id
   */
  public function setCustomPlaylistId($custom_playlist_id) {
    $this->custom_playlist_id = $custom_playlist_id;
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
  public function getWeight() {
    return $this->weight;
  }

  /**
   * @param mixed $weight
   */
  public function setWeight($weight) {
    $this->weight = $weight;
  }

  public function save(){
    $record = new stdClass();

    $record->custom_playlist_id = $this->getCustomPlaylistId();
    $record->video_nid = $this->getVideoNid();
    $record->weight = $this->getWeight();

    $primary_keys = array();

    if($this->getId()){
      $record->id = $this->getId();
      $primary_keys = array('id');
    }

    drupal_write_record('users_video_custom_playlists_segments', $record, $primary_keys);

    $this->setId($record->id);
  }

  public function delete(){
    db_delete('users_video_custom_playlists_segments')
      ->condition('id', $this->getId())
      ->execute();
  }

}
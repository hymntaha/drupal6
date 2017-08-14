<?php

class UserVideoManager {
  private $uid;
  private $videos;
  private $playlists;

  public function __construct($uid) {
    $this->uid = $uid;
    $this->videos = $this->getVideosFromDB();
    $this->playlists = $this->getPlaylistsFromDB();
  }

  /**
   * @return \UserVideo[]
   */
  public function getVideos() {
    return $this->videos;
  }

  /**
   * @return \UserPlaylist[]
   */
  public function getPlaylists() {
    return $this->playlists;
  }

  /**
   * @return mixed
   */
  public function getUid() {
    return $this->uid;
  }

  public function hasVideo($video_nid){
    foreach($this->videos as $userVideo){
      if($userVideo->getVideoNid() == $video_nid){
        return true;
      }
    }

    return false;
  }

  public function hasPlaylist($playlist_nid){
    foreach($this->playlists as $userPlaylist){
      if($userPlaylist->getPlaylistNid() == $playlist_nid){
        return true;
      }
    }

    return false;
  }

  public function addVideo($video_segment_nid, $order_id = null){
    if(!$this->hasVideo($video_segment_nid)){
      $userVideo = new UserVideo($this->uid, $video_segment_nid, $order_id, 0);
      $userVideo->save();
    }
  }

  public function deleteVideo($video_segment_nid){
    if($this->hasVideo($video_segment_nid)){
      $userVideo = new UserVideo($this->uid, $video_segment_nid, null, 0);
      $userVideo->delete();

      foreach($this->getPlaylists() as $userPlaylist){
        foreach($userPlaylist->getVideos() as $userPlaylistVideo){
          if($userPlaylistVideo->getVideoNid() == $video_segment_nid){
            $userPlaylistVideo->delete();
          }
        }
      }
    }
  }

  public function replaceVideo($old_video_nid, $new_video_nid){
    foreach($this->getPlaylists() as $userPlaylist){

      $action = 'update';
      if($userPlaylist->hasVideo($new_video_nid)){
        $action = 'delete';
      }

      foreach($userPlaylist->getVideos() as $userPlaylistVideo){
        if($userPlaylistVideo->getVideoNid() == $old_video_nid){
          switch($action){
            case 'update':
              $userPlaylistVideo->setVideoNid($new_video_nid);
              $userPlaylistVideo->save();
              break;
            case 'delete':
              $userPlaylistVideo->delete();
              break;
          }
        }
      }
    }

    if(!$this->hasVideo($new_video_nid)){
      foreach($this->getVideos() as $userVideo){
        if($userVideo->getVideoNid() == $old_video_nid){
          $userVideo->setVideoNid($new_video_nid);
          $userVideo->save();
          break;
        }
      }
    }

    $userVideo = new UserVideo($this->uid, $old_video_nid, null, 0);
    $userVideo->delete();

  }

  public function addPlaylistFromNode($playlist, $order = null){
    $userPlaylist = new UserPlaylist($this->uid, $playlist->title, false, $playlist->nid);
    $userPlaylist->save();

    $videos = field_get_items('node',$playlist,'field_playlist_videos');

    if(is_array($videos)){
      $weight = 0;

      foreach($videos as $video){
        $order_id = null;
        if($order){
          $order_id = $order->order_id;
        }

        $userVideo = new UserVideo($this->uid, $video['target_id'], $order_id, 0);
        $userVideo->save();

        $userPlaylistVideo = new UserPlaylistVideo($userPlaylist->getId(), $video['target_id'], $weight);
        $userPlaylistVideo->save();

        $weight++;
      }
    }
  }

  /**
   * @return UserVideo[]
   */
  private function getVideosFromDB(){
    $videos = array();

    $query = db_select('users_video_segments', 'v')
      ->fields('v')
      ->condition('v.uid', $this->uid)
      ->orderBy('v.favorite', 'DESC');

    $query->innerJoin('node', 'n', 'v.video_nid = n.nid');
    $query->orderBy('n.title', 'ASC');

    $result = $query->execute();

    foreach($result as $row){
      $videos[] = new UserVideo($row->uid, $row->video_nid, $row->order_id, $row->favorite);
    }

    return $videos;
  }

  /**
   * @return UserPlaylist[]
   */
  private function getPlaylistsFromDB(){
    $playlists = array();

    $result = db_select('users_video_custom_playlists', 'p')
      ->fields('p', array('id'))
      ->condition('uid', $this->uid)
      ->orderBy('editable', 'ASC')
      ->orderBy('name', 'ASC')
      ->execute();

    foreach($result as $row){
      $playlists[] = UserPlaylist::load($row->id);
    }

    return $playlists;
  }

}
<?php
/** @var UserVideoManager $user_video_manager */
?>

<?php if(!empty($user_video_manager->getPlaylists())):?>
<div class="user-dashboard-row clearfix">
  <h2 class="blue-title my-videos">My Playlists</h2>
  <?php foreach($user_video_manager->getPlaylists() as $userPlaylist):?>
    <?=video_playlist_get_link($userPlaylist);?>
  <?php endforeach;?>
  <div class="user-video-manager-options">
    <?=l('Create A New Playlist', 'playlist/'.$user_video_manager->getUid().'/add')?>
    <?=l('See All Your Videos', 'your-videos')?>
  </div>
</div>
<?php endif;?>

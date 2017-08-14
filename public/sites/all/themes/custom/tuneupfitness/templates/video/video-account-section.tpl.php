<?php
/** @var UserVideoManager $user_video_manager */
?>

<?php if (!empty($at_home_playlists) || !empty($other_playlists)): ?>
    <div class="user-dashboard-row clearfix">
        <h2 class="blue-title my-videos">Self-Care Fitness Videos – My Playlists</h2>
      <?php if (!empty($at_home_playlists)): ?>
        <div class="user-playlist playlist-non-editable">
            <a class="playlist-watch-link" data-toggle="collapse"
               href="#at-home-playlists" aria-expanded="false"
               aria-controls="at-home-playlists">
                <span class="playlist-image"><?=$at_home_playlists_image?></span>
                <span class="video-title">At Home Program</span>
            </a>
            <div class="collapse" id="at-home-playlists">
              <?php foreach ($at_home_playlists as $userPlaylist): ?>
                <?= video_playlist_get_link($userPlaylist); ?>
              <?php endforeach; ?>
            </div>
        </div>
      <?php endif; ?>
      <?php foreach ($other_playlists as $userPlaylist): ?>
        <?= video_playlist_get_link($userPlaylist); ?>
      <?php endforeach; ?>
        <div class="user-video-manager-options">
          <?= l('Create A New Playlist', 'playlist/' . $user_video_manager->getUid() . '/add', array(
            'attributes' => array(
              'class' => array(
                'btn',
                'btn-primary'
              )
            )
          )) ?>
          <?= l('See All Your Videos', 'your-videos', array(
            'attributes' => array(
              'class' => array(
                'btn',
                'btn-default'
              )
            )
          )) ?>
        </div>
    </div>
<?php elseif(count($user_video_manager->getVideos())):?>
<div class="user-dashboard-row clearfix">
    <h2 class="blue-title my-videos">Self-Care Fitness Videos – My Playlists</h2>
    <div class="user-video-manager-options">
      <?= l('Create A New Playlist', 'playlist/' . $user_video_manager->getUid() . '/add', array(
        'attributes' => array(
          'class' => array(
            'btn',
            'btn-primary'
          )
        )
      )) ?>
      <?= l('See All Your Videos', 'your-videos', array(
        'attributes' => array(
          'class' => array(
            'btn',
            'btn-default'
          )
        )
      )) ?>
    </div>
</div>
<?php endif;?>

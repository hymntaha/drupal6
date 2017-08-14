<div class="video-playlist-watch">
  <div class="player">
    <?=render($embed);?>
  </div>
  <div class="playlist-videos">
    <?php $i = 0;?>
    <?php foreach($videos['nodes'] as $video):?>
      <?php if(is_array($video)):?>
        <div data-playlist-id="<?=$i;?>" class="playlist-video">
          <?=render($video);?>
          <?php $i++;?>
        </div>
      <?php endif;?>
    <?php endforeach;?>
  </div>
  <div class="video-extra-content">
    <?=render($extra_content);?>
  </div>
  <div class="other-videos">
    <?=render($other_videos);?>
  </div>
</div>
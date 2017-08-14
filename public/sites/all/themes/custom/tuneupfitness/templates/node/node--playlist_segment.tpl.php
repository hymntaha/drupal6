<div id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?> row clearfix"<?php print $attributes; ?>>
  <div class="col-xs-3">
    <?=render($content['field_video_thumbnail']);?>
  </div>
  <div class="col-xs-9">
      <div class="video-segment-title">
          <?=$title?>
      </div>
      <?php if($title != 'About Yoga Tune Up'):?>
      <div class="toggle-favorite" data-is-favorite="<?=$content['favorite']?>">
          <i class="fa" aria-hidden="true"></i>
      </div>
      <?php endif;?>
  </div>
</div>
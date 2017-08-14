<div id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?> row clearfix"<?php print $attributes; ?>>
  <div class="col-xs-3">
    <?=render($content['field_video_thumbnail']);?>
  </div>
  <div class="col-xs-9">
      <div class="video-segment-title">
          <?=$title?>
      </div>
  </div>
</div>
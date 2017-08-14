<div class="row">
  <div class="col-md-10">
    <div class="row">
      <div class="col-md-3">
        <?=render($form['video_categories'])?>
      </div>
      <div class="col-md-3">
        <?=render($form['video_body_focus'])?>
      </div>
      <div class="col-md-3">
        <?=render($form['video_activity'])?>
      </div>
      <div class="col-md-3">
        <?=render($form['video_series'])?>
      </div>
    </div>
  </div>
  <div class="col-md-2">
    <?=render($form['favorite'])?>
    <?=render($form['reset_filters'])?>
  </div>
</div>
<?=drupal_render_children($form)?>
<div class="video-count-wrapper">
    <strong>Total Videos: <span class="video-count"></span></strong>
</div>
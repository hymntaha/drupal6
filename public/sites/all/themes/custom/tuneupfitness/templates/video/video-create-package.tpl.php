<div class="row">
    <div class="col-md-3 pull-right-md">
      <?= render($form); ?>
    </div>
    <div class="col-md-9">
      <?= render($body); ?>
      <div id="create-packages-extra-info" class="alert alert-dismissible<?=$extra_info == '' ? ' hidden' : ''?>">
          <button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <div class="content">
            <?= $extra_info ?>
          </div>
      </div>
      <?= render($filter_form); ?>
        <div id="create-packages-videos" class="hidden">
          <?= render($videos); ?>
        </div>
        <div class="text-right">
            <button class="btn btn-default<?= $video_count == 0 ? ' hidden' : '' ?>"
                    id="add-all-to-package">Add all <span
                        class="video-count"><?= $video_count ?></span> video(s) to
                Package
            </button>
            <button id="all-added" class="btn btn-default disabled hidden" disabled="disabled">All Videos Added</button>
        </div>
    </div>
</div>
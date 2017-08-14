<div class="video-playlist-watch">
    <div class="video-playlist-watch-row row">
        <div class="col-sm-7 col-lg-8">
            <div class="player">
                <?= render($embed); ?>
            </div>
        </div>
        <div class="col-sm-5 col-lg-4">
            <div class="hidden-xs">
                <h5 id="in-this-playlist-title">In This Playlist</h5>
                <div class="playlist-videos playlist-videos-adjust">
                    <?php $i = 0; ?>
                    <?php foreach ($videos['nodes'] as $video): ?>
                        <?php if (is_array($video)): ?>
                            <div data-playlist-id="<?= $i; ?>"
                                 class="playlist-video">
                                <?= render($video); ?>
                                <?php $i++; ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="visible-xs">
                <h5 id="in-this-playlist-mobile-title">
                    <a class="btn btn-primary" role="button" data-toggle="collapse" href="#playlist-videos-collapse" aria-expanded="false" aria-controls="playlist-videos-collapse">In This Playlist <span class="glyphicon glyphicon-chevron-down"></span></a>
                </h5>
                <div id="playlist-videos-collapse" class="playlist-videos collapse">
                    <?php $i = 0; ?>
                    <?php foreach ($videos['nodes'] as $video): ?>
                        <?php if (is_array($video)): ?>
                            <div data-playlist-id="<?= $i; ?>"
                                 class="playlist-video">
                                <?= render($video); ?>
                                <?php $i++; ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="video-playlist-watch-row row">
        <div class="col-xs-6 col-sm-5 col-lg-3">
            <?= l('Back to Dashboard', 'user'); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-7 col-lg-8">
            <div class="video-extra video-extra-content">
                <?= render($extra_content); ?>
            </div>
        </div>
        <div class="col-sm-5 col-lg-4">
            <div class="hidden-xs">
                <div class="video-extra other-videos">
                    <h5>Your Other Videos</h5>
                    <?= render($other_videos); ?>
                </div>
            </div>
            <div class="visible-xs">
                <div class="other-videos">
                    <h5><a class="btn btn-primary" role="button" data-toggle="collapse"  href="#playlist-other-videos-collapse" aria-expanded="false" aria-controls="playlist-other-videos-collapse">Your Other Videos <span class="glyphicon glyphicon-chevron-down"></span></a></h5>
                    <div id="playlist-other-videos-collapse" class="collapse">
                        <?= render($other_videos); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
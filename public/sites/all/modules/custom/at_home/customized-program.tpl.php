<div class="column left-column">
    <header><?=$atcp_header?></header>
    <div class="customized-paragraphs">
      <?=$atcp_p1?>
      <?=$atcp_p2?>
      <?=$atcp_p3?>
    </div>
    <div class="cp-additional column left-column">
      <?=$atcp_left?>
    </div>
    <div class="column right-column">
        <div class="get-me-started-wrapper">
            <div class="get-me-started-header">
              <?=$atcp_box_header?>
                <div class="get-me-started-content">
                  <?=render($subscription_teaser);?>
                </div>
            </div>
            <div class="get-me-started-footer">
              <?=$atcp_box_footer?>
                <p class="details"><a data-toggle="modal" data-target="#at-home-customized-info" href="#">Click here</a> for more details.</p>
                <div id="at-home-customized-info" class="modal fade" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <div class="modal-title">
                                    At Home Program Starter Package Details
                                </div>
                            </div>
                            <div class="modal-body">
                              <?=$atcp_box_popup?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="column right-column">
  <?=$atcp_right?>
</div>
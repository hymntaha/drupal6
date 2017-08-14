<?php
$sidebar_second = render($page['sidebar_second']);
?>
<div class="container">
  <header id="header" role="banner">
    <?php if ($logo): ?>
      <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>"
         rel="home" id="logo"><img src="<?php print $logo; ?>"
                                   alt="<?php print t('Home'); ?>"/></a>
    <?php endif; ?>
  </header>

  <div id="main">
    <div id="main-wrapper" class="clearfix row">
      <div class="<?= ($sidebar_second) ? 'col-md-9' : 'col-md-12'; ?>">
        <div id="content" role="main">
          <?php print $messages; ?>
          <?php print render($page['content']); ?>
        </div>
      </div>
      <?php if ($sidebar_second): ?>
        <aside class="col-md-3">
          <?php print $sidebar_second; ?>
        </aside>
      <?php endif; ?>
    </div>
  </div>
</div>

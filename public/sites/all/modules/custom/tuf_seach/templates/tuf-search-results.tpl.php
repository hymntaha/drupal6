<?php
/** @var TUFSearchResult[] $results */
?>
<h2><?=$category?></h2>
<div class="search-results">
  <?php $i = 0;?>
  <?php foreach($results as $result):?>
    <?php if($limit && $limit == $i):?>
      <?php continue;?>
    <?php endif;?>
    <?=render($result)?>
    <?php $i++;?>
  <?php endforeach;?>
</div>
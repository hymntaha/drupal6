<?php
/** @var TUFSearchResult[] $results */
?>
<div class="row">
  <div class="col-md-8 col-md-offset-2">
    <hr />
    <h3><?=$category['title']?></h3>
    <div class="list-group search-results">
      <?php if(empty($results)):?>
        <div class="list-group-item">
          <p class="list-group-item-text">There were no results in this category.</p>
        </div>
      <?php else:?>
        <?php $i = 0;?>
        <?php foreach($results as $result):?>
          <?php if($limit && $limit == $i):?>
            <?php continue;?>
          <?php endif;?>
          <?=render($result)?>
          <?php $i++;?>
        <?php endforeach;?>
      <?php endif;?>
    </div>
    <?php if($limit > 0 && !empty($results)):?>
    <a href="<?=$category['url']?>" type="button" class="btn btn-large search-<?=$category['code']?>">See all <?=$category['count']?> <?=$category['title']?> results</a>
    <?php endif;?>
  </div>
</div>
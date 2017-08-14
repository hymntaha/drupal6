<div class="row">
  <div class="col-xs-12 text-center">
    <h2>You searched for "<?=$keyword?>"</h2>
    <hr>
  </div>
</div>
<div class="row">
  <div class="col-md-8 col-md-offset-2">
    <h3>Search Results</h3>
    <a href="/<?=current_path()?>" class="btn btn-default" type="button">All <span class="badge"><?=$total_count?></span></a>
    <?php foreach($categories as $category):?>
      <a href="<?=$category['url']?>" type="button" class="btn search-<?=$category['code']?>"><?=$category['title']?> <span class="badge"><?=$category['count']?></span></a>
    <?php endforeach;?>
  </div>
</div>
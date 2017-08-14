<a class="list-group-item" href="<?=$url?>">
  <?php if(!empty($tag)):?>
    <span class="badge text-capitalize"><?=$tag?></span>
  <?php endif;?>
  <h4 class="list-group-item-heading"><?=$title?></h4>
  <?=$teaser?>
</a>
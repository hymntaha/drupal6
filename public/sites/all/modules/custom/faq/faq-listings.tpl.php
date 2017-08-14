<div class="panel-group faq-listings" id="accordion" role="tablist" aria-multiselectable="true">
    <?php $counter = 0; ?>
    <?php foreach($categories as $category_name => $faqs): ?>
    <div class="panel panel-default faq-category">
        <div class="panel-heading" role="tab" id="heading-<?=$counter?>">
            <h4 class="panel-title category-name">
                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-<?=$counter?>" aria-expanded="true" aria-controls="collapse-<?=$counter?>">
                    <?=$category_name?>
                </a>
            </h4>
        </div>
        <div id="collapse-<?=$counter?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-<?=$counter?>">
            <div class="panel-body">
                <?=render($faqs);
                $counter++; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
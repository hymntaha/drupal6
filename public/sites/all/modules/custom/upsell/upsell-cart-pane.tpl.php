<?php if(count($products)): ?>
<div class="cart-upsell-pane">
	<?php if(!empty($callout)): ?><p class="red-title" style="font-weight:normal;"><?=$callout?></p><?php endif;?>
	<p class="blue-title"><?= $cart_has_upsell ? 'Additional ' : ''?> Suggested Products</p>
	<?=render($products)?>
</div>
<?php endif;?>
<div class="next-shipment" id="next-shipment-container">
	<div class="billing-cycle">
		<h2 class="blue-title">Next Shipment</h2>
		<h3>Billing Cycle:</h3>
		<p>Bills on <strong><span id="next-shipment-date"><?=$next_shipment_date?></span></strong>, ships thereafter (<span id="shipping-option"><?=$shipping_option?></span>)</p>
	</div>
	<div class="shipping-next">
		<h3>Shipping Next:</h3>
		<?php $i = 0;?>
		<?php foreach($shipping_next as $product): ?>
			<div class="row<?=$i == 0 ? ' first' : ''?> clearfix" data-product-key="l<?=$product->level?>m<?=$product->month?>">
				<span class="title label"><strong><?=$product->title?></strong></span>
				<span class="price"><?=$product->display_price?></span>
				<?php if(count($shipping_next) > 1):?>
				<span class="remove">x</span>
				<span class="skip">skip</span>
				<?php endif;?>
			</div>
			<?php $i++?>
		<?php endforeach;?>
		<div class="total-calc">
            <?php if(!empty($total_calc['shipping_title'])):?>
			<p class="shipping"><span class="title label"><?=$total_calc['shipping_title']?>:</span> <span class="price"><?=$total_calc['shipping_price']?></span></p>
            <?php endif;?>
			<?php if(!empty($total_calc['tax'])): ?>
			<p class="tax"><span class="title label">Tax:</span> <span class="price"><?=$total_calc['tax']?></span></p>
			<?php endif;?>
			<p class="total"><span class="title label"><strong>Total Cost:</strong></span> <span class="price"><?=$total_calc['total']?></span> (for <?=count($shipping_next)?> DVD<?=count($shipping_next) > 1 ? 's' : ''?>)</p>
		</div>
	</div>
    <button id="edit-trigger-ship-now">
        Ship Order Now
    </button>
</div>
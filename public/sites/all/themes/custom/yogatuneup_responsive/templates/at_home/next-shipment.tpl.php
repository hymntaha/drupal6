<div class="next-shipment" id="next-shipment-container">
    <div class="next-shipment-border">
        <div class="billing-cycle">
            <h2 class="blue-title">Next Order</h2>
        </div>
        <div class="shipping-next">
            <?php $i = 0;?>
            <?php $shippable = FALSE; ?>
            <?php foreach($shipping_next as $product): ?>
                <?php $shippable = $shippable || $product->shippable;?>
                <div class="row<?=$i == 0 ? ' first' : ''?> shipping-next-row clearfix" data-product-key="l<?=$product->level?>m<?=$product->month?>">
                    <?php if(count($shipping_next) > 1):?>
                        <span class="remove">
                        <span class="glyphicon glyphicon-remove"></span>
                    </span>
                    <?php endif;?>
                    <span class="title label"><strong><?=$product->title?></strong></span>
                    <span class="price"><?=$product->display_price?></span>
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
                <p class="total"><span class="title label"><strong>Total Cost:</strong></span> <span class="price"><?=$total_calc['total']?></span> (for <?=count($shipping_next)?> <?=$shippable ? 'DVD' : 'Online Video'?><?=count($shipping_next) > 1 ? 's' : ''?>)</p>
            </div>
        </div>
    </div>
    <div class="row ship-now">
        <div class="col-xs-12 text-right">
        <span>This order bills on <strong><span id="next-shipment-date"><?=$next_shipment_date?></span></strong>
            <?php if(!empty($total_calc['shipping_title'])):?>
                , ships thereafter (<span id="shipping-option"><?=$shipping_option?></span>)
            <?php endif;?>
        </span>
            &nbsp; or &nbsp;
            <button id="edit-trigger-ship-now" class="btn">
                Ship Order Now
            </button>
        </div>
    </div>
</div>
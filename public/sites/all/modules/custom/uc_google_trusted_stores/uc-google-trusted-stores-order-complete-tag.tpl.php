<!-- START Google Trusted Stores Order -->
<div id="gts-order" style="display:none;" translate="no">

    <!-- start order and merchant information -->
    <span id="gts-o-id"><?=$order->order_id?></span>
    <span id="gts-o-email"><?=$order->primary_email?></span>
    <span id="gts-o-country"><?=_uc_google_trusted_stores_get_order_information($order, 'country')?></span>
    <span id="gts-o-currency"><?=$order->currency?></span>
    <span id="gts-o-total"><?=$order->order_total?></span>
    <span id="gts-o-discounts"><?=_uc_google_trusted_stores_get_order_information($order, 'discounts')?></span>
    <span id="gts-o-shipping-total"><?=_uc_google_trusted_stores_get_order_information($order, 'shipping')?></span>
    <span id="gts-o-tax-total"><?=_uc_google_trusted_stores_get_order_information($order, 'tax')?></span>
    <span id="gts-o-est-ship-date"><?=_uc_google_trusted_stores_get_order_information($order, 'est_ship_date')?></span>
    <span id="gts-o-est-delivery-date"><?=_uc_google_trusted_stores_get_order_information($order, 'est_delivery_date')?></span>
    <span id="gts-o-has-preorder"><?=_uc_google_trusted_stores_get_order_information($order, 'backorder')?></span>
    <span id="gts-o-has-digital"><?=_uc_google_trusted_stores_get_order_information($order, 'digital')?></span>
    <!-- end order and merchant information -->

    <!-- start repeated item specific information -->
    <?php foreach($order->products as $product):?>
    <span class="gts-item">
        <span class="gts-i-name"><?=$product->title?></span>
        <span class="gts-i-price"><?=round($product->price,2)?></span>
        <span class="gts-i-quantity"><?=$product->qty?></span>
        <span class="gts-i-prodsearch-id"><?=$product->model?></span>
        <span class="gts-i-prodsearch-store-id">109650929</span>
    </span>
    <?php endforeach;?>
    <!-- end repeated item specific information -->

</div>
<!-- END Google Trusted Stores Order -->
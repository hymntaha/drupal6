<div class="checkout-buttons-box-wrapper hidden-xs hidden-sm">
    <div class="checkout-buttons-box">
        <div>
            <p>All dollar amounts are in USD. Exchange rates may vary.</p>
        </div>
        <div>
            <?= l('Finalize Your Order', '', array(
                    'attributes' => array(
                            'class' => array(
                                    'finalize-order-btn-proxy',
                                    'trigger-checkout-submit',
                                    'btn',
                                    'btn-large',
                                    'btn-primary',
                            ),
                    ),
            )); ?>
        </div>
        <div class="row">
            <div class="col-md-6">
                <?= l('Have a coupon/gift code?', '', array('attributes' => array('class' => array('coupon-code-link')))); ?>
            </div>
            <div class="col-md-6 text-right">
                <?= l('Continue Shopping', 'shop-yoga-tune-up', array('attributes' => array('class' => array('faded-link')))); ?>
            </div>
        </div>
        <input type="hidden" name="uc-coupon-apply" value=""/>
    </div>
</div>
<?= l('Update Your Order', 'cart', array('attributes' => array('id' => 'order-summary-update-order-link', 'class' => array('faded-link')))); ?>
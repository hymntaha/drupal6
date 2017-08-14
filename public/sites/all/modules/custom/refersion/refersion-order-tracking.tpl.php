<!-- REFERSION TRACKING: BEGIN -->
<script src="//www.refersion.com/tracker/v3/pub_03af9506df57af918800.js"></script>
<script>
    _refersion(function(){

        _rfsn._addTrans({
            'order_id': '<?=$order_id?>',
            <?php if($shipping):?>
            'shipping': '<?=$shipping?>',
            <?php endif;?>
            <?php if($tax):?>
            'tax': '<?=$tax?>',
            <?php endif;?>
            <?php if($discount):?>
            'discount': '<?=$discount['amount']?>',
            'discount_code': '<?=$discount['code']?>',
            <?php endif;?>
            'currency_code': 'USD'
        });

        _rfsn._addCustomer({
            'first_name': '<?=$customer['first_name']?>',
            'last_name': '<?=$customer['last_name']?>',
            'email': '<?=$customer['email']?>',
            'ip_address': '<?=$customer['ip_address']?>'
        });

        <?php foreach($items as $item):?>
        _rfsn._addItem({
            'sku': '<?=$item['sku']?>',
            'quantity': '<?=$item['qty']?>',
            'price': '<?=$item['price']?>'
        });
        <?php endforeach;?>

        _rfsn._sendConversion();

    });
</script>
<!-- REFERSION TRACKING: END -->
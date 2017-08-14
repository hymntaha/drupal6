<!-- BEGIN: Google Trusted Stores -->
<script type="text/javascript"> var gts = gts || [];

    gts.push(["id", "693018"]);
    gts.push(["badge_position", "BOTTOM_LEFT"]);
    gts.push(["locale", "en_US"]);
    <?php if(!empty($sku)):?>
    gts.push(["google_base_offer_id", "<?=$sku?>"]);
    <?php endif;?>
    gts.push(["google_base_subaccount_id", "109650929"]);

    (function () {
        var gts = document.createElement("script");
        gts.type = "text/javascript";
        gts.async = true;
        gts.src = "https://www.googlecommerce.com/trustedstores/api/js";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(gts, s);
    })();
</script>
<!-- END: Google Trusted Stores -->
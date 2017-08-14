<?php if(!empty($adv_id) && !empty($pix_id)):?>
<script type="text/javascript">
<?php if(!empty($order_subtotal)):?>
adroll_conversion_value_in_dollars = <?=$order_subtotal?>;
<?php endif;?>
adroll_adv_id = "<?=$adv_id?>";
adroll_pix_id = "<?=$pix_id?>";
(function () {
var oldonload = window.onload;
window.onload = function(){
   __adroll_loaded=true;
   var scr = document.createElement("script");
   var host = (("https:" == document.location.protocol) ? "https://s.adroll.com" : "http://a.adroll.com");
   scr.setAttribute('async', 'true');
   scr.type = "text/javascript";
   scr.src = host + "/j/roundtrip.js";
   ((document.getElementsByTagName('head') || [null])[0] ||
    document.getElementsByTagName('script')[0].parentNode).appendChild(scr);
   if(oldonload){oldonload()}};
}());
</script>
<?php endif;?>
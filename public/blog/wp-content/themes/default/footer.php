<?php wp_footer(); ?>

<?php if(isset($archine) && $archine =="no") {} else { ?>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-7945470-1");
pageTracker._trackPageview();
} catch(err) {}</script>

<script type="text/javascript">
adroll_adv_id = "YUUWEB5G45AQBGUWTZU2GC";
adroll_pix_id = "QCKSLO4ZSREOJEXD2ODXZL";
(function () {
var oldonload = window.onload;
window.onload = function(){ __adroll_loaded=true; var scr = document.createElement("script"); var host = (("https:" == document.location.protocol) ? "https://s.adroll.com" : "http://a.adroll.com"); scr.setAttribute('async', 'true'); scr.type = "text/javascript"; scr.src = host + "/j/roundtrip.js"; ((document.getElementsByTagName('head') || [null])[0] || document.getElementsByTagName('script')[0].parentNode).appendChild(scr); if(oldonload){oldonload()}};
}());
</script>
<!-- Go to www.addthis.com/dashboard to customize your tools -->
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-553a94a949b7d7ca" async="async"></script>

<?php }


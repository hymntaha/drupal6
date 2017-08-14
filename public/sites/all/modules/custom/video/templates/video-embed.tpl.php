<div id='mediaplayer-<?=$nid?>'></div>
<script type="text/javascript">
   jwplayer('mediaplayer-<?=$nid?>').setup(<?=drupal_json_encode($json_array)?>);
</script>
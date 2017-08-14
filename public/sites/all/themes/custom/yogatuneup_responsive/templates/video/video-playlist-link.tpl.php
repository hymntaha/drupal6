<?php
$uri = 'watch/'.$alias;
if($alias == 'at-home-program'){
    $uri = 'user/'.$uid.'/at-home-videos';
}
$path = '';
if(!empty($image)){
    $path .= '<span class="col-xs-3 col-md-2">'.$image.'</span>';
    $path .= '<span class="col-xs-9 col-md-10"><span class="video-title">'.$title.'</span></span>';
}
else{
    $path .= '<span class="col-xs-12"><span class="video-title">'.$title.'</span></span>';
}
?>
<div class="video row">
    <?=l($path, $uri, array('html' => TRUE))?>
</div>
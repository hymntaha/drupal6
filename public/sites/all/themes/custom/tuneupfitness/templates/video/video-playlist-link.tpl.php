<?php
/** @var UserPlaylist $playlist */

$uri = 'watch/' . $alias;
if ($alias == 'at-home-program') {
  $uri = 'user/' . $uid . '/at-home-videos';
}
$path = '';
if (!empty($image)) {
  $path .= '<span class="playlist-image">' . $image . '</span>';
}
$path .= '<span class="video-title">' . $title . '</span>';
?>

<div class="user-playlist <?=$playlist->getEditable() ? 'playlist-editable' : 'playlist-non-editable'?>">
  <?php if ($playlist->getEditable()): ?>
    <?= l($path, $uri, array('html' => TRUE, 'attributes' => array('class' => array('playlist-watch-link')))) ?><?= l('Manage', 'playlist/' . $playlist->getId() . '/edit', array('attributes' => array('class' => array('playlist-manage-link')))) ?>
  <?php else: ?>
    <?= l($path, $uri, array('html' => TRUE, 'attributes' => array('class' => array('playlist-watch-link')))) ?>
  <?php endif; ?>
</div>
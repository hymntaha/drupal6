<?php
   session_start();
?>

<title>Tune Up Fitness Blog <?php wp_title(); ?></title>

<?php if (have_posts()):while(have_posts()):the_post();endwhile;endif;?>

<link rel="stylesheet" href="//<?=$_SERVER['HTTP_HOST']?>/blog/wp-content/themes/default/style.css" type="text/css" media="screen" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<link href="//<?=$_SERVER['HTTP_HOST']?>/blog/css/main.css" rel="stylesheet" type="text/css" />
<link href="//<?=$_SERVER['HTTP_HOST']?>/blog/css/mod_yoo_carousel.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="//<?=$_SERVER['HTTP_HOST']?>/blog/js/mootools.js"></script>
<script type="text/javascript" src="//<?=$_SERVER['HTTP_HOST']?>/blog/js/mod_yoo_carousel.js"></script>


<?php wp_head(); 
   $base_url_host_string = "http".($_SERVER['HTTPS'] ? "s" : "")."://".$_SERVER['HTTP_HOST'];

   if (($_SERVER['HTTP_HOST'] == 'xicom') || ($_SERVER['HTTP_HOST'] == 'xicom-200')) {
      define("HTTP_BASE_URL","http://beta.yogatuneup.com/blog/");
      define("HTTP_ADMIN_BASE_URL","http://beta.yogatuneup.com/blog/");
      define("HTTP_BASE_DIR","/www/beta.yogatuneup.com/blog/");
      define("Image_Path","http://beta.yogatuneup.com/blog/view/");
   }
   else if(stripos($_SERVER['HTTP_HOST'],'avatarnewyork.com')){
      define("HTTP_BASE_URL",$base_url_host_string."/");
      define("HTTP_ADMIN_BASE_URL",$base_url_host_string."/admin/");
      define("HTTP_BASE_DIR","/home/web/yogatuneup/jesse/staging");
      define("Image_Path",$base_url_host_string."/");
   }
   else 
   {
      define("HTTP_BASE_URL",$base_url_host_string."/");
      define("HTTP_ADMIN_BASE_URL",$base_url_host_string."/admin/");
      define("HTTP_BASE_DIR","/home/yoga1/www/yogatuneup.com/");
      define("Image_Path",$base_url_host_string."/");
   }

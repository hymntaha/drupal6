<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */

ytu_page_start();
?>

	<div id="content" class="col-sm-8 widecolumn">

<div align="left">
<a href="/blog">
<h1 class="page-header">Tune Up Fitness<sup class="blue14">&reg;</sup> Blog</h1>
</a>

</div>
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

		<div class="navigation">
			<div class="alignleft"><?php previous_post_link('&laquo; %link') ?></div>
			<div class="alignright"><?php next_post_link('%link &raquo;') ?></div>
		</div>

		<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
			<h2 ><?php the_title(); ?></h2>
			<table width="100%" border="0">
  <tr>
    <td width="3%" align="left"><?php the_author_image(); ?></td>
    <td width="97%" valign="top" align="left"><div >By: <?php the_author_posts_link(); ?>  | <?php /* This is commented, because it requires a little adjusting sometimes.
							You'll need to download this plugin, and follow the instructions:
							http://binarybonsai.com/archives/2004/08/17/time-since-plugin/ */
							/* $entry_datetime = abs(strtotime($post->post_date) - (60*120)); echo time_since($entry_datetime); echo ' ago'; */ ?> <?php the_time('l, F jS, Y') ?>
       | <a href="<?php comments_link(); ?>">
      Comments <?php comments_number('0', '1', '%'); ?>
</a>
	   <div >
	   Category: 
       <?php the_category(', ') ?> | <?php the_tags(); ?></div></div></td>
  </tr>
</table>
				<!-- Go to www.addthis.com/dashboard to customize your tools -->
				<?php 
					$url = get_permalink(); 
					$title = the_title('','', false); 
					//echo $url . $title ;
					do_action( 'addthis_widget', $url, $title, 'above' );
				?>
				

			<div class="entry">
				<?php the_content('<p class="serif">Read the rest of this entry &raquo;</p>'); ?>

				<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
		  </div>
		</div>
		
		<div >
			<!-- Go to www.addthis.com/dashboard to customize your tools -->
			<?php do_action( 'addthis_widget', $url, $title, 'above' ); ?>
            <div class="addthis_sharing_toolbox" data-url="<?php the_permalink(); ?>" data-title="<?php the_title_attribute(); ?>"></div>
		</div>
		
		<div class="about-author"><h2>About This Author</h2></div>
		<div class="entry" ><?php the_author_description(); ?></div>
<!--
	<script language="JavaScript" src="http://itde.vccs.edu/rss2js/feed2js.php?src=http%3A%2F%2Fwww.yogatuneup.com%2Fblog%2Fcategory%2Fhealth%2Ffeed%2F&chan=n&num=100&desc=0&date=n&targ=n" type="text/javascript"></script>

<noscript>
<a href="http://itde.vccs.edu/rss2js/feed2js.php?src=http%3A%2F%2Fwww.yogatuneup.com%2Fblog%2Fcategory%2Fhealth%2Ffeed%2F&chan=n&num=100&desc=0&date=n&targ=n&html=y">View RSS feed</a>
</noscript>
-->
	<?php comments_template(); ?>

	<?php endwhile; else: ?>

		<p>Sorry, no posts matched your criteria.</p>

<?php endif; ?>

	</div>

	
<?php get_sidebar(); ?>
<?php ytu_page_end(); ?>

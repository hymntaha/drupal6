<?php ytu_page_start(); ?>
<div id="content" class="col-sm-8 narrowcolumn"  >

		<h1 class="page-header">Tune Up Fitness<sup class="blue14">&reg;</sup> Blog</h1>

	<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); ?>

			<div class="post" id="post-<?php the_ID(); ?>">
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				<table width="100%" border="0">
				  <tr>
				    <td width="3%"><?php the_author_image(); ?></td>
				    <td width="97%" valign="top">
					    <div>By: <?php the_author_posts_link(); ?>  | <?php /* This is commented, because it requires a little adjusting sometimes.
												You'll need to download this plugin, and follow the instructions:
												http://binarybonsai.com/archives/2004/08/17/time-since-plugin/ */
												/* $entry_datetime = abs(strtotime($post->post_date) - (60*120)); echo time_since($entry_datetime); echo ' ago'; */ ?> <?php the_time('l, F jS, Y') ?>
				       | <a href="<?php comments_link(); ?>">
				      Comments <?php comments_number('0', '1', '%'); ?>
						</a>
					   <div >
						   Category:
					       <?php the_category(', ') ?> | <?php the_tags(''); ?></div>
				       </div>
				    </td>
				  </tr>
				</table>

				<div  class="entry">
					<?php the_content('<b>Read the rest of this blog post &raquo;</b>'); ?>
				</div>


			<!-- get the post related data -->
			<?php
				$url = get_permalink();
				$title = the_title('','', false);
				//echo $url . $title ;
			?>

			<!-- Go to www.addthis.com/dashboard to customize your tools -->
			<?php do_action( 'addthis_widget', $url, $title, 'above' ); ?>
                <div class="addthis_sharing_toolbox" data-url="<?php the_permalink(); ?>" data-title="<?php the_title_attribute(); ?>"></div>
				<p class="postmetadata"><?php the_tags('Tags: ', ', ', '<br />'); ?> Posted in <?php the_category(', ') ?> | <?php edit_post_link('Edit', '', ' | '); ?>  <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></p>
		</div><!-- post -->

		<?php endwhile; ?>

		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Older Entries') ?></div>
			<div class="alignright"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
		</div>

	<?php else : ?>

		<h2 class="center">Not Found</h2>
		<p class="center">Sorry, but you are looking for something that isn't here.</p>
		<?php include (TEMPLATEPATH . "/searchform.php"); ?>

	<?php endif; ?>

</div><!-- content -->

<?php get_sidebar(); ?>

<?php ytu_page_end(); ?>
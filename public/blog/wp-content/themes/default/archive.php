<?php ytu_page_start(); ?>

<div id="content" class="col-sm-8 narrowcolumn" >

<div align="left">

<h1 class="page-header">Tune Up Fitness<sup class="blue14">&reg;</sup> Blog</h1>

</div>
<!-- This sets the $curauth variable -->

    <?php
    if(isset($_GET['author_name'])) :
        $curauth = get_userdatabylogin($first_name);
    else :
        $curauth = get_userdata(intval($author));
    endif;
    ?>

    <h2><?php echo $curauth->first_name; ?> <?php echo $curauth->last_name; ?></h2>

    <dl>
        <dt></dt>
        <dd></dd>
        <span class="bio1" ><?php the_author_image(); ?></span>
        <span class="bio1"><?php echo $curauth->user_description; ?></span>
    </dl>

    <h2><?php //echo $curauth->first_name;?> Archives</h2>

    <ul>
<!-- The Loop -->

    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        <li>
            <a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link: <?php the_title(); ?>">
            <?php the_title(); ?></a>,
        </li>

    <?php endwhile; else: ?>
        <p><?php _e('No posts by this author.'); ?></p>

    <?php endif; ?>

<!-- End Loop -->

    </ul>
</div>
<?php get_sidebar(); ?>
<?php ytu_page_end(); ?>

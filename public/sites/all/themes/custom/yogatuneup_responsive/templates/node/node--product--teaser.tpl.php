<?php
/**
 * @file
 * Zen theme's implementation to display a node.
 *
 * Available variables:
 * - $title: the (sanitized) title of the node.
 * - $content: An array of node items. Use render($content) to print them all,
 *   or print a subset such as render($content['field_example']). Use
 *   hide($content['field_example']) to temporarily suppress the printing of a
 *   given element.
 * - $user_picture: The node author's picture from user-picture.tpl.php.
 * - $date: Formatted creation date. Preprocess functions can reformat it by
 *   calling format_date() with the desired parameters on the $created variable.
 * - $name: Themed username of node author output from theme_username().
 * - $node_url: Direct url of the current node.
 * - $display_submitted: Whether submission information should be displayed.
 * - $submitted: Submission information created from $name and $date during
 *   template_preprocess_node().
 * - $classes: String of classes that can be used to style contextually through
 *   CSS. It can be manipulated through the variable $classes_array from
 *   preprocess functions. The default values can be one or more of the
 *   following:
 *   - node: The current template type, i.e., "theming hook".
 *   - node-[type]: The current node type. For example, if the node is a
 *     "Blog entry" it would result in "node-blog". Note that the machine
 *     name will often be in a short form of the human readable label.
 *   - node-teaser: Nodes in teaser form.
 *   - node-preview: Nodes in preview mode.
 *   - view-mode-[mode]: The view mode, e.g. 'full', 'teaser'...
 *   The following are controlled through the node publishing options.
 *   - node-promoted: Nodes promoted to the front page.
 *   - node-sticky: Nodes ordered above other non-sticky nodes in teaser
 *     listings.
 *   - node-unpublished: Unpublished nodes visible only to administrators.
 *   The following applies only to viewers who are registered users:
 *   - node-by-viewer: Node is authored by the user currently viewing the page.
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 *
 * Other variables:
 * - $node: Full node object. Contains data that may not be safe.
 * - $type: Node type, i.e. story, page, blog, etc.
 * - $comment_count: Number of comments attached to the node.
 * - $uid: User ID of the node author.
 * - $created: Time the node was published formatted in Unix timestamp.
 * - $pubdate: Formatted date and time for when the node was published wrapped
 *   in a HTML5 time element.
 * - $classes_array: Array of html class attribute values. It is flattened
 *   into a string within the variable $classes.
 * - $zebra: Outputs either "even" or "odd". Useful for zebra striping in
 *   teaser listings.
 * - $id: Position of the node. Increments each time it's output.
 *
 * Node status variables:
 * - $view_mode: View mode, e.g. 'full', 'teaser'...
 * - $teaser: Flag for the teaser state (shortcut for $view_mode == 'teaser').
 * - $page: Flag for the full page state.
 * - $promote: Flag for front page promotion state.
 * - $sticky: Flags for sticky post setting.
 * - $status: Flag for published status.
 * - $comment: State of comment settings for the node.
 * - $readmore: Flags true if the teaser content of the node cannot hold the
 *   main body content. Currently broken; see http://drupal.org/node/823380
 * - $is_front: Flags true when presented in the front page.
 * - $logged_in: Flags true when the current user is a logged-in member.
 * - $is_admin: Flags true when the current user is an administrator.
 *
 * Field variables: for each field instance attached to the node a corresponding
 * variable is defined, e.g. $node->body becomes $body. When needing to access
 * a field's raw values, developers/themers are strongly encouraged to use these
 * variables. Otherwise they will have to explicitly specify the desired field
 * language, e.g. $node->body['en'], thus overriding any language negotiation
 * rule that was previously applied.
 *
 * @see template_preprocess()
 * @see template_preprocess_node()
 * @see zen_preprocess_node()
 * @see template_process()
 */

?>
<article class="node-<?php print $node->nid; ?> <?php print $classes; ?> list-view clearfix"<?php print $attributes; ?>>

    <header>
      <?php print render($title_prefix); ?>
      <?php if (!$page && $view_mode != 'testimonial'): ?>
          <?php if ($view_mode == 'video'): ?>
              <h2<?php print $title_attributes; ?>><?php print $title; ?></h2>
          <?php else: ?>
              <h2<?php print $title_attributes; ?>><a href="<?php print $node_url; ?>"><?php print $title; ?></a></h2>
          <?php endif;?>
      <?php endif; ?>
      <?php print render($title_suffix); ?>

      <?php if ($display_submitted): ?>
        <p class="submitted">
          <?php print $user_picture; ?>
          <?php print $submitted; ?>
        </p>
      <?php endif; ?>

      <?php if ($unpublished): ?>
        <p class="unpublished"><?php print t('Unpublished'); ?></p>
      <?php endif; ?>
    </header>

  <?php
    // We hide the comments and links now so that we can render them later.
    hide($content['comments']);
    hide($content['uc_product_image']);
    hide($content['taxonomy_catalog']);
    hide($content['sell_price']);
    hide($content['add_to_cart']);
    hide($content['video_playlist']);
    hide($content['product_active']);
    hide($content['trimmed']);
    hide($content['video_active']);
    print render($content['uc_product_image']);
  ?>

  <div class="right-content">
    <?php print render($content); ?>
    <?php if(isset($content['video_playlist']['sell_price'])):?>
      <div class="product-type-selector">
        <div class="product-type clearfix">
          <label for="product-type-video-<?=$node->nid?>"><?=$content['video_playlist']['#node']->shippable ? 'Kit with ' : ''?>Online Video:</label>
          <input data-nid="<?=$node->nid?>" id="product-type-video-<?=$node->nid?>" type="radio" <?= $content['video_active']['#value'] ? 'checked="checked"' : ''?> name="product_type-<?=$node->nid?>" value="video" />
        </div>
        <div class="product-type clearfix">
          <label for="product-type-product-<?=$node->nid?>"><?=$content['video_playlist']['#node']->shippable ? 'Kit with ' : ''?>Physical DVD:</label>
          <input data-nid="<?=$node->nid?>" id="product-type-product-<?=$node->nid?>" type="radio" <?= $content['product_active']['#value'] ? 'checked="checked"' : ''?> name="product_type-<?=$node->nid?>" value="product" />
        </div>
      </div>
    <?php endif;?>
    <?php if(isset($content['video_playlist']['sell_price'])):?>
      <div data-option="product" data-nid="<?=$node->nid?>" class="add-to-cart-wrapper product-selector <?= $content['product_active']['#value'] ? 'active ' : ''?>clearfix">
        <?php print(render($content['sell_price']));?>
        <?php print(render($content['add_to_cart']));?>
      </div>
      <div data-option="video" data-nid="<?=$node->nid?>" class="add-to-cart-wrapper product-selector <?= $content['video_active']['#value'] ? 'active ' : ''?>clearfix">
        <?php print(render($content['video_playlist']['sell_price']));?>
        <?php print(render($content['video_playlist']['add_to_cart']));?>
      </div>
    <?php else:?>
      <div class="add-to-cart-wrapper clearfix">
        <?php print(render($content['sell_price']));?>
        <?php print(render($content['add_to_cart']));?>
      </div>
    <?php endif;?>
  </div>

</article><!-- /.node -->

<?php if($display_second_teaser):?>
<article class="node-<?php print $node->nid; ?> <?php print $classes; ?> grid-view col-sm-6 col-md-4 clearfix"<?php print $attributes; ?>>
  <div class="base">
    <div class="image clearfix">
      <?php print render($content['uc_product_image']); ?>
    </div>


    <div class="info-box">
      <?php print render($title_prefix); ?>
      <?php if (!$page && $view_mode != 'testimonial'): ?>
        <?php if ($view_mode == 'video'): ?>
          <h2<?php print $title_attributes; ?>><?php print $title; ?></h2>
        <?php else: ?>
          <h2<?php print $title_attributes; ?>><a href="<?php print $node_url; ?>"><?php print $title; ?></a></h2>
        <?php endif;?>
      <?php endif; ?>
      <?php print render($title_suffix); ?>

      <?php print(render($content['sell_price']));?>
    </div>

    <div class="overlay">
      <?php print render($title_prefix); ?>
      <?php if (!$page && $view_mode != 'testimonial'): ?>
        <?php if ($view_mode == 'video'): ?>
          <h2<?php print $title_attributes; ?>><?php print $title; ?></h2>
        <?php else: ?>
          <h2<?php print $title_attributes; ?>><a href="<?php print $node_url; ?>" class="prod-link"><?php print $title; ?></a></h2>
        <?php endif;?>
      <?php endif; ?>
      <?php print render($title_suffix); ?>

    <?php if(isset($content['video_playlist']['sell_price'])):?>
        <div class="product-type-selector">
            <div class="product-type clearfix">
                <label for="product-type-video-<?=$node->nid?>"><?=$content['video_playlist']['#node']->shippable ? 'Kit with ' : ''?>Online Video:</label>
                <input data-nid="<?=$node->nid?>" id="product-type-video-<?=$node->nid?>" type="radio" <?= $content['video_active']['#value'] ? 'checked="checked"' : ''?> name="product_type-<?=$node->nid?>" value="video" />
            </div>
            <div class="product-type clearfix">
                <label for="product-type-product-<?=$node->nid?>"><?=$content['video_playlist']['#node']->shippable ? 'Kit with ' : ''?>Physical DVD:</label>
                <input data-nid="<?=$node->nid?>" id="product-type-product-<?=$node->nid?>" type="radio" <?= $content['product_active']['#value'] ? 'checked="checked"' : ''?> name="product_type-<?=$node->nid?>" value="product" />
            </div>
        </div>
    <?php endif;?>
    <?php if(isset($content['video_playlist']['sell_price'])):?>
        <div data-option="product" data-nid="<?=$node->nid?>" class="add-to-cart-wrapper product-selector <?= $content['product_active']['#value'] ? 'active ' : ''?>clearfix">
            <?php print(render($content['sell_price']));?>
            <?php print(render($content['add_to_cart']));?>
        </div>
        <div data-option="video" data-nid="<?=$node->nid?>" class="add-to-cart-wrapper product-selector <?= $content['video_active']['#value'] ? 'active ' : ''?>clearfix">
            <?php print(render($content['video_playlist']['sell_price']));?>
            <?php print(render($content['video_playlist']['add_to_cart']));?>
        </div>
    <?php else:?>

      <?php print(render($content['sell_price']));?>

      <?php print render($content['trimmed']); ?>

      <?php print(render($content['add_to_cart']));?>
    <?php endif;?>
    </div>

  </div>
</article><!-- /.node -->
<?php endif;?>
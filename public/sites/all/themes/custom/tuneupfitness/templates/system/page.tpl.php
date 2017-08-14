<?php
   /**
   * @file
   * Default theme implementation to display a single Drupal page.
   *
   * The doctype, html, head and body tags are not in this template. Instead they
   * can be found in the html.tpl.php template in this directory.
   *
   * Available variables:
   *
   * General utility variables:
   * - $base_path: The base URL path of the Drupal installation. At the very
   *   least, this will always default to /.
   * - $directory: The directory the template is located in, e.g. modules/system
   *   or themes/bartik.
   * - $is_front: TRUE if the current page is the front page.
   * - $logged_in: TRUE if the user is registered and signed in.
   * - $is_admin: TRUE if the user has permission to access administration pages.
   *
   * Site identity:
   * - $front_page: The URL of the front page. Use this instead of $base_path,
   *   when linking to the front page. This includes the language domain or
   *   prefix.
   * - $logo: The path to the logo image, as defined in theme configuration.
   * - $site_name: The name of the site, empty when display has been disabled
   *   in theme settings.
   * - $site_slogan: The slogan of the site, empty when display has been disabled
   *   in theme settings.
   *
   * Navigation:
   * - $main_menu (array): An array containing the Main menu links for the
   *   site, if they have been configured.
   * - $secondary_menu (array): An array containing the Secondary menu links for
   *   the site, if they have been configured.
   * - $breadcrumb: The breadcrumb trail for the current page.
   *
   * Page content (in order of occurrence in the default page.tpl.php):
   * - $title_prefix (array): An array containing additional output populated by
   *   modules, intended to be displayed in front of the main title tag that
   *   appears in the template.
   * - $title: The page title, for use in the actual HTML content.
   * - $title_suffix (array): An array containing additional output populated by
   *   modules, intended to be displayed after the main title tag that appears in
   *   the template.
   * - $messages: HTML for status and error messages. Should be displayed
   *   prominently.
   * - $tabs (array): Tabs linking to any sub-pages beneath the current page
   *   (e.g., the view and edit tabs when displaying a node).
   * - $action_links (array): Actions local to the page, such as 'Add menu' on the
   *   menu administration interface.
   * - $feed_icons: A string of all feed icons for the current page.
   * - $node: The node object, if there is an automatically-loaded node
   *   associated with the page, and the node ID is the second argument
   *   in the page's path (e.g. node/12345 and node/12345/revisions, but not
   *   comment/reply/12345).
   *
   * Regions:
   * - $page['help']: Dynamic help text, mostly for admin pages.
   * - $page['highlighted']: Items for the highlighted content region.
   * - $page['content']: The main content of the current page.
   * - $page['sidebar_first']: Items for the first sidebar.
   * - $page['sidebar_second']: Items for the second sidebar.
   * - $page['header']: Items for the header region.
   * - $page['footer']: Items for the footer region.
   *
   * @see bootstrap_preprocess_page()
   * @see template_preprocess()
   * @see template_preprocess_page()
   * @see bootstrap_process_page()
   * @see template_process()
   * @see html.tpl.php
   *
   * @ingroup themeable
   */
?>
<?php if (!empty($page['header_top'])): ?>
   <div class="header-top-container">
      <div class="container">
         <div class="row">
            <?= render($page['header_top']); ?>
         </div>
      </div>
   </div>
<?php endif; ?>

<div class="navbar-default">
   <div class="navbar-header">
      <!-- .btn-navbar is used as the toggle for collapsed navbar content -->
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
         <span class="sr-only">Toggle navigation</span>
         <span class="icon-bar"></span>
         <span class="icon-bar"></span>
         <span class="icon-bar"></span>
      </button>
      <?php if ($logo): ?>
         <a class="visible-sm visible-xs navbar-brand" href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>">
            <img class="logo" itemprop="logo" src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" />
         </a>
      <?php endif; ?>
   </div>
</div>

<?php if (!empty($page['header_middle'])): ?>
   <div class="header-middle-container hidden-xs hidden-sm">
      <div class="container">
         <div class="row">
            <?= render($page['header_middle']); ?>
         </div>
      </div>
   </div>
<?php endif; ?>

<header id="navbar" role="banner" class="<?php print $navbar_classes; ?>">
   <?php if (!empty($primary_nav) || !empty($secondary_nav) || !empty($page['navigation'])): ?>
   <div class="navbar-collapse collapse">
      <?php if (!empty($page['header_middle'])): ?>
         <div class="header-middle-container visible-xs visible-sm">
            <div class="row">
               <?= render($page['header_middle']); ?>
            </div>
         </div>
      <?php endif; ?>
      <nav role="navigation">
      <?php if (!empty($primary_nav)): ?>
      <?php print render($primary_nav); ?>
      <?php endif; ?>
      <?php if (!empty($secondary_nav)): ?>
      <?php print render($secondary_nav); ?>
      <?php endif; ?>
      <?php if (!empty($page['navigation'])): ?>
      <?php print render($page['navigation']); ?>
      <?php endif; ?>
      </nav>
   </div>
   <?php endif; ?>
</header>

<?php if(!empty($breadcrumb)):?>
  <div id="page-header">
     <div class="container">
        <?php if (!empty($site_slogan)): ?>
           <p class="lead"><?php print $site_slogan; ?></p>
        <?php endif; ?>
        <?=$breadcrumb;?>
     </div>
  </div>
<?php endif;?>

<div>
   <div class="container-fluid">
      <?php if (!empty($page['highlighted'])): ?>
      <div class="highlighted jumbotron"><?php print render($page['highlighted']); ?></div>
      <?php endif; ?>
      <?php if (!empty($title)): ?>
      <div class="page-title">
         <div class="container">
             <div class="row">
               <?php if(empty($h1_title_prefix)):?>
                 <div class="col-xs-12">
                     <h1 class="<?php print $title_classes; ?>" <?php print $title_attributes; ?>><?php print $title; ?></h1>
                 </div>
               <?php else:?>
                 <div class="col-sm-6">
                     <h1 class="<?php print $title_classes; ?>" <?php print $title_attributes; ?>><?php print $title; ?></h1>
                 </div>
                 <div class="col-sm-6">
                   <?=$h1_title_prefix?>
                 </div>
               <?php endif;?>
             </div>
         </div>
      </div>
      <?php endif; ?>
   </div>

   <!-- /#Extra Banner -->
   <?php if (!empty($page['above_content'])): ?>
   <div class="above-content-container">
      <?= render($page['above_content']); ?>
   </div>
   <?php endif; ?>

   <div class="<?=$main_container_classes?>">

      <div class="row">

         <?php if (!empty($page['sidebar_first'])): ?>
         <aside class="col-sm-3" role="complementary">
         <?php print render($page['sidebar_first']); ?>
         </aside>  <!-- /#sidebar-first -->
         <?php endif; ?>
         <section<?php print $content_column_class; ?>>
         <a id="main-content"></a>
         <?php print render($title_prefix); ?>
         <?php print render($title_suffix); ?>
         <?php print $messages; ?>
         <?php if (!empty($tabs)): ?>
         <?php print render($tabs); ?>
         <?php endif; ?>
         <?php if (!empty($page['help'])): ?>
         <?php print render($page['help']); ?>
         <?php endif; ?>
         <?php if (!empty($action_links)): ?>
         <ul class="action-links"><?php print render($action_links); ?></ul>
         <?php endif; ?>
         <?php print render($page['content']); ?>
         </section>

         <?php if (!empty($page['sidebar_second'])): ?>
         <aside class="col-sm-3" role="complementary">
         <?php print render($page['sidebar_second']); ?>
         </aside>  <!-- /#sidebar-second -->
         <?php endif; ?>

      </div>
   </div>
   <div class="footer">
         <?php print render($page['above_footer']); ?>
   </div>
   <footer itemprop="brand" itemscope itemtype="https://schema.org/Brand" itemref="_logo6" class="footer container-fluid">
   <?php print render($page['footer']); ?>
   </footer>

   <div class="footer container container-fluid">

      <?php print render($page['footer_rights']); ?>

   </div>
</div>
<?php print render($adroll_tracking_code); ?>
<?php echo variable_get('infusionsoft_tracking_code', '')?>

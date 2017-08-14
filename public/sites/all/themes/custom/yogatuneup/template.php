<?php
/**
 * @file
 * Contains the theme's functions to manipulate Drupal's default markup.
 *
 * A QUICK OVERVIEW OF DRUPAL THEMING
 *
 *   The default HTML for all of Drupal's markup is specified by its modules.
 *   For example, the comment.module provides the default HTML markup and CSS
 *   styling that is wrapped around each comment. Fortunately, each piece of
 *   markup can optionally be overridden by the theme.
 *
 *   Drupal deals with each chunk of content using a "theme hook". The raw
 *   content is placed in PHP variables and passed through the theme hook, which
 *   can either be a template file (which you should already be familiary with)
 *   or a theme function. For example, the "comment" theme hook is implemented
 *   with a comment.tpl.php template file, but the "breadcrumb" theme hooks is
 *   implemented with a theme_breadcrumb() theme function. Regardless if the
 *   theme hook uses a template file or theme function, the template or function
 *   does the same kind of work; it takes the PHP variables passed to it and
 *   wraps the raw content with the desired HTML markup.
 *
 *   Most theme hooks are implemented with template files. Theme hooks that use
 *   theme functions do so for performance reasons - theme_field() is faster
 *   than a field.tpl.php - or for legacy reasons - theme_breadcrumb() has "been
 *   that way forever."
 *
 *   The variables used by theme functions or template files come from a handful
 *   of sources:
 *   - the contents of other theme hooks that have already been rendered into
 *     HTML. For example, the HTML from theme_breadcrumb() is put into the
 *     $breadcrumb variable of the page.tpl.php template file.
 *   - raw data provided directly by a module (often pulled from a database)
 *   - a "render element" provided directly by a module. A render element is a
 *     nested PHP array which contains both content and meta data with hints on
 *     how the content should be rendered. If a variable in a template file is a
 *     render element, it needs to be rendered with the render() function and
 *     then printed using:
 *       <?php print render($variable); ?>
 *
 * ABOUT THE TEMPLATE.PHP FILE
 *
 *   The template.php file is one of the most useful files when creating or
 *   modifying Drupal themes. With this file you can do three things:
 *   - Modify any theme hooks variables or add your own variables, using
 *     preprocess or process functions.
 *   - Override any theme function. That is, replace a module's default theme
 *     function with one you write.
 *   - Call hook_*_alter() functions which allow you to alter various parts of
 *     Drupal's internals, including the render elements in forms. The most
 *     useful of which include hook_form_alter(), hook_form_FORM_ID_alter(),
 *     and hook_page_alter(). See api.drupal.org for more information about
 *     _alter functions.
 *
 * OVERRIDING THEME FUNCTIONS
 *
 *   If a theme hook uses a theme function, Drupal will use the default theme
 *   function unless your theme overrides it. To override a theme function, you
 *   have to first find the theme function that generates the output. (The
 *   api.drupal.org website is a good place to find which file contains which
 *   function.) Then you can copy the original function in its entirety and
 *   paste it in this template.php file, changing the prefix from theme_ to
 *   yogatuneup_. For example:
 *
 *     original, found in modules/field/field.module: theme_field()
 *     theme override, found in template.php: yogatuneup_field()
 *
 *   where yogatuneup is the name of your sub-theme. For example, the
 *   zen_classic theme would define a zen_classic_field() function.
 *
 *   Note that base themes can also override theme functions. And those
 *   overrides will be used by sub-themes unless the sub-theme chooses to
 *   override again.
 *
 *   Zen core only overrides one theme function. If you wish to override it, you
 *   should first look at how Zen core implements this function:
 *     theme_breadcrumbs()      in zen/template.php
 *
 *   For more information, please visit the Theme Developer's Guide on
 *   Drupal.org: http://drupal.org/node/173880
 *
 * CREATE OR MODIFY VARIABLES FOR YOUR THEME
 *
 *   Each tpl.php template file has several variables which hold various pieces
 *   of content. You can modify those variables (or add new ones) before they
 *   are used in the template files by using preprocess functions.
 *
 *   This makes THEME_preprocess_HOOK() functions the most powerful functions
 *   available to themers.
 *
 *   It works by having one preprocess function for each template file or its
 *   derivatives (called theme hook suggestions). For example:
 *     THEME_preprocess_page    alters the variables for page.tpl.php
 *     THEME_preprocess_node    alters the variables for node.tpl.php or
 *                              for node--forum.tpl.php
 *     THEME_preprocess_comment alters the variables for comment.tpl.php
 *     THEME_preprocess_block   alters the variables for block.tpl.php
 *
 *   For more information on preprocess functions and theme hook suggestions,
 *   please visit the Theme Developer's Guide on Drupal.org:
 *   http://drupal.org/node/223440 and http://drupal.org/node/1089656
 */


/**
 * Override or insert variables into the maintenance page template.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("maintenance_page" in this case.)
 */
/* -- Delete this line if you want to use this function
function yogatuneup_preprocess_maintenance_page(&$variables, $hook) {
  // When a variable is manipulated or added in preprocess_html or
  // preprocess_page, that same work is probably needed for the maintenance page
  // as well, so we can just re-use those functions to do that work here.
  yogatuneup_preprocess_html($variables, $hook);
  yogatuneup_preprocess_page($variables, $hook);
}
// */

/**
 * Override or insert variables into the html templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("html" in this case.)
 */

function yogatuneup_preprocess_html(&$variables, $hook) {
  $variables['extra_tags'] = '';
  if(!in_array('node-type-homepage', $variables['classes_array']) && !in_array('node-type-product-landing', $variables['classes_array'])){
    $variables['classes_array'][] = 'border-page';
  }
  else{
    $node = current($variables['page']['content']['system_main']['nodes']);
    if(empty($node['field_feature_image'])){
      $variables['classes_array'][] = 'border-page';
      $variables['classes_array'][] = 'no-feature-image';
    }
  }
  if($node = menu_get_object()){
    if($node->type == 'homepage'){
      $variables['extra_tags'] .= '<meta name="google-site-verification" content="RHc7qANqESCI94BOPcSHsgpnc7pNGAsZKkRm-G-M0A4" />';
    }
  }
  $variables['rdf_namespaces'] .= ' xmlns:fb="http://ogp.me/ns/fb#"';

  $alias = drupal_get_path_alias($_GET['q']);

  if($alias == 'cart' || $alias == 'cart/login' || $alias == 'cart/checkout' || $alias == 'cart/checkout/review' ||  $alias == 'cart/checkout/complete' || $alias == 'user/password'){
    $variables['extra_tags'] .= '<meta name="viewport" content="width=device-width, initial-scale=1" />';
  }

}
// */

/**
 * Override or insert variables into the page templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("page" in this case.)
 */

function yogatuneup_preprocess_page(&$variables, $hook) {
  if(account_is_admin() || (account_is_teacher() && isset($variables['node']) && $variables['node']->uid == $variables['user']->uid)){
    //Show Tabs
  }
  //Place the feature image in its own page variable
  if(isset($variables['node']) && ($variables['node']->type == 'product_landing' || $variables['node']->type == 'homepage')){
    if(isset($variables['page']['content']['system_main']['nodes'][$variables['node']->nid]['field_feature_image'])){
      $variables['page']['feature_image'] = $variables['page']['content']['system_main']['nodes'][$variables['node']->nid]['field_feature_image'];
      $variables['page']['feature_image']['#prefix'] = '<div class="feature-image-wrapper">';
      $variables['page']['feature_image']['#suffix'] = '</div>';
    }
  }
  if(isset($variables['node']) && ($variables['node']->type == 'at_home_level')){
    $variables['page']['top_content'] = array(
      '#prefix' => '<div class="subscription-header">'._mm('ahpl_sub_header').'</div>',
      'subscriptions' => $variables['page']['content']['system_main']['nodes'][$variables['node']->nid]['field_subscriptions'],
      '#suffix' => '<div class="subscription-cancel">'._mm('ahpl_sub_cancel').'</div>',
    );
  }

  if(module_exists('adroll')){
    $order_subtotal = '';
    if(isset($variables['page']['content']['system_main']['#theme']) && $variables['page']['content']['system_main']['#theme'] == 'uc_cart_complete_sale'){
      if(isset($variables['page']['content']['system_main']['#order'])){
        $order_subtotal = order_get_product_coupon_subtotal($variables['page']['content']['system_main']['#order']);
      }
    }
    $variables['adroll_tracking_code'] = array(
      '#theme' => 'adroll_tracking_code',
      '#adv_id' => variable_get('adv_id',''),
      '#pix_id' => variable_get('pix_id',''),
      '#order_subtotal' => $order_subtotal,
    );
  }

  $alias = drupal_get_path_alias($_GET['q']);

  if($alias == 'cart' || $alias == 'cart/login' || $alias == 'cart/checkout' || $alias == 'cart/checkout/review' || $alias == 'cart/checkout/complete' || $alias == 'user/password' ){
    drupal_add_css(drupal_get_path('theme', 'yogatuneup') . '/css/bootstrap-checkout.css');
    $variables['theme_hook_suggestion'] = 'page__responsive';
  }

}

/**
 * Override or insert variables into the node templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("node" in this case.)
 */
function yogatuneup_preprocess_node(&$variables, $hook) {
  $variables['theme_hook_suggestions'][] = 'node__' . $variables['node']->type . '__'.$variables['view_mode'];
  $variables['theme_hook_suggestions'][] = 'node__' . $variables['node']->nid . '__'.$variables['view_mode'];

  $function = __FUNCTION__ . '_' . $variables['node']->type;
  if (function_exists($function)) {
    $function($variables, $hook);
  }
}

function yogatuneup_preprocess_node_teacher(&$variables, $hook){
  $account = user_load($variables['uid']);
  if(account_is_integrated_teacher($account)){
    $variables['title'] .= ' - Integrated Teacher';
  }
}

function yogatuneup_preprocess_field(&$variables, $hook){
  $function = __FUNCTION__ . '_' . $variables['element']['#field_name'];
  if (function_exists($function)) {
    $function($variables, $hook);
  }
}

function yogatuneup_preprocess_field_field_image(&$variables, $hook){
  if($variables['element']['#bundle'] == 'field_image_link'){
    if(isset($variables['element']['#object']->field_link[LANGUAGE_NONE][0]['url'])){
      $variables['items'][0]['#path']['path'] = $variables['element']['#object']->field_link[LANGUAGE_NONE][0]['url'];

      if($node = menu_get_object())
        if($node->type == 'pr'){
          $variables['items'][0]['#path']['options']['attributes']['target'] = '_blank';
        }
    }
  }
}

/**
 * Override or insert variables into the comment templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("comment" in this case.)
 */
/* -- Delete this line if you want to use this function
function yogatuneup_preprocess_comment(&$variables, $hook) {
  $variables['sample_variable'] = t('Lorem ipsum.');
}
// */

/**
 * Override or insert variables into the region templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("region" in this case.)
 */

function yogatuneup_preprocess_region(&$variables, $hook) {
  switch ($variables['elements']['#region']) {
    case 'bottom':
      if(module_exists('affiliate')){
        $affiliate_iframe = array(
          '#theme' => 'affiliate_iframe',
          '#iframe_src' => variable_get('affiliate_url',''),
          '#redirect_url' => variable_get('affiliate_redirect_url',''),
        );
        $variables['content'] .= render($affiliate_iframe);
      }
      break;
  }
}

/**
 * Override or insert variables into the block templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("block" in this case.)
 */
/* -- Delete this line if you want to use this function
function yogatuneup_preprocess_block(&$variables, $hook) {
  // Add a count to all the blocks in the region.
  // $variables['classes_array'][] = 'count-' . $variables['block_id'];

  // By default, Zen will use the block--no-wrapper.tpl.php for the main
  // content. This optional bit of code undoes that:
  //if ($variables['block_html_id'] == 'block-system-main') {
  //  $variables['theme_hook_suggestions'] = array_diff($variables['theme_hook_suggestions'], array('block__no_wrapper'));
  //}
}
// */

/**
 * Theme the username title of the user login form
 * and the user login block.
 */
function yogatuneup_lt_username_title($variables) {
  switch ($variables['form_id']) {
    case 'user_login':
      // Label text for the username field on the /user/login page.
      return t('Email:');
      break;

    case 'user_login_block':
      // Label text for the username field when shown in a block.
      return t('Email:');
      break;
  }
}

/**
 * Theme the username description of the user login form
 * and the user login block.
 */
function yogatuneup_lt_username_description($variables) {
  switch ($variables['form_id']) {
    case 'user_login':
      // The username field's description when shown on the /user/login page.
      break;
    case 'user_login_block':
      return '';
      break;
  }
}

/**
 * Theme the password title of the user login form
 * and the user login block.
 */
function yogatuneup_lt_password_title($variables) {
  // Label text for the password field.
  return t('Password:');
}

/**
 * Theme the password description of the user login form
 * and the user login block.
 */
function yogatuneup_lt_password_description($variables) {
  switch ($variables['form_id']) {
    case 'user_login':
      // The password field's description on the /user/login page.
      break;

    case 'user_login_block':
      // If showing the login form in a block, don't print any descriptive text.
      return '';
      break;
  }
}

/**
 * Theme function for field formatter.
 */
function yogatuneup_office_hours_formatter_default($vars) {
  $days = $vars['days'];
  $settings = $vars['settings'];
  $daynames = $vars['daynames'];
  $open = $vars['open'];
  $max_label_length = 3; // This is the minimum width for day labels. It is adjusted when adding new labels.
  $HTML_hours = '';
  $HTML_current_status = '';
  foreach ($days as $day => &$info) {
    // Format the label
    $label = $daynames[$info['startday']].'s';
    $label .= !isset($info['endday']) ? '' : $settings['separator_grouped_days'] . $daynames[$info['endday']].'s';
    $label .= $settings['separator_day_hours'];
    $max_label_length = max($max_label_length, drupal_strlen($label));

    // Format the time
    if (!$info['times']) {
      $times = filter_xss( t($settings['closedformat']) );
    }
    else {
      $times = array();
      foreach ($info['times'] as $block_times) {
        $times[] = theme(
                    'office_hours_time_range',
                    array(
                      'times'       => $block_times,
                      'format'      => empty($settings['hoursformat']) ? 'G:i' : 'g:i a',
                      'separator'   => $settings['separator_hours_hours'],
                    )
                  );
      }
      $times = implode($settings['separator_more_hours'], $times);
    }

    $info['output_label'] = $label;
    $info['output_times'] = $times;
  }

  // Start the loop again, since only now we have the definitive $max_label_length.
  foreach ($days as $day => &$info) {
    // Remove unwanted lines.
    switch ($settings['showclosed']) {
      case 'all':
        break;
      case 'open':
        if (!isset($info['times'])) {
          continue 2;
        }
        break;
      case 'next':
        if (!$info['current'] && !$info['next']) {
          continue 2;
        }
        break;
      case 'none':
        continue 2;
        break;
    }

    // Generate HTML for Hours.
    $HTML_hours .= '<span class="oh-display">'
//             . '<span class="oh-display-label">'
               . '<span class="oh-display-label">'
               . $info['output_label']
               . '</span>'
               . '<span class="oh-display-times oh-display-' . (!$info['times'] ? 'closed' : 'hours')
               . ($info['current'] ? ' oh-display-current' : '')
               . '">'
               . $info['output_times'] . $settings['separator_days']
               . '</span>'
               . '</span>';
  }

  $HTML_hours = '<span class="oh-wrapper' . ($settings['grouped'] ? ' oh-display-grouped' : '' ) . '">' .
                $HTML_hours .
                '</span>';

  // Generate HTML for CurrentStatus.
  if ($open) {
    $HTML_current_status = '<span class="oh-current-open">' . t($settings['current_status']['open_text']) . '</span>';
  }
  else {
    $HTML_current_status = '<span class="oh-current-closed">' . t($settings['current_status']['closed_text']) . '</span>';
  }

  switch ($settings['current_status']['position']) {
    case 'before':
      $HTML = $HTML_current_status . $HTML_hours;
      break;
    case 'after':
      $HTML = $HTML_hours . $HTML_current_status;
      break;
    case 'hide':
    default: // Not shown.
      $HTML = $HTML_hours;
      break;
  }

  return $HTML;
}

function yogatuneup_uc_cart_review_table($variables) {
  $items = $variables['items'];
  $show_subtotal = $variables['show_subtotal'];

  $subtotal = 0;

  // Set up table header.
  $header = array(
    array('data' => t('Products'), 'class' => array('products')),
    array('data' => t('Unit Price'), 'class' => array('unit-price')),
    array('data' => theme('uc_qty_label'), 'class' => array('qty')),
    array('data' => t('Price'), 'class' => array('price')),
  );

  $coupon_total = 0;

  foreach($items as $delta => $item){
    if(isset($item->module) && $item->module == 'uc_coupon'){
      $coupon_total += $item->price;
      unset($items[$delta]);
    }
  }

  // Set up table rows.
  $display_items = entity_view('uc_order_product', $items, 'cart');
  if (!empty($display_items['uc_order_product'])) {
    foreach (element_children($display_items['uc_order_product']) as $key) {
      $display_item = $display_items['uc_order_product'][$key];
      if (count(element_children($display_item))) {
        $total = $display_item['#total'];
        $subtotal += $total;

        //Add Backordered if item is backordered
        if(isset($display_item['#entity']->data['backordered']) && $display_item['#entity']->data['backordered']){
          $display_item['description']['#markup'] .= '<div class="backordered">Backordered</div>';
        }

        $description = $display_item['title']['#markup'];
        if (!empty($display_item['description']['#markup'])) {
          $description .= $display_item['description']['#markup'];
        }
        $qty = $display_item['qty']['#default_value'];
        $suffix = !empty($display_item['#suffixes']) ? implode(' ', $display_item['#suffixes']) : '';

        $unit_price = theme('uc_price',array('price' => $display_item['#entity']->price));
        $unit_price_modify = array();

        if(!empty($display_item['#entity']->data['uc_coupon'])){
          $unit_price_modify = reset($display_item['#entity']->data['uc_coupon']);
        }

        if(upsell_cart_item_is_upsell($display_item['#entity'])){
          $unit_price_modify = upsell_get_price_modify($display_item['#entity']);
        }

        if(!empty($unit_price_modify)){
          $unit_price = '<span class="strikethrough">'.theme('uc_price',array('price' => $unit_price_modify['original_price'])).'</span><br />'.theme('uc_price',array('price' => $unit_price_modify['price']));
        }

        $rows[] = array(
          array('data' => $description, 'class' => array('products')),
          array('data' => $unit_price, 'class' => array('unit-price')),
          array('data' => array('#theme' => 'uc_qty', '#qty' => $qty), 'class' => array('qty')),
          array('data' => array('#theme' => 'uc_price', '#price' => $total, '#suffix' => $suffix), 'class' => array('price')),
        );
      }
    }
  }

  // Add the subtotal as the final row.
  if ($show_subtotal) {
    $rows[] = array(
      'data' => array(
        // One cell
        array(
          'data' => array(
            '#theme' => 'uc_price',
            '#prefix' => '<span id="subtotal-title">' . t('Subtotal:') . '</span> ',
            '#price' => $subtotal,
          ),
          // Cell attributes
          'colspan' => 4,
          'class' => array('subtotal'),
        ),
      ),
      // Row attributes
      'class' => array('subtotal'),
    );
  }


  if($coupon_total){
      $rows[] = array(
        'data' => array(
          // One cell
          array(
            'data' => array(
              '#theme' => 'uc_price',
              '#prefix' => '<span class="coupon-title">' . t('Discount Amount Applied Below:') . '</span> ',
              '#price' => $coupon_total,
            ),
            // Cell attributes
            'colspan' => 4,
            'class' => array('coupon'),
          ),
        ),
        // Row attributes
        'class' => array('coupon'),
      );
  }


  return theme('table', array('header' => $header, 'rows' => $rows, 'attributes' => array('class' => array('cart-review'))));
}

/**
 * Themes the sale completion page.
 *
 * @param $variables
 *   An associative array containing:
 *   - message: Message containing order number info, account info, and link to
 *     continue shopping.
 *
 * @ingroup themeable
 */
function yogatuneup_uc_cart_complete_sale($variables) {
  return nl2br($variables['message']);
}

/**
 * Themes the checkout review order page.
 *
 * @param $variables
 *   An associative array containing:
 *   - form: A render element representing the form, that by default includes
 *     the 'Back' and 'Submit order' buttons at the bottom of the review page.
 *   - panes: An associative array for each checkout pane that has information
 *     to add to the review page, keyed by the pane title:
 *     - <pane title>: The data returned for that pane or an array of returned
 *       data.
 *
 * @return
 *   A string of HTML for the page contents.
 *
 * @ingroup themeable
 */
function yogatuneup_uc_cart_checkout_review($variables) {
  $panes = $variables['panes'];
  $form = $variables['form'];

  $items = uc_cart_get_contents();
  $number_of_items = count($items);
  $total = 0;

  foreach( $items as $item ){
    $total += ($item->qty * $item->price);
  }

  $output = '<h2>Review Order</h2>';

  $output .= '<div class="row">';

  if(isset($panes['Delivery information']['address']['data'])){
    $output .= '<div class="col-md-4"><div class="inner-border"><h3>Ship to:</h3>' . $panes['Delivery information']['address']['data'] . '</div></div>';
  }

  $output .= '<div class="col-md-4"><div class="inner-border"><h3>Bill to:</h3>' . $panes['Billing information']['address']['data'] . '</div></div>';

  $output .= '<div class="col-md-4"><div class="inner-border"><h3>Payment:</h3>';

  $show = FALSE;
  foreach ($panes['Payment method'] as $key) {
    if($key['title'] == 'Paying by'){
      $show = TRUE;
    }
    if($show){
      $output .= '<div>' . $key['title'] . ': ' . $key['data'] . '</div>';
    }
  }

  // PO Number
  $output .= '<div>' . $panes['PO Number']['contents']['title']. ': '.$panes['PO Number']['contents']['data'].'</div>';


  $output .= '</div></div>';

  $output .= '<div class="col-md-12 hidden-xs"><h3>Cart Information:</h3>' . $panes['Cart contents'][0] . '</div>';
  $output .= '<div class="col-md-12 visible-xs"><h3>Cart Information:</h3>' . $panes['Cart contents'][0] . '</div>';
  $output .= $panes['Order summary'][0];

  $output .= '</div>';


  $output .= drupal_render($form);

  return $output;
}

/**
 * Generates markup for payment totals.
 *
 * @ingroup themeable
 */
function yogatuneup_uc_payment_totals($variables) {
  $order = $variables['order'];
  $line_items = $order->line_items;

  uasort($line_items,'drupal_sort_weight');

  $output = '<table id="uc-order-total-preview">';

  foreach ($line_items as $line) {
    if (!empty($line['title'])) {
      $line_item_classes = array('line-item-' . $line['type']);
      if($line['amount'] == 0){
        $line_item_classes[] = 'hide-line-item';
      }
      $attributes = drupal_attributes(array('class' => $line_item_classes));
      $output .= '<tr' . $attributes . '><td class="title">' . filter_xss($line['title'], array('button','div')) . '</td>'
        . '<td class="price">' . theme('uc_price', array('price' => $line['amount'])) . '</td></tr>';
    }
  }

  $output .= '</table>';

  return $output;
}

/**
 * Preprocesses a formatted invoice with an order's data.
 *
 * @see uc_order--admin.tpl.php
 * @see uc_order--customer.tpl.php
 */
function yogatuneup_preprocess_uc_order(&$variables) {
  $order = &$variables['order'];

  $variables['help_text'] = FALSE;

  switch ($variables['op']) {
    case 'checkout-mail':
      $variables['thank_you_message'] = TRUE;
    case 'admin-mail':
      $variables['email_text'] = TRUE;
      $variables['store_footer'] = TRUE;
    case 'view':
    case 'print':
      $variables['business_header'] = TRUE;
      $variables['shipping_method'] = TRUE;
      break;
  }

  $variables['backordered'] = FALSE;

  $variables['shippable'] = uc_order_is_shippable($order);

  $variables['products'] = $order->products;
  $display = entity_view('uc_order_product', $order->products);

  foreach ($variables['products'] as &$product) {
    $price_modify = array();
    $code = '';

    if(!empty($product->data['uc_coupon'])){
      $price_modify = reset($product->data['uc_coupon']);
      $code = key($product->data['uc_coupon']);
    }

    $product->details = '';

    if(upsell_cart_item_is_upsell($product)){
      $price_modify = upsell_get_price_modify($product);
      $product->details .= '<div>'.(UPSELL_DISCOUNT*100).'% Off</div>';
    }

    $product->total_price = render($display['uc_order_product'][$product->order_product_id]['total']);
    $price = uc_currency_format($display['uc_order_product'][$product->order_product_id]['price']['#price']);
    if(!empty($price_modify)){
      $price = '<span style="text-decoration: line-through;">'.uc_currency_format($price_modify['original_price']).'</span> '.uc_currency_format($price_modify['price']);
    }
    if($product->qty > 1 || !empty($price_modify)){
      if($product->qty > 1){
        $product->individual_price = t('(!price each)', array('!price' => $price));
      }
      else{
        $product->individual_price = t('(!price)', array('!price' => $price));
      }
    }

    if (!empty($product->data['attributes'])) {
      $attributes = array();
      foreach ($product->data['attributes'] as $attribute => $option) {
        $attributes[] = t('@attribute: @options', array('@attribute' => $attribute, '@options' => implode(', ', (array)$option)));
      }
      $product->details .= theme('item_list', array('items' => $attributes));
    }
    if(!empty($code)){
      $product->details .= '<div>'.uc_coupon_get_product_description_for_code($code).'</div>';
    }
    if(isset($product->data['backordered']) && $product->data['backordered']){
      $product->details .= '<div style="color:red;font-weight:bold;">Backordered</div>';
      $variables['backordered'] = TRUE;
    }
  }

  $variables['line_items'] = uc_order_load_line_items_display($variables['order']);
  $order->line_items = $variables['line_items'];

  foreach($variables['line_items'] as $delta => $line_item){
    if($line_item['type'] = 'coupon' && $line_item['amount'] == 0){
      unset($variables['line_items'][$delta]);
    }
  }

  // Generate tokens to use as template variables.
  $types = array(
    'uc_order' => $order,
  );

  $token_info = token_info();

  $replacements = array();
  foreach (array('site', 'store', 'uc_order') as $type) {
    $replacements[$type] = token_generate($type, drupal_map_assoc(array_keys($token_info['tokens'][$type])), $types);
  }

  foreach ($replacements as $type => $tokens) {
    foreach ($tokens as $token => $value) {
      $key = str_replace('-', '_', $type . '_' . $token);
      $key = str_replace('uc_', '', $key);
      $variables[$key] = $value;
    }
  }

  if(stripos($variables['order_payment_method'], 'net30') !== FALSE){
    $variables['order_payment_method'] = _mm('email_payment_method_net30','INVOICE TOTAL PAYABLE WITHIN 30 DAYS<br />Please remit payment to the address above payable to: TUNE UP FITNESS WORLDWIDE',true);
    if(isset($order->payment_details['data']->field_po_num[LANGUAGE_NONE][0]['value'])){
      $variables['order_payment_method'] .= 'PO#: '.$order->payment_details['data']->field_po_num[LANGUAGE_NONE][0]['value'];
    }
  }

  // Add hook suggestions, default to customer template.
  $variables['theme_hook_suggestions'] = array(
    'uc_order__customer',
    'uc_order__' . $variables['template'],
  );
}

/**
 * Displays an attribute option with an optional total or adjustment price.
 *
 * @param $variables
 *   An associative array containing:
 *   - option: The option name.
 *   - price: The price total or adjustment, if any.
 *
 * @see _uc_attribute_alter_form()
 * @ingroup themeable
 */
function yogatuneup_uc_attribute_option($variables) {
  $output = $variables['option'];
  return $output;
}

/**
 * Generates markup for payment totals.
 *
 * @ingroup themeable
 */
function yogatuneup_checkout_uc_payment_totals_review($variables) {
  $order = $variables['order'];
  $line_items = $order->line_items;

  uasort($line_items,'drupal_sort_weight');

  $output = '';
  $i = 0;

  $backordered = '';
  foreach($order->products as $product){
    if(isset($product->data['backordered']) && $product->data['backordered']){
      $backordered = '<div class="backordered">'._mm('checkout_review_backordered','',TRUE).'</div>';
      break;
    }
  }

  foreach ($line_items as $delta => $line) {
    if($line['type'] == 'aubtotal'){
      $output .= '<div class="col-md-12 review-line review-subtotal">'.$backordered.'<strong>' . $line['title'] . ':</strong> ' . theme('uc_price', array('price' => $line['amount'])) . '</div>';
    }
    else{
      if($line['type'] == 'coupon' && $line['amount'] == 0){
        $output .= '';
      }
      else{
        $output .= '<div class="col-md-12 review-line"><strong>' . $line['title'] . ':</strong> ' . theme('uc_price', array('price' => $line['amount'])) . '</div>';
      }
    }
    $i++;
  }

  return $output;
}

/**
 * Returns HTML for an individual feed item for display in the block.
 *
 * @param $variables
 *   An associative array containing:
 *   - item: The item to be displayed.
 *   - feed: Not used.
 *
 * @ingroup themeable
 */
function yogatuneup_aggregator_block_item($variables) {
  $output = '';

  $output .= '<div class="blog-item">';
  $output .= '<div class="blog-timestamp">'.date('n/d/Y',$variables['item']->timestamp).'</div>';
  $output .= '<h3>'.l($variables['item']->title,$variables['item']->link,array('absolute' => TRUE, 'attributes' => array('target' => '_blank'))).'</h3>';
  $output .= '<div class="blog-description">'.str_replace('[&#8230;]','...<br />',$variables['item']->description).'</div>';
  $output .= l('Read More >',$variables['item']->link,array('absolute' => TRUE, 'attributes' => array('target' => '_blank')));
  $output .= '</div>';

  return $output;
}
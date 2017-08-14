<?php

require_once('templates/block/block.vars.php');
require_once('templates/field/field.vars.php');
require_once('templates/menu/menu-link.func.php');
require_once('templates/node/node.vars.php');
require_once('templates/system/page.vars.php');
require_once('templates/region/region.vars.php');

/**
 * Implements hook_form_alter().
 */
function tuneupfitness_form_alter(&$form, &$form_state, $form_id) {
  switch ($form_id) {
    case 'at_home_subscription_queue_form':
      unset($form['actions']['remove']['#prefix']);
      unset($form['actions']['shipped']['#prefix']);
      break;
    case 'at_home_subscription_shipping_options_form':
      $form['shipping_options']['#title'] = '<div class="shipping-options-description">' . _mm('ah_pm_shipping_options') . '</div>';
      break;
    case 'uc_cart_checkout_form':
      unset($form['actions']['cancel']);
      unset($form['actions']['continue_shopping']);

      $form['actions']['continue']['#attributes']['class'][] = 'visible-xs';
      $form['actions']['continue']['#attributes']['class'][] = 'visible-sm';

      $form['actions']['cancel']['#value']             = t('Update Order');
      $form['actions']['continue']['#value']           = t('Finalize Your Order');

      $form['panes']['cart']['#attributes']['class'][] = 'hidden';

      if(isset($form['panes']['payment']['details']['cc_policy'])){
        $form['panes']['payment']['details']['cc_policy']['#prefix'] = '<div class="col-xs-12">';
        $form['panes']['payment']['details']['cc_policy']['#suffix'] = '</div>';
      }

      $form['panes']['order_summary']['#attributes']   = array();
      $form['panes']['order_summary']['bottom']['#markup'] = theme('checkout_order_summary_suffix');
      $form['panes']['order_summary']['#attributes']['class'][] = 'order_summary';

      $form['panes']['customer']['#attributes']['class'][] = 'panel-success';
      $form['panes']['coupon']['#attributes']['class'][] = 'panel-success';

      break;
    case 'uc_cart_checkout_review_form':
      $form['actions']['back']['#weight'] = 10;


      break;
    case 'at_home_subscription_at_home_ship_now_form':
      $form['actions']['go_back']['#markup'] = l(
        'I do not want to place an order at this time.',
        'user/' . $form_state['build_info']['args'][0]->uid . '/program-manager',
        array('attributes' => array('class' => array('blue-button')))
      );
      $form['actions']['go_back']['#weight'] = 2;
      $form['actions']['submit']['#attributes']['class'][] = 'custom-green-button';
      break;
    case 'user_profile_form':
      if (!account_is_admin()) {
        $form['field_pain_ailment']['#prefix'] = '<div class="row">'.$form['field_pain_ailment']['#prefix'];
        $form['field_educational_programs']['#suffix'] .= '</div>';
        $form['field_fitness_enthusiast']['#prefix'] = '<div class="row">'.$form['field_fitness_enthusiast']['#prefix'];
        $form['field_notification_emails']['#suffix'] .= '</div>';
      }
      break;
  }
}

function tuneupfitness_uc_cart_quick_checkout($variables) {

}

function tuneupfitness_uc_cart_review_table($variables) {
  $items         = $variables['items'];
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

  foreach ($items as $delta => $item) {
    if (isset($item->module) && $item->module == 'uc_coupon') {
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

        if(isset($display_item['#entity']->data['at_home_product_queue_info'])){
          $display_item['title']['#markup'] = $display_item['#entity']->title;
        }

        //Add Backordered if item is backordered
        if (isset($display_item['#entity']->data['backordered']) && $display_item['#entity']->data['backordered']) {
          $display_item['description']['#markup'] .= '<div class="backordered">Backordered</div>';
        }

        $description = $display_item['title']['#markup'];
        if (!empty($display_item['description']['#markup'])) {
          $description .= $display_item['description']['#markup'];
        }
        $qty    = $display_item['qty']['#default_value'];
        $suffix = !empty($display_item['#suffixes']) ? implode(' ', $display_item['#suffixes']) : '';

        $unit_price        = theme('uc_price', array('price' => $display_item['#entity']->price));
        $unit_price_modify = array();

        if (!empty($display_item['#entity']->data['uc_coupon'])) {
          $unit_price_modify = reset($display_item['#entity']->data['uc_coupon']);
        }

        if (!empty($display_item['#entity']->data['video_discount'])) {
          $unit_price_modify = reset($display_item['#entity']->data['video_discount']);
        }

        if (upsell_cart_item_is_upsell($display_item['#entity'])) {
          $unit_price_modify = upsell_get_price_modify($display_item['#entity']);
        }

        if (!empty($unit_price_modify)) {
          $unit_price = '<span class="strikethrough">' . theme('uc_price', array('price' => $unit_price_modify['original_price'])) . '</span><br />' . theme('uc_price', array('price' => $unit_price_modify['price']));
          if (isset($unit_price_modify['max_discount_quantity'])) {
            $prices = array();
            foreach ($unit_price_modify['max_discount_quantity'] as $max_discount_qty) {
              $prices[] = theme('uc_price', array('price' => $max_discount_qty['unit_price'])) . '&nbsp;x' . $max_discount_qty['qty'];
            }
            $unit_price = implode('<br />', $prices);
          }
        }

        $rows[] = array(
          array('data' => $description, 'class' => array('products')),
          array('data' => $unit_price, 'class' => array('unit-price')),
          array(
            'data'  => array('#theme' => 'uc_qty', '#qty' => $qty),
            'class' => array('qty')
          ),
          array(
            'data'  => array(
              '#theme'  => 'uc_price',
              '#price'  => $total,
              '#suffix' => $suffix
            ),
            'class' => array('price')
          ),
        );
      }
    }
  }

  // Add the subtotal as the final row.
  if ($show_subtotal) {
    $rows[] = array(
      'data'  => array(
        // One cell
        array(
          'data'    => array(
            '#theme'  => 'uc_price',
            '#prefix' => '<span id="subtotal-title">' . t('Subtotal:') . '</span> ',
            '#price'  => $subtotal,
          ),
          // Cell attributes
          'colspan' => 4,
          'class'   => array('subtotal'),
        ),
      ),
      // Row attributes
      'class' => array('subtotal'),
    );
  }


  if ($coupon_total) {
    $rows[] = array(
      'data'  => array(
        // One cell
        array(
          'data'    => array(
            '#theme'  => 'uc_price',
            '#prefix' => '<span class="coupon-title">' . t('Discount Amount Applied Below:') . '</span> ',
            '#price'  => $coupon_total,
          ),
          // Cell attributes
          'colspan' => 4,
          'class'   => array('coupon'),
        ),
      ),
      // Row attributes
      'class' => array('coupon'),
    );
  }


  return theme('table', array(
    'header'     => $header,
    'rows'       => $rows,
    'attributes' => array('class' => array('cart-review'))
  ));
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
function tuneupfitness_uc_cart_complete_sale($variables) {
  return nl2br($variables['message']) . checkout_order_tracking_pixels($variables['order']);
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
function tuneupfitness_uc_cart_checkout_review($variables) {
  $panes = $variables['panes'];
  $form  = $variables['form'];

  $items           = uc_cart_get_contents();
  $number_of_items = count($items);
  $total           = 0;

  foreach ($items as $item) {
    $total += ($item->qty * $item->price);
  }

  $output = '';

  $output .= '<div class="row">';

  if (isset($panes['Shipping Information']['address']['data'])) {
    $output .= '<div class="col-sm-4"><div class="inner-border"><h3>Ship to:</h3>' . $panes['Shipping Information']['address']['data'] . '</div></div>';
  }

  $output .= '<div class="col-sm-4"><div class="inner-border"><h3>Bill to:</h3>' . $panes['Billing information']['address']['data'] . '</div></div>';

  $output .= '<div class="col-sm-4"><div class="inner-border"><h3>Payment:</h3>';

  $show = FALSE;
  foreach ($panes['Payment method'] as $key) {
    if ($key['title'] == 'Paying by') {
      $show = TRUE;
    }
    if ($show) {
      $output .= '<div>' . $key['title'] . ': ' . $key['data'] . '</div>';
    }
  }

  // PO Number
  $output .= '<div>' . $panes['Purchase Order']['contents']['title'] . ': ' . $panes['Purchase Order']['contents']['data'] . '</div>';


  $output .= '</div></div>';

  $output .= '<div class="col-sm-12 cart-info hidden-xs"><h3>Cart Information:</h3>' . $panes['Cart contents'][0] . '</div>';
  $output .= '<div class="col-sm-12 cart-info visible-xs"><h3>Cart Information:</h3>' . $panes['Cart contents'][0] . '</div>';
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
function tuneupfitness_uc_payment_totals($variables) {
  $order  = $variables['order'];
  $totals = $order->line_items;
  $items  = $order->products;


  uasort($totals, 'drupal_sort_weight');

  // Set up table header.
  $header = array(
    array('data' => '', 'class' => array('products')),
    array('data' => t('Each'), 'class' => array('unit-price')),
    array('data' => theme('uc_qty_label'), 'class' => array('qty')),
    array('data' => t('Price'), 'class' => array('price')),
  );

  $rows = array();

  $coupon_total = 0;

  foreach ($items as $delta => $item) {
    if (isset($item->module) && $item->module == 'uc_coupon') {
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

        //Add Backordered if item is backordered
        if (isset($display_item['#entity']->data['backordered']) && $display_item['#entity']->data['backordered']) {
          $display_item['description']['#markup'] .= '<div class="backordered">Backordered</div>';
        }

        $description = $display_item['title']['#markup'];
        if (!empty($display_item['description']['#markup'])) {
          $description .= $display_item['description']['#markup'];
        }
        $qty    = $display_item['qty']['#default_value'];
        $suffix = !empty($display_item['#suffixes']) ? implode(' ', $display_item['#suffixes']) : '';

        $unit_price        = theme('uc_price', array('price' => $display_item['#entity']->price));
        $unit_price_modify = array();

        if (!empty($display_item['#entity']->data['uc_coupon'])) {
          $unit_price_modify = reset($display_item['#entity']->data['uc_coupon']);
        }

        if (!empty($display_item['#entity']->data['video_discount'])) {
          $unit_price_modify = reset($display_item['#entity']->data['video_discount']);
        }

        if (upsell_cart_item_is_upsell($display_item['#entity'])) {
          $unit_price_modify = upsell_get_price_modify($display_item['#entity']);
        }

        if (!empty($unit_price_modify)) {
          $unit_price = '<span class="strikethrough">' . theme('uc_price', array('price' => $unit_price_modify['original_price'])) . '</span><br />' . theme('uc_price', array('price' => $unit_price_modify['price']));
          if (isset($unit_price_modify['max_discount_quantity'])) {
            $prices = array();
            foreach ($unit_price_modify['max_discount_quantity'] as $max_discount_qty) {
              $prices[] = theme('uc_price', array('price' => $max_discount_qty['unit_price'])) . '&nbsp;x' . $max_discount_qty['qty'];
            }
            $unit_price = implode('<br />', $prices);
          }
        }

        $rows[] = array(
          array('data' => $description, 'class' => array('products')),
          array('data' => $unit_price, 'class' => array('unit-price')),
          array(
            'data'  => array('#theme' => 'uc_qty', '#qty' => $qty),
            'class' => array('qty')
          ),
          array(
            'data'  => array(
              '#theme'  => 'uc_price',
              '#price'  => $total,
              '#suffix' => $suffix
            ),
            'class' => array('price')
          ),
        );
      }
    }
  }


  if ($coupon_total) {
    $rows[] = array(
      'data'  => array(
        // One cell
        array(
          'data'    => array(
            '#theme'  => 'uc_price',
            '#prefix' => '<span class="coupon-title">' . t('Discount Amount Applied Below:') . '</span> ',
            '#price'  => $coupon_total,
          ),
          // Cell attributes
          'colspan' => 4,
          'class'   => array('coupon'),
        ),
      ),
      // Row attributes
      'class' => array('coupon'),
    );
  }

  $first = TRUE;
  foreach ($totals as $line) {
    if (!empty($line['title'])) {

      if($line['type'] == 'coupon' && $line['amount'] == 0){
        $rows[] = array(
          'class' => $first ? array('line-item', 'first', 'hidden') : array('line-item', 'hidden'),
          'data' => array(
            array(
              'data'    => filter_xss($line['title']),
              'class'   => array('products'),
              'colspan' => 3
            ),
            array(
              'data'  => theme('uc_price', array('price' => $line['amount'])),
              'class' => array('unit-price')
            ),
          ),
        );
      }
      else{
        $rows[] = array(
          'class' => $first ? array('line-item', 'first') : array('line-item'),
          'data' => array(
            array(
              'data'    => filter_xss($line['title']),
              'class'   => array('products'),
              'colspan' => 3
            ),
            array(
              'data'  => theme('uc_price', array('price' => $line['amount'])),
              'class' => array('unit-price')
            ),
          ),
        );

        $first = FALSE;
      }
    }
  }

  return theme('table', array(
    'header'     => $header,
    'rows'       => $rows,
    'attributes' => array('class' => array('cart-review', 'table-no-striping')),
    'sticky'     => false,
  ));
}

/**
 * Preprocesses a formatted invoice with an order's data.
 *
 * @see uc_order--admin.tpl.php
 * @see uc_order--customer.tpl.php
 */
function tuneupfitness_preprocess_uc_order(&$variables) {
  $order = &$variables['order'];

  $variables['help_text'] = FALSE;

  switch ($variables['op']) {
    case 'checkout-mail':
      $variables['thank_you_message'] = TRUE;
    case 'admin-mail':
      $variables['email_text']   = TRUE;
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
  $display               = entity_view('uc_order_product', $order->products);

  foreach ($variables['products'] as &$product) {
    $price_modify = array();
    $code         = '';

    if (!empty($product->data['uc_coupon'])) {
      $price_modify = reset($product->data['uc_coupon']);
      $code         = key($product->data['uc_coupon']);
    }

    if (!empty($product->data['video_discount'])) {
      $price_modify = reset($product->data['video_discount']);
    }


    $product->details = '';

    if (upsell_cart_item_is_upsell($product)) {
      $price_modify = upsell_get_price_modify($product);
      $product->details .= '<div>' . (UPSELL_DISCOUNT * 100) . '% Off</div>';
    }

    $product->total_price = render($display['uc_order_product'][$product->order_product_id]['total']);
    $price                = uc_currency_format($display['uc_order_product'][$product->order_product_id]['price']['#price']);
    if (!empty($price_modify)) {
      $price = '<span style="text-decoration: line-through;">' . uc_currency_format($price_modify['original_price']) . '</span> ' . uc_currency_format($price_modify['price']);
      if (isset($price_modify['max_discount_quantity'])) {
        $prices = array();
        foreach ($price_modify['max_discount_quantity'] as $max_discount_qty) {
          $prices[] = theme('uc_price', array('price' => $max_discount_qty['unit_price'])) . '&nbsp;x' . $max_discount_qty['qty'];
        }
        $price = implode(' ', $prices);
      }
    }
    if ($product->qty > 1 || !empty($price_modify)) {
      if ($product->qty > 1) {
        $product->individual_price = t('(!price each)', array('!price' => $price));
      }
      else {
        $product->individual_price = t('(!price)', array('!price' => $price));
      }
    }

    if (!empty($product->data['attributes'])) {
      $attributes = array();
      foreach ($product->data['attributes'] as $attribute => $option) {
        $attributes[] = t('@attribute: @options', array(
          '@attribute' => $attribute,
          '@options'   => implode(', ', (array) $option)
        ));
      }
      $product->details .= theme('item_list', array('items' => $attributes));
    }
    if (!empty($code)) {
      $product->details .= '<div>' . uc_coupon_get_product_description_for_code($code) . '</div>';
    }
    if (isset($product->data['backordered']) && $product->data['backordered']) {
      $product->details .= '<div style="color:red;font-weight:bold;">Backordered</div>';
      $variables['backordered'] = TRUE;
    }
  }

  $variables['line_items'] = uc_order_load_line_items_display($variables['order']);
  $order->line_items       = $variables['line_items'];

  foreach ($variables['line_items'] as $delta => $line_item) {
    if ($line_item['type'] == 'coupon' && $line_item['amount'] == 0) {
      unset($variables['line_items'][$delta]);
    }
    if ($line_item['type'] == 'shipping') {
      $variables['line_items'][$delta]['weight'] = 7;
    }
  }

  uasort($variables['line_items'], 'drupal_sort_weight');

  $variables['order_total_title']      = t('Total for this Order:');
  $variables['order_total_suffix']     = '';
  $variables['order_delivery_country'] = $order->delivery_country;

  if ($order->delivery_country == 124) {
    $variables['order_total_title']  = t('Total Landed Cost:');
    $variables['order_total_suffix'] = t('All pricing shown in USD');
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
      $key             = str_replace('-', '_', $type . '_' . $token);
      $key             = str_replace('uc_', '', $key);
      $variables[$key] = $value;
    }
  }

  if (stripos($variables['order_payment_method'], 'net30') !== FALSE) {
    $variables['order_payment_method'] = _mm('email_payment_method_net30', 'INVOICE TOTAL PAYABLE WITHIN 30 DAYS<br />Please remit payment to the address above payable to: TUNE UP FITNESS WORLDWIDE', TRUE);
    if (isset($order->payment_details['data']->field_po_num[LANGUAGE_NONE][0]['value'])) {
      $variables['order_payment_method'] .= 'PO#: ' . $order->payment_details['data']->field_po_num[LANGUAGE_NONE][0]['value'];
    }
  }

  $variables['site_logo'] = str_replace('class', 'style="width:200px;" class', $variables['site_logo']);

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
function tuneupfitness_uc_attribute_option($variables) {
  $output = $variables['option'];
  return $output;
}

/**
 * @file
 * Theme functions for the uc_store module.
 */

/**
 * Displays a price in the standard format and with consistent markup.
 *
 * @param $variables
 *   An associative array containing:
 *   - price: A numerical price value.
 *   - suffixes: An array of suffixes to be attached to this price.
 *
 * @ingroup themeable
 */
function tuneupfitness_uc_price($variables) {
  // Structured data parameters for sell price
  $output = '<span itemprop="price" class="uc-price" data-price="'.$variables['price'].'">' . uc_currency_format($variables['price']) . '</span>';
  if (!empty($variables['suffixes'])) {
    $output .= '<span class="price-suffixes">' . implode(' ', $variables['suffixes']) . '</span>';
  }
  return $output;
}

/**
 * Generates markup for payment totals.
 *
 * @ingroup themeable
 */
function tuneupfitness_checkout_uc_payment_totals_review($variables) {
  $order      = $variables['order'];
  $line_items = $order->line_items;


  uasort($line_items, 'drupal_sort_weight');

  $output = '';
  $i      = 0;

  $backordered = '';
  foreach ($order->products as $product) {
    if (isset($product->data['backordered']) && $product->data['backordered']) {
      $backordered = '<div class="backordered">' . _mm('checkout_review_backordered', '', TRUE) . '</div>';
      break;
    }
  }

  foreach ($line_items as $delta => $line) {
    if ($line['type'] == 'subtotal') {
      $output .= '<div class="col-sm-12 review-line review-subtotal">' . $backordered . '<strong>' . $line['title'] . ':</strong> ' . theme('uc_price', array('price' => $line['amount'])) . '</div>';
    }
    else {
      if ($line['type'] == 'coupon' && $line['amount'] == 0) {
        $output .= '';
      }
      else {
        $output .= '<div class="col-sm-12 review-line"><strong>' . $line['title'] . ':</strong> ' . theme('uc_price', array('price' => $line['amount'])) . '</div>';
      }
    }
    $i++;
  }

  if (module_exists('checkout') && !checkout_order_is_domestic($order)) {
    $output .= '<div class="col-sm-12 review-line"><strong><em>All pricing shown in USD</em></strong></div>';
  }

  return $output;
}

/** Override Drupal Bootstrap Button re-theming
 *
 */

function tuneupfitness_button($variables) {
  $element                        = $variables['element'];
  $element['#attributes']['type'] = 'submit';
  element_set_attributes($element, array('id', 'name', 'value'));

  $element['#attributes']['class'][] = 'form-' . $element['#button_type'];
  if (!empty($element['#attributes']['disabled'])) {
    $element['#attributes']['class'][] = 'form-button-disabled';
  }

  return '<input' . drupal_attributes($element['#attributes']) . ' />';
}

/**
 * @file
 * Theme functions for the uc_credit module.
 */

/**
 * Themes the credit card CVV help link.
 */
function tuneupfitness_uc_credit_cvv_help($variables) {
  $output = '<div class="uc-credit-cvv-help">';
  $output .= theme('image', array('path' => drupal_get_path('module', 'uc_credit') . '/images/info.png'));
  $output .= ' ' . l(t("What's the CVV?"), '#', array(
      'attributes' => array(
        'data-target' => ".cvv-info-modal",
        'data-toggle' => "modal",
        'type'        => "button",
      )
    ));
  $output .= '</div>';
  $output .= '<div aria-labelledby="myLargeModalLabel" class="modal fade cvv-info-modal" role="dialog" tabindex="-1">
      <div class="modal-dialog modal-lg">
         <div class="modal-content">
            <div class="modal-header">
               <h6 class="modal-title" id="gridSystemModalLabel">' . t('What is the CVV?') . '</h6>
            </div>';

  $output .= '<div class="modal-body">
               <p>' . t('CVV stands for Card Verification Value. This number is used as a security feature to protect you from credit card fraud.  Finding the number on your card is a very simple process.  Just follow the directions below.') . '</p>';


  $cc_types = array(
    'visa'       => t('Visa'),
    'mastercard' => t('MasterCard'),
    'discover'   => t('Discover')
  );
  foreach ($cc_types as $id => $type) {
    if (variable_get('uc_credit_' . $id, TRUE)) {
      $valid_types[] = $type;
    }
  }
  if (count($valid_types) > 0) {
    $output .= '<br /><b>' . implode(', ', $valid_types) . ':</b><p>';
    $output .= theme('image', array(
      'path'       => drupal_get_path('module', 'uc_credit') . '/images/visa_cvv.jpg',
      'attributes' => array('align' => 'left'),
    ));
    $output .= t('The CVV for these cards is found on the back side of the card.  It is only the last three digits on the far right of the signature panel box.');
    $output .= '</p>';
  }

  if (variable_get('uc_credit_amex', TRUE)) {
    $output .= '<br /><p><b>' . t('American Express') . ':</b><p>';
    $output .= theme('image', array(
      'path'       => drupal_get_path('module', 'uc_credit') . '/images/amex_cvv.jpg',
      'attributes' => array('align' => 'left'),
    ));
    $output .= t('The CVV on American Express cards is found on the front of the card.  It is a four digit number printed in smaller text on the right side above the credit card number.');
    $output .= '</p>';
  }
  $output .= '</div>';

  $output .= '<div class="modal-footer"><button class="btn btn-info" data-dismiss="modal" type="button">Cancel</button></div>
         </div>
      </div>
   </div>';

  return $output;
}

/**
 * Default theme function for the checkout form.
 *
 * @param $variables
 *   An associative array containing:
 *   - form: A render element representing the form.
 *
 * @see uc_cart_checkout_form()
 * @ingroup themeable
 */
function tuneupfitness_uc_cart_checkout_form($variables) {
  $output = '';

  $output .= '<div class="row">';

  $output .= '<div class="col-xs-12 col-md-4 pull-right">';
  $output .= render($variables['form']['panes']['order_summary']);
  $output .= '</div>';

  $output .= '<div class="col-xs-12 col-md-8">';
  $output .= drupal_render_children($variables['form']);
  $output .= '</div>';

  return $output;
}

function tuneupfitness_at_home_subscription_uc_cart_checkout_review($variables){
  $output = '';

  $address = $variables['account_info']['account']->address['shipping']['raw'];
  $cc_info = $variables['account_info']['account']->cc_info;
  $shipping_next = at_home_subscription_account_info_get_shipping_next($variables['account_info']);

  $total_calc = at_home_subscription_calculate_total_cost($variables['account_info']['account'], $shipping_next);

  $ship_to = uc_address_format(
    $address['first_name'],
    $address['last_name'],
    $address['company'],
    $address['street1'],
    $address['street2'],
    $address['city'],
    $address['zone'],
    $address['postal_code'],
    $address['country']
  );

  $header = array(
    'Products',
    'Unit Price',
    'Qty',
    'Price',
  );

  $rows = array();
  foreach($shipping_next as $product){
    $price_display = theme('uc_product_price',array('element' => array('#value' => $product->price)));
    $rows[] = array(
      $product->title,
      $price_display,
      1,
      $price_display,
    );
  }

  $output .= '<div class="row">';

  $output .= '<div class="shipping col-sm-4"><h3>Ship to:</h3>';
  $output .= $ship_to;
  $output .= '</div>';

  $output .= '<div class="billing col-sm-4"><h3>Bill to:</h3>';
  $output .= $ship_to;
  $output .= '</div>';

  $output .= '<div class="payment col-sm-4"><h3>Payment:</h3>';
  $output .= 'Paying by Credit card<br />';
  $output .= 'Saved card: '.$cc_info['profile']['cc_type'].' - ending in '.$cc_info['profile']['cc_last_four'].' (default)<br />';
  $output .= '</div>';

  $output .= '<div class="cart-info col-xs-12"><h3>Cart Information:</h3>';
  $output .= theme('table',array('header' => $header, 'rows' => $rows));
  $output .= '</div>';

  $output .= '<div class="col-xs-12 text- review-lines">';

  if(!empty($total_calc['tax']) || !empty($total_calc['shipping_title'])){
    $output .= '<div class="review-line">';
    $output .= '<strong>Subtotal:</strong> '.$total_calc['subtotal'];
    $output .= '</div>';
  }

  if(!empty($total_calc['tax'])){
    $output .= '<div class="review-line">';
    $output .= '<strong>Tax:</strong> '.$total_calc['tax'];
    $output .= '</div>';
  }

  if(!empty($total_calc['shipping_title'])){
    $output .= '<div class="review-line">';
    $output .= '<strong>'.$total_calc['shipping_title'].':</strong> '.$total_calc['shipping_price'];
    $output .= '</div>';
  }

  $output .= '<div class="review-line">';
  $output .= '<strong>Total:</strong> '.$total_calc['total'];
  $output .= '</div>';

  $output .= '</div>';

  $output .= '<div class="col-xs-12 review-form">';
  $output .= drupal_render($variables['form']);
  $output .= '</div>';

  $output .= '</div>';

  return $output;
}

/**
 * Implementation of hook_theme_registry_alter.
 *
 * Alters theme registry to use our empty shopping cart theme functions.
 */
function tuneupfitness_theme_registry_alter(&$theme_registry) {
  if (!empty($theme_registry['uc_empty_cart'])) {
    $theme_registry['uc_empty_cart']['function'] = 'tuneupfitness_uc_empty_cart';
  }
}


/**
 * Return the text displayed for an empty shopping cart.
 *
 * It's the same from Ubercart but with cart-form-pane id added, so Aajx Cart block can locate cart form placeholder.
 * #1238594 buf fix.
 *
 * @ingroup themeable
 */
function tuneupfitness_uc_empty_cart() {
  $output = '<p id="cart-form-pane">' . t('There are no products in your shopping cart.') . '</p>';

  if(at_home_display_cart_link_to_program_finder()){
    $output .= '<p>'.l('Go Back to Package Selection', 'customized-program').'</p>';
  }

  return $output;
}

function tuneupfitness_addressfield_formatter__components($vars) {
  $loc = $vars['address'];
  $components = $vars['components'];
  $separator = $vars['separator'];

  $out = array();
  foreach ($components as $key) {
    if (!empty($loc[$key])) {
      $out[$key] = $loc[$key];
    }
    elseif ($key == 'country_full' && !empty($loc['country'])) {
      if($loc['country'] == 'US'){
        $out[$key] = 'USA';
      }
      else{
        $out[$key] = _addressfield_tokens_country($loc['country']);
      }
    }
    elseif ($key == 'administrative_area_full' && !empty($loc['country']) && !empty($loc['administrative_area'])) {
      $out[$key] = _addressfield_tokens_state($loc['country'], $loc['administrative_area']);
    }
  }

  return implode($separator, $out);
}

function tuneupfitness_bootstrap_colorize_text_alter(&$texts){
  $texts['matches'][decode_entities('&#xf218;')] = 'primary';
  $texts['matches'][t('Add to cart')] = 'primary';
  $texts['matches'][t('Proceed to Checkout')] = 'primary';
  $texts['matches'][t('Yoga Experience')] = 'info';
  $texts['matches'][t('Your Information')] = 'info';
  $texts['matches'][t('Show Me My Program')] = 'info';
  $texts['contains'][t('Submit')] = 'info';
  $texts['contains'][t('Search')] = 'info';
}

function tuneupfitness_video_custom_playlist_form($variables){
  $output = '';
  $form = $variables['form'];
  $rows = array();
  $table_id = 'playlist-sort';

  /** @var UserPlaylist|null $playlist */
  $playlist = null;
  if(!empty($form['playlist_id']['#value'])){
    $playlist = UserPlaylist::load($form['playlist_id']['#value']);
  }

  foreach(element_children($form['playlist_sort']) as $id){
    $class = array('draggable', 'hidden');

    if(!is_null($playlist)){
      foreach($playlist->getVideos() as $userPlaylistVideo){
        if($id == $userPlaylistVideo->getVideoNid()){
          $class = array('draggable');
          break;
        }
      }
    }

    $rows[] = array(
      'data' => array(
        drupal_render($form['playlist_sort'][$id]['name']),
        drupal_render($form['playlist_sort'][$id]['weight']),
        '<button class="btn btn-default btn-xs playlist-remove">x</button>',
      ),
      'class' => $class,
      'id' => 'playlist-sort-'.$id,
    );
  }

  $output .= '<div class="panel panel-default"><div class="panel-heading">Your Playlist</div><div id="no-videos" class="panel-body'.(!is_null($playlist) ? ' hidden': '').'"><div class="col-xs-12">No Videos in your Playlist</div></div><div class="panel-body">';

  $output .= theme('table', array(
    'rows' => $rows,
    'attributes' => array('id' => $table_id),
    'context' => array(
      'striped' => FALSE,
      'hover' => FALSE,
    ),
  ));

  $output .= '</div></div>';

  drupal_add_tabledrag($table_id, 'order', 'sibling', 'weight');

  $output .= drupal_render_children($form);

  return $output;
}

function tuneupfitness_video_create_video_package_form($variables){
  $output = '';
  $form = $variables['form'];
  $rows = array();
  $table_id = 'create-video-package-table';

  foreach(element_children($form['create_package_videos']) as $id){
    $rows[] = array(
      'data' => array(
        drupal_render($form['create_package_videos'][$id]['name']),
        drupal_render($form['create_package_videos'][$id]['price']),
        '<button class="create-package-remove">x</button>',
      ),
      'class' => array('hidden'),
      'id' => 'create-package-'.$id,
    );
  }

  $output .= '<div class="panel panel-default"><div class="panel-heading">Selected Videos <div class="pull-right"><a id="clear-videos" href="#">Clear</a></div></div><div id="no-videos" class="panel-body"><div class="col-xs-12">No Videos Selected</div></div><div class="panel-body">';

  $output .= theme('table', array(
    'rows' => $rows,
    'attributes' => array('id' => $table_id),
    'context' => array(
      'striped' => FALSE,
      'hover' => FALSE,
    ),
  ));

  $output .= '</div></div>';

  $output .= drupal_render_children($form);

  return $output;
}

function tuneupfitness_image_formatter($variables) {
  $item = $variables['item'];
  $image = array(
    'path' => $item['uri'],
  );

  if (array_key_exists('alt', $item)) {
    $image['alt'] = $item['alt'];
  }

  if (isset($item['attributes'])) {
    $image['attributes'] = $item['attributes'];
  }

  if (isset($item['width']) && isset($item['height'])) {
    $image['width'] = $item['width'];
    $image['height'] = $item['height'];
  }

  // Do not output an empty 'title' attribute.
  if (isset($item['title']) && drupal_strlen($item['title']) > 0) {
    $image['title'] = $item['title'];
  }

  if(isset($item['url_prefix'])){
    $image['url_prefix'] = $item['url_prefix'];
  }

  if ($variables['image_style']) {
    $image['style_name'] = $variables['image_style'];
    $output = theme('image_style', $image);
  }
  else {
    $output = theme('image', $image);
  }

  // The link path and link options are both optional, but for the options to be
  // processed, the link path must at least be an empty string.
  if (isset($variables['path']['path'])) {
    $path = $variables['path']['path'];
    $options = isset($variables['path']['options']) ? $variables['path']['options'] : array();
    // When displaying an image inside a link, the html option must be TRUE.
    $options['html'] = TRUE;
    $output = l($output, $path, $options);
  }

  return $output;
}

function tuneupfitness_image($variables) {
  $attributes = $variables['attributes'];
  $attributes['src'] = file_create_url($variables['path']);

  if(isset($variables['url_prefix'])){
    $attributes['src'] = $variables['url_prefix'].$attributes['src'];
  }

  foreach (array('width', 'height', 'alt', 'title') as $key) {

    if (isset($variables[$key])) {
      $attributes[$key] = $variables[$key];
    }
  }

  return '<img' . drupal_attributes($attributes) . ' />';
}
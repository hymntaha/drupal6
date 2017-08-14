<?php
/**
 * @file
 * Defines the CTools Export UI plugin for custom payment methods.
 */
$menu_items = explode('/', UC_CUSTOM_PAYMENT_BASE_PATH);
$plugin = array(
  'schema' => 'uc_custom_payment',
  'access' => 'administer store',
  'menu' => array(
    'menu prefix' => implode('/', array_slice($menu_items, 0, -1)),
    'menu item' => end($menu_items),
    'menu title' => 'Payment methods',
    'menu description' => 'Administer custom payment methods.',
  ),
  'title singular' => t('custom payment method'),
  'title plural' => t('custom payment methods'),
  'title singular proper' => t('Custom payment method'),
  'title plural proper' => t('Custom payment methods'),
  'handler' => 'uc_custom_payment_ui',
);

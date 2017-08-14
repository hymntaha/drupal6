<?php
/**
 * @file
 *
 * Ubercart User Credit module api/hooks.
 * Version 7.x-1.x
 */

/**
 * hook_uc_user_credit().
 *
 * Allow modules to act when an admin adjusts a users store credit
 *
 * @param $account
 *      The user account.
 * @param $credit
 *      The amount of user credit of the adjustment.
 * @param $full_credit
 * 		The full amount of credit the user has.
 */
function hook_uc_user_credit($account, $credit, $full_credit) {
  //Send an email when the admin adjusts the users credit
  if($credit > 0){

    $settings = array(
      'account' => $account->mail,
      'credit' => uc_currency_format($credit),
      'full_credit' => uc_currency_format($full_credit),
      'subject' => 'You have been given store credit.',
    );

    drupal_mail('uc_user_credit','user_credit_email',$account->mail,user_preferred_language($account), $settings);
  }
}

/**
 * hook_uc_user_credit_order_is_eligible().
 *
 * Allow modules to validate whether this order is eligible for user credit.
 *
 * @param $credit
 *      The amount of user credit the user has.
 * @param $order
 * 		The order against which the user credit will be validated.
 *
 * @return
 *   TRUE if the user credit is eligible for this order.
 *   FALSE if the user credit is not eligible for this order.
 */
function hook_uc_user_credit_order_is_eligible($credit, $order) {
  //Do not allow user credit if the order subtotal is less then $50.
  $subtotal = uc_order_get_total($order, TRUE);
  if($subtotal < 50){
    return FALSE;
  }

  return TRUE;
}
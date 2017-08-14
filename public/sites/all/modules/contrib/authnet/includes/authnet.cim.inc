<?php

/**
 * @file
 * Authorize.net API CIM wrapper functions
 */

/* ******************************************************************************
 * CIM customer profile wrapper functions
 * *****************************************************************************/

/**
 * Load an existing customer profile from Authorize.net.
 *
 * @param $customer_profile_id
 *   The customer profile id.
 *
 * @return
 *   Returns an AuthorizeNetCustomer object if available, FALSE otherwise.
 */
function authnet_cim_profile_load($customer_profile_id) {

  // Create an Authorize.net request object.
  $request = authnet_request_new('CIM');

  // Retrieve the customer profile.
  $response = $request->getCustomerProfile($customer_profile_id);

  // Log the response object.
  authnet_log_response($response);

  // If the request was successful, return the profile.
  if ($response->isOK()) {

    // Define the non-unique indices to look for (paymentProfiles and shipToList)
    $nonunique = array('paymentProfiles', 'shipToList');

    // Convert the profile from a SimpleXMLElement to an AuthorizeNetCustomer object.
    $customer_profile = authnet_customer_profile_new(_authnet_simplexml_to_array($response->xml->profile, $nonunique));

    // Invoke hook_authnet_cim_profile_load and pass in the customer profile object.
    module_invoke_all('authnet_cim_profile_load', $customer_profile);

    // Return the profile.
    return $customer_profile;
  }

  // Return FALSE if the above didn't return anything else.
  return FALSE;
}

/**
 * Save a customer profile to Authorize.net.
 * If the $customer_profile->customerProfileId propery is set, then the profile will be updated.
 * Otherwise, a new one will be saved.
 *
 * @param AuthorizeNetCustomer &$customer_profile
 *   The customer profile object to be saved, passed by reference.
 *
 * @return
 *   Returns TRUE if the save was successful, FALSE otherwise.
 */
function authnet_cim_profile_save(AuthorizeNetCustomer &$customer_profile) {

  // Create an Authorize.net request object.
  $request = authnet_request_new('CIM');

  // Determine the validation mode.
  $validation = variable_get('authnet_cim_validation_mode', 'none');
  /**
   * @todo
   * The validation mode is currently passed in, but the module doesn't handle the validation responses yet.
   * Use $response->getValidationResponses() to retrieve validation responses, and then parse/display them.
   */

  // If the $customer_profile->customerProfileId propery is set, then update an existing profile.
  if (isset($customer_profile->customerProfileId) && !empty($customer_profile->customerProfileId)) {

    // Authorize.net does not accept payment profiles or ship to addresses when updating a profile, so unset them here.
    // They will be reloaded later.
    unset($customer_profile->paymentProfiles);
    unset($customer_profile->shipToList);

    // Send the request.
    $response = $request->updateCustomerProfile($customer_profile->customerProfileId, $customer_profile);
    $new = FALSE;
  }

  // Otherwise, save a new customer profile.
  else {

    // Only use validation if there are payment profiles assigned to this new customer profile.
    if (empty($customer_profile->paymentProfiles)) {
      $validation = 'none';
    }

    // Send the request.
    $response = $request->createCustomerProfile($customer_profile, $validation);
    $new = TRUE;
  }

  // Log the response object.
  authnet_log_response($response);

  // If the request was successful...
  if ($response->isOk()) {

    // If a new profile was saved...
    if ($new) {

      // Get the new profile id from the response.
      $customer_profile_id = $response->getCustomerProfileId();

      // If the new customer profile has payment profiles with credit cards, save their expiration date information so that it's available in the insert hook after reloading the profile.
      $expiration_dates = array();
      if (!empty($customer_profile->paymentProfiles)) {
        foreach ($customer_profile->paymentProfiles as $payment_profile) {
          if (!empty($payment_profile->payment->creditCard->expirationDate)) {
            $expiration_dates[$payment_profile->customerPaymentProfileId] = $payment_profile->payment->creditCard->expirationDate;
          }
        }
      }

      // Load the full customer profile object with another call to Authorize.net.
      $customer_profile = authnet_cim_profile_load($customer_profile_id);

      // Restore the expiration dates.
      if (!empty($customer_profile->paymentProfiles)) {
        foreach ($customer_profile->paymentProfiles as $payment_profile) {
          if (!empty($expiration_dates[$payment_profile->customerPaymentProfileId])) {
            $payment_profile->payment->creditCard->expirationDate = $expiration_dates[$payment_profile->customerPaymentProfileId];
          }
        }
      }

      // Invoke hook_authnet_cim_profile_insert and pass in the customer profile object.
      module_invoke_all('authnet_cim_profile_insert', $customer_profile);
    }

    // If the profile is being updated...
    else {

      // Load the full customer profile object with another call to Authorize.net.
      $customer_profile = authnet_cim_profile_load($customer_profile->customerProfileId);

      // Invoke hook_authnet_cim_profile_update and pass in the customer profile object.
      module_invoke_all('authnet_cim_profile_update', $customer_profile);
    }

    // Return TRUE.
    return TRUE;
  }
  elseif ($response->getMessageCode() == 'E00039') {
    // Duplicate customer profile exists. Authorize.net matches duplicates for
    // createCustomerProfileRequest using fields: merchantCustomerId,
    // description, and e-mail. It should therefore be safe to load the dupe.
    // Dupe ID is only contained in response error message and must be scraped. 
    // @See https://support.authorize.net/authkb/index?page=content&id=A116
    $matches = array();
    preg_match_all('/A duplicate record with ID ([0-9]*) already exists/', $response->getMessageText(), $matches);
    
    if (!empty($matches[1][0])) {
      $customer_profile_loaded = authnet_cim_profile_load($matches[1][0]);
      if ($customer_profile_loaded != FALSE) {
        $customer_profile = $customer_profile_loaded;
        
        // Invoke hook_authnet_cim_profile_insert and pass in the newly loaded
        // customer profile object so that it is saved locally as entity.
        module_invoke_all('authnet_cim_profile_insert', $customer_profile);
        
        // Return TRUE so transaction can continue.
        return TRUE;
      }
    }
  }

  // Return FALSE if the above didn't return anything else.
  return FALSE;
}

/**
 * Delete a customer profile from Authorize.net
 *
 * @param $customer_profile_id
 *   The id of the customer profile to be deleted.
 *
 * @return
 *   Returns TRUE on success, FALSE otherwise.
 */
function authnet_cim_profile_delete($customer_profile_id) {

  // Create an Authorize.net request object.
  $request = authnet_request_new('CIM');

  // Delete the profile.
  $response = $request->deleteCustomerProfile($customer_profile_id);

  // Log the response object.
  authnet_log_response($response);

  // If the request was successful...
  if ($response->isOk()) {

    // Invoke hook_authnet_cim_profile_delete and pass in the customer profile id.
    module_invoke_all('authnet_cim_profile_delete', $customer_profile_id);

    // Return TRUE.
    return TRUE;
  }

  // Return FALSE if the above didn't return anything else.
  return FALSE;
}

/* ******************************************************************************
 * CIM payment profile wrapper functions
 * *****************************************************************************/

/**
 * Load an existing customer payment profile from Authorize.net.
 *
 * @param $customer_profile_id
 *   The customer profile id.
 * @param $payment_profile_id
 *   The customer payment profile id.
 *
 * @return
 *   Returns an AuthorizeNetPaymentProfile object if available, FALSE otherwise.
 */
function authnet_cim_payment_profile_load($customer_profile_id, $payment_profile_id) {

  // Create an Authorize.net request object.
  $request = authnet_request_new('CIM');

  // Retrieve the customer profile.
  $response = $request->getCustomerPaymentProfile($customer_profile_id, $payment_profile_id);

  // Log the response object.
  authnet_log_response($response);

  // If the request was successful, return the profile.
  if ($response->isOK()) {

    // Convert the payment profile from a SimpleXMLElement to an AuthorizeNetPaymentProfile object.
    $payment_profile = authnet_payment_profile_new(_authnet_simplexml_to_array($response->xml->paymentProfile));

    // Invoke hook_authnet_cim_payment_profile_load and pass in the payment profile object.
    module_invoke_all('authnet_cim_payment_profile_load', $customer_profile_id, $payment_profile);

    // Return the profile.
    return $payment_profile;
  }

  // Return FALSE if the above didn't return anything else.
  return FALSE;
}

/**
 * Save a payment profile to Authorize.net.
 * If the $payment_profile->customerPaymentProfileId propery is set, then the profile will be updated.
 * Otherwise, a new one will be saved.
 *
 * @param $customer_profile_id
 *   The customer profile id.
 * @param AuthorizeNetPaymentProfile &$payment_profile
 *   The payment profile object to be saved, passed by reference.
 *
 * @return
 *   Returns TRUE if the save was successful, FALSE otherwise.
 */
function authnet_cim_payment_profile_save($customer_profile_id, AuthorizeNetPaymentProfile &$payment_profile) {

  // Create an Authorize.net request object.
  $request = authnet_request_new('CIM');

  // Determine the validation mode.
  $validation = variable_get('authnet_cim_validation_mode', 'none');
  /**
   * @todo
   * The validation mode is currently passed in, but the module doesn't handle the validation responses yet.
   * Use $response->getValidationResponses() to retrieve validation responses, and then parse/display them.
   */

  // If the $payment_profile->customerPaymentProfileId propery is set, then the profile will be updated.
  if (isset($payment_profile->customerPaymentProfileId) && !empty($payment_profile->customerPaymentProfileId)) {
    $response = $request->updateCustomerPaymentProfile($customer_profile_id, $payment_profile->customerPaymentProfileId, $payment_profile, $validation);
    $new = FALSE;
  }

  // Otherwise, save a new payment profile.
  else {
    $response = $request->createCustomerPaymentProfile($customer_profile_id, $payment_profile, $validation);
    $new = TRUE;
  }

  // Log the response object.
  authnet_log_response($response);

  // If the request was successful...
  if ($response->isOk()) {

    // If a new profile was created...
    if ($new) {

      // Get the new profile id from the response.
      $payment_profile_id = $response->getPaymentProfileId();

      // If the new payment profile has a credit card expiration date, save it so that it's available in the insert hook after reloading the profile.
      $expiration_date = '';
      if (!empty($payment_profile->payment->creditCard->expirationDate)) {
        $expiration_date = $payment_profile->payment->creditCard->expirationDate;
      }

      // Load the full payment profile object with another call to Authorize.net.
      $payment_profile = authnet_cim_payment_profile_load($customer_profile_id, $payment_profile_id);

      // Restore the expiration date.
      if (!empty($expiration_date)) {
        $payment_profile->payment->creditCard->expirationDate = $expiration_date;
      }

      // Invoke hook_authnet_cim_payment_profile_insert and pass in the payment profile object.
      module_invoke_all('authnet_cim_payment_profile_insert', $customer_profile_id, $payment_profile);
    }

    // If the profile is being updated...
    else {

      // Invoke hook_authnet_cim_payment_profile_update and pass in the payment profile object.
      module_invoke_all('authnet_cim_payment_profile_update', $customer_profile_id, $payment_profile);
    }

    // Return TRUE.
    return TRUE;
  }

  // Return FALSE if the above didn't return anything else.
  return FALSE;
}

/**
 * Delete a payment profile from Authorize.net
 *
 * @param $customer_profile_id
 *   The customer profile id.
 * @param $payment_profile_id
 *   The id of the payment profile to be deleted.
 *
 * @return
 *   Returns TRUE if successful, FALSE otherwise.
 */
function authnet_cim_payment_profile_delete($customer_profile_id, $payment_profile_id) {

  // Create an Authorize.net request object.
  $request = authnet_request_new('CIM');

  // Delete the profile.
  $response = $request->deleteCustomerPaymentProfile($customer_profile_id, $payment_profile_id);

  // Log the response object.
  authnet_log_response($response);

  // If the request was successful...
  if ($response->isOk()) {

    // Invoke hook_authnet_cim_payment_profile_delete and pass in the payment profile id.
    module_invoke_all('authnet_cim_payment_profile_delete', $customer_profile_id, $payment_profile_id);

    // Return TRUE.
    return TRUE;
  }

  // Return FALSE if the above didn't return anything else.
  return FALSE;
}

/* ******************************************************************************
 * CIM shipping address wrapper functions
 * *****************************************************************************/

/**
 * Load an existing customer shipping address from Authorize.net.
 *
 * @param $customer_profile_id
 *   The customer profile id.
 * @param $customer_address_id
 *   The customer address id id.
 *
 * @return
 *   Returns an AuthorizeNetAddress object if available, FALSE otherwise.
 */
function authnet_cim_shipping_address_load($customer_profile_id, $customer_address_id) {

  // Create an Authorize.net request object.
  $request = authnet_request_new('CIM');

  // Retrieve the customer shipping address.
  $response = $request->getCustomerShippingAddress($customer_profile_id, $customer_address_id);

  // Log the response object.
  authnet_log_response($response);

  // If the request was successful, return the address.
  if ($response->isOK()) {

    // Convert the address from a SimpleXMLElement to an AuthorizeNetAddress object.
    $address = authnet_address_new(_authnet_simplexml_to_array($response->xml->address));

    // Return the address.
    return $address;
  }

  // Return FALSE if the above didn't return anything else.
  return FALSE;
}

/**
 * Save a shipping address to Authorize.net.
 * If the $address->customerAddressId propery is set, then the address will be updated.
 * Otherwise, a new one will be saved.
 *
 * @param $customer_profile_id
 *   The customer profile id.
 * @param AuthorizeNetAddress &$address
 *   The address object to be saved, passed by reference.
 *
 * @return
 *   Returns an AuthorizeNetAddress object if available, FALSE otherwise.
 */
function authnet_cim_shipping_address_save($customer_profile_id, AuthorizeNetAddress &$address) {

  // Create an Authorize.net request object.
  $request = authnet_request_new('CIM');

  // If the $address->customerAddressId propery is set, then the profile will be updated.
  if (isset($address->customerAddressId) && !empty($address->customerAddressId)) {
    $response = $request->updateCustomerShippingAddress($customer_profile_id, $address->customerAddressId, $address);
    $new = FALSE;
  }

  // Otherwise, save a new address.
  else {
    $response = $request->createCustomerShippingAddress($customer_profile_id, $address);
    $new = TRUE;
  }

  // Log the response object.
  authnet_log_response($response);

  // If the request was successful...
  if ($response->isOk()) {

    // If a new address was created...
    if ($new) {

      // Get the new address id from the response.
      $customer_address_id = $response->getCustomerAddressId();

      // Load the full address object with another call to Authorize.net.
      $address = authnet_cim_shipping_address_load($customer_profile_id, $customer_address_id);
    }

    // Return the address.
    return $address;
  }

  // Return FALSE if the above didn't return anything else.
  return FALSE;
}

/**
 * Delete a shipping address from Authorize.net
 *
 * @param $customer_profile_id
 *   The customer profile id.
 * @param $customer_address_id
 *   The customer address id.
 *
 * @return
 *   Returns TRUE if successful, FALSE otherwise.
 */
function authnet_cim_shipping_address_delete($customer_profile_id, $customer_address_id) {

  // Create an Authorize.net request object.
  $request = authnet_request_new('CIM');

  // Delete the address.
  $response = $request->deleteCustomerShippingAddress($customer_profile_id, $customer_address_id);

  // Log the response object.
  authnet_log_response($response);

  // If the request was successful, return TRUE.
  if ($response->isOk()) {
    return TRUE;
  }

  // Return FALSE if the above didn't return anything else.
  return FALSE;
}

/* ******************************************************************************
 * CIM transaction wrapper functions
 * *****************************************************************************/

/**
 * Send a new transaction to Authorize.net.
 *
 * @param $transaction
 *   The AuthorizeNetTransaction object that contains all of the transaction data.
 * @param $method
 *   The method of transaction to perform. Must be one of the defined AUTHNET_METHOD_* constants (see authnet.module).
 *
 * @return
 *   Returns TRUE if successful, FALSE otherwise.
 */
function authnet_cim_transaction(AuthorizeNetTransaction $transaction, $method = AUTHNET_METHOD_AUTH_CAPTURE) {

  // Define the available transaction methods.
  $methods = array(
    AUTHNET_METHOD_AUTH_CAPTURE => 'AuthCapture',
    AUTHNET_METHOD_PRIOR_AUTH_CAPTURE => 'PriorAuthCapture',
    AUTHNET_METHOD_AUTH_ONLY => 'AuthOnly',
    AUTHNET_METHOD_CAPTURE_ONLY => 'CaptureOnly',
    AUTHNET_METHOD_CREDIT => 'Refund',
    AUTHNET_METHOD_VOID => 'Void',
  );

  // If a valid method is set...
  if (isset($methods[$method])) {

    // Create an Authorize.net request object.
    $request = authnet_request_new('CIM');

    //Round the transaction amount to two decimal places
    $transaction->amount = round($transaction->amount, 2);

    // Send the transaction to Authorize.net.
    $response = $request->createCustomerProfileTransaction($methods[$method], $transaction);

    // Log the response object.
    authnet_log_response($response);

    // If the request was successful, return TRUE.
    if ($response->isOk()) {
      return TRUE;
    }
  }

  // Return FALSE if the above didn't return anything else.
  return FALSE;
}

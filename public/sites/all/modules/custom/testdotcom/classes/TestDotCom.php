<?php

class TestDotCom {
  const REQUEST_YES = 'yes';
  const REQUEST_NO = 'no';
  const REQUEST_NOCHANGE = 'nochange';
  const RESPONSE_STATUS_SUCCESS = 'Success';
  const RESPONSE_STATUS_FAIL = 'Fail';

  private $webserviceUrl;
  private $accountLoginCode;
  private $accountXMLCode;
  private $accountIDSpecialCode;
  private $authorLogin;
  private $authorPassword;

  /**
   * TestDotCom constructor.
   */
  public function __construct() {
    $this->webserviceUrl        = variable_get('testdotcom_webservice_url', '');
    $this->accountLoginCode     = variable_get('testdotcom_login_code', '');
    $this->accountXMLCode       = variable_get('testdotcom_xml_data_access_code', '');
    $this->accountIDSpecialCode = variable_get('testdotcom_account_id_special_code', '');
    $this->authorLogin          = 'TUFWRobert';
    $this->authorPassword       = 'TUFW123';
  }

  public function checkConnection() {
    return $this->webserviceCall('test20130401', array('TestString' => 'test'));
  }

  public function addUser($account, $password) {
    $call = 'add20121001';

    $params = array(
      'UserName'              => account_get_full_name($account),
      'UserEmail'             => $account->mail,
      'UserNewHireDateMonth'  => 0,
      'UserNewHireDateDay'    => 0,
      'UserNewHireDateYear'   => 0,
      'UserCredits'           => 0,
      'UserAccountInactive'   => TestDotCom::REQUEST_NO,
      'UserIgnoreAccounting'  => TestDotCom::REQUEST_NO,
      'UserTimeZone'          => 0,
      'UserLocalCode'         => 0,
      'UserLogin'             => testdotcom_get_username_from_account($account),
      'UserPassword'          => $password,
    );

    $response = $this->webserviceCall($call, $params);

    switch($response['message']){
      case 'New User Added':
      case 'Could not save, the Login Code entered has already been used in another record, please choose another code and try again.':
        return $response;
        break;
    }

    $this->logCallResponse($call, $response);

    return FALSE;
  }

  public function updateGroupMembership($account, $product){
    $call = 'updateGroupMembership20110405';

    $params = array(
      'UserLogin' => testdotcom_get_username_from_account($account),
      'GroupUserLookupCode' => testdotcom_get_product_group_code($product),
      'GroupUserIsMember' => TestDotCom::REQUEST_YES,
      'GroupUserIsUserAdmin' => TestDotCom::REQUEST_NOCHANGE,
      'GroupUserIsContentAdmin' => TestDotCom::REQUEST_NOCHANGE,
      'GroupUserIsReportingAdmin' => TestDotCom::REQUEST_NOCHANGE,
      'GroupUserIsProctorAdmin' => TestDotCom::REQUEST_NOCHANGE,
    );

    $response = $this->webserviceCall($call, $params);

    if($response['message'] == 'Updated User Group User Membership'){
      return $response;
    }

    $this->logCallResponse($call, $response);

    return FALSE;
  }

  public function checkGroupMembership($account, $product){
    $call = 'checkGroupMembership20110405';

    $params = array(
      'UserLogin' => testdotcom_get_username_from_account($account),
      'GroupUserLookupCode' => '868156',
    );

    $response = $this->webserviceCall($call, $params);

    if($response['status'] != TestDotCom::RESPONSE_STATUS_FAIL){
      return $response;
    }

    $this->logCallResponse($call, $response);

    return FALSE;
  }

  public function addPurchaseTransaction($account, $product, $order_id){
    $call = 'addPurchaseTransaction20121001';

    $params = array(
      'UserLogin' => testdotcom_get_username_from_account($account),
      'ContentLookupCode' => testdotcom_get_product_content_code($product),
      'PurchaseTransAmount' => round($product->price, 2),
      'PurchaseTransStatusCode' => 11,
      'PurchaseTransID' => $order_id,
    );

    $response = $this->webserviceCall($call, $params);

    if($response['status'] == TestDotCom::RESPONSE_STATUS_SUCCESS){
      return TRUE;
    }

    $this->logCallResponse($call, $response);

    return FALSE;
  }

  public function checkPurchaseTransaction($account, $product){
    $call = 'checkPurchaseTransaction20121001';

    $params = array(
      'UserLogin' => testdotcom_get_username_from_account($account),
      'ContentLookupCode' => testdotcom_get_product_content_code($product),
    );

    $response = $this->webserviceCall($call, $params);

    if($response['status'] == TestDotCom::RESPONSE_STATUS_SUCCESS){
      return TRUE;
    }

    return FALSE;
  }

  public function getContentLookupCodeValues(){
    $call = 'getContentLookupCodeValues20130401';

    $params = array();

    $response = $this->webserviceCall($call, $params);

    if($response['status'] != TestDotCom::RESPONSE_STATUS_FAIL){
      $rows = explode('|', $response['message']);
      $result = array();

      foreach($rows as $row){
        $result[] = explode('~',$row);
      }

      return $result;
    }

    return FALSE;
  }

  private function webserviceCall($call, $params = array()) {
    $response = FALSE;

    $client = new SoapClient($this->webserviceUrl, array(
      'cache_wsdl'         => WSDL_CACHE_NONE,
      'trace'              => 1,
      'connection_timeout' => 90,
    ));

    $call_params = array_merge(array(
      'AccountLoginCode'     => $this->accountLoginCode,
      'AccountXMLCode'       => $this->accountXMLCode,
      'AccountIDSpecialCode' => $this->accountIDSpecialCode,
      'AuthorLogin'          => $this->authorLogin,
      'AuthorPassword'       => $this->authorPassword,
    ), $params);

    try{

      $response = $this->parseResponse($client->__soapCall($call, $call_params));

    } catch(SoapFault $e){
      watchdog('TestDotCom', 'Call !call failed with message: !message', array(
        '!call'    => $call,
        '!message' => $e->getMessage()
      ));
    }

    return $response;
  }

  /**
   * @param $response
   * @return array
   */
  private function parseResponse($response){
    if(empty($response)){
      return FALSE;
    }

    $parsed_response = explode('|', $response, 2);

    if(count($parsed_response) == 1){
      return array(
        'message' => trim($parsed_response[0]),
      );
    }

    return array(
      'status' => trim($parsed_response[0]),
      'message' => trim($parsed_response[1]),
    );
  }

  /**
   * @param array $response
   */
  private function logCallResponse($call, $response){
    watchdog('TestDotCom', 'Call !call failed with message: !message', array(
      '!call'    => $call,
      '!message' => $response['message'],
    ));
  }
}
<?php

define('ZOHO_AUTH_TOKEN',variable_get('zoho_auth_token', ''));
define('ZOHO_APPLICATION_NAME',variable_get('zoho_application_name', ''));
define('ZOHO_OWNER_NAME',variable_get('zoho_owner_name', ''));
define('BASE_URL','https://creator.zoho.com/api/');

class ZohoCreatorRESTAPI {
    var $version = "0.1";
    var $errorMessage = "";
    var $errorCode = "";
    var $authToken = ZOHO_AUTH_TOKEN;
    var $applicationName = ZOHO_APPLICATION_NAME;
    var $zcOwnerName = ZOHO_OWNER_NAME;
    var $format;
    var $scope; 

    function ZohoCreatorRESTAPI($format='json', $scope='creatorapi') {
        $this->format = $format;
        $this->scope = $scope;
    }

    function listFields($formName){
        $url = BASE_URL . $this->format . '/' . $this->applicationName . '/' . $formName . '/fields/';

        $params['authtoken'] = $this->authToken;
        $params['scope'] = $this->scope;

        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($params),
            ),
        );
        $context  = stream_context_create($options);
        return file_get_contents($url, false, $context);
    }

    function add($formName, $params=array()) {
        $url = BASE_URL . $this->zcOwnerName . '/' . $this->format . '/' . $this->applicationName . '/form/' . $formName . '/record/add/';

        $params['authtoken'] = $this->authToken;
        $params['scope'] = $this->scope;

        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($params),
            ),
        );
        $context  = stream_context_create($options);
        return file_get_contents($url, false, $context);
    }

    function edit($formName, $criteria, $params) {
        $url = BASE_URL . $this->zcOwnerName . '/' . $this->format . '/' . $this->applicationName . '/form/' . $formName . '/record/update/';

        $params['authtoken'] = $this->authToken;
        $params['scope'] = $this->scope;
        $params['criteria'] = $criteria;

        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($params),
            ),
        );
        $context  = stream_context_create($options);
        return file_get_contents($url, false, $context);
    }

    function view($formName, $criteria, $params) {
        $url = BASE_URL . $this->format . '/' . $this->applicationName . '/view/' . $formName;

        $params['authtoken'] = $this->authToken;
        $params['scope'] = $this->scope;
        $params['criteria'] = $criteria;
        $params['raw'] = 'true';

        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($params),
            ),
        );
        $context  = stream_context_create($options);
        return file_get_contents($url, false, $context);
    }

}

?>
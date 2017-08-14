<?php
  /*
===================================
� Copyright Webgility LLC 2007-2011
----------------------------------------
This file and the source code contained herein are the property of Webgility LLC
and are protected by United States copyright law. All usage is restricted as per
the terms & conditions of Webgility License Agreement. You may not alter or remove
any trademark, copyright or other notice from copies of the content.

The code contained herein may not be reproduced, copied, modified or redistributed in any form
without the express written consent by an officer of Webgility LLC.

File last updated		:   01/17/2012
Drupal version 			:	drupal-7.x
Ubercart version 		:	ubercart 7.x-3.0-rc3
===================================
*/

  if (((int)str_replace("M", "", ini_get("memory_limit"))) < 128)
    ini_set("memory_limit", "128M");

  ini_set("display_errors", "Off");
  error_reporting(E_ALL);
# Code for changing directory and accessing include folder

  /* if (!defined('MAINTENANCE_MODE') || MAINTENANCE_MODE != 'update') {
module_invoke_all('init');
}*/
  $public_directory = dirname($_SERVER['PHP_SELF']);
  $directory_array = explode('/', $public_directory);
  $key = array_search('sites', $directory_array);
  $path = "";
  $path1 = "";

  if (isset($key) && $key != '') {
    $i = $key;
    for ($i; $i < count($directory_array); $i++) {
      //$path = '../';
      $path1 .= $path;
    }

  } else {
    for ($i = 1; $i < count($directory_array); $i++) {
      //$path = '../';
      $path1 .= $path;
    }
  }
  $path1 = "/var/aegir/platforms/yogatuneup-7.15/";
  chdir($path1);
# current directory
  require_once 'includes/bootstrap.inc';

  include("sites/all/modules/contrib/ubercart/shipping/uc_shipping/uc_shipping.admin.inc");
  if (file_exists('sites/ecc.yogatuneup.info/settings.php')) {
    require_once 'sites/ecc.yogatuneup.info/settings.php';
  }
  if (file_exists('includes/database/query.inc')) {
    require_once 'includes/database/query.inc';
  }
  if (file_exists('includes/database/database.inc')) {
    require_once 'includes/database/database.inc';
  }
  if (file_exists('includes/cache.inc')) {
    require_once 'includes/cache.inc';
  }
  if (file_exists('includes/module.inc')) {
    require_once 'includes/module.inc';
  }
  if (file_exists('includes/password.inc')) {
    require_once 'includes/password.inc';
  }


  define('DRUPAL_ROOT', getcwd());
//# current directory
//require_once 'includes/common.inc';
//require_once 'modules/system/system.module';
  global $drupal_version;
  global $version;
  global $files;
  if (file_exists('modules/system/system.info')) {
    $file_arr = file("modules/system/system.info");
    $file_output = array_reverse($file_arr);
    $dv = $file_output[3];
    $fv = explode('"', $dv);
    $drupal_version_array = explode('.', $fv[1]);
    $drupal_version = $drupal_version_array[0];
  } else {
    $drupal_full_version = VERSION;

    $drupal_version_array = explode('.', $drupal_full_version);

    $drupal_version = $drupal_version_array[0];
  }

//if($drupal_version >= '7')
  {
    //$var = $phases['DRUPAL_BOOTSTRAP_FULL'];
    //drupal_bootstrap($var,$new_phase = TRUE);
    drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL, $new_phase = true);

    $files = system_rebuild_module_data();
    foreach ($files as $filename => $file) {
      $form['name'][$filename] = $file->info['name'];
      $form['package'][$filename] = $file->info['package'];
      $form['version'][$filename] = $file->info['version'];
      if (($form['name'][$filename] == 'Store') && ($form['package'][$filename] == 'Ubercart - core')) {
        $version = $form['version'][$filename];
      }
    }
  }


# DO NOT DOWNLOAD ORDERS IN FAILED, DECLINED AND NOT FINISHED STATES
# I=Not finished, Q=Queued, P=Processed, B=Backordered, D=Declined, F=Failed, C=Complete
  require_once("D.WgCommon.php");

  class Webgility_Ecc_UB extends WgCommon
  {


    function auth_user($username, $password)
    {
      $WgBaseResponse = new WgBaseResponse();
      try {
        // return true;

        $user = db_query("SELECT uid, name, pass FROM {users} WHERE  LOWER(name) = LOWER(:username)", array(':username' => $username))->fetchObject();
        $num_rows = $user->uid ? 1 : 0;
        $account = user_load($user->uid);


        if ($num_rows == 0) {
          $WgBaseResponse->setStatusCode('1');
          $WgBaseResponse->setStatusMessage('Invalid login. Authorization failed');

          return $this->response($WgBaseResponse->getBaseresponse());
          //exit;
        }

        if (!user_access("administer store", $account)) {
          $WgBaseResponse->setStatusCode('2');
          $WgBaseResponse->setStatusMessage('Account does not have permission. Authorization failed');
          return $this->response($WgBaseResponse->getBaseresponse());

        } else {
          $check = user_check_password($password, $account);
          if ($check != 1) {
            $WgBaseResponse->setStatusCode('2');
            $WgBaseResponse->setStatusMessage('Invalid password. Authorization failed');
            return $this->response($WgBaseResponse->getBaseresponse());
          }


          return 0;
        }

      } catch (Exception $e) {
        $WgBaseResponse->setStatusCode('1');
        $WgBaseResponse->setStatusMessage('Invalid login. Authorization failed');
        return $this->response($WgBaseResponse->getBaseresponse());
        //exit;
      }

    }


    function getVersion()
    {
      global $version;
      $files = system_rebuild_module_data();


      if (isset($files)) {

        if ($files['uc_credit']->status == 0) {
          $message = "Please enable Credit Card module";

        } elseif ($files['taxonomy']->status == 0) {
          $message = "Please enable Taxonomy module";
        } elseif ($files['uc_payment']->status == 0) {
          $message = "Please enable Payemnt module";
        } elseif ($files['uc_shipping']->status == 0) {
          $message = "Please enable Shipping module";
        } elseif ($files['uc_order']->status == 0) {
          $message = "Please enable Order module";
        } elseif ($files['uc_taxes']->status == 0) {
          $message = "Please enable Tax module";
        }

        if ($version) {
          $resultArr['version'] = $version;
        } else {
          $resultArr['version'] = 0;
        }
        $resultArr['message'] = $message;

        return $resultArr;
      }

    }


    # Function to check the admin username and password and also the eCC Version and Store Version

    function checkAccessInfo($username, $password)
    {
      $versionarr1 = array("7.x-3.0-beta3", "7.x-3.0-rc3", "7.x-3.0", "7.x-3.1");
      $responseArray = array();
      #Check for authorization
      $status = $this->auth_user($username, $password);
      if ($status != '0') {
        return $status;
      }
      $WgBaseResponse = new WgBaseResponse();
      $WgBaseResponse->setStatusCode('0');
      $code = "0";
      $result = $this->getVersion();
      //print_r($result);
      if (trim($result['message']) != '') {
        $WgBaseResponse->setStatusCode('1');
        $WgBaseResponse->setStatusMessage($result['message']);
        return $this->response($WgBaseResponse->getBaseresponse());

      }

      $message = "Successfully connected to your online store.";
      $responseArray['StatusCode'] = $code;

      if ($result['version'] != "0") {
        if (!in_array(trim($result['version']), $versionarr1)) {
          $WgBaseResponse->setStatusMessage($message . " However, your store version is " . $result['version'] . " which hasn't been fully tested with eCC. If you'd still like to continue, click OK to continue or contact Webgility to confirm compatibility.");
        } else {
          $WgBaseResponse->setStatusMessage($message);

        }
      } else {
        $WgBaseResponse->setStatusMessage($message . " However, eCC is unable to detect your store version. If you'd still like to continue, click OK to continue or contact Webgility to confirm compatibility.");
      }
      return $this->response($WgBaseResponse->getBaseresponse());

    }


    # Returns the Company Info of the Store
    function getCompanyInfo($username, $password)
    {
      $CompanyInfo = new WG_CompanyInfo();
      $status = $this->auth_user($username, $password);
      if ($status != '0') {
        return $status;
      }

      $store_name = variable_get('uc_store_name', null);

      $store_owner = variable_get('uc_store_owner', null);
      $store_email = variable_get('uc_store_email', null);

      $store_phone = variable_get('uc_store_phone', null);
      $store_fax = variable_get('uc_store_fax', null);


      $street1 = variable_get('uc_store_street1', null);

      $street2 = variable_get('uc_store_street2', null);

      $store_country_id = uc_store_default_country();

      $store_country = "";

      $store_state_id = variable_get('uc_store_zone', null);
      $store_postal_code = variable_get('uc_store_postal_code', null);
      $store_city = variable_get('uc_store_city', null);

      $CompanyInfo->setStatusCode('0');
      $CompanyInfo->setStatusMessage('All Ok');
      $CompanyInfo->setStoreName($store_name);
      $CompanyInfo->setStoreID('');
      $CompanyInfo->setAddress(htmlspecialchars($street1, ENT_NOQUOTES));
      $CompanyInfo->setAddress2(htmlspecialchars($street2, ENT_NOQUOTES));
      $CompanyInfo->setcity(htmlspecialchars($store_city, ENT_NOQUOTES));
      $CompanyInfo->setState(htmlspecialchars($store_state, ENT_NOQUOTES));
      $CompanyInfo->setCountry(htmlspecialchars(store_country, ENT_NOQUOTES));
      $CompanyInfo->setZipcode(htmlspecialchars($store_postal_code, ENT_NOQUOTES));
      $CompanyInfo->setPhone(htmlspecialchars($store_phone, ENT_NOQUOTES));
      $CompanyInfo->setFax(htmlspecialchars($store_fax, ENT_NOQUOTES));
      $CompanyInfo->setEmail(htmlspecialchars($store_email, ENT_NOQUOTES));
      $CompanyInfo->setWebsite(htmlspecialchars($_SERVER['SERVER_NAME'], ENT_NOQUOTES));

      return $this->response($CompanyInfo->getCompanyInfo());
    }


    # Returns All the Payment Methods used by the store
    function getPaymentMethods($username, $password)
    {
      $status = $this->auth_user($username, $password);
      if ($status != '0') {
        return $status;
      }
      $PaymentMethods = new WG_PaymentMethods();

      $methods = _uc_payment_method_list();

      if ($methods) {
        $PaymentMethods->setStatusCode('0');
        $PaymentMethods->setStatusMessage('All Ok');
        $i = 1;
        foreach ($methods as $iInfo) {
          $PaymentMethod = new WG_PaymentMethod();
          $PaymentMethod->setMethodId(htmlspecialchars($i, ENT_NOQUOTES));
          $PaymentMethod->setMethod(htmlentities($iInfo['name'], ENT_QUOTES));
          $PaymentMethod->setDetail(htmlentities($iInfo['desc'], ENT_QUOTES));
          $PaymentMethods->setPaymentMethods($PaymentMethod->getPaymentMethod());
          $i++;
        }
      }
      unset($methods);
      return $this->response($PaymentMethods->getPaymentMethods());
    }


    # Returns all the shipping methods used by the store

    function getShippingMethods($username, $password)
    {
      global $files;
      $status = $this->auth_user($username, $password);
      if ($status != '0') {
        return $status;
      }
      //$methods = uc_quote_shipping_method_options();

      if ($files['uc_shipping']->status == 1) {

        $shipping_methods = module_invoke_all('uc_shipping_method');
        //print_r($shipping_methods);
//
      }
      //print_r($shipping_methods);die("hi");
      $ShippingMethods = new WG_ShippingMethods();
      $ShippingMethods->setStatusCode('0');
      $ShippingMethods->setStatusMessage('All Ok');

      if (isset($shipping_methods)) {
        foreach ($shipping_methods as $iInfo) {

          $ShippingMethod = new WG_ShippingMethod();
          $ShippingMethod->setCarrier(htmlentities($iInfo['title'], ENT_QUOTES));


          if (is_array($iInfo['quote']['accessorials'])) {

            foreach ($iInfo['quote']['accessorials'] as $iInfo1) {

              $ShippingMethod->setMethods(htmlentities($iInfo1, ENT_QUOTES));

            }
          } else {
            $ShippingMethod->setMethods("");
          }
          $ShippingMethods->setShippingMethods($ShippingMethod->getShippingMethod());
        }
      }
      return $this->response($ShippingMethods->getShippingMethods());

    }


    #
    # function to return the store Category list so synch with QB inventory
    #
    function getCategory($username, $password)
    {
      $status = $this->auth_user($username, $password);
      if ($status != '0') {
        return $status;
      }


      //$vocabulary = taxonomy_get_vocabularies();
      $Categories = new WG_Categories();
      $Categories->setStatusCode('0');
      $Categories->setStatusMessage('All Ok');

      $tree = array();

      $cat_sql = db_query("SELECT t.tid, t.vid, t.name, parent FROM {taxonomy_term_data} t INNER JOIN {taxonomy_term_hierarchy} h ON t.tid = h.tid  ORDER BY weight, name");
      while ($cat_data = $cat_sql->fetchObject()) {
        $Category = new WG_Category();
        $Category->setCategoryID($cat_data->tid);
        $Category->setCategoryName(htmlentities($cat_data->name));
        $Category->setParentID(htmlentities($cat_data->parent));
        $Categories->setCategories($Category->getCategory());
      }

//
      unset($vocabulary, $tree);
      return $this->response($Categories->getCategories());
    }


    #
    # function to return the store tax list so synch with QB inventory
    #
    function getTaxes($username, $password)
    {
      $status = $this->auth_user($username, $password);
      if ($status != '0') {
        return $status;
      }
      $Taxes = new WG_Taxes();

      $taxes = uc_taxes_rate_load();

      $Taxes->setStatusCode('0');
      $Taxes->setStatusMessage('All Ok');
      if ($taxes) {

        foreach ($taxes as $iInfo) {
          $Tax = new WG_Tax();
          $Tax->setTaxID($iInfo->id);
          $Tax->setTaxName(htmlentities($iInfo->name, ENT_QUOTES));
          $Taxes->setTaxes($Tax->getTax());
        }
      }
      unset($taxes);


      return $this->response($Taxes->getTaxes());
    }


    # retrive all order status
    function getOrderStatus($username, $password)
    {
      $status = $this->auth_user($username, $password);
      if ($status != '0') {
        return $status;
      }

      foreach (uc_order_status_list('general') as $orderstatus1) {
        $firstset[] = $orderstatus1;
        $array = array($statusid1 => $status1);
      }
      foreach (uc_order_status_list('specific') as $orderstatus2) {
        $secondset[] = $orderstatus2;
      }

      $orderstatus = array_merge($firstset, $secondset);
      $OrderStatuses = new WG_OrderStatuses();

      if ($orderstatus) {
        $OrderStatuses->setStatusCode('0');
        $OrderStatuses->setStatusMessage('All Ok');

        foreach ($orderstatus as $iInfo) {
          $OrderStatus = new WG_OrderStatus();
          $OrderStatus->setOrderStatusID($iInfo['id']);
          $OrderStatus->setOrderStatusName(htmlentities($iInfo['title'], ENT_QUOTES));
          $OrderStatuses->setOrderStatuses($OrderStatus->getOrderStatus());

        }
      }
      unset($orderstatus1, $orderstatus2, $orderstatus);
      return $this->response($OrderStatuses->getOrderStatuses());
    }


    #
    # function to return the store item list so synch with QB inventory
    #


    function getItems($username, $password, $start_item_no = 0, $limit = 500)
    {
      global $version, $drupal_version, $files;
      global $ca_tax_setting; // from ecc config file
      $status = $this->auth_user($username, $password);
      if ($status != '0') {
        return $status;
      }
      $Items = new WG_Items();
      ### IF stock module is enabled
      if (isset($files)) {
        if ($files['uc_stock']->status == 1) {
          $stock_module_avialbe = 1;

        } else {
          $stock_module_avialbe = 0;
        }
      }


      $sql = "SELECT  COUNT(n.nid) FROM {node}  n ,{uc_products} p, {node_type} np  where n.vid = p.vid and np.type = n.type and np.module = 'uc_product'  order by n.nid desc ";
      $count_query_product = db_query($sql)->fetchField();


      if ($count_query_product > 0) {

        $sql_parent = db_query("SELECT tid, parent from {taxonomy_term_hierarchy} ");
        while ($all = $sql_parent->fetchObject()) {
          $all_parent[$all->tid] = $all->parent;
        }
        $taxes = uc_taxes_rate_load();
        if ($ca_tax_setting == true) {
          $sql_ca = db_query("SELECT value from {variable} WHERE name LIKE 'uc_tax_ca_product_types' ");
          while ($all = $sql_ca->fetchObject()) {
            $all_prod_class = unserialize($all->value);
          }
        }


        if ($taxes) {

          foreach ($taxes as $k=> $iInfo2) {
            foreach ($iInfo2->taxed_product_types as $k2=> $value) {
              $taxable_arr[] = $value;
            }
          }
        }

        $taxable_arr = array_unique($taxable_arr);

        if (count($all_prod_class) > 0) {

          foreach ($all_prod_class as $k => $var) {
            if ($var != '' && $var != '0') {
              $taxable_ca_arr[] = $var;
            }
          }

        }


        $result1 = db_query("SELECT DISTINCT n.nid, n.*,np.* FROM {node}  n ,{uc_products} p, {node_type} np  where n.vid = p.vid and np.type = n.type and np.module = 'uc_product' order by n.nid  limit $start_item_no,$limit");
        $Items->setStatusCode('0');
        $Items->setStatusMessage('All Ok');
        $Items->setTotalRecordFound($count_query_product ? $count_query_product : '0');

        while ($iInfo1 = $result1->fetchObject()) { //$iInfo1->nid = '151';
          $iInfo_arr[] = $this->menu_get_item_v7("node/" . $iInfo1->nid);
        }
        foreach ($iInfo_arr as $k=> $iInfo) {

          if ($stock_module_avialbe == 1) {
            $stock_obj = db_query("SELECT * FROM {uc_product_stock} WHERE  sku = '" . addslashes($iInfo->model) . "'")->fetchObject();
            if (!empty($stock_obj)) {

              $LowQtyLimit = $stock_obj->threshold;
              $stock = $stock_obj->stock;
              if ($stock == '')
                $stock = '0';
            }


          } else {
            $stock = '0';
          }

          $Item = new WG_Item();
          $Item->setItemID(html_entity_decode($iInfo->nid, ENT_QUOTES));

          $Item->setItemCode(html_entity_decode($iInfo->model, ENT_QUOTES));
          $Item->setItemDescription(html_entity_decode($iInfo->title, ENT_QUOTES));
          $Item->setItemShortDescr(html_entity_decode($iInfo->body['und'][0]['value'], ENT_QUOTES));


          $categoriesI = 0;
          $term_id = db_query("SELECT tid FROM {taxonomy_index} WHERE  nid = '" . $iInfo->nid . "'");
          if (!empty($term_id)) {

            while ($all = $term_id->fetchObject()) {
              $cat_id = db_query("SELECT name FROM {taxonomy_term_data} WHERE  tid = '" . $all->tid . "'");
              $cat_name = $cat_id->fetchObject();

              $catArray['CategoryId'] = $all->tid;
              $catArray['CategoryName'] = $cat_name->name;
              $catArray['ParentId'] = "";
              $Item->setCategories($catArray);
              $categoriesI++;
              unset($category);
            }
          }


          $Item->setQuantity($stock);
          $Item->setUnitPrice($iInfo->sell_price);
          $Item->setCostPrice($iInfo->cost);
          $Item->setListPrice($iInfo->list_price);
          $Item->setWeight($iInfo->weight);
          $Item->setLowQtyLimit($LowQtyLimit);
          if ($shipping > 0) {
            $Item->setFreeShipping("N");
          } else {
            $Item->setFreeShipping("Y");
          }

          $Item->setDiscounted($iInfo->discount ? $iInfo->discount : 0);
          $Item->setShippingFreight($shipping ? $shipping : 0);
          $Item->setWeight_Symbol($iInfo->weight_units);
          $Item->setWeight_Symbol_Grams('grams');
          unset($product_type);
          $product_type = $iInfo->type;
          if (in_array($product_type, $taxable_arr)) {
            $Item->setTaxExempt("N");
          } else {
            $Item->setTaxExempt("Y");
          }
          if ($ca_tax_setting == true) {
            if (in_array($product_type, $taxable_ca_arr)) {
              $Item->setTaxExempt("N");
            } else {
              $Item->setTaxExempt("Y");
            }
          }
          $itemsv_query = $iInfo->attributes;

          $var = 0;
          $Variants = new WG_Variants();
          $result2 = db_query("SELECT * FROM {uc_product_adjustments} WHERE nid = " . $iInfo->nid . " ");
          while ($obj = $result2->fetchObject()) {
            $default_model = $obj->model;
            $combination = unserialize($obj->combination);
            if ($default_model != $iInfo->model) {
              if ($stock_module_avialbe == 1) {
                $stock1_arr = db_query("SELECT sku, nid, stock FROM {uc_product_stock} WHERE  sku = '" . $default_model . " ' ");
                while ($st = $stock1_arr->fetchObject()) {
                  $stock1 = $st;
                }
              } else {
                $stock1 = 0;
              }
              $vcode = $ivInfo;
              foreach ($combination as $comb) {
                if (isset($comb) && $comb != "") {
                  $sql = db_query("SELECT nid, oid,cost,price,weight FROM {uc_product_options} WHERE nid = " . $iInfo->nid . " and oid = " . $comb . " ");
                  while ($pricesql = $sql->fetchObject()) {
                    $price += $pricesql->price;
                    $weight += $pricesql->weight;
                    $cost += $pricesql->cost;

                  }
                }
              }
              $VariantArray['ItemCode'] = $default_model;
              $VariantArray['VarientID'] = $iInfo->nid;
              $VariantArray['Quantity'] = $stock1->stock ? $stock1->stock : 0;
              $VariantArray['UnitPrice'] = $price + $iInfo->sell_price;
              $VariantArray['Weight'] = number_format($weight, 2);
              $Item->setItemVariants($VariantArray);
              $var++;
              unset($price);
              unset($weight);
              unset($cost);
            }
          }
          unset($obj);
          $op = 0;
          $Options = new WG_Options();
          if (is_array($ioInfo_new1) && count($ioInfo_new1) > 0) {
            foreach ($ioInfo_new1 as $ioInfo_new) {
              $optionArray['ItemOption']['ID'] = $ioInfo_new['oid'];
              $optionArray['ItemOption']['Value'] = $ioInfo_new['name'];
              $optionArray['ItemOption']['Name'] = $ioInfo_new['value'];
              $Item->setItemOptions($optionArray);
              $op++;
            }
          }


          $Items->setItems($Item->getItem());
        }
        unset($iInfo1, $iInfo);
      }

      return $this->response($Items->getItems());
    }


    function menu_get_item_v7($path = null, $router_item = null)
    {
      $router_items = &drupal_static(__FUNCTION__);

      if (!isset($path)) {
        $path = $_GET['q'];
      }
      if (isset($router_item)) {
        $router_items[$path] = $router_item;
      }
      if (!isset($router_items[$path])) {
        $original_map = arg(null, $path);
        // Since there is no limit to the length of $path, use a hash to keep it
        // short yet unique.
        $cid = 'menu_item:' . hash('sha256', $path);

        $parts = array_slice($original_map, 0, MENU_MAX_PARTS);
        $ancestors = menu_get_ancestors($parts);
        $router_item = db_query_range('SELECT * FROM {menu_router} WHERE path IN (:ancestors) ORDER BY fit DESC', 0, 1, array(':ancestors' => $ancestors))->fetchAssoc();
        // cache_set($cid, $router_item, 'cache_menu');
        if ($router_item) {
          // Allow modules to alter the router item before it is translated and
          // checked for access.
          drupal_alter('menu_get_item', $router_item, $path, $original_map);

          $map = _menu_translate($router_item, $original_map);

          $router_item['original_map'] = $original_map;
          if ($map === false) {

            $router_items[$path] = false;
            return false;
          }

          // if ($router_item['access']) {
          $router_item['map'] = $map;
          $router_item['page_arguments'] = array_merge(menu_unserialize($router_item['page_arguments'], $map), array_slice($map, $router_item['number_parts']));
          //}
        }
        $router_items[$path] = $router_item;

      }
      // return $router_items[$path];
      return $router_items[$path]['page_arguments'][0];
    }

    # Functions to Sync the Items and the Varients with the QB
    function synchronizeItems($username, $password, $data, $storeid, $others)
    {

      global $version, $drupal_version, $files;
      $status = $this->auth_user($username, $password);
      if ($status != '0') {
        return $status;
      }
      $Items = new WG_Items();
      $Items->setStatusCode('0');
      $Items->setStatusMessage('All Ok');
      if (isset($files)) {
        if ($files['uc_stock']->status == '1') {
          $stock_module_avialbe = '1';

        } else {
          $stock_module_avialbe = '0';
        }
      }
      //$requestArray=json_decode($data, true);
      $requestArray = $data;

      $pos = strpos($others, '/');
      if ($pos) {
        $array_others = explode("/", $others);

      } else {

        $array_others = array();
        $array_others[] = $others;
      }

      if (!is_array($requestArray)) {
        $Items->setStatusCode('9997');
        $Items->setStatusMessage('Unknown request or request not in proper format');
        return $this->response($Items->getItems());
      }

      if (count($requestArray) == 0) {
        $Items->setStatusCode('9996');
        $Items->setStatusMessage('REQUEST array(s) doesnt have correct input format');
        return $this->response($Items->getItems());
      }
      $itemsProcessed = 0;

      #Go throught items
      $itemsCount = 0;
      $_err_message_arr = Array();
      $i = 0;

      foreach ($requestArray as $k=> $v4) {

        $Item = new WG_Item();
        $productID = $v4['ProductID'];
        $sku = $v4['Sku'];
        $productName = $v4['ProductName'];
        $qty = $v4['Qty'];
        $price = $v4['Price'];
        $cprice = $v4['Cost'];
        $updated_attrib = 0;

        foreach ($array_others as $ot) {

          foreach ($v4['ItemVariants'] as $key1=> $value1) {
            //					$status="Success";
            $vsku = $value1['Sku'];
            $varient_id = $value1['VarientID'];
            $varient_qty = $value1['Quantity'];
            $varient_price = $value1['UnitPrice'];

            if ($others == "QTY" || $others == "BOTH" || $ot == "QTY") {
              if ($stock_module_avialbe == '1') {
                $sql = "SELECT COUNT(nid) from {uc_product_stock} where sku =" . $this->mySQLSafe($vsku) . "";
                $row = db_query($sql)->fetchField();

                if ($row > 0) {
                  db_update('uc_product_stock')
                    ->fields(array('stock'=> $varient_qty))
                    ->condition('sku', $vsku)
                    ->execute();

                  $status = "Success";
                } else {
                  $status = "Stock for this product not found";
                }
              } else {
                $status = "Stock module is disabled";
              }

            }
            if ($others == "PRICE" || $others == "BOTH" || $ot == "PRICE") {
              $status = "Syncronization of varient's price is not supoprted by this version";
            }

            if ($others == "COST" || $others == "BOTH" || $ot == "COST") {
              $status = "Syncronization of varient's cost price is not supoprted by this version";
            }
            $updated_attrib++;
            if (isset($varient_id) && $varient_id != "") {
              $Variant = new WG_Variant();
              $Variant->setStatus('Success');
              $Variant->setVarientID($varient_id);
              $Variant->setVariantSku($vsku);
              $Item->setItemVariants($Variant->getVariant());

              $Item->setStatus('Success');
              $Item->setProductID($v4['ProductID']);
              $Item->setSku($v4['Sku']);
              $Items->setItems($Item->getItem());

            }

          }


          if ($updated_attrib == 0) {

            $data = db_query("SELECT COUNT(nid) FROM {node} WHERE nid=" . $this->mySQLSafe($productID))->fetchField();
            if ($data > 0) {

              if ($others == "QTY" || $others == "BOTH" || $ot == "QTY") {
                if ($stock_module_avialbe == '1') {
                  $row = db_query("SELECT COUNT(nid) from {uc_product_stock} where  sku =" . $this->mySQLSafe($sku) . " ")->fetchField();
                  if ($row > 0) {
                    db_update('uc_product_stock')
                      ->fields(array('stock'=> $qty))
                      ->condition('sku', $sku)
                      ->execute();
                    $status = "Success";
                  } else {
                    db_insert('uc_product_stock')
                      ->fields(array('sku' => $sku, 'nid' => $productID, 'active'=> 1, 'stock'=> $qty, 'threshold'=> 0))
                      ->execute();
                    $status = "Success";
                  }
                } else {
                  $status = "Stock module is disabled";
                }
              }
              if ($others == "PRICE" || $others == "BOTH" || $ot == "PRICE") {
                $row = db_query("SELECT COUNT(nid) from {uc_products} where  model =" . $this->mySQLSafe($sku) . " ")->fetchField();

                if ($row > 0) {
                  db_update('uc_products')
                    ->fields(array('sell_price'=> $price))
                    ->condition('model', $sku)
                    ->execute();
                  $status = "Success";
                } else {
                  $status = "Price for this product not found";
                }
              }
              if ($others == "COST" || $others == "BOTH" || $ot == "COST") {
                $row = db_query("SELECT COUNT(nid) from {uc_products} where  model =" . $this->mySQLSafe($sku) . " ")->fetchField();

                if ($row > 0) {
                  db_update('uc_products')
                    ->fields(array('cost'=> $cprice))
                    ->condition('model', $sku)
                    ->execute();
                  $status = "Success";
                } else {
                  $status = "Cost Price for this product not found";
                }
              }
              $itemsProcessed++;
            } else
              $status = "Product ID not found";


          } else if ($updated_attrib == $k1 + 1) {
            $itemsProcessed++;
          }
          $itemsProcessed++;

          //$xmlResponse->createTag("Options", array(),'', $item);

        }
        $Item->setStatus('Success');
        $Item->setProductID($v4['ProductID']);
        $Item->setSku($v4['Sku']);
        $Items->setItems($Item->getItem());


      }

      return $this->response($Items->getItems());
    }

    # Return the Count of the orders remained with specific dates and status
    function getOrdersRemained($start_date, $start_order_no, $time_from)
    {
      global $version;
      $previous_orders = 0;

      if ($version == "6.x-2.x-dev") {
        $sql = "SELECT COUNT(*) FROM {uc_orders} o LEFT JOIN {uc_order_statuses} os ON o.order_status = os.order_status_id WHERE o.created >= '" . $start_date . "' AND o.order_id > " . $start_order_no . " and o.order_status IN (" . QB_ORDERS_DOWNLOAD_EXCL_LIST . ") ORDER BY o.order_id  ";
        $previous_orders = db_result(db_query($sql));
      } else {
        if ($time_from) {
          $sql = db_query("SELECT COUNT(*) FROM {uc_orders} o LEFT JOIN {uc_order_statuses} os ON o.order_status = os.order_status_id WHERE o.modified > '" . $time_from . "' and o.order_status IN (" . QB_ORDERS_DOWNLOAD_EXCL_LIST . ") ORDER BY o.order_id  ")->fetchField();
        } else {
          $sql = db_query("SELECT COUNT(*) FROM {uc_orders} o LEFT JOIN {uc_order_statuses} os ON o.order_status = os.order_status_id WHERE o.created >= '" . $start_date . "' AND o.order_id > " . $start_order_no . " and o.order_status IN (" . QB_ORDERS_DOWNLOAD_EXCL_LIST . ") ORDER BY o.order_id  ")->fetchField();

        }
        return $sql;

      }

      return $previous_orders;
    }

    # Add ordershipments

    function addOrderShipment($username, $password, $data, $storeid, $others)
    {

      global $version, $drupal_version;
      //$shipmentorders['Orders'] = array(array('OrderId'=>1,'OrderNO'=>''),array('OrderId'=>2,'OrderNO'=>''));

      $status = $this->auth_user($username, $password);

      if ($status != '0') {
        return $status;
      }

      $requestArray = $data;
      $Orders = new WG_OrdersShipment();
      if (!is_array($requestArray)) {
        $Orders->setStatusCode('9997');
        $Orders->setStatusMessage('Unknown request or request not in proper format');
        return $this->response($Items->getItems());
      } else if (count($requestArray) == 0) {
        $Orders->setStatusCode('9996');
        $Orders->setStatusMessage('REQUEST tag(s) doesnt have correct input format');
        return $this->response($Items->getItems());
      } else {
        $Orders->setStatusCode('0');
        $Orders->setStatusMessage('All Ok');
      }


      foreach ($requestArray as $orders) {

        foreach ($orders as $order) {
          $order_id = $order['OrderId'];
          $order_no = $order['OrderNo'];

          $Order = new WG_OrderShipment();
          $Order->setOrderId($order['OrderId']);
          $Order->setOrderNo($order['OrderNo']);

          foreach ($order['Shipments'] as $shipment) {

            $tracking_num = $shipment['TrackingNumber'];
            $method = $shipment['Method'];
            $carrier = $shipment['Carrier'];
            $ship_id = $shipment['ShipmentID'];
            $product_new = array();


            foreach ($shipment['Items'] as $item) {

              $item_qty_shipped = $item['ItemQtyShipped'];
              $item_name = $item['ItemName'];
              $item_sku = $item['ItemSku'];
              $item_id = $item['ItemID'];

              /* shipment check before creation */

              $shipping_types_products = array();
              $order_details = uc_order_load($order['OrderNo']);

              foreach ($order_details->products as $product) {
                if ($product->data['shippable']) {
                  $product->shipping_type = uc_product_get_shipping_type($product);
                  $shipping_types_products[$product->shipping_type][] = $product;
                }
              }

              $shipping_type_weights = variable_get('uc_quote_type_weight', array());

              $result = db_query("SELECT op.order_product_id, SUM(pp.qty) AS quantity FROM {uc_packaged_products} AS pp LEFT JOIN {uc_packages} AS p ON pp.package_id = p.package_id LEFT JOIN {uc_order_products} AS op ON op.order_product_id = pp.order_product_id WHERE p.order_id = :id GROUP BY op.order_product_id", array(':id' => $order['OrderNo']));
              $packaged_products = $result->fetchAllKeyed();

              $shipping_type_options = uc_quote_shipping_type_options();
              foreach ($shipping_types_products as $shipping_type => $products) {

                foreach ($products as $product) {
                  $unboxed_qty = $product->qty;

                  if (isset($packaged_products[$product->order_product_id])) {
                    $unboxed_qty -= $packaged_products[$product->order_product_id];
                  }
                  if ($product->order_product_id == $item_id) {
                    if ($unboxed_qty > 0 && $item['ItemQtyShipped'] == $unboxed_qty) {
                      $product_new[$item_id] = (object)array("checked"=> 1, "qty"=> $item_qty_shipped);
                    }
                  }
                }
              }

              //$product[]["qty"]=$item_qty_shipped;

            }

            if (count($product_new) > 0) {
              $package = (object)array("products"=> $product_new, "shipping_type"=> 'small_package', "order_id"=> $order_no, "data"=> array("attributes" => array("shippable"=> 1, "type"=> "product", "module"=> "uc_product")));
              $order_obj = uc_order_load($order_no, $reset = false);
              $address = explode("<BR />", uc_order_address($order_obj, "billing"));

              $first_name = explode(" ", $address[1]);
              $city_name = explode(",", $address[4]);
              $postal_code = explode(" ", $city_name[1]);

              $dest = (object)array("email"=> "", "first_name"=> $first_name[0], "last_name"=> $first_name[1], "company"=> $address[0], "street1"=> $address[2], "street2"=> $address[3], "city"=> $city_name[0], "postal_code"=> $postal_code[2]);

              uc_shipping_package_save($package);
              $yourdatetime = time();

              $shipment_obj = (object)array("order_id"=> $order_no, "destination"=> $dest, "packages"=> array($package), "shipping_method"=> $method, "accessorials"=> $method, "carrier"=> $carrier, "tracking_number"=> $tracking_num, "ship_date"=> $yourdatetime, "expected_delivery"=> $yourdatetime);
              uc_shipping_shipment_save($shipment_obj);

              $Shipment = new WG_Shipment();
              $Shipment->setShipmentID(htmlentities($shipment['ShipmentID'], ENT_QUOTES));
              $Shipment->setStatus(htmlentities("Success", ENT_QUOTES));
              $Order->setShipments($Shipment->getShipment());
            } else {
              $Shipment = new WG_Shipment();
              $Shipment->setShipmentID(htmlentities($shipment['ShipmentID'], ENT_QUOTES));
              $Shipment->setStatus(htmlentities("Order cannot be shipped.Either its shipment is already created or there is other problem. Please review manually.", ENT_QUOTES));
              $Order->setShipments($Shipment->getShipment());

            }
            /*$order['IsNotifyCustomer']='true';
					if ($order['IsNotifyCustomer']=='true')
					{
						$order_data = uc_order_load($order['OrderNo']);
						global $base_url;
						$pos = strpos($base_url,'sites');
						if($pos!="")
						{
							$base_url =str_replace("/sites/all/modules/eccv3","",$base_url);
						}
						elseif(strpos($base_url,'modules'))
						{
							 $base_url =str_replace("/modules/eccv3","",$base_url);
						}

						if($drupal_version > "6")
						{
							rules_invoke_event('uc_order_status_email_update', $order_data);
						}
						else
						{
							if($drupal_version != '5')
							{
								ca_pull_trigger('uc_order_status_email_update', $order_data);
							}
						}

					}*/
          }
        }


      }

      $Orders->setOrder($Order->getShipments());


      return $this->response($Orders->getOrdersShipment());
    }

    # Return the shipments for a order

    function getShipment($username, $password, $shipmentorders, $others)
    {

      global $version, $drupal_version;
      $shipmentorders['Orders'] = array(array('OrderId'=> 1, 'OrderNO'=> ''), array('OrderId'=> 2, 'OrderNO'=> ''));

      $status = $this->auth_user($username, $password);

      if ($status != '0') {
        return $status;
      }

      $Orders = new WG_OrdersShipment();
      $Orders->setStatusCode("0");
      $Orders->setStatusMessage("Success");

      /*foreach($shipmentorders['Orders'] as $order)
		{*/
      $obj = (object)array("order_id"=> "13");

      $Order = new WG_OrderShipment();
      $Order->setOrderId($order['OrderId']);
      $Order->setOrderNo($order['OrderId']);
      $shipments = uc_shipping_order_shipments($obj);

      foreach ($shipments['shipments']['#rows'] as $shipment) {

        $single_shipment = uc_shipping_shipment_load($shipment[0]);

        $Shipment = new WG_Shipment();
        $Shipment->setShipmentID(htmlentities($single_shipment->sid, ENT_QUOTES));
        $Shipment->setCarrier($single_shipment->carrier);
        $Shipment->setMethod($single_shipment->shipping_method);
        $Shipment->setTrackingNumber($single_shipment->tracking_number);

        foreach ($single_shipment->packages as $items) {
          foreach ($items->products as $product) {
            $Item = new WG_Item();
            $Item->setItemCode(html_entity_decode($product->order_product_id, ENT_QUOTES));
            $Item->setSku(html_entity_decode($product->order_product_id, ENT_QUOTES));
            $Item->setProductName(html_entity_decode($product->title, ENT_QUOTES));
            $Item->setQuantity(html_entity_decode($product->qty, ENT_QUOTES));
            $Shipment->setShipmentItems($Item->getItem());
          }
        }

        $Order->setShipments($Shipment->getShipment());
      }
      $Orders->setOrder($Order->getShipments());


      return $this->response($Orders->getOrdersShipment());

    }

    # Return the Orders to sync with the QB according to the date and the staus and order id.
    function getOrders($username, $password, $datefrom, $start_order_no, $ecc_excl_list, $order_per_response = 25, $LastModifiedDate, $storeid, $others, $cvvdetails)
    {
      global $version, $drupal_version;
      global $ca_tax_setting; // from ecc config
      $orderlist = '';

      /*foreach($others as $k=>$v)
		{
		$orderlist = $orderlist?($orderlist.",'".$v['OrderId']."'"):"'".$v['OrderId']."'";
		}*/

      if (is_array($others))
        foreach ($others as $k=> $v) {
          $orderlist[] = $v['OrderId'];
        }
      $status = $this->auth_user($username, $password);

      if ($status != '0') {
        return $status;
      }
      $currency = $store_name = variable_get('uc_currency_code', null);
      if (!isset($datefrom) or empty($datefrom)) {
        $datefrom = date('m-d-Y');
      }

      list($mm, $dd, $yy) = explode("-", $datefrom);
      $time_from = mktime(0, 0, 0, $mm, $dd, $yy);

      #fetch Id of Order Status
      foreach (uc_order_status_list('general') as $orderstatus1) {
        $firstset[] = $orderstatus1;
        $array = array($statusid1 => $status1);
      }
      foreach (uc_order_status_list('specific') as $orderstatus2) {
        $secondset[] = $orderstatus2;
      }
      $orderstatus = array_merge($firstset, $secondset);
      $ecc_excl_list = str_replace("'", "", trim($ecc_excl_list));

      $ecc_list = explode(",", $ecc_excl_list);

      $list = array();

      foreach ($orderstatus as $storeOrder) {
        foreach ($ecc_list as $status_list) {

          if (trim($status_list) == trim($storeOrder['title'])) {

            $list[$storeOrder['title']] = "'" . $storeOrder['id'] . "'";
          }

        }
      }

      if ($orderlist != '') {
        foreach ($orderstatus as $storeOrder) {
          $list[$storeOrder['title']] = "'" . $storeOrder['id'] . "'";
        }
      }

      unset($orderstatus, $storeOrder, $orderstatus1, $orderstatus2, $uc_order_status_list);
      $ecc_orderstatus_list = implode(",", $list);

      #check for authorisation

      //$this->auth_user($username,$password);
      //die("hhh");

      define("QB_ORDERS_DOWNLOAD_EXCL_LIST", $ecc_orderstatus_list);
      define("QB_ORDERS_PER_RESPONSE", $order_per_response);
      if ($orderlist == "") {
        $time_from = "";
        if ($LastModifiedDate != "") {
          //$LastModifiedDate="08-11-2012 15:28:37";

          $ldate = explode(" ", $LastModifiedDate);
          list($mm, $dd, $yy) = explode("-", $ldate[0]);
          list($h, $m, $s) = explode(":", $ldate[1]);
          //$time_from = mktime(0,0,0,$mm,$dd,$yy);
          $time_from = mktime($h, $m, $s, $mm, $dd, $yy);
        }

        $orders_remained = $this->getOrdersRemained($start_date, $start_order_no, $time_from);
      } else {
        $orders_remained = count($orderlist);

        $no_orders = false;


        if ($orders_remained < 1) {
          $no_orders = true;
        }

      }
      $orders_remained = $orders_remained > 0 ? $orders_remained : 0;

      $orders = array();
      $Orders = new WG_Orders();


      if ($orderlist != '') {

        foreach ($orderlist as $k=> $v) {
          //$orderlist_str = $orderlist?($orderlist_str.",'".$v."'"):"'".$v."'";
          if ($orderlist_str == "") {
            $orderlist_str = "'" . $v . "'";
          } else {
            $orderlist_str = $orderlist_str . ",'" . $v . "'";
          }
        }

        $sql = "SELECT o.order_id,o.data,o.modified, o.uid, o.billing_first_name, o.billing_last_name, o.order_total, o.order_status, o.created, os.title FROM {uc_orders} o LEFT JOIN {uc_order_statuses} os ON o.order_status = os.order_status_id WHERE o.order_id IN (" . $orderlist_str . ") " . $query_for_update . " ORDER BY o.order_id ASC";

      } else {
        if ($LastModifiedDate != "") {
          //$LastModifiedDate="08-11-2012 15:28:37";

          $ldate = explode(" ", $LastModifiedDate);
          list($mm, $dd, $yy) = explode("-", $ldate[0]);
          list($h, $m, $s) = explode(":", $ldate[1]);
          //$time_from = mktime(0,0,0,$mm,$dd,$yy);
          $time_from = mktime($h, $m, $s, $mm, $dd, $yy);

          $sql = "SELECT o.order_id,o.data,o.modified, o.uid, o.billing_first_name, o.billing_last_name, o.order_total, o.order_status, o.created, os.title FROM {uc_orders} o LEFT JOIN {uc_order_statuses} os ON o.order_status = os.order_status_id WHERE o.modified > '" . $time_from . "' and o.order_status IN (" . QB_ORDERS_DOWNLOAD_EXCL_LIST . ") " . $query_for_update . " ORDER BY o.modified ASC  " . (QB_ORDERS_PER_RESPONSE > 0 ? "LIMIT 0, " . QB_ORDERS_PER_RESPONSE : '');

        } else {

          $sql = "SELECT o.order_id,o.data,o.modified, o.uid, o.billing_first_name, o.billing_last_name, o.order_total, o.order_status, o.created, os.title FROM {uc_orders} o LEFT JOIN {uc_order_statuses} os ON o.order_status = os.order_status_id WHERE o.created >= '" . $start_date . "' and o.order_id  > " . $start_order_no . " and o.order_status IN (" . QB_ORDERS_DOWNLOAD_EXCL_LIST . ") " . $query_for_update . " ORDER BY o.order_id ASC  " . (QB_ORDERS_PER_RESPONSE > 0 ? "LIMIT 0, " . QB_ORDERS_PER_RESPONSE : '');

        }

      }


      $result = db_query($sql);

      while ($test = $result->fetchObject()) {
        $orders1[] = $test;

        foreach ($orders1 as $member=> $data) {
          $orders[$member] = $data;
        }
      }


      $no_orders = count($orders);

      if ($orderlist != '') {
        $orders_remained = $no_orders > 0 ? $no_orders : 0;
      }

      if ($no_orders <= 0) {
        $no_orders = true;
        $Orders->setStatusCode($no_orders ? "9999" : "0");
        $Orders->setStatusMessage($no_orders ? "No Orders returned" : "Total Orders:" . $orders_remained);
        //return $xmlResponse->generate();
        return $this->response($Orders->getOrders());
      }
      #Fetch Zone code

      $zone_sql = db_query("select zone_id,zone_code from {uc_zones}");
      while ($zone = $zone_sql->fetchObject()) {
        $zone_code[$zone->zone_id] = $zone->zone_code;
      }


      #Fetch Country name

      $country_sql = db_query("select country_id, country_name from {uc_countries}");
      while ($country = $country_sql->fetchObject()) {
        $country_name[$country->country_id] = $country->country_name;
      }

      $taxes = uc_taxes_rate_load();

      if ($taxes) {

        foreach ($taxes as $k=> $iInfo2) {
          foreach ($iInfo2->taxed_product_types as $k2=> $value) {
            $taxable_arr[] = $value;
          }
        }
      }

      $taxable_arr = array_unique($taxable_arr);

      if ($ca_tax_setting == true) {
        $sql_ca = db_query("SELECT value from {variable} WHERE name LIKE 'uc_tax_ca_product_types' ");
        while ($all = $sql_ca->fetchObject()) {
          $all_prod_class = unserialize($all->value);
        }


        foreach ($all_prod_class as $k => $var) {
          if ($var != '' && $var != '0') {
            $taxable_ca_arr[] = $var;
          }
        }

      }

      if ($orders) {

        $Orders->setStatusCode(0);
        $Orders->setStatusMessage("Total Orders:" . $orders_remained);

        $store_name = variable_get('uc_store_name', null);

        foreach ($orders as $order_data) {

          unset($order_details);
          unset($order);
          unset($order_details->line_items);
          unset($order_details->payment_method);
          unset($order_details->products);
          $order_details = uc_order_load($order_data->order_id);

          $weightsymbol = 'lbs';
          $weight_symbol_grams = '453.6';

          $total = $order_details->order_total;

          unset($totaltax, $totalship);
          foreach ($order_details->line_items as $taxes) {
            if ($taxes['type'] == 'tax') {
              $totaltax += $taxes['amount'];
            }

          }
          $ship_method = "";
          foreach ($order_details->line_items as $shipping) {
            if ($shipping['type'] == 'shipping') {
              $totalship += $shipping['amount'];
              $ship_method = $ship_method . $shipping['title'];
            }
          }

          $extra = unserialize($order['extra']);

          $time = strtotime($order_data->created);


          $Order = new WG_Order();
          $orderid = $order_data->order_id;
          $Order->setOrderId($order_data->order_id);
          $Order->setTitle('');
          $Order->setFirstName(htmlentities($order_data->billing_first_name, ENT_QUOTES));
          $Order->setLastName(htmlentities($order_data->billing_last_name, ENT_QUOTES));
          $Order->setDate(date("m-d-Y", $order_data->created));
          $Order->setLastModifiedDate(date("m-d-Y H:i:s", $order_data->modified));

          $Order->setTime(format_date($order_data->created, 'custom', 'H:i:s'));
          $Order->setStoreID(htmlentities($store_name, ENT_QUOTES));
          $Order->setStoreName(htmlentities($store_name, ENT_QUOTES));
          $Order->setCurrency($currency ? $currency : '');
          $Order->setWeight_Symbol($weightsymbol);
          $Order->setWeight_Symbol_Grams($weight_symbol_grams);

          $Order->setStatus(htmlentities(array_search("'" . $order_details->order_status . "'", $list), ENT_QUOTES));

          unset($admin_comments);
          $result = db_query("select comment_id, message from {uc_order_admin_comments} oa where oa.order_id =" . $orderid . " ORDER BY oa.comment_id");
          if ($drupal_version > "6") {
            //while ($ad_comment = db_fetch_array($result))
            while ($ad_comment = $result->fetchObject()) {
              $admin_comments = $ad_comment->message;
            }
          } else {
            while ($ad_comment = db_fetch_array($result)) {
              $admin_comments = $ad_comment['message'];
            }
          }

          $Order->setNotes(htmlspecialchars(strip_tags($admin_comments)));

          $Order->setFax('');
          $comments = db_query("select message from {uc_order_comments} oc where oc.order_id =" . $orderid . " ORDER BY oc.comment_id  ")->fetchField();


          //$Order->setComment('');
          $Order->setComment(htmlentities($comments ? $comments : "", ENT_QUOTES));
          # Orders/Bill info
          $payment_method = $order_details->payment_method;

          $payresult = db_query("SELECT * FROM {uc_payment_receipts} where order_id ='" . $orderid . "'");
          while ($test1 = $payresult->fetchObject()) {
            $payinfo = $test1;
          }

          if (!empty($payinfo)) {
            $payment_method = $payinfo->method;
          }


          $billing_first_name = htmlentities($order_details->billing_first_name, ENT_QUOTES);
          $billing_last_name = htmlentities($order_details->billing_last_name, ENT_QUOTES);
          $billing_company = htmlentities($order_details->billing_company, ENT_QUOTES);
          $billing_street1 = htmlentities($order_details->billing_street1, ENT_QUOTES);
          $billing_street2 = htmlentities($order_details->billing_street2, ENT_QUOTES);
          $billing_city = htmlentities($order_details->billing_city, ENT_QUOTES);
          $billing_postal_code = htmlentities($order_details->billing_postal_code, ENT_QUOTES);
          $primary_email = htmlentities($order_details->primary_email, ENT_QUOTES);
          $billing_phone = htmlentities($order_details->billing_phone, ENT_QUOTES);
          $zone_id = htmlentities($order_details->billing_zone, ENT_QUOTES);
          $country_id = htmlentities($order_details->billing_country, ENT_QUOTES);

     	  $abc = unserialize($order_data->data);


          $key = uc_credit_encryption_key();

          if (class_exists(UbercartEncryption)) {
            $crypt = new UbercartEncryption;
          } else if (class_exists(uc_encryption_class)) {
            $crypt = new uc_encryption_class;
          }

          $transaction_id = '';
          if (isset($order_details->payment_details['po_number']) && $order_details->payment_details['po_number'] != "") {

            $ponumber = $order_details->payment_details['po_number'];
          } else {
            if ($version < '7.x-3.0-rc3') {
              $order_details->payment_details = unserialize($crypt->decrypt($key, $abc['cc_data']));
            } else {
              $cc_data_test = ($crypt->decrypt($key, $abc['cc_data']));
              if (strpos($cc_data_test, ':') === false) {
                $order_details->payment_details = unserialize(base64_decode($cc_data_test));
              } else {
                $order_details->payment_details = unserialize($cc_data_test);
              }
            }
          }

          $Bill = new WG_Bill();
          $CreditCard = new WG_CreditCard();
          if (is_array($order_details->payment_details)) { 
            $card_type = $order_details->payment_details['cc_type'];
            $card_no = $order_details->payment_details['cc_number'];
            $card_owner = $order_details->payment_details['cc_owner'];
            $card_cvv = $order_details->payment_details['cc_cvv'];


            if (!empty($order_details->payment_details['cc_exp_month']) || !empty($order_details->payment_details['cc_exp_year'])) {
              $expiration_date = ($order_details->payment_details['cc_exp_month'] . "/" . $order_details->payment_details['cc_exp_year']);
            } else {
              $expiration_date = "";
            }

            if (is_array($abc['cc_txns'])) {
              if (is_array($abc['cc_txns']['authorizations'])) {
                $transaction_id = array_keys($abc['cc_txns']['authorizations']);
                $transaction_id = $transaction_id[0];
              } else {
                $transaction_id = '';
              }
            } else {
              $transaction_id = '';
            }

            if ($cvvdetails != 'DONOTSEND') {
              # Credit card
              $CreditCard->setCreditCardType($card_type);
              $CreditCard->setCreditCardCharge('');
              $CreditCard->setExpirationDate($expiration_date);
              $CreditCard->setCreditCardName($card_owner);
              $CreditCard->setCreditCardNumber($card_no);
              $CreditCard->setCVV2($card_cvv);
              $CreditCard->setAdvanceInfo('');
            }

            if ('' == $transaction_id) {
			  //$version == '7.x-3.4' CONDITION ADDED FOR THIS STORE ON 9-SEP-2013
              if ($version < '7.x-3.0-rc3' || $version == '7.x-3.4') {
                $payinfo->data = unserialize($payinfo->data);
				$CreditCard->setTransactionId($payinfo->data['txn_id']);
              } else { 
                $CreditCard->setTransactionId($payinfo->data);
              }

            } else { 
              $CreditCard->setTransactionId($transaction_id);
            }
          } else { 
            if ('' == $transaction_id) {
              if ($drupal_version > "6") {
                ## Table {uc_payment_paypal_ipn} is not exist in versions
              } else { 
                $result = db_query("SELECT txn_id FROM {uc_payment_paypal_ipn} where order_id ='" . $orderid . "'");
                $shipment = db_fetch_object($result);
              }

              $CreditCard->getCreditCard();
              $CreditCard->setTransactionId($shipment->txn_id);

            }
          }

          $CreditCard->getCreditCard();
          $Bill->setCreditCardInfo($CreditCard->getCreditCard());
          unset($card_type, $expiration_date, $card_no, $card_owner, $card_cvv, $transaction_id);
          #Bill
          $Bill->setPayMethod(htmlentities($payment_method, ENT_QUOTES));
          //$Bill->setPayStatus('');
          $Bill->setTitle('');
          $Bill->setFirstName($billing_first_name);
          $Bill->setLastName($billing_last_name);
          $Bill->setCompanyName($billing_company);
          $Bill->setAddress1($billing_street1);
          $Bill->setAddress2($billing_street2);
          $Bill->setCity($billing_city);
          $Bill->setState($zone_code[$zone_id]);
          $Bill->setZip($billing_postal_code);
          $Bill->setCountry($country_name[$country_id]);
          $Bill->setEmail($primary_email);
          $Bill->setPhone($billing_phone);
          $Bill->setPONumber($ponumber ? $ponumber : "");
          $Order->setOrderBillInfo($Bill->getBill());

          # Order Shipping Info
          $shipping_first_name = htmlentities($order_details->delivery_first_name, ENT_QUOTES);
          $shipping_last_name = htmlentities($order_details->delivery_last_name, ENT_QUOTES);
          $shipping_company = htmlentities($order_details->delivery_company, ENT_QUOTES);
          $shipping_street1 = htmlentities($order_details->delivery_street1, ENT_QUOTES);
          $shipping_street2 = htmlentities($order_details->delivery_street2, ENT_QUOTES);
          $shipping_city = htmlentities($order_details->delivery_city, ENT_QUOTES);
          $shipping_postal_code = htmlentities($order_details->delivery_postal_code, ENT_QUOTES);
          $shipping_phone = htmlentities($order_details->delivery_phone, ENT_QUOTES);
          $zone_id = htmlentities($order_details->delivery_zone, ENT_QUOTES);
          $country_id = htmlentities($order_details->delivery_country, ENT_QUOTES);

          // Retrieve Carrier's Title using id (from associative array)
          /*$All_shipping_carrier = module_invoke_all('uc_shipping_method');

				if(is_array($All_shipping_carrier))
				{
					foreach($All_shipping_carrier as $key1 => $all_carrier)
					{

						$shipping_name_key[] =  $key1."-".$all_carrier['title'];
					}
				}
				$carrier = "";
				$carrierid = $order_details->quote['method'];
				foreach ($shipping_name_key as $methods_carrier)
				{
					$methods_carrier_array = explode('-',$methods_carrier);
					if($methods_carrier_array[0] == $carrierid)
					{
						$carrier[] = $methods_carrier_array[1];
					}
				}*/

          $result = db_query("SELECT * FROM {uc_shipments} WHERE order_id = " . $orderid);
          while ($test1 = $result->fetchObject()) {
            $shipment_test = $test1;
          }
          //

          if ($shipment_test) {
            $shipment_id = $shipment_test->sid;
            $shipment_details = uc_shipping_shipment_load($shipment_id);
            $ship_method = $shipment_details->shipping_method;
            $ship_carrier = $shipment_details->carrier;
          } else {
            // Retrieve Carrier's Title using id (from associative array)
            $All_shipping_carrier = module_invoke_all('uc_shipping_method');

            if (is_array($All_shipping_carrier)) {
              foreach ($All_shipping_carrier as $key1 => $all_carrier) {

                $shipping_name_key[] = $key1 . "-" . $all_carrier['title'];
              }
            }
            $carrier = "";
            $carrierid = $order_details->quote['method'];
            foreach ($shipping_name_key as $methods_carrier) {
              $methods_carrier_array = explode('-', $methods_carrier);
              if ($methods_carrier_array[0] == $carrierid) {
                $carrier[] = $methods_carrier_array[1];
              }
            }

            $ship_method = $ship_method;
            $ship_carrier = $carrier[0];

          }
          $Ship = new WG_Ship();
          $Ship->setShipMethod(htmlentities($ship_method, ENT_QUOTES));
          $Ship->setCarrier($ship_carrier);


          if ($shipment_details->tracking_number) {
            $tracking_number = $shipment_details->tracking_number;
          } else {
            $tracking_number = '';
          }

          #Ship Node
          $Ship->setTrackingNumber($tracking_number);
          $Ship->setTitle('');
          $Ship->setFirstName($shipping_first_name);
          $Ship->setLastName($shipping_last_name);
          $Ship->setCompanyName($shipping_company);

          $Ship->setAddress1($shipping_street1);
          $Ship->setAddress2($shipping_street2);
          $Ship->setCity($shipping_city);
          $Ship->setState($zone_code[$zone_id]);
          $Ship->setZip($shipping_postal_code);
          $Ship->setCountry($country_name[$country_id]);
          $Ship->setEmail('');
          $Ship->setPhone($shipping_phone);
          $Order->setOrderShipInfo($Ship->getShip());

          # get items of order

          foreach ($order_details->products as $product_info) {

            $Item = new WG_Item();
            $order_product_id = $product_info->order_product_id;
            $item_code = $product_info->model;
            $item_name = $product_info->title;
            $qty = $product_info->qty;
            $unitprice = $product_info->price;
            $weight = $product_info->weight;
            $cost = $product_info->cost;
            //$description = db_fetch_array(db_query($sql));
            //$desc=$description['body'];
            if ($qty == '')
              $qty = 0;

            $result = db_query("SELECT length,width,height,length_units FROM {uc_products} WHERE nid = " . $product_info->nid);
            while ($test1 = $result->fetchObject()) {
              $dimensions = $test1;
            }


            $Dimention['Length'] = (float)$dimensions->length;
            $Dimention['Width'] = (float)$dimensions->width;
            $Dimention['Height'] = (float)$dimensions->height;
            $Dimention['Unit'] = $dimensions->length_units ? $dimensions->length_units : "";
            $Item->setDimention($Dimention);
            # Itme node
            $Item->setItemID(html_entity_decode($order_product_id, ENT_QUOTES));
            $Item->setItemCode(html_entity_decode($item_code, ENT_QUOTES));
            $Item->setItemDescription(html_entity_decode($item_name, ENT_QUOTES));
            $Item->setItemShortDescr(html_entity_decode($desc, ENT_QUOTES));
            $Item->setQuantity($qty);

            //$Item->setCostPrice($cost);
            $cost = (float)$cost;
            $Item->setListPrice($cost);
            $Item->setCostPrice($cost);
            $unitprice = (float)$unitprice;
            $Item->setUnitPrice($unitprice);
            $weight = (float)$weight;
            $Item->setWeight($weight);
            $Item->setFreeShipping($data->free_shipping);
            $iInfo[$data->discount_avail] = (float)$iInfo[$data->discount_avail];
            $Item->setDiscounted($iInfo[$data->discount_avail]);
            $Item->setshippingFreight($data->shipping_freight);

            $Item->setWeight_Symbol($weightsymbol);
            $Item->setWeight_Symbol_Grams($weight_symbol_grams);
            unset($product_type);
            $Qresult1 = db_query("SELECT np.* FROM {node}  n ,{uc_products} p, {node_type} np  where n.vid = p.vid and np.type = n.type and np.module = 'uc_product' and n.nid = " . $product_info->nid . "");
            while ($QiInfo1 = $Qresult1->fetchObject()) {
              $product_type = $QiInfo1->type;
            }


            if (in_array($product_type, $taxable_arr)) {
              $Item->setTaxExempt("N");
            } else {
              $Item->setTaxExempt("Y");
            }
            if ($ca_tax_setting == true) {
              if (in_array($product_type, $taxable_ca_arr)) {
                $Item->setTaxExempt("N");
              } else {
                $Item->setTaxExempt("Y");
              }
            }
            $onetimecharge = "0.00";
            $Item->setOneTimeCharge(number_format($onetimecharge, 2, '.', ''));
            $itemtaxamt = '';

            $Item->setItemTaxAmount($itemtaxamt);
            //$Item->setCustomField('');
            //$responseArray['ItemOptions'] = array();
//
            $Itemoption = new WG_Itemoption();

            if (is_array($product_info->data['attributes']) && count($product_info->data['attributes']) > 0) {
              foreach ($product_info->data['attributes'] as $attributes => $value) {
                foreach ($value as $keyvalue) {
                  $Itemoption->setOptionName(htmlentities($attributes));
                  $Itemoption->setOptionValue(htmlentities($keyvalue));
                }
                $Item->setItemOptions($Itemoption->getItemoption());
              }

            }


            $Order->setOrderItems($Item->getItem());
          } // end items


          $coupon_name = 0.0;
          $generic_name = 0.0;
          $uc_discounts = 0.0;
          foreach ($order_details->line_items as $disc_type) {
            if ($disc_type['type'] == 'coupon') {
              if (isset($disc_type['title'])) {
                $cop_name = explode(" ", $disc_type['title']);
                $coupon_title = "Discount Coupon (" . $cop_name[1] . ")";
                $coupon_sku = $cop_name[1];
              } else {
                $coupon_title = "Discount Coupon";
                $coupon_sku = "Discount Coupon";
              }
              $coupon_amt = abs($disc_type['amount']);

              $Item->setItemCode(htmlentities($coupon_sku, ENT_QUOTES));
              $Item->setItemDescription(htmlentities($coupon_title, ENT_QUOTES));
              $Item->setQuantity('1');
              $Item->setUnitPrice('-' . $coupon_amt);
              $Item->setWeight('0');
              $Item->setFreeShipping('N');
              $Order->setOrderItems($Item->getItem());
              //break;
            }
            if ($disc_type['type'] == 'generic') {
              $generic_title = $disc_type['title'];
              $generic_amt = abs($disc_type['amount']);

              $Item->setItemCode(htmlentities($disc_type['type'], ENT_QUOTES));
              $Item->setItemDescription(htmlentities($generic_title, ENT_QUOTES));
              $Item->setQuantity('1');
              $Item->setUnitPrice('-' . $generic_amt);
              $Item->setWeight('0');
              $Item->setFreeShipping('N');
              $Order->setOrderItems($Item->getItem());
              //break;

            }
            if ($disc_type['type'] == 'uc_discounts') {
              //$uc_discounts = $disc_type['amount'];
              $discount_title = $disc_type['title'];
              $discount_amt = abs($disc_type['amount']);

            }
            if ($disc_type['type'] == 'gift_certificate') {
              $gift_title = $disc_type['title'];
              $gift_amt = abs($disc_type['amount']);


              $Item->setItemCode(htmlentities($gift_title, ENT_QUOTES));
              $Item->setItemDescription(htmlentities($gift_title, ENT_QUOTES));
              $Item->setQuantity('1');
              $Item->setUnitPrice('-' . $gift_amt);
              $Item->setWeight('0');
              $Item->setFreeShipping('N');
              $Order->setOrderItems($Item->getItem());
              //break;
            }


          }

          $charges = new WG_Charges();
          $charges->setDiscount($discount_amt ? $discount_amt : '0.00');
          unset($discount_amt);
          $charges->setStoreCredit($storecredit ? $storecredit : '0.0');
          $totaltax = (float)$totaltax;
          $charges->setTax($totaltax);
          unset($totaltax);

          $charges->setShipping($totalship ? $totalship : '0.0');
          unset($totalship);
          $charges->setTotal($total);

          $Order->setOrderChargeInfo($charges->getCharges());

          $Order->setShippedOn(date("m-d-Y", $order_data->created));


          $Order->setShippedVia($carrier[0]);

          $Orders->setOrders($Order->getOrder());
        }
        unset($order_details);
      }
      //print_r($Orders->getOrders());
      //die("mmm");
      unset($orders);
      return $this->response($Orders->getOrders());
    }


    #
    #
    # Update Orders shipping status method
    # Will update Order Notes and tracking number of  order
    # Input parameter Username,Password, array (OrderID,ShippedOn,ShippedVia,ServiceUsed,TrackingNumber)
    #
    function UpdateOrdersShippingStatus($username, $password, $data, $statustype = 'Cancel')
    {
      global $version, $drupal_version, $files;
      $status = $this->auth_user($username, $password);
      if ($status != '0') {
        return $status;
      }

      $Orders = new WG_Orders();
      //$response_array = json_decode($Orders_json_array,true);
      $response_array = $data;
      if (!is_array($response_array)) {
        $Orders->setStatusCode("9997");
        $Orders->setStatusMessage("Unknown request or request not in proper format");
        return $this->response($Orders->getOrders());
        exit();
      }
      if (count($response_array) == 0) {
        $Orders->setStatusCode("9996");
        $Orders->setStatusMessage("REQUEST array(s) doesnt have correct input format");
        return $this->response($Orders->getOrders());
        exit();
      }
      if (count($response_array) == 0) {
        $no_orders = true;
      } else {
        $no_orders = false;
      }
      $Orders->setStatusCode($no_orders ? "1000" : "0");
      $Orders->setStatusMessage($no_orders ? "No new orders." : "All Ok");
      if ($no_orders) {
        return json_encode($response_array);
      }

      # Fetch All order status of Cart
      $sql = db_query("SELECT order_status_id,title from {uc_order_statuses} ");
      //while($status_all = db_fetch_array($sql))
      while ($status_all = $sql->fetchObject()) {
        $statuses[$status_all->title] = $status_all->order_status_id;
      }


      $i = 0;

      foreach ($response_array as $k=> $v) //request
      {

        if (isset($order_wg)) {
          unset($order_wg);
        }

        foreach ($v as $k1=> $v1) {
          $order_wg[$k1] = $v1;
        }

        $order_id = $order_wg['OrderID'];
        # for fetching user id
        $user = db_query("SELECT uid,name,pass FROM {users} WHERE name = " . $this->mySQLSafe($username) . " ")->fetchObject();
        $uid = $user->uid;


        if (strtolower($statustype) == strtolower('Cancel')) {
          $status = 'canceled';
        } else {
          $status = $statuses[$order_wg['OrderStatus']];
        }
//
        $info = "\nOrder shipped ";

        if ($order_wg['ShippedOn'] != "")
          $info .= " on " . substr($order_wg['ShippedOn'], 0, 10);

        if ($order_wg['ServiceUsed'] != "")
          $info .= ". " . $order_wg['ServiceUsed'];

        if ($order_wg['TrackingNumber'] != "")
          $info .= " Tracking Number is " . $order_wg['TrackingNumber'] . ".";

        ### Commented line as it was appending admin comment in Ubercart's Order comment field

        if ($order_wg['IsNotifyCustomer'] == 'N') {
          $notify = 0;
        }
        if ($order_wg['IsNotifyCustomer'] == 'Y') {
          $notify = 1;
        }
        $time = time();
//					# Update  Order Status
//
        uc_order_update_status($order_id, $status);

        //$result =db_query("select * from {uc_order_comments} oc where oc.order_id = ".$order_id." ORDER BY oc.comment_id  ");
        $result = db_query("select comment_id, order_status from {uc_order_comments} oc where oc.order_id =" . $this->mySQLSafe($order_id) . " ORDER BY oc.comment_id  ");
        $comments = array();
        while ($comment = $result->fetchObject()) {
          $comments = $comment;
        }


        $result = db_query("select comment_id, message from {uc_order_admin_comments} oa where oa.order_id =" . $this->mySQLSafe($order_id) . " ORDER BY oa.comment_id  ");
        $admin_comments = array();

        while ($ad_comment = $result->fetchObject()) {
          $admin_comments = $ad_comment;
        }

        $status_var = $comments->order_status;


        if ($status_var == $status && strtolower($status) != 'canceled') {
          //db_query("UPDATE {uc_order_comments} SET message = '".$info."',order_status ='$status' , notified =$notify, created =$time where comment_id = ".$order_comments['comment_id']." ");

          db_insert('uc_order_admin_comments')
            ->fields(array('order_id' => $order_id, 'uid' => $uid, 'message' => htmlspecialchars($info), 'created'=> $time))
            ->execute();

          /*		db_update('uc_order_comments')
								  ->fields(array('message' => $info,'order_status' => $status,'notified'=>$notify,'created'=>$time))
								  ->condition('comment_id',$comments->comment_id)
								  ->execute();
					*/

        } elseif (strtolower($status) == 'canceled') {

          uc_order_comment_save($order_id, $uid, 'Order status changed to ' . $status . '.', $type = 'order', $status = $status, $notify);

        } else {
          uc_order_comment_save($order_id, $uid, $info, $type = 'order', $status = $status, $notify);
          //uc_order_comment_save($order_id, $uid, $info, $type = 'admin', $status = $status, $notify);

        }


        if (strtolower($statustype) == 'posttostore') {
//					# Fetch delivery & Billing address
          $add1 = db_query("SELECT delivery_first_name first_name ,delivery_last_name last_name,delivery_phone phone,delivery_company company,	delivery_street1 street1,delivery_street2 street2,delivery_city city,delivery_zone zone,delivery_postal_code postal_code,delivery_country country,primary_email email from {uc_orders} where order_id =" . $this->mySQLSafe($order_id));
          while ($add = $add1->fetchObject()) {
            $deliveryadd = $add;

          }

          $add2 = db_query("SELECT billing_first_name first_name ,billing_last_name last_name,billing_phone phone,billing_company company,	billing_street1 street1,billing_street2 street2,billing_city city,billing_zone zone,billing_postal_code postal_code,billing_country country from {uc_orders} where order_id =" . $this->mySQLSafe($order_id));
          while ($add_b = $add2->fetchObject()) {
            $billingadd = $add_b;
          }

          #########
          if ($files['uc_shipping']->status == 1) {


            $package_id = db_query("SELECT package_id  from {uc_packages} where order_id = " . $this->mySQLSafe($order_id))->fetchField();

            $sql = db_query("Select order_product_id,order_id,nid,data,qty from {uc_order_products} where order_id=" . $this->mySQLSafe($order_id));

            unset($order_products);

            while ($order_prod = $sql->fetchObject()) {
              $order_products[] = $order_prod;

              $prd['checked'] = 1;
              $prd['qty'] = $order_prod->qty;
              $prd['package'] = 1;
              $package_wg['products'][$order_prod->order_product_id] = (object)$prd;
            }


            $package_wg['order_id'] = $order_id;
            $package_wg['shipping_type'] = 'small_package';
            $package_wg['tracking_number'] = $order_wg['TrackingNumber'];

            //
            //
            //					# Insert or Update the tracking number abd other data for  Order number .

            $sql = db_query("Select * from {uc_shipments} where order_id=" . $this->mySQLSafe($order_id));
            while ($ship = $sql->fetchObject()) {
              $record = $ship;
            }

            #$dt = explode("/",$order_wg['ShippedOn']);
            #$dt1 = gmmktime(12, 0, 0, $dt[0], $dt[1], $dt[2]);
            $dt1 = strtotime($order_wg['ShippedOn']);

            $order_details123 = uc_order_load($order_id);

            foreach ($order_details123->line_items as $shipping123) {
              if ($shipping123['type'] == 'shipping') {
                $totalship += $shipping123['amount'];
                $ship_method = $ship_method . $shipping123['title'];
              }
            }

            /* shipment check before creation */

            $shipping_types_products = array();

            foreach ($order_details123->products as $product) {
              if ($product->data['shippable']) {
                $product->shipping_type = uc_product_get_shipping_type($product);
                $shipping_types_products[$product->shipping_type][] = $product;
              }
            }

            $shipping_type_weights = variable_get('uc_quote_type_weight', array());

            $result = db_query("SELECT op.order_product_id, SUM(pp.qty) AS quantity FROM {uc_packaged_products} AS pp LEFT JOIN {uc_packages} AS p ON pp.package_id = p.package_id LEFT JOIN {uc_order_products} AS op ON op.order_product_id = pp.order_product_id WHERE p.order_id = :id GROUP BY op.order_product_id", array(':id' => $order_id));
            $packaged_products = $result->fetchAllKeyed();

            $shipping_type_options = uc_quote_shipping_type_options();
            foreach ($shipping_types_products as $shipping_type => $products) {

              foreach ($products as $product) {
                $unboxed_qty = $product->qty;

                if (isset($packaged_products[$product->order_product_id])) {
                  $unboxed_qty -= $packaged_products[$product->order_product_id];
                }

                if ($unboxed_qty > 0 && $item['ItemQtyShipped'] == $unboxed_qty) {

                  if ($record) {
                    $shipment_id = $record->sid;

                    db_update('uc_shipments')
                      ->fields(array('o_first_name'     => $deliveryadd->first_name, 'o_last_name' => $deliveryadd->last_name,
                                     'o_company'        => $deliveryadd->company,
                                     'o_street1'        => $deliveryadd->street1,
                                     'o_street2'        => $deliveryadd->street2,
                                     'o_city'           => $deliveryadd->city,
                                     'o_zone'           => $deliveryadd->zone,
                                     'o_postal_code'    => $deliveryadd->postal_code,
                                     'o_country'        => $deliveryadd->country,
                                     'd_first_name'     => $billingadd->first_name,
                                     'd_last_name'      => $billingadd->last_name,
                                     'd_company'        => $billingadd->company,
                                     'd_street1'        => $billingadd->street1,
                                     'd_street2'        => $billingadd->street2,
                                     'd_city'           => $billingadd->city,
                                     'd_zone'           => $billingadd->zone,
                                     'd_postal_code'    => $billingadd->postal_code,
                                     'd_country'        => $billingadd->country,
                                     'shipping_method'  => $order_wg['ShippedVia'],
                                     'accessorials'     => '',
                                     'carrier'          => $order_wg['ServiceUsed'],
                                     'tracking_number'  => $order_wg['TrackingNumber'],
                                     'ship_date'        => $dt1,
                                     'expected_delivery'=> $dt1,
                                     'cost'             => $totalship))
                      ->condition('order_id', $order_id)
                      ->execute();


                    //die("vvvv");
                    $result1 = "Success";


                  } else {
                    # if required follow to function 'uc_shipping_shipment_edit_submit'

                    $shipment = new stdClass();
                    $shipment->order_id = $order_id;
                    if (isset($record['sid'])) {
                      $shipment->sid = $record['sid'];
                    }

                    $origin = array('first_name'=> $deliveryadd->first_name, 'last_name'=> $deliveryadd->last_name, 'company'=> $deliveryadd->company, 'street1'=> $deliveryadd->street1, 'street2'=> $deliveryadd->street2, 'city'=> $deliveryadd->city, 'zone'=> $deliveryadd->zone, 'country'=> $deliveryadd->country, 'postal_code'=> $deliveryadd->postal_code, 'phone'=> '');

                    $shipment->origin = (object)$origin;

                    $address = array('order_id'=> $order_id, 'uid'=> $uid, 'delivery_first_name'=> $billingadd->first_name, 'delivery_last_name'=> $billingadd->last_name, 'delivery_phone'=> '', 'delivery_company'=> $billingadd->company, 'delivery_street1'=> $billingadd->street1, 'delivery_street2'=> $billingadd->street2, 'delivery_city'=> $billingadd->city, 'delivery_zone'=> $billingadd->zone, 'delivery_postal_code'=> $billingadd->postal_code, 'delivery_country'=> $billingadd->country, 'billing_first_name'=> $deliveryadd->first_name, 'billing_last_name'=> $deliveryadd->last_name, 'billing_phone'=> '', 'billing_company'=> $deliveryadd->company, 'billing_street1'=> $deliveryadd->street1, 'billing_street2'=> $deliveryadd->street2, 'billing_city'=> $deliveryadd->city, 'billing_zone'=> $deliveryadd->zone, 'billing_postal_code'=> $deliveryadd->postal_code, 'billing_country'=> $deliveryadd->country);


                    $destination = array('address'=> (object)$address, 'first_name'=> $deliveryadd->first_name, 'last_name'=> $deliveryadd->last_name, 'company'=> $deliveryadd->company, 'street1'=> $deliveryadd->street1, 'street2'=> $deliveryadd->street2, 'city'=> $deliveryadd->city, 'zone'=> $deliveryadd->zone, 'country'=> $deliveryadd->country, 'postal_code'=> $deliveryadd->postal_code, 'phone'=> '');


                    $shipment->destination = (object)$destination;


                    $shipment->packages = array();
                    $shipment->shipping_method = $order_wg['ShippedVia'];
                    $shipment->accessorials = '';
                    $shipment->carrier = $order_wg['ServiceUsed'];
                    $shipment->transaction_id = '';
                    $shipment->tracking_number = $order_wg['TrackingNumber'];
                    $shipment->ship_date = $dt1;
                    $shipment->expected_delivery = $dt1;
                    $shipment->cost = $totalship;

                    uc_shipping_shipment_save($shipment);

                    $shipment_id = db_query('SELECT MAX(sid) FROM {uc_shipments}')->fetchField();
                    $result1 = "Success";
                    //die("hihihi");


                  }
                }
              }
            }
            //


            //
            $package_wg['sid'] = $shipment_id;

            if (!$package_id) {
              uc_shipping_package_save($package_wg);
              $package_id = db_query('SELECT MAX(package_id) FROM {uc_packages}')->fetchField();


            }
            foreach ($order_products as $order_product) {
              $order_product->data = unserialize($order_product->data);
              $order_product->data['package_id'] = intval($package_id);
              db_update('uc_order_products')
                ->fields(array('data' => serialize($order_product->data)))
                ->condition('order_product_id', $order_product->order_product_id)
                ->execute();


            }
          }
          $result1 = "Success";
        } else {
          $result1 = "Success";
        }

//					# send a notify mail
        if ($result1 == 'Success' && $order_wg['IsNotifyCustomer'] == 'Y') {
          $order_data = uc_order_load($order_id);
          global $base_url;
          $pos = strpos($base_url, 'sites');
          if ($pos != "") {
            $base_url = str_replace("/sites/all/modules/eccv3", "", $base_url);
          } elseif (strpos($base_url, 'modules')) {
            $base_url = str_replace("/modules/eccv3", "", $base_url);
          }

          rules_invoke_event('uc_order_status_email_update', $order_data);


        }

        $order_date = db_query("SELECT modified,order_status FROM {uc_orders} WHERE order_id = " . $this->mySQLSafe($order_id) . " ")->fetchObject();
        $order_status = $order_date->order_status;

        //$xmlResponse->createTag('Status',  array(), $result, $orderNode, __ENCODE_RESPONSE);
        $Order = new WG_Order();
        $Order->setOrderID($order_id);
        $Order->setStatus($result1);

        $Order->setLastModifiedDate(date("m-d-Y H:i:s", $order_date->modified));
        $Order->setOrderNotes($admin_comments->message);
        $Order->setOrderStatus($order_status);

        $Orders->setOrders($Order->getOrder());
        $Order_counter++;

//
        $i++;
      }
//print_r($Orders->getOrders());
//die("hi");
      return $this->response($Orders->getOrders());
    }


    function UpdateOrdersStatusAcknowledge($username, $password, $data, $others)
    {

      //global $version,$drupal_version;
      $status = $this->auth_user($username, $password);
      if ($status != '0') {
        return $status;
      }

      $Orders = new WG_Orders();
      //$response_array = json_decode($Orders_json_array,true);
      $response_array = $data;

      if (!is_array($response_array)) {
        $Orders->setStatusCode("9997");
        $Orders->setStatusMessage("Unknown request or request not in proper format");
        return $this->response($Orders->getOrders());
        exit();
      }
      if (count($response_array) == 0) {
        $Orders->setStatusCode("9996");
        $Orders->setStatusMessage("REQUEST array(s) doesnt have correct input format");
        return $this->response($Orders->getOrders());
        exit();
      }
      if (count($response_array) == 0) {
        $no_orders = true;
      } else {
        $no_orders = false;
      }
      $Orders->setStatusCode($no_orders ? "1000" : "0");
      $Orders->setStatusMessage($no_orders ? "No new orders." : "All Ok");
      if ($no_orders) {
        return $this->response($Orders->getOrders());
      }

      # Fetch All order status of Cart
      $sql = db_query("SELECT order_status_id,title from {uc_order_statuses} ");

      while ($status_all = $sql->fetchObject()) {
        $statuses[$status_all->title] = $status_all->order_status_id;
      }


      $i = 0;

      foreach ($response_array as $k=> $v) //request
      {

        if (isset($order_wg)) {
          unset($order_wg);
        }

        foreach ($v as $k1=> $v1) {

          $order_wg[$k1] = $v1;
        }

        $order_id = $order_wg['OrderID'];

        # for fetching user id
        $user = db_query("SELECT uid,name,pass FROM {users} WHERE name = " . $this->mySQLSafe($username) . " ")->fetchObject();
        $uid = $user->uid;


        if (strtolower($statustype) == strtolower('Cancel')) {
          $status = 'canceled';
        } else {
          $status = $statuses[$order_wg['OrderStatus']];
        }

        $info = "\nOrder shipped ";

        if ($order_wg['ShippedOn'] != "")
          $info .= " on " . substr($order_wg['ShippedOn'], 0, 10);

        if ($order_wg['ServiceUsed'] != "")
          $info .= ". " . $order_wg['ServiceUsed'];

        if ($order_wg['TrackingNumber'] != "")
          $info .= " Tracking Number is " . $order_wg['TrackingNumber'] . ".";

        if ($order_wg['OrderNotes'] != "")
          $info .= " \n" . $order_wg['OrderNotes'];


        $time = time();
        # Update  Order Status

        uc_order_update_status($order_id, $status);

        //$result =db_query("select * from {uc_order_comments} oc where oc.order_id = ".$order_id." ORDER BY oc.comment_id  ");

        $result = db_query("select comment_id, order_status from {uc_order_comments} oc where oc.order_id =" . $this->mySQLSafe($order_id) . " ORDER BY oc.comment_id  ");
        $comments = array();
        while ($comment = $result->fetchObject()) {
          $comments = $comment;
        }

        $status_var = $comments->order_status;


        if ($status_var == $status && strtolower($status) != 'canceled') {
          db_update('uc_order_comments')
            ->fields(array('message'=> 'Order status changed to ".$status.".', 'order_status' => $status, 'notified'=> $notify, 'created'=> $time))
            ->condition('comment_id', $comments->comment_id)
            ->execute();


        } elseif (strtolower($status) == 'canceled') {
          if (isset($comments)) {
            db_update('uc_order_comments')
              ->fields(array('message'=> 'Order status changed to ".$status.".', 'order_status' => $status, 'notified'=> $notify, 'created'=> $time))
              ->condition('comment_id', $comments->comment_id)
              ->execute();

          } else {
            uc_order_comment_save($order_id, $uid, 'Order status changed to ".$status.".', $type = 'order', $status = $status, $notify);

          }
        } else {
          uc_order_comment_save($order_id, $uid, 'Order status changed to ' . $status . '.', $type = 'order', $status, $notify);

        }

        $result = "Success";

        $order_date = db_query("SELECT modified,order_status FROM {uc_orders} WHERE order_id = " . $this->mySQLSafe($order_id) . " ")->fetchObject();
        $order_status = $order_date->order_status;
        //$xmlResponse->createTag('Status',  array(), $result, $orderNode, __ENCODE_RESPONSE);
        $Order = new WG_Order();
        $Order->setOrderID($order_id);
        $Order->setStatus($result);
        $Order->setLastModifiedDate(date("m-d-Y H:i:s", $order_date->modified));
        $Order->setOrderNotes($info);
        $Order->setOrderStatus($order_status);
        $Orders->setOrders($Order->getOrder());
        $Order_counter++;

        $i++;
      }

      return $this->response($Orders->getOrders());
    }


    # Function to add the product in the store which found in QB

    function addProduct($username, $password, $item_json_array)
    {
      global $version, $drupal_version, $files;

      global $user, $categoryid, $version;
      $status = $this->auth_user($username, $password);
      if ($status != '0') {
        return $status;
      }
      $Items = new WG_Items();
      $Items->setStatusCode('0');
      $Items->setStatusMessage('All Ok');
      if (isset($files)) {
        if ($files['uc_stock']->status == 1) {
          $stock_module_avialbe = 1;

        } else {
          $stock_module_avialbe = 0;
        }
      }
      //$requestArray = json_decode($item_json_array,true);
      $requestArray = $item_json_array;

      if (!is_array($requestArray)) {
        $Items->setStatusCode('9997');
        $Items->setStatusMessage('Unknown request or request not in proper format');
        return $this->response($Items->getItems());
      }

      if (count($requestArray) == 0) {
        $Items->setStatusCode('9996');
        $Items->setStatusMessage('REQUEST tag(s) doesnt have correct input format');
        return $this->response($Items->getItems());
      }

      $itemsCount = 0;
      $itemsProcessed = 0;

      # Go throught items
      $itemsCount = 0;
      $_err_message_arr = Array();

      $attributes = array();
      $attribute_option = array();

      $catlog = false;
      if ($files['uc_catalog']->status == 0) {
        $catalog = true;

      }

      foreach ($requestArray as $kv=> $vItem) //request
      {
        $itemsCount++;
        unset($nid);

        $nid = $vItem['ItemID'];
        $productcode = $vItem['ItemCode'];
        $product = $vItem['ItemName'];
        $descr = $vItem['ItemDesc'];
        //$descr=$vItem['ItemShortDescr'];
        $free_shipping = $vItem['FreeShipping'];
        $free_tax = $vItem['TaxExempt'];
        $tax_id = $vItem['TaxID'];
        $item_match = $vItem['ItemMatchBy'];
        $manufacturerid = $vItem['ManufacturerID'];
        $avail_qty = $vItem['Quantity'];
        $price = $vItem['UnitPrice'];
        $cost = $vItem['CostPrice'];
        $weight = $vItem['Weight'];
        $published = $vItem['ItemPublished'];
        if ($published == true) {
          $st = 1;
        } else {
          $st = 0;
        }
        //echo $nid."==".$product."==".$descr."==".$free_shipping."==".$free_tax."==".$tax_id."==".$item_match."==".$manufacturerid."==".$avail_qty."==".$price."==".$weight."==".

        # If variants node exist in XML

        $bk = 0;

        $uniq_options = array();
        $uniq_options_vals = array();
        //unset($variant_data);
        //foreach($_variantsTag as $k1=>$ItemvariantsTag)  $requestArray
        $b = 0;
        foreach ($vItem['ItemVariants'] as $kv=> $Itemvariants) {


          $variant_data[$b]['variantid'] = $Itemvariants['ItemCode'];
          $variant_data[$b]['variantqty'] = $Itemvariants['Quantity'];
          $variant_data[$b]['variantUnitprice'] = $Itemvariants['UnitPrice'];

          $br = 0;

          $all_options = '';

          foreach ($Itemvariants['ItemOptions'] as $k=> $optionsTag) {

            if ($optionsTag['OptionName'] && $optionsTag['OptionName'] != '') {
              $all_options[$br]['optionname'] = $optionsTag['OptionName'];
              if (!in_array($optionsTag['OptionName'], $uniq_options)) {
                $uniq_options[$bk] = $optionsTag['OptionName'];
                $bk++;
              }

            }
            if ($optionsTag['OptionValue'] && $optionsTag['OptionValue'] != '') {
              $all_options[$br]['optionvalue'] = $optionsTag['OptionValue'];

              #if(!in_array($optionvalue,$uniq_options_vals[$optionname]))
              if (!in_array($optionsTag['OptionValue'], $uniq_options_vals)) {
                $uniq_options_vals[$optionsTag['OptionName']][] = $optionsTag['OptionValue'];
              }

            }

            if (isset($optionsTag['OptionPrice']) && $optionsTag['OptionPrice'] != '') {
              $all_options[$br][optionprice] = $optionsTag['OptionPrice'];
              $uniq_options_vals1[$optionsTag['OptionName']][$optionsTag['OptionValue']] = $optionsTag['OptionPrice'];

            }
            $br++;
          }
          $variant_data[$b]['options'] = $all_options;

          $b++;
        }

        if (is_array($vItem['Categories'])) {
          $arrayCategories = $vItem['Categories'];
          $categoryid = array();
          foreach ($arrayCategories as $k3=> $vCategories) //Categories
          {
            if (isset($vCategories['CategoryId']) && $vCategories['CategoryId'] != '') {
              $catid[] = $vCategories['CategoryId'];
            }
            //$categoryid[] = $vCategories['CategoryId'].",";
          }
          //$categoryid = strrev(substr($categoryid,0,-1));
          //$categoryid = strrev($categoryid);
        }
        # Create array of array as Ubercart use it in this form
        $categoryid = array();
        $categoryid = $catid;

        $is_nid = db_query("SELECT a.nid FROM {node} a, {uc_products} b where a.title = " . $this->mySQLSafe($product) . " and b.model=" . $this->mySQLSafe($productcode) . " and a.nid=b.nid order by a.nid DESC")->fetchField();


        $Success = 'Success';
        if ($is_nid != '') {
          $nid = $is_nid;
          //$Success = "Created";
        }

        # ITEMID not exist in request, So create a product .
        if ($nid == '') {
          # Insert a product
          $user = user_load_by_name($username);

          # create node with values

          ## For not promoting product on front Page
          foreach ($categoryid as $k=> $v) {
            $cat[$k] = array('tid'=> $v);
          }

          $nodearr = array(nid => "", vid=> '', uid=> $user->uid, created=> '', type=> product, language=> 'und', changed=> '', title => $product, teaser_js=> '', teaser_include=> '', body => $descr, format=> '', model => $productcode, list_price=> '', cost=> $cost, shippable=> '1', sell_price => $price, weight => $weight, weight_units=> '', length_units=> '', dim_length=> '', dim_width=> '', dim_height=> '', pkg_qty=> 1, default_qty=> 1, ordering=> '', promote=> '0', revision=> '', log=> '', name=> $user->name, date=> '', status=> $st, sticky=> '', path=> '', taxonomy_catalog=> array('und'=> $cat), flatrate=> '', shipping_type=> '', first_name=> '', last_name=> '', company=> '', street1=> '', street2=> '', city=> '', container=> '', teaser=> $descr, validated=> '');


          $node = (object)($nodearr);
          //print_r($node);
          //die("hihi");
          $node_temp = $node;
          # Let modules modify the node before it is saved to the database.
          field_attach_presave('node', $node);

          $node->is_new = false;

          # Apply filters to some default node fields:
          if (empty($node->nid)) {
            // Insert a new node.
            $node->is_new = true;
            # When inserting a node, $node->log must be set because {node_revisions}.log does not (and cannot) have a default value.  If the user does not have permission to create revisions, however, the form will not contain an element for log so $node->log will be unset at this point.
            if (!isset($node->log)) {
              $node->log = '';
            }

            # For the same reasons, make sure we have $node->teaser and $node->body.  We should consider making these fields nullable  in a future version since node types are not required to use them.

            if (!isset($node->teaser)) {
              $node->teaser = '';
            }
            if (!isset($node->body)) {
              $node->body = '';
            }
          } elseif (!empty($node->revision)) {
            $node->old_vid = $node->vid;
          } else {
            # When updating a node, avoid clobberring an existing log entry with an empty one.
            if (empty($node->log)) {
              unset($node->log);
            }
          }

          # Set some required fields:
          if (empty($node->created)) {
            $node->created = time();
          }
          # The changed timestamp is always updated for bookkeeping purposes (revisions, searching, ...)
          $node->changed = time();

          $node->timestamp = time();
          $node->format = isset($node->format) ? $node->format : FILTER_FORMAT_DEFAULT;

          # Generate the node table query and the node_revisions table query.
          if ($node->is_new) {


            module_invoke_all('node_presave', $node);

            module_invoke_all('entity_presave', $node, 'node');


            _node_save_revision($node, $user->uid);

            drupal_write_record('node', $node);


            db_update('node_revision')
              ->fields(array('nid'=> $node->nid))
              ->condition('vid', $node->vid)
              ->execute();


            $op = 'insert';

          }

          # Call the node specific callback (if any).


          //node_invoke($node, $op);
          //drupal_write_record('uc_products', $node);

          if (!isset($node->unique_hash)) {
            $node->unique_hash = md5($node->vid . $node->nid . $node->model . $node->list_price . $node->cost . $node->sell_price . $node->weight . $node->weight_units . $node->length . $node->width . $node->height . $node->length_units . $node->pkg_qty . $node->default_qty . $node->shippable . REQUEST_TIME);
          }

          db_insert('uc_products')
            ->fields(array('vid'=> $node->vid, 'nid'=> $node->nid, 'model'=> $node->model, 'cost'=> $node->cost, 'sell_price'=> $node->sell_price, 'pkg_qty'=> $node->pkg_qty, 'default_qty'=> $node->default_qty, 'unique_hash'=> $node->unique_hash, 'weight'=> $node->weight,))
            ->execute();


          $entity_type = "node";
          $bundle = "product";
          $deleted = "0";
          $delta = "0";
          $language = "und";

          db_insert('field_data_body')
            ->fields(array('entity_type'=> $entity_type, 'bundle'=> $bundle, 'deleted'=> $deleted, 'entity_id'=> $node->nid, 'revision_id'=> $node->vid, 'language'=> $language, 'delta'=> $delta, 'body_value'=> $node->body,))
            ->execute();

          module_invoke_all('node_' . $op, $node);
          module_invoke_all('entity_' . $op, $node, 'node');
          # Insert categories
          foreach ($node->taxonomy_catalog['und'] as $k=> $id) {
            if ($catalog == true) {
              $entity_type = "node";
              $bundle = "product";
              db_insert('field_data_taxonomy_catalog')
                ->fields(array('entity_type'=> $entity_type, 'bundle'=> $bundle, 'deleted'=> 0, 'entity_id'=> $node->nid, 'revision_id'=> $node->vid, 'language'=> "und", 'delta'=> $k, 'taxonomy_catalog_tid'=> $id['tid']))
                ->execute();

              db_insert('field_revision_taxonomy_catalog')
                ->fields(array('entity_type'=> $entity_type, 'bundle'=> $bundle, 'deleted'=> 0, 'entity_id'=> $node->nid, 'revision_id'=> $node->vid, 'language'=> "und", 'delta'=> $k, 'taxonomy_catalog_tid'=> $id['tid']))
                ->execute();
            }

          }


//
//
//				# Update the node access table for this node. node_access_acquire_grants($node); Clear the page and block caches.cache_clear_all();
//
//				watchdog('action', 'Saved @type %title', array('@type' => node_get_types('name', $node), '%title' => $node->title));

          if ($stock_module_avialbe == '1') {
            $sku_exist = db_query("SELECT COUNT(*) FROM {uc_product_stock} where sku = " . $this->mySQLSafe($node->model) . " ")->fetchField();
          }


          $nid = $node->nid;
          $active_node = 1;
          $avail_qty1 = $avail_qty;
          if ($variant_data) {
            $active_node = 0;
            $avail_qty1 = 0;
          }
          if ($sku_exist > 0) {
            db_update('uc_product_stock')
              ->fields(array('nid'=> $node->nid, 'active'=> $active_node, 'stock'=> $avail_qty1))
              ->condition('sku', $node->model)
              ->execute();

          } else {
            if ($stock_module_avialbe == '1') {
              db_insert('uc_product_stock')
                ->fields(array('sku'=> $node->model, 'nid'=> $node->nid, 'active'=> $active_node, 'stock'=> $avail_qty1))
                ->execute();
              $status = "Success";


            }
            $nid = $node->nid;

          }
        }


        #Calling function for add image
        if ($vItem['Image']) {
          $this->addItemImage($nid, $vItem['Image'], $storeid = 1);
        }

        $Item = new WG_Item();
        $Item->setStatus($Success);
        $Item->setProductID($nid);
        $Item->setSku(htmlentities($productcode));
        $Item->setProductName(htmlentities($product));

        #  If variant Exist in XML then insert

        if (isset($variant_data) && $variant_data != "" && is_array($variant_data)) {
          //print_r($variant_data);
          $sql = db_query("SELECT aid,name FROM {uc_attributes}");
          //while ($attribute1 = db_fetch_object($sql))
          while ($attribute1 = $sql->fetchObject()) {
            $attributes[$attribute1->aid] = trim($attribute1->name);
          }


          foreach ($uniq_options_vals as $atk=> $atv) { //echo $atk."===".$atv;
            $aid = array_search($atk, $attributes);

            if (!$aid) {
              db_insert('uc_attributes')
                ->fields(array('name'=> $atk, 'label'=> $atk, 'ordering'=> 0, 'required'=> 0, 'display'=> 1, 'description'=> $atk))
                ->execute();
              $aid = db_query('SELECT MAX(aid) FROM {uc_attributes}')->fetchField();
              //$status = "Success";

              $attributes[$aid] = $atk;
            }
            $nid1 = db_query("SELECT nid FROM {uc_product_attributes} WHERE aid = $aid and nid = $nid ")->fetchField();

            if (!$nid1) {
              $required = '1';
              db_insert('uc_product_attributes')
                ->fields(array('nid'=> $nid, 'aid'=> $aid, 'label'=> $atk, 'required'=> $required))
                ->execute();

            }

            foreach ($atv as $atk1=> $atv1) {
              $oid = db_query("SELECT oid FROM {uc_attribute_options} WHERE aid = " . $this->mySQLSafe($aid) . " and name = " . $this->mySQLSafe($atv1) . " ")->fetchField();


              if (!$oid) {
                $cost = '0.0';
                $price = '0.0';
                db_insert('uc_attribute_options')
                  ->fields(array('aid'=> $aid, 'name'=> $atv1, 'cost'=> $cost, 'price'=> $price))
                  ->execute();

                $oid = db_query('SELECT MAX(oid) FROM {uc_attribute_options}')->fetchField();

              }

              $n_price = db_query("SELECT price from {uc_product_options} where nid = " . $this->mySQLSafe($nid) . " and oid = " . $this->mySQLSafe($oid))->fetchField();

              if (!$n_price) {
                db_insert('uc_product_options')
                  ->fields(array('nid'=> $nid, 'oid'=> $oid, 'cost'=> $uniq_options_vals1[$atk][$atv1], 'price'=> $uniq_options_vals1[$atk][$atv1]))
                  ->execute();

              } elseif ($uniq_options_vals1[$atk][$atv1] != $n_price) {

                $message .= "There is price mismatch in one of attribute please do it manually.";
              }

              $sql = db_query("SELECT oid,name FROM {uc_attribute_options} where oid =" . $this->mySQLSafe($oid) . "");
              //while ($attribute_option1 = db_fetch_object($sql))
              while ($attribute_option1 = $sql->fetchObject()) {
                $attribute_option[$attribute_option1->oid] = $attribute_option1->name;
              }

              $required = 1;
              db_update('uc_product_attributes')
                ->fields(array('default_option'=> $oid))
                ->condition('nid', $nid)
                ->condition('aid', $aid)
                ->condition('required', $required)
                ->execute();


            }
          }
          unset($uniq_options_vals1, $uniq_options_vals, $nid1, $n_price, $atv);
          foreach ($variant_data as $variant_data1) {
            unset($comb_array);
            $comb_array = array();
            foreach ($variant_data1['options'] as $variant_data1_options) {
              $aid = array_search($variant_data1_options['optionname'], $attributes);
              $oid = array_search($variant_data1_options['optionvalue'], $attribute_option);
              $comb_array[$aid] = "" . $oid . "";
            }
            ksort($comb_array);

            //#need to recheck it
            $n_id = db_query("SELECT nid from {uc_product_adjustments} where  model  = " . $this->mySQLSafe($variant_data1['variantid']) . "")->fetchField();

            if (!$n_id) {
              $comb_array = serialize($comb_array);
              db_insert('uc_product_adjustments')
                ->fields(array('nid'=> $nid, 'combination'=> $comb_array, 'model'=> $variant_data1['variantid']))
                ->execute();

            } else {
              if ($n_id != $nid) {
                $message .= "The variation " . $variant_data1['variantid'] . " is already created";
              }
            }
            if ($stock_module_avialbe == '1') {
              $n_id = db_query("SELECT nid from {uc_product_stock} where  sku  = " . $this->mySQLSafe($variant_data1['variantid']) . "")->fetchField();


              if (!$n_id) {
                db_insert('uc_product_stock')
                  ->fields(array('sku'=> $variant_data1['variantid'], 'nid'=> $nid, 'active'=> 1, 'stock'=> $variant_data1['variantqty'], 'threshold'=> 0))
                  ->execute();

              } else { //echo $n_id."===".$nid;
                if ($n_id == $nid) {
                  db_update('uc_product_stock')
                    ->fields(array('stock'=> $variant_data1['variantqty']))
                    ->condition('sku', $variant_data1['variantid'])
                    ->condition('nid', $nid)
                    ->execute();

                } else {
                  //$message.="The variation ".$variant_data1['variantid']." is assigned to another product. Please do it manually ";
                }
              }
            }
            $Variant = new WG_Variant();
            $Variant->setStatus($message ? $message : 'Success');
            $Variant->setVarientID($nid);
            $Variant->setVariantSku(htmlentities($variant_data1['variantid']));
            //$Variant->setProductName(htmlentities($product));
            $Item->setItemVariants($Variant->getVariant());
            //print_r($Variant->getVariant());

          }
          unset($nid, $variant_data1);
        }

        $Items->setItems($Item->getItem());

        unset($attributes, $n_id, $attribute_option, $attribute_option1);
        unset($categoryid, $catid);
        $itemsCount++;

        $i++;
      }
      unset($variant_data, $all_options);
      //print_r($Items->getItems());
      //die("hihi");

      return $this->response($Items->getItems());
    }


    function GetImage($username, $password, $data, $storeid = 1, $others)
    {


      global $version, $drupal_version;
      global $user, $categoryid, $version;
      $status = $this->auth_user($username, $password);
      if ($status != '0') {
        return $status;
      }
      $files_dir = file_stream_wrapper_get_instance_by_uri('public://')->getDirectoryPath();
      $Items = new WG_Items();
      $Items->setStatusCode('0');
      $Items->setStatusMessage('All Ok');

      //$requestArray = json_decode($item_json_array,true);
      $requestArray = $data;

      if (!is_array($requestArray)) {
        $Items->setStatusCode('9997');
        $Items->setStatusMessage('Unknown request or request not in proper format');
        return $this->response($Items->getItems());
      }

      if (count($requestArray) == 0) {
        $Items->setStatusCode('9996');
        $Items->setStatusMessage('REQUEST tag(s) doesnt have correct input format');
        return $this->response($Items->getItems());
      }

      $itemsCount = 0;
      $itemsProcessed = 0;

      # Go throught items
      $itemsCount = 0;
      $_err_message_arr = Array();

      $attributes = array();
      $attribute_option = array();

      $responseArray = array();

      foreach ($requestArray as $kv=> $vItem) //request
      {

        $status = "Success";
        $productID = $vItem['ItemID'];

        define('DIR_IMAGE_ECC', DRUPAL_ROOT . '/' . $files_dir);


        //Code to set image node
        if ($drupal_version > "6") {
          $image_query = db_query("SELECT * FROM content_field_image_cache AS ctp INNER JOIN files AS f ON ctp.field_image_cache_fid=f.fid WHERE  ctp.nid = '" . $productID . "'");
          while ($image_result = $image_query->fetchObject()) {
            if ($image_result->filename != '' && strlen($image_result->filename) > 0) {

              $products_image = $image_result->filepath;

              #$responseArray = array();
              $responseArray['ItemID'] = $productID;
              $responseArray['Image'] = base64_encode(file_get_contents(DIR_IMAGE_ECC . $products_image));
              #$Items->setItems($responseArray);
              break;
            }
          }
        } else {

          $image_query = db_query("SELECT * FROM content_type_product AS ctp INNER JOIN files AS f ON ctp.field_image_fid =f.fid WHERE  ctp.nid = '" . $productID . "'");
          #echo $drupal_version;die('reached');
          while ($image_result = db_fetch_object($image_query)) {
            if ($image_result->filename != '' && strlen($image_result->filename) > 0) {
              $products_image = $image_result->filename;

              $responseArray['ItemID'] = $productID;
              $responseArray['Image'] = base64_encode(file_get_contents(DIR_IMAGE_ECC . '/' . $products_image));
              #$Items->setItems($responseArray);
              break;
            }
          }
        }

        //End code to set image node
        break;

      }

      if (count($responseArray) > 0) {
        $Items->setItems($responseArray);
      }
      //print_r($Items->getItems());
      //die("hihi");

      return $this->response($Items->getItems());


    }


    function addItemImage($itemid, $image, $storeid = 1)
    {


      global $user, $version, $drupal_version;

      #If image already exist in folder then uberart create a new image for it. This code performing this task.
      $module_directory = DRUPAL_ROOT . $files_dir;
      $dh = opendir($module_directory);
      $files_dir = file_stream_wrapper_get_instance_by_uri('public://')->getDirectoryPath();
      $image_name = time() . '.jpg';
      define('DIR_IMAGE_ECC', DRUPAL_ROOT . '/' . $files_dir);
      #echo DIR_IMAGE_ECC;die('reached');
      //Base 64 encoded string $image
      $unsaved = $image;

      $str = base64_decode($image);
      if (substr(decoct(fileperms(DIR_IMAGE_ECC)), 2) == '777') {

        $fp = fopen(DIR_IMAGE_ECC . '/' . $image_name, 'w+');
        fwrite($fp, $str);
        fclose($fp);

        $dst = DIR_IMAGE_ECC . '/imagefield_thumbs/';
        if (!is_dir($dst)) {
          mkdir($dst, 0777);
          chmod($dst, 0777);
        }
        unset($dst);

        $fp = fopen(DIR_IMAGE_ECC . '/imagefield_thumbs/' . $image_name, 'w+');
        fwrite($fp, $str);
        fclose($fp);

        $dst = DIR_IMAGE_ECC . '/imagecache/product/';
        if (!is_dir($dst)) {
          mkdir($dst, 0777);
          chmod($dst, 0777);
        }
        unset($dst);

        $fp = fopen(DIR_IMAGE_ECC . '/imagecache/product/' . $image_name, 'w+');
        fwrite($fp, $str);
        fclose($fp);

        $dst = DIR_IMAGE_ECC . '/imagecache/product_list/';
        if (!is_dir($dst)) {
          mkdir($dst, 0777);
          chmod($dst, 0777);
        }
        unset($dst);

        $source = DIR_IMAGE_ECC . '/imagecache/product/' . $image_name;
        $destination = DIR_IMAGE_ECC . '/imagecache/product_list/' . $image_name;
        //image_gd_resize($source, $destination, $width, $height);

        #$fp = fopen(DIR_IMAGE_ECC.'/imagecache/product_list/'.$image_name, 'w+');
        #fwrite($fp, $str);
        #fclose($fp);

        $dst = DIR_IMAGE_ECC . '/imagecache/uc_thumbnail/';
        if (!is_dir($dst)) {
          mkdir($dst, 0777);
          chmod($dst, 0777);
        }
        unset($dst);

        $fp = fopen(DIR_IMAGE_ECC . '/imagecache/uc_thumbnail/' . $image_name, 'w+');
        fwrite($fp, $str);
        fclose($fp);

        $dst = DIR_IMAGE_ECC . '/imagecache/cart/';
        if (!is_dir($dst)) {
          mkdir($dst, 0777);
          chmod($dst, 0777);
        }
        unset($dst);

        $fp = fopen(DIR_IMAGE_ECC . '/imagecache/cart/' . $image_name, 'w+');
        fwrite($fp, $str);
        fclose($fp);


        $user = user_load(array('name' => $username));
        $path = DIR_IMAGE_ECC . '/' . $image_name;
        $size = filesize($path);
        $image_info = image_get_info($path);
        $filemime = $image_info['mime_type'];
        $filepath = 'public://' . $image_name;
        $timestamp = time();
        //db_query("INSERT INTO {files} ( uid, filename, filepath, 	filemime, filesize,status,timestamp) VALUES (".$user->uid.", '".$image_name."','".$filepath."', '".$filemime."', ".$size.",0,$timestamp)");
        $queryinsert = db_insert('{file_managed}');
        $queryinsert->fields(array(
          'uid'       => 1,
          'filename'  => $image_name,
          'uri'       => $filepath,
          'filemime'  => $filemime,
          'filesize'  => $size,
          'status'    => 0,
          'timestamp' => $timestamp
        ));
        $fid = $queryinsert->execute();

        db_query("UPDATE {file_managed} SET status = '1' WHERE fid = '" . $fid . "' ");

        $queryinsert = db_insert('{field_data_uc_product_image}');
        $queryinsert->fields(array(
          'entity_type'          => 'node',
          'bundle'               => 'product',
          'entity_id'            => $itemid,
          'delta'                => 0,
          'revision_id'          => $itemid,
          'language'             => 'und',
          'uc_product_image_fid' => $fid

        ));
        $uc_fid = $queryinsert->execute();

        //db_query("INSERT INTO {field_data_uc_product_image} (entity_type, bundle, entity_id,revision_id,language,uc_product_image_fid,uc_product_image_alt,uc_product_image_title,uc_product_image_width,uc_product_image_height) VALUES ('node','product','".$itemid."','".$itemid."','und','".$fid.",'','','',''')");
        //db_query("INSERT INTO {file_managed} ( uid, filename, uri, 	filemime, filesize,status,timestamp) VALUES (1, '".$image_name."','".$filepath."', '".$filemime."', ".$size.",0,$timestamp)");
        //InsertQuery::execute();
        //$fid = db_last_insert_id('files', 'fid');

        //$field_image_data='a:2:{s:3:"alt";s:0:"";s:5:"title";s:0:"";}';
        //db_query("INSERT INTO {cache_field} ( data,expire, created, serialized) VALUES ('".$unsaved."', '0',$timestamp, '1')");
        //db_query("INSERT INTO {cache_form} ( data,expire, created, serialized) VALUES ('".$unsaved."', $timestamp,$timestamp, '1')");
        //if($version['version'] <= '6.x-2.3') {
        /*if($version <= '6.x-2.3') {
				db_query("INSERT INTO {content_type_product} (vid,nid,field_image_fid,field_image_list) VALUES (".$itemid.",".$itemid.",".$fid.",1)");
			} else {
				$is_delta = db_query("SELECT delta FROM {content_field_image_cache} where nid = '".$itemid."' order by delta DESC");
				$is_delta = db_result($is_delta);
				if($is_delta >= 0) {$is_delta	=	$is_delta+1;} else {$is_delta = 0;}
				//echo "INSERT INTO {content_field_image_cache} (vid,nid,delta,field_image_cache_fid,field_image_cache_list) VALUES (".$itemid.",".$itemid.",".$is_delta.",".$fid.",1)";
				db_query("INSERT INTO {content_field_image_cache} (vid,nid,delta,field_image_cache_fid,field_image_cache_list) VALUES (".$itemid.",".$itemid.",".$is_delta.",".$fid.",1)");
			}*/


        return true;
      } else {

        return false;
      }

      #return $this->response($Items->getItems());
    }


    #
    # function to return the store Manufacturer list so synch with QB inventory
    #
    function getManufacturers($username, $password)
    {

      $status = $this->auth_user($username, $password);
      if ($status != '0') {
        return $status;
      }
      $Manufacturers = new WG_Manufacturers();
      $Manufacturers->setStatusCode('0');
      $Manufacturers->setStatusMessage('All Ok');
      $Manufacturer = new Manufacturer();
      $Manufacturers->setManufacturers($Manufacturer->getManufacturer());
      return $this->response($Manufacturers->getManufacturers());
    }


    public function mySQLSafe($value, $quote = "'")
    {
      //We are going to do this to keep the functions from contantly running
      if (empty($this->magic)) {
        $this->magic = (bool)get_magic_quotes_gpc();
      }
      if (empty($this->escape)) {

        if (function_exists('mysql_real_escape_string')) {
          $this->escape = 'mysql_real_escape_string';
        } else {
          $this->escape = 'mysql_escape_string';
        }
      }

      if (empty($value)) {
        return $quote . $quote;
      }

      ## Stripslashes
      if ($this->magic) {
        $value = stripslashes($value);
      }

      ## Strip quotes if already in
      $value = str_replace(array("\\'", "'"), "&#39;", $value);

      ## Quote value
      if ($this->escape == 'mysql_real_escape_string' && !empty($this->db)) {
        $value = mysql_real_escape_string($value, $this->db);
      } else {
        $value = mysql_escape_string($value);
      }

      $value = $quote . trim($value) . $quote;

      return $value;
    }

    #Add new functions related to customer
    public function addCustomers($username, $password, $data, $storeid = 1, $others = '')
    {
      global $base_url, $version, $drupal_version;
      $pos = strpos($base_url, 'sites');
      if ($pos != "") {
        $base_url = str_replace("/sites/all/modules/eccv3", "", $base_url);
      } elseif (strpos($base_url, 'modules')) {
        $base_url = str_replace("/modules/eccv3", "", $base_url);
      }
      $status = $this->auth_user($username, $password);
      if ($status != "0") {
        return $status;
      }
      $storeId = $storeid;

      $Customers = new Customers();
      $Customers->setStatusCode('0');
      $Customers->setStatusMessage('All Ok');

      $requestArray = $data;
      //$requestArray = json_decode($item_json_array, true);
      if (!is_array($requestArray)) {
        $Items->setStatusCode('9997');
        $Items->setStatusMessage('Unknown request or request not in proper format');
        return $this->response($Items->getItems());
      }

      if (count($requestArray) == 0) {
        $Items->setStatusCode('9996');
        $Items->setStatusMessage('REQUEST tag(s) doesnt have correct input format');
        return $this->response($Items->getItems());
      }

      foreach ($requestArray as $k=> $vCustomer) {

        //$customer  = new Mage_Customer_Model_Customer();
        $Email = $vCustomer['Email'];
        $CustomerId = $vCustomer['CustomerId'];
        $firstname = $vCustomer['FirstName'];
        $middlename = $vCustomer['MiddleName'];
        $lastname = $vCustomer['LastName'];
        $company = $vCustomer['Company'];
        $street1 = $vCustomer['Address1'];
        $street2 = $vCustomer['Address2'];
        $city = $vCustomer['City'];
        $region = $vCustomer['State'];
        $postcode = $vCustomer['Zip'];
        $country_code = $vCustomer['Country'];
        $tel = $vCustomer['Phone'];
        $group = $vCustomer['CustomerGroup'];
        $country_id = '';

        $email_exist = user_load_by_mail($Email);

        //$email_exist=user_load_by_mail($Email);

        if (!$email_exist->uid) {

          // setup the details
          $pass = user_password(8);

          $user = array(
            'name'     => $Email,
            'pass'     => $pass, // field to save in the database
            'password' => $pass, // required to send in notification mail
            'mail'     => $Email,
            'access'   => '0',
            'status'   => 1,
            'timezone' => 0,
            'init'     => $Email
          );

          $account = user_save(null, $user);
          $account->password = $pass; // Add plain text password into user account to generate mail tokens.
          if ($vCustomer['IsNotifyCustomer'] == 'Y') {
            _user_mail_notify('status_activated', $account);
            //_user_mail_notify('register_admin_created', $account);
          }
          $new_user_id = $account->uid;

          $Customer = new Customer();
          $Customer->setCustomerId($new_user_id);
          $Customer->setStatus('Success');
          $Customer->setFirstName($firstname);
          $Customer->setMiddleName($middlename);
          $Customer->setLastName($lastname);
          $Customer->setCustomerGroup($group);
          $Customer->setemail($Email);
          $Customer->setCompany($company);
          $Customer->setAddress1($vCustomer['Address1']);
          $Customer->setAddress2($vCustomer['Address2']);
          $Customer->setCity($city);
          $Customer->setState($region);
          $Customer->setZip($postcode);
          $Customer->setCountry($country_code);
          $Customer->setPhone($tel);


          $Customers->setCustomer($Customer->getCustomer());


        } else {


          $Customer = new Customer();
          $Customer->setStatus('Customer email already exist');
          $Customer->setCustomerId($email_exist->uid);
          $Customer->setFirstName($firstname);
          $Customer->setLastName($lastname);
          $Customer->setemail($Email);
          $Customer->setCompany($company);
          $Customers->setCustomer($Customer->getCustomer());

        }
      }
      return $this->response($Customers->getCustomers());
    }

    function getCustomersNew($username, $password, $datefrom, $customerid, $limit, $storeid = 1, $others)
    {
      global $base_url, $version, $drupal_version;
      $datefrom = $datefrom ? $datefrom : 0;
      $status = $this->auth_user($username, $password);
      if ($status != "0") {
        return $status;
      }
      $storeId = $storeid;
      $Customers = new Customers();

      $customersArray = $this->_getCustomer($customerid, $datefrom, $storeId, $limit);

      $result = db_query('SELECT count(*) as cnt FROM {users}');
      $record = $result->fetchObject();

      //$customersArray = $customersObj->toarray();
      $no_customer = false;
      if (count($customersArray) <= 0) {
        $no_customer = true;
      }
      $Customers->setStatusCode($no_customer ? "0" : "0");
      $Customers->setStatusMessage($no_customer ? "No Customer returned" : "Total Customer:" . count($customersArray));
      $Customers->setTotalRecordFound($record->cnt ? $record->cnt : '0');
      $Customers->setTotalRecordSent(count($customersArray) ? count($customersArray) : '0');

      foreach ($customersArray as $customer) {

        $Customer = new Customer();
        $Customer->setCustomerId($customer["entity_id"]);
        $Customer->setFirstName($customer["firstname"]);
        $Customer->setMiddleName($customer["middlename"]);
        $Customer->setLastName($customer["lastname"]);
        $Customer->setCustomerGroup($customer["group_id"]);
        $Customer->setcompany($customer["company"]);
        $Customer->setemail($customer["email"]);
        $Customer->setAddress1($customer["street"]);
        $Customer->setAddress2("");
        $Customer->setCity($customer["city"]);
        $Customer->setState($customer["region"]);
        $Customer->setZip($customer["postcode"]);
        $Customer->setCountry($customer["country"]);
        $Customer->setPhone($customer["telephone"]);
        $Customer->setCreatedAt($customer["created_at"]);
        $Customer->setUpdatedAt($customer["updated_at"]);

        $Customers->setCustomer($Customer->getCustomer());


      }


      return $this->response($Customers->getCustomers());
    }

    public function _getCustomer($start_item_no, $datefrom, $storeId, $limit)
    {
      $start_no = 0;
      global $base_url, $version, $drupal_version;
      $rowData = array();

      $result = db_query('SELECT uid FROM {users} where uid > ' . $start_item_no . ' order by uid limit  ' . $start_no . ',' . $limit . '');
      if ($drupal_version < "7") {
        while ($record = db_fetch_object($result)) {
          $user = user_load($record->uid);

          $email = $user->mail;
          $group_id = '';
          $created_at = $user->created;
          $updated_at = $user->created;
          if ($user->uid > 0) {
            $rowData[] = array('entity_id'=> $user->uid, 'email'=> $email, 'group_id'=> $group_id, 'firstname' => $user->name, 'middlename' => "", 'lastname' => "", 'company' => "", 'city' => "", 'country'=> "", 'region' => "", 'postcode' => "", 'telephone' => "", 'fax' => "", 'street' => "", 'created_at' => $created_at, 'updated_at' => $updated_at);
          }

        }
      } else {

        while ($record = $result->fetchObject()) {
          $user = user_load($record->uid);

          $email = $user->mail;
          $group_id = '';
          $created_at = $user->created;
          $updated_at = $user->created;
          if ($user->uid > 0) {
            $rowData[] = array('entity_id'=> $user->uid, 'email'=> $email, 'group_id'=> $group_id, 'firstname' => $user->name, 'middlename' => "", 'lastname' => "", 'company' => "", 'city' => "", 'country'=> "", 'region' => "", 'postcode' => "", 'telephone' => "", 'fax' => "", 'street' => "", 'created_at' => $created_at, 'updated_at' => $updated_at);
          }

        }
      }


      return $rowData;

    }

    #
    #
    # Update Orders via status type method
    # Will update Order Notes and tracking number of  order
    # Input parameter Username,Password, array (OrderID,ShippedOn,ShippedVia,ServiceUsed,TrackingNumber)
    #
    function AutoSyncOrder($username, $password, $data, $statustype, $storeid, $others)
    {

      global $version, $drupal_version, $files;
      $status = $this->auth_user($username, $password);
      if ($status != '0') {
        return $status;
      }

      $Orders = new WG_Orders();
      //$response_array = json_decode($Orders_json_array,true);
      $response_array = $data;
      if (!is_array($response_array)) {
        $Orders->setStatusCode("9997");
        $Orders->setStatusMessage("Unknown request or request not in proper format");
        return $this->response($Orders->getOrders());
        exit();
      }
      if (count($response_array) == 0) {
        $Orders->setStatusCode("9996");
        $Orders->setStatusMessage("REQUEST array(s) doesnt have correct input format");
        return $this->response($Orders->getOrders());
        exit();
      }
      if (count($response_array) == 0) {
        $no_orders = true;
      } else {
        $no_orders = false;
      }
      $Orders->setStatusCode($no_orders ? "1000" : "0");
      $Orders->setStatusMessage($no_orders ? "No new orders." : "All Ok");
      if ($no_orders) {
        return json_encode($response_array);
      }

      # Fetch All order status of Cart
      $sql = db_query("SELECT order_status_id,title from {uc_order_statuses} ");
      //while($status_all = db_fetch_array($sql))
      while ($status_all = $sql->fetchObject()) {
        $statuses[$status_all->title] = $status_all->order_status_id;
      }


      $i = 0;


      foreach ($response_array as $k=> $v) //request
      {

        if (isset($order_wg)) {
          unset($order_wg);
        }
        foreach ($v as $k1=> $v1) {
          $order_wg[$k1] = $v1;
        }

        # for fetching user id
        $user = db_query("SELECT uid,name,pass FROM {users} WHERE name = " . $this->mySQLSafe($username) . " ")->fetchObject();
        $uid = $user->uid;

        $order_id = $order_wg['OrderID'];
        //$order_id = $order_wg['Orderno'];
        $time = time();
        switch ($statustype) {

          case 'paymentUpdate':

            $payment_exist = db_query("SELECT order_id FROM {uc_payment_receipts} WHERE order_id = " . $this->mySQLSafe($order_id) . " ")->fetchObject();
            if ($payment_exist) {
              $result1 = "error";
            } else {

              foreach ($order_wg['Payments'] as $payment) {
                db_insert('uc_payment_receipts')
                  ->fields(array('order_id' => $order_id, 'uid' => $uid, 'method' => $payment['MethodName'], 'amount' => ($order_wg['OrderTotal']), 'received'=> $time))
                  ->execute();
              }

              db_update('uc_orders')
                ->fields(array('modified ' => $time))
                ->condition('order_id', $order_id)
                ->execute();

              $result1 = "Success";
            }

            break;

          case 'statusUpdate':
            break;

          case 'notesUpdate':
            $note_updated = true;

            db_insert('uc_order_admin_comments')
              ->fields(array('order_id' => $order_id, 'uid' => $uid, 'message' => htmlspecialchars($order_wg['OrderNotes']), 'created'=> $time))
              ->execute();

            db_update('uc_orders')
              ->fields(array('modified ' => $time))
              ->condition('order_id', $order_id)
              ->execute();
            $result1 = "Success";
            break;

          case 'shipmentUpdate':
            $order_id = $order_wg['OrderID'];

            $tracking_num = $order_wg['TrackingNumber'];

            $method = $order_wg['ShippedVia'];
            $carrier = $order_wg['ServiceUsed'];

            $product_new = array();

            $order_details = uc_order_load($order_id);

            foreach ($order_details->products as $item) {


              $item_qty_shipped = $item->qty;
              $item_name = $item->mode;
              $item_sku = $item->order_product_id;
              $item_id = $item->order_product_id;

              /* shipment check before creation */

              $shipping_types_products = array();
              $order_details = uc_order_load($order_id);

              foreach ($order_details->products as $product) {
                if ($product->data['shippable']) {
                  $product->shipping_type = uc_product_get_shipping_type($product);
                  $shipping_types_products[$product->shipping_type][] = $product;
                }
              }

              $shipping_type_weights = variable_get('uc_quote_type_weight', array());

              $result = db_query("SELECT op.order_product_id, SUM(pp.qty) AS quantity FROM {uc_packaged_products} AS pp LEFT JOIN {uc_packages} AS p ON pp.package_id = p.package_id LEFT JOIN {uc_order_products} AS op ON op.order_product_id = pp.order_product_id WHERE p.order_id = :id GROUP BY op.order_product_id", array(':id' => $order_id));
              $packaged_products = $result->fetchAllKeyed();

              $shipping_type_options = uc_quote_shipping_type_options();
              foreach ($shipping_types_products as $shipping_type => $products) {

                foreach ($products as $product) {
                  $unboxed_qty = $product->qty;

                  if (isset($packaged_products[$product->order_product_id])) {
                    $unboxed_qty -= $packaged_products[$product->order_product_id];

                  }

                  if ($product->order_product_id == $item_id) {

                    if ($unboxed_qty > 0) {
                      $product_new[$item_id] = (object)array("checked"=> 1, "qty"=> $item_qty_shipped);
                    }
                  }
                }
              }

              //$product[]["qty"]=$item_qty_shipped;

            }


            if (count($product_new) > 0) {

              $package = (object)array("products"=> $product_new, "shipping_type"=> 'small_package', "order_id"=> $order_id, "data"=> array("attributes" => array("shippable"=> 1, "type"=> "product", "module"=> "uc_product")));
              $order_obj = uc_order_load($order_id, $reset = false);
              $address = explode("<BR />", uc_order_address($order_obj, "billing"));

              $first_name = explode(" ", $address[1]);
              $city_name = explode(",", $address[4]);
              $postal_code = explode(" ", $city_name[1]);

              $dest = (object)array("email"=> "", "first_name"=> $first_name[0], "last_name"=> $first_name[1], "company"=> $address[0], "street1"=> $address[2], "street2"=> $address[3], "city"=> $city_name[0], "postal_code"=> $postal_code[2]);

              uc_shipping_package_save($package);
              $yourdatetime = time();

              $shipment_obj = (object)array("order_id"=> $order_id, "destination"=> $dest, "packages"=> array($package), "shipping_method"=> $method, "accessorials"=> $method, "carrier"=> $carrier, "tracking_number"=> $tracking_num, "ship_date"=> $yourdatetime, "expected_delivery"=> $yourdatetime);

              uc_shipping_shipment_save($shipment_obj);
              $result1 = "Success";

            } else {
              $result1 = "error";

            }

            break;
        }

        $sql1 = "SELECT o.order_status, o.modified,n.message FROM {uc_orders}  o,{uc_order_admin_comments} n  WHERE n.order_id IN (" . $order_id . ") and o.order_id = n.order_id order by n.comment_id DESC";

        $result5 = db_query($sql1);
        while ($test1 = $result5->fetchObject()) {
          $orders2[] = $test1;


        }

        $Order = new WG_Order();
        $Order->setOrderID($order_id);
        $Order->setStatus($result1);
        $Order->setLastModifiedDate(date("m-d-Y H:i:s", $orders2[0]->modified));
        $Order->setOrderNotes(htmlspecialchars(strip_tags($orders2[0]->message)));
        $Order->setOrderStatus($orders2[0]->order_status);

        $Orders->setOrders($Order->getOrder());

      }
      return $this->response($Orders->getOrders());
    }

  } // Class end

  ob_clean();
  ob_start();

  if (isset($_REQUEST['request'])) {
    $wpObject = new Webgility_Ecc_UB();

    $wpObject->parseRequest();
  }


?>

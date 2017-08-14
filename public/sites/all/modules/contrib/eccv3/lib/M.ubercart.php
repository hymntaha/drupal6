<?php
/*
===================================
© Copyright Webgility LLC 2007-2011
----------------------------------------
This file and the source code contained herein are the property of Webgility LLC
and are protected by United States copyright law. All usage is restricted as per 
the terms & conditions of Webgility License Agreement. You may not alter or remove 
any trademark, copyright or other notice from copies of the content.

The code contained herein may not be reproduced, copied, modified or redistributed in any form
without the express written consent by an officer of Webgility LLC.

File last updated		:   04/01/2012
Drupal version 			:	drupal-5.x - 7.x
Ubercart version 		:	ubercart 5.x-1.5  - 7.x-3.0-beta3
===================================
*/


# Code for changing directory and accessing include folder
$public_directory = dirname($_SERVER['PHP_SELF']);
$directory_array = explode('/', $public_directory); 
$key = array_search('sites',$directory_array);
$path = "";
$path1 ="";


if(isset($key) && $key!='')
 { 
 	$i=$key;
	for($i;$i<count($directory_array);$i++)
	{ 
		$path = '../';
		$path1.=$path;
	}
	
}
else
{
	for($i=2;$i<count($directory_array);$i++)
	{
		$path = '../';
		$path1.=$path;
	}
}

chdir($path1);
# current directory
require_once 'includes/bootstrap.inc';

if(file_exists('sites/default/settings.php'))
{
	require_once 'sites/default/settings.php';
}
if(file_exists('includes/database/query.inc'))
{
	require_once 'includes/database/query.inc';
}
if(file_exists('includes/database/database.inc'))
{
	require_once 'includes/database/database.inc';
}

if(file_exists('includes/cache.inc'))
{
	require_once 'includes/cache.inc';
}
if(file_exists('includes/module.inc'))
{
	require_once 'includes/module.inc';
}
if(file_exists('includes/password.inc'))
{
	require_once 'includes/password.inc';
}

if(file_exists('includes/image.gd.inc'))
{
	require_once 'includes/image.gd.inc';
}

define('DRUPAL_ROOT', getcwd());
//# current directory
//require_once 'includes/common.inc';
//require_once 'modules/system/system.module';

if(file_exists('modules/system/system.info'))
{
	$file_arr=file("modules/system/system.info");
	$file_output = array_reverse($file_arr);
	$dv = $file_output[3];
	$fv = explode('"',$dv);
	$drupal_version_array = explode('.',$fv[1]);
	$drupal_version = $drupal_version_array[0];
}
else
{
	$drupal_full_version = VERSION;
	
	$drupal_version_array = explode('.',$drupal_full_version);
	
	$drupal_version = $drupal_version_array[0];
}

if($drupal_version >= '7')
{
	//$var = $phases['DRUPAL_BOOTSTRAP_FULL'];
	//drupal_bootstrap($var,$new_phase = TRUE);
	drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL,$new_phase = TRUE);
	
	$files = system_rebuild_module_data();
	foreach ($files as $filename => $file) 
	{ 
	
		$form['name'][$filename] = $file->info['name'];
		
		$form['package'][$filename] = $file->info['package'];
		$form['version'][$filename] = $file->info['version'];
		if( ($form['name'][$filename] == 'Store')&&($form['package'][$filename]=='Ubercart - core'))
		{
			$version = $form['version'][$filename];
		} 
	} 
	
}
else
{
	if($drupal_version == '5')
	{
		### For Drupal 5
		if(file_exists('includes/database.mysql.inc'))
		{ 
			require_once 'includes/database.mysql.inc';
		}
		
	}
	drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
	$files = module_rebuild_cache();
			
	foreach ($files as $filename => $file) 
	{
		$form['name'][$filename] = $file->info['name'];
		
		$form['package'][$filename] = $file->info['package'];
		$form['version'][$filename] = $file->info['version'];
		if( ($form['name'][$filename] == 'Store')&&($form['package'][$filename]=='Ubercart - core'))
		{
			$version = $form['version'][$filename];
		}
	}
}






# DO NOT DOWNLOAD ORDERS IN FAILED, DECLINED AND NOT FINISHED STATES
# I=Not finished, Q=Queued, P=Processed, B=Backordered, D=Declined, F=Failed, C=Complete
require_once("M.WgCommon.php");

class Webgility_Ecc_UB extends WgCommon
{	
	
	
	function auth_user($username,$password)
	{
		global $version,$drupal_version;
	
		$WgBaseResponse = new WgBaseResponse();		
		try
		{
	 	   // return true;
		  
		  	if($drupal_version > "6")
			{
		  		$account = db_query("SELECT uid,name,pass FROM {users} WHERE  LOWER(name) = LOWER(:username)", array(':username' => $username))->fetchObject();
				$num_rows = $account->uid?1:0;
			}
			else
			{
				
				$user = db_fetch_array(db_query("SELECT uid,name,pass FROM {users} u WHERE LOWER(name) = LOWER(".$this->mySQLSafe($username).")"));
				$num_rows = $user['uid']?1:0;
			}
			
		   if ($num_rows == 0 )
			{
				$WgBaseResponse->setStatusCode('1');
				$WgBaseResponse->setStatusMessage('Invalid login. Authorization failed');
				
				return $this->response($WgBaseResponse->getBaseresponse());		   
				//exit;
		   }
			//$password = md5($password);
		   if($drupal_version > "6")
		   { 
		   		$check = user_check_password($password, $account);
				if($check!= 1) 
				{ 
					$WgBaseResponse->setStatusCode('2');
					$WgBaseResponse->setStatusMessage('Invalid password. Authorization failed');
					return $this->response($WgBaseResponse->getBaseresponse());		
				}
		   }
		   else
		   {
				$password = md5($password);
				if( $user['pass'] != $password) 
				{
					
						$WgBaseResponse->setStatusCode('2');
						$WgBaseResponse->setStatusMessage('Invalid password. Authorization failed');
						return $this->response($WgBaseResponse->getBaseresponse());		   
						//exit;
					
				} 
			} 
			
		   return 0;

       }
	   catch (Exception $e)
	   {
			$WgBaseResponse->setStatusCode('1');
			$WgBaseResponse->setStatusMessage('Invalid login. Authorization failed');
			return $this->response($WgBaseResponse->getBaseresponse());		   
			//exit;
       }
	   
	}
	
	
	function getVersion()
	{
		global $version,$drupal_version;
		if($drupal_version > "6")
		{
			$files = system_rebuild_module_data();
		}
		else
		{
			$files = module_rebuild_cache();
		}
		if(isset($files))
		{
			if($files['uc_credit']->status == 0)
			{
				$message = "Please enable Credit Card module";
				
			}
			elseif($files['taxonomy']->status == 0)
			{
				$message = "Please enable Taxonomy module";
			}
			elseif($files['uc_payment']->status == 0)
			{
				$message = "Please enable Payemnt module";
			}
			elseif($files['uc_shipping']->status == 0)
			{
				$message = "Please enable Shipping module";
			}
			elseif($files['uc_order']->status == 0)
			{
				$message = "Please enable Order module";
			}
			elseif($files['uc_taxes']->status == 0)
			{
				$message = "Please enable Tax module";
			}
			
			if($version)
			{
				$resultArr['version'] = $version;
			}
			else
			{
				$resultArr['version'] = 0;
			}
			$resultArr['message'] = $message;
			
			return $resultArr;
		}
	
	}

	
	# retrive all order status
	function getOrderStatus($username,$password)
	{
		$status = $this->auth_user($username,$password);
		if($status !='0')
		{
			return $status;
		}
		
		
		foreach (uc_order_status_list('general') as $orderstatus1) {
			$firstset[] =$orderstatus1;
			$array = array($statusid1 => $status1);
		}
		 foreach (uc_order_status_list('specific') as $orderstatus2) {
			$secondset[] = $orderstatus2;
		}
		
		$orderstatus =  array_merge( $firstset, $secondset);
		$OrderStatuses = new WG_OrderStatuses();
	
		if($orderstatus)
		{
			$OrderStatuses->setStatusCode('0');
			$OrderStatuses->setStatusMessage('All Ok');	
			foreach($orderstatus as $iInfo) 
			{		
				$OrderStatus =new WG_OrderStatus();
				$OrderStatus->setOrderStatusID($iInfo['id']);
				$OrderStatus->setOrderStatusName($iInfo['title']);
				$OrderStatuses->setOrderStatuses($OrderStatus->getOrderStatus());
				
			}
		}	
		unset($orderstatus1,$orderstatus2,$orderstatus);
		return $this->response($OrderStatuses->getOrderStatuses());			
	}
	
	
	function getStores($username,$password,$store_type='ubercart')
	{
		global $version,$drupal_version;
			
		$status = $this->auth_user($username,$password);
		if($status !='0')
		{
			return $status;
		}
		
		//print_r($_SERVER);
		
		$store_name = variable_get('uc_store_name', NULL);
		
		$Stores = new WG_Storesinfo();		
		
		$Stores->setStatusCode('0');
		$Stores->setStatusMessage('All Ok');	
		
		$Store = new WG_Store();
		$Store->setStoreID('1');
		$Store->setStoreName(htmlspecialchars($store_name, ENT_NOQUOTES));
		$Store->setStoreWebsiteId('1');
		$Store->setStoreWebsiteName('Ubercart website');
		$Store->setStoreRootCategoryId('1');
		$Store->setStoreDefaultStoreId('1');
		$Store->setStoreType('ubercart');						
		$Stores->setstores($Store->getStore());										
		return $this->response($Stores->getStoresInfo());
	}
	
	function getCustomers($username,$password,$datefrom,$customerid=0,$limit,$storeid=1,$others)
	{
	
		global $version,$drupal_version;
		$status = $this->auth_user($username,$password);
		if($status !='0')
		{
			return $status;
		}
		
		
		$Customers = new WG_Customers();
		$Customers->setStatusCode('0');	
	
		$count_customer = 0; 
		
		/*$user_sql = db_query("SELECT * FROM {users} WHERE uid > $customerid ORDER BY name LIMIT 0, $limit");
		
		
		$result = db_query($user_sql);
		
		print_r($result);
		
		$customers = array();
		
		if($drupal_version > "6") {
			while ($test = $result->fetchObject()) { 
				$customers[]  = $test;
				print_r($test);
			} 
		} else {
			while ($test = db_fetch_array($result)) {
				print_r($test);
				$customers[]  = $test;		
			}
		}
		die('reached');*/
		
		$count_query_customer = db_result(db_query("SELECT  COUNT(uid) FROM {users} WHERE uid > $customerid order by uid"));
		
		
		$user_sql = db_query("SELECT * FROM {users} WHERE uid > $customerid ORDER BY uid LIMIT 0, $limit");
		
		while($iInfo = db_fetch_array($user_sql) )
		{ 
			//print_r($iInfo);
			if($iInfo['uid'] > 0) {
				$Customer = new WG_Customer();
				$Customer->setCustomerId($iInfo['uid']);
				$Customer->setFirstName(htmlentities($iInfo['name']), ENT_QUOTES);
				$Customer->setMiddleName('');
				$Customer->setLastName('');
				$Customer->setCustomerGroup('1');
				$Customer->setemail($iInfo['mail']);
				$Customer->setAddress1('');
				$Customer->setAddress2('');
				$Customer->setCity('');
				$Customer->setState('');
				$Customer->setZip('');
				$Customer->setCountry('');
				$Customer->setPhone('');
				$Customer->setCreatedAt(date("Y-m-d H:i:s", $iInfo['created']));
				$Customer->setUpdatedAt(date("Y-m-d H:i:s", $iInfo['access']));
				$Customer->setLifeTimeSale("");
				$Customer->setAverageSale("");
				
				$Customers->setCustomer($Customer->getCustomer());
				//End code to set customer information
				$count_customer++;
			}
		}
		
		$Customers->setStatusMessage("Total Customer:".$count_query_customer);
		$Customers->setTotalRecordFound((int)$count_query_customer);	
		$Customers->setTotalRecordSent((int)$count_customer);
		
		return $this->response($Customers->getCustomers());
	}
	
	function getStoreCustomerByIdForEcc($username,$password,$datefrom,$customerid,$limit,$storeid=1,$others) {
		$status = $this->auth_user($username,$password);
		if($status !='0')
		{
			return $status;
		}
		
		
		$Customers = new WG_Customers();
		$Customers->setStatusCode('0');	
	
		$count_customer = 0; 
		
		$user_sql = db_query("SELECT * FROM {users} WHERE uid = $customerid ORDER BY name");
		
		while($iInfo = db_fetch_array($user_sql) )
		{ 
			//print_r($iInfo);
			if($iInfo['uid'] > 0) {
				$Customer = new WG_Customer();
				$Customer->setCustomerId($iInfo['uid']);
				$Customer->setFirstName(htmlentities($iInfo['name']), ENT_QUOTES);
				$Customer->setMiddleName('');
				$Customer->setLastName('');
				$Customer->setCustomerGroup('1');
				$Customer->setemail($iInfo['mail']);
				$Customer->setAddress1('');
				$Customer->setAddress2('');
				$Customer->setCity('');
				$Customer->setState('');
				$Customer->setZip('');
				$Customer->setCountry('');
				$Customer->setPhone('');
				$Customer->setCreatedAt(date("Y-m-d H:i:s", $iInfo['created']));
				$Customer->setUpdatedAt(date("Y-m-d H:i:s", $iInfo['access']));
				$Customer->setLifeTimeSale("");
				$Customer->setAverageSale("");
				
				$Customers->setCustomer($Customer->getCustomer());
				//End code to set customer information
				$count_customer++;
			}
		}
		
		$Customers->setStatusMessage("Total Customer:".$count_customer);
		$Customers->setTotalRecordFound((int)$count_customer);	
		$Customers->setTotalRecordSent((int)$count_customer);
		
		return $this->response($Customers->getCustomers());
	}
	#
	# function to return the store item list so synch with QB inventory
	#
	
	function addItemImage($username,$password,$itemid,$image,$storeid=1) {
		
		
		global $user,$version,$drupal_version;
		//$version = getVersion();
		
		/*$postion_char		=	strpos(substr($_SERVER['PHP_SELF'],1), '/');
		$store_site_path	=	 'http://'.$_SERVER['HTTP_HOST'].'/'.substr($_SERVER['PHP_SELF'], 1, $postion_char).'/';*/
		
		$store_site_path	=	 'http://'.$_SERVER['HTTP_HOST'].'/';
		
		//$status = $this->auth_user($username,$password);
		//if($status !='0') {return $status;}
		$Items = new WG_Items();
		
		
		$module_directory = DRUPAL_ROOT.file_directory_path();
		$dh = opendir($module_directory);
		
		$image_name = time().'.jpg';
		define('DIR_IMAGE_ECC', DRUPAL_ROOT.'/'.file_directory_path());
		#echo DIR_IMAGE_ECC;die('reached');
		//Base 64 encoded string $image
		$str	=	base64_decode($image);
		if(substr(decoct(fileperms(DIR_IMAGE_ECC)),2) == '777') {
		
			$fp = fopen(DIR_IMAGE_ECC.'/'.$image_name, 'w+');
			fwrite($fp, $str);
			fclose($fp);
			
			$dst	=	DIR_IMAGE_ECC.'/imagefield_thumbs/'; if(!is_dir($dst)) {mkdir($dst, 0777);chmod($dst, 0777);}unset($dst);
			
			$fp = fopen(DIR_IMAGE_ECC.'/imagefield_thumbs/'.$image_name, 'w+');
			fwrite($fp, $str);
			fclose($fp);
			
			$dst	=	DIR_IMAGE_ECC.'/imagecache/product/'; if(!is_dir($dst)) {mkdir($dst, 0777);chmod($dst, 0777);}unset($dst);
			
			$fp = fopen(DIR_IMAGE_ECC.'/imagecache/product/'.$image_name, 'w+');
			fwrite($fp, $str);
			fclose($fp);
			
			$dst	=	DIR_IMAGE_ECC.'/imagecache/product_list/'; if(!is_dir($dst)) {mkdir($dst, 0777);chmod($dst, 0777);}unset($dst);
			
			$source	=	DIR_IMAGE_ECC.'/imagecache/product/'.$image_name;
			$destination	=	DIR_IMAGE_ECC.'/imagecache/product_list/'.$image_name;
			image_gd_resize($source, $destination, $width, $height);
			
			#$fp = fopen(DIR_IMAGE_ECC.'/imagecache/product_list/'.$image_name, 'w+');
			#fwrite($fp, $str);
			#fclose($fp);
			
			$dst	=	DIR_IMAGE_ECC.'/imagecache/uc_thumbnail/'; if(!is_dir($dst)) {mkdir($dst, 0777);chmod($dst, 0777);}unset($dst);
			
			$fp = fopen(DIR_IMAGE_ECC.'/imagecache/uc_thumbnail/'.$image_name, 'w+');
			fwrite($fp, $str);
			fclose($fp);
			
			$dst	=	DIR_IMAGE_ECC.'/imagecache/cart/'; if(!is_dir($dst)) {mkdir($dst, 0777);chmod($dst, 0777);}unset($dst);
			
			$fp = fopen(DIR_IMAGE_ECC.'/imagecache/cart/'.$image_name, 'w+');
			fwrite($fp, $str);
			fclose($fp);
			
			$user = user_load(array('name' => $username));
			$path	=	DIR_IMAGE_ECC.'/'.$image_name;
			$size = filesize($path);
			$image_info = image_get_info($path);
			$filemime = $image_info['mime_type'];
			$filepath = 'sites/default/files/'.$image_name;
			$timestamp = time();
			db_query("INSERT INTO {files} ( uid, filename, filepath, 	filemime, filesize,status,timestamp) VALUES (".$user->uid.", '".$image_name."','".$filepath."', '".$filemime."', ".$size.",0,$timestamp)");
			$fid = db_last_insert_id('files', 'fid');
			
			$field_image_data='a:2:{s:3:"alt";s:0:"";s:5:"title";s:0:"";}';
			
			//if($version['version'] <= '6.x-2.3') { 
			if($version <= '6.x-2.3') { 
				db_query("INSERT INTO {content_type_product} (vid,nid,field_image_fid,field_image_list) VALUES (".$itemid.",".$itemid.",".$fid.",1)");
			} else {
				$is_delta = db_query("SELECT delta FROM {content_field_image_cache} where nid = '".$itemid."' order by delta DESC");
				$is_delta = db_result($is_delta);
				if($is_delta >= 0) {$is_delta	=	$is_delta+1;} else {$is_delta = 0;}
				//echo "INSERT INTO {content_field_image_cache} (vid,nid,delta,field_image_cache_fid,field_image_cache_list) VALUES (".$itemid.",".$itemid.",".$is_delta.",".$fid.",1)";
				db_query("INSERT INTO {content_field_image_cache} (vid,nid,delta,field_image_cache_fid,field_image_cache_list) VALUES (".$itemid.",".$itemid.",".$is_delta.",".$fid.",1)");
			}
			db_query("UPDATE {files} SET status = '1' WHERE fid = '".$fid."' ");
			
			
			$Items->setStatusCode('0');
			$Items->setStatusMessage('All Ok');
			$Items->setItemImageFlag('1');
			
			$Item = new WG_Item();
			$site_image_url	=	trim(substr($GLOBALS['base_url'], 0, strpos($GLOBALS['base_url'],'sites')));
			$image_node_array['ItemImages']=array('ItemID'=>$itemid,'ItemImageID'=>$fid,'ItemImageFileName'=>$image_name,'ItemImageUrl'=>$site_image_url.$filepath);
			
			$Item->setItemImages($image_node_array['ItemImages']);
			$Items->setItems($Item->getItem()); 
			
		} else {
		
			$Items->setStatusCode('1');
			$Items->setStatusMessage('Images directory is not writeable.');
			$Items->setItemImageFlag('0');
		
		}
		
		return $this->response($Items->getItems());
	}
	
	
	function getItems($username,$password,$datefrom,$start,$limit,$storeid,$others)
	{ 
		global $version,$drupal_version;
		
		/*$postion_char		=	strpos(substr($_SERVER['PHP_SELF'],1), '/');
		$store_site_path	=	 'http://'.$_SERVER['HTTP_HOST'].'/'.substr($_SERVER['PHP_SELF'], 1, $postion_char).'/';*/
		
		$store_site_path	=	 'http://'.$_SERVER['HTTP_HOST'].'/';
		
		$status = $this->auth_user($username,$password);
		if($status !='0')
		{
			return $status;
		}
		$Items = new WG_Items();
		
		if($drupal_version > "6")
		{
			$count_query_product = db_query("SELECT  COUNT(n.nid) FROM {node}  n ,{uc_products} p, {node_type} np  where n.vid = p.vid and np.type = n.type and np.module = 'uc_product' and n.nid > $start  order by n.nid")->fetchField();
			
		}
		else
		{
			$count_query_product = db_result(db_query("SELECT  COUNT(n.nid) FROM {node}  n ,{uc_products} p, {node_type} np  where n.vid = p.vid and np.type = n.type and np.module = 'uc_product' and n.nid > $start  order by n.nid"));
		}
		
		$itemCountVar = 0;
		if($count_query_product>0)
		{
			if($drupal_version > "6")
			{
				$sql_parent = db_query("SELECT tid, parent from {taxonomy_term_hierarchy} ");
				while ($all = $sql_parent->fetchObject())
				{ 
					$all_parent[$all->tid] = $all->parent ;
				} 
				
			}
			else
			{
				$sql_parent = db_query("SELECT tid, parent from {term_hierarchy} ");
				while ($all = db_fetch_array($sql_parent))
				{
					$all_parent[$all['tid']] = $all['parent'] ;
				}
			}
			if($drupal_version<'6')
			{
				$taxes = uc_taxes_get_rates();
			}
			else
			{
				$taxes = uc_taxes_rate_load();	
			}
			if($taxes)
			{
				
				foreach ($taxes as $k=>$iInfo2) 
				{		
					foreach($iInfo2->taxed_product_types as $k2=>$value)
					{
						$taxable_arr[] = $value;	
					}
				}
			}
			
			$taxable_arr = array_unique($taxable_arr);
				
			
			$result1= db_query("SELECT DISTINCT n.nid, n.*,np.* FROM {node}  n ,{uc_products} p, {node_type} np  where n.vid = p.vid and np.type = n.type and np.module = 'uc_product' and n.nid > $start order by n.nid  limit 0,$limit");
			$Items->setStatusCode('0');
			$Items->setStatusMessage('All Ok');
			$Items->setTotalRecordFound($count_query_product?$count_query_product:'0');
			
			if($drupal_version > "6")
			{ 
				while ($iInfo1 =$result1->fetchObject()) 
				{	//$iInfo1->nid = '151';
					$iInfo_arr[] = $this->menu_get_item_v7("node/".$iInfo1->nid);	
					
					
				} 
				
				
				
			}
			else
			{ 
				while ($iInfo1 = db_fetch_object($result1)) 
				{	if($drupal_version == "5")
					{	
						
						$iInfo_arr[] = node_load($iInfo1->nid);	
					}
					else
					{
						//$iInfo_arr[] = $this->menu_get_item_v6("node/".$iInfo1->nid);
						//$iInfo1->nid = '418';
						$iInfo_arr[] = $this->menu_get_item_v6("node/".$iInfo1->nid);	
					
					}
				} //print_r($iInfo_arr);
			}
			
			foreach($iInfo_arr as $k=> $iInfo)
			{	
				if($drupal_version > "6")
				{ 
					
					$stock_obj = db_query("SELECT * FROM {uc_product_stock} WHERE  sku = '".$iInfo->model."'")->fetchObject();
					if(!empty($stock_obj))
					{

						$LowQtyLimit = $stock_obj->threshold;
						$stock = $stock_obj->stock;
						if($stock=='')
						$stock='0';
					}
					else
					{
						$stock='0';
					}
					
				}
				else
				{  
					$stock = db_fetch_array(db_query("SELECT * FROM {uc_product_stock} WHERE  sku = '".$iInfo->model."'"));
					if(!empty($stock))
					{
						$LowQtyLimit = $stock['threshold'];
						$stock = $stock['stock'];
						if($stock=='')
						$stock='0';
					}
					else
					{
						$stock='0';
					}
				}
				$Item = new WG_Item();
				$Item->setItemID($iInfo->nid);
				
				$Item->setItemCode($iInfo->model);
				$Item->setItemDescription($iInfo->title);
				if($drupal_version > "6")
				{
					$Item->setItemShortDescr($iInfo->body['und'][0]['value']);
				}
				else
				{
					$Item->setItemShortDescr($iInfo->body);
				}
				
			
				//Code to set image node
				if($drupal_version > "6")
				{
					$image_query = db_query("SELECT * FROM content_field_image_cache AS ctp INNER JOIN files AS f ON ctp.field_image_cache_fid=f.fid WHERE  ctp.nid = '".$iInfo->nid."'");
					while($image_result	=	$image_query->fetchObject()) {
						if($image_result->filename != '' && strlen($image_result->filename) > 0) {
							$site_image_url	=	trim(substr($GLOBALS['base_url'], 0, strpos($GLOBALS['base_url'],'sites')));	
							$image['ItemImages']=array('ItemID'=>$iInfo->nid,'ItemImageID'=>$image_result->fid,'ItemImageFileName'=>$image_result->filename,'ItemImageUrl'=>$site_image_url.$image_result->filepath);
							$Item->setItemImages($image['ItemImages']);
						}
					}
				}
				else
				{
					$image_query = db_query("SELECT * FROM content_field_image_cache AS ctp INNER JOIN files AS f ON ctp.field_image_cache_fid=f.fid WHERE  ctp.nid = '".$iInfo->nid."'");
					while($image_result = db_fetch_object($image_query)) {
						if($image_result->filename != '' && strlen($image_result->filename) > 0) {
							$site_image_url	=	trim(substr($GLOBALS['base_url'], 0, strpos($GLOBALS['base_url'],'sites')));	
							$image['ItemImages']=array('ItemID'=>$iInfo->nid,'ItemImageID'=>$image_result->fid,'ItemImageFileName'=>$image_result->filename,'ItemImageUrl'=>$site_image_url.$image_result->filepath);
							$Item->setItemImages($image['ItemImages']);
						}
					}
				}
				
				//End code to set image node
			
			
			
				$categoriesI = 0;
				if(is_array($iInfo->taxonomy) && count($iInfo->taxonomy)>0)
				{
					foreach($iInfo->taxonomy as $category)
					{ 
						$catArray['CategoryId'] = $category->tid;
						$catArray['Category'] = $category->name;
						//$catArray['ParentId'] = $all_parent[$category->tid];
						$Item->setCategories($catArray);
						$categoriesI++;
					}
					unset($category);
				} 
				
				$Item->setQuantity($stock);
				$Item->setUnitPrice($iInfo->sell_price);
				//$Item->setListPrice($iInfo->list_price);
				$Item->setListPrice($iInfo->cost);
				$Item->setWeight($iInfo->weight);
				$Item->setLowQtyLimit($LowQtyLimit);
				if($shipping>0)
				{ 
					$Item->setFreeShipping("N");
				}
				else 
				{
					$Item->setFreeShipping("Y");
				}
					
				$Item->setDiscounted($iInfo->discount? $iInfo->discount:0);
				$Item->setShippingFreight($shipping?$shipping:0);
				$Item->setWeight_Symbol($iInfo->weight_units);
				$Item->setWeight_Symbol_Grams('grams');
				unset($product_type);
				$product_type = $iInfo->type;
				if(in_array($product_type,$taxable_arr))
				{
					$Item->setTaxExempt("N");
				}
				else
				{
					$Item->setTaxExempt("Y");
				}
				
				
				$Item->setUpdatedAt($iInfo->changed ? date("Y-m-d H:i:s",$iInfo->changed) : date("Y-m-d H:i:s",$iInfo->created));
				
				
				$itemsv_query = $iInfo->attributes;			
				
				$var=0;
				$Variants = new WG_Variants();
				if($drupal_version > "6")
				{ 
					$result2 = db_query("SELECT * FROM {uc_product_adjustments} WHERE nid = ".$iInfo->nid." ");
					while($obj = $result2->fetchObject())
					{
						$default_model = $obj->model;
						$combination = unserialize($obj->combination);
						if($default_model != $iInfo->model)
						{ 
							$stock1_arr = db_query("SELECT sku, nid, stock FROM {uc_product_stock} WHERE  sku = '".$default_model." ' " );
							while($st = $stock1_arr->fetchObject())
							{
								$stock1 = $st;
							}
							$vcode = $ivInfo;
							foreach ($combination as $comb)
							{ 
								if(isset($comb) && $comb!="")
								{
									$sql = db_query("SELECT nid, oid,cost,price,weight FROM {uc_product_options} WHERE nid = ".$iInfo->nid." and oid = ".$comb." ");
									while($pricesql = $sql->fetchObject())
									{
										$price += $pricesql->price;
										$weight += $pricesql->weight;
										$cost += $pricesql->cost;
										
									}
								}
							}
							$VariantArray['ItemCode'] = $default_model;
							$VariantArray['VarientID'] = $iInfo->nid;
							$VariantArray['Quantity'] = $stock1->stock?$stock1->stock:0;
							$VariantArray['UnitPrice'] = $price+$iInfo->sell_price;
							$VariantArray['Weight'] = number_format($weight, 2 );
							$Item->setItemVariants($VariantArray);
							$var++;
							unset($price);
							unset($weight);	
							unset($cost);	
						}
					}
					unset($obj);
					$op=0;
					$Options = new WG_Options();
					if(is_array($ioInfo_new1) && count($ioInfo_new1)>0)
					{
						foreach($ioInfo_new1 as $ioInfo_new)
						{
							$optionArray['ItemOption']['ID'] = $ioInfo_new['oid'];
							$optionArray['ItemOption']['Value'] = $ioInfo_new['name'];
							$optionArray['ItemOption']['Name'] = $ioInfo_new['value'];
							$Item->setItemOptions($optionArray);
							$op++;
						}
					}
					
				}
				
				else
				{
					$result2 = db_query("SELECT * FROM {uc_product_adjustments} WHERE nid = ".$iInfo->nid." ");  
					while ($obj = db_fetch_object($result2)) 
					{
						$default_model = $obj->model;
						$combination = unserialize($obj->combination);
						if($default_model != $iInfo->model)
						{  
							$stock1 = db_fetch_array(db_query("SELECT sku, nid, stock FROM {uc_product_stock} WHERE  sku = '%s'", $default_model));
							
							$vcode = $ivInfo;
							foreach ($combination as $comb)
							{ 
								if(isset($comb) && $comb!="")
								{
									$sql = db_query("SELECT nid, oid,cost,price,weight FROM {uc_product_options} WHERE nid = ".$iInfo->nid." and oid = ".$comb." ");
									while($pricesql = db_fetch_array($sql))
									{
										$price += $pricesql['price'];
										$weight += $pricesql['weight'];
										$cost += $pricesql['cost'];
										
									}
								}
							}
							unset($combination);
							$VariantArray['ItemCode'] = $default_model;
							$VariantArray['VarientID'] = $iInfo->nid;
							$VariantArray['Quantity'] = $stock1['stock']?$stock1['stock']:0;
							$VariantArray['UnitPrice'] = $price+$iInfo->sell_price;
							$VariantArray['Weight'] = number_format($weight, 2 );
							$Item->setItemVariants($VariantArray);
							$var++;
							unset($price);
							unset($weight);	
							unset($cost);		
						}
						
					} 
					unset($obj);
					#
					# get item options if any
					#
				//	$iOptions = $xmlResponse->createTag("ItemOptions", array(), '', $itemNode, __ENCODE_RESPONSE);
					$op=0;
					$Options = new WG_Options();
					if(is_array($ioInfo_new1) && count($ioInfo_new1)>0)
					{
						foreach($ioInfo_new1 as $ioInfo_new)
						{
							$optionArray['ItemOption']['ID'] = $ioInfo_new['oid'];
							$optionArray['ItemOption']['Value'] = $ioInfo_new['name'];
							$optionArray['ItemOption']['Name'] = $ioInfo_new['value'];
							$Item->setItemOptions($optionArray);
							$op++;
						}
					}
				}
				$itemCountVar++;
				$Items->setItems($Item->getItem()); 
			}
			unset($iInfo1,$iInfo);
		}
		$Items->setTotalRecordSent((int)$itemCountVar);
		return $this->response($Items->getItems());
	}
	
	function getPriceQtyBySku($username,$password,$limit,$storeid=1,$items) {
	
		
		 
		global $version,$drupal_version;
		
		$status = $this->auth_user($username,$password);
		if($status !='0')
		{
			return $status;
		}
		$Items = new WG_Items();
		
		if($drupal_version > "6")
		{
			$count_query_product = db_query("SELECT  COUNT(n.nid) FROM {node}  n ,{uc_products} p, {node_type} np  where n.vid = p.vid and np.type = n.type and np.module = 'uc_product' and p.model IN(".$items.") order by n.nid desc ")->fetchField();
			
		}
		else
		{
			$count_query_product = db_result(db_query("SELECT  COUNT(n.nid) FROM {node}  n ,{uc_products} p, {node_type} np  where n.vid = p.vid and np.type = n.type and np.module = 'uc_product' and p.model IN(".$items.") order by n.nid desc "));
		}
		
		$itemCountVar = 0;
		if($count_query_product>0)
		{
			if($drupal_version > "6")
			{
				$sql_parent = db_query("SELECT tid, parent from {taxonomy_term_hierarchy} ");
				while ($all = $sql_parent->fetchObject())
				{ 
					$all_parent[$all->tid] = $all->parent ;
				} 
				
			}
			else
			{
				$sql_parent = db_query("SELECT tid, parent from {term_hierarchy} ");
				while ($all = db_fetch_array($sql_parent))
				{
					$all_parent[$all['tid']] = $all['parent'] ;
				}
			}
			if($drupal_version<'6')
			{
				$taxes = uc_taxes_get_rates();
			}
			else
			{
				$taxes = uc_taxes_rate_load();	
			}
			if($taxes)
			{
				
				foreach ($taxes as $k=>$iInfo2) 
				{		
					foreach($iInfo2->taxed_product_types as $k2=>$value)
					{
						$taxable_arr[] = $value;	
					}
				}
			}
			
			$taxable_arr = array_unique($taxable_arr);
				
			
			$result1= db_query("SELECT DISTINCT n.nid, n.* FROM {node}  n ,{uc_products} p, {node_type} np  where n.vid = p.vid and np.type = n.type and np.module = 'uc_product' and p.model IN(".$items.") order by n.nid desc");
			$Items->setStatusCode('0');
			$Items->setStatusMessage('All Ok');
			$Items->setTotalRecordFound($count_query_product?$count_query_product:'0');
			
			if($drupal_version > "6")
			{ 
				while ($iInfo1 =$result1->fetchObject()) 
				{	//$iInfo1->nid = '151';
					$iInfo_arr[] = $this->menu_get_item_v7("node/".$iInfo1->nid);	
					
					
				} 
				
				
				
			}
			else
			{ 
				while ($iInfo1 = db_fetch_object($result1)) 
				{	if($drupal_version == "5")
					{	
						
						$iInfo_arr[] = node_load($iInfo1->nid);	
					}
					else
					{
						//$iInfo_arr[] = $this->menu_get_item_v6("node/".$iInfo1->nid);
						//$iInfo1->nid = '418';
						$iInfo_arr[] = $this->menu_get_item_v6("node/".$iInfo1->nid);	
					
					}
				} //print_r($iInfo_arr);
			}
			
			foreach($iInfo_arr as $k=> $iInfo)
			{	
				if($drupal_version > "6")
				{ 
					
					$stock_obj = db_query("SELECT * FROM {uc_product_stock} WHERE  sku = '".$iInfo->model."'")->fetchObject();
					if(!empty($stock_obj))
					{

						$LowQtyLimit = $stock_obj->threshold;
						$stock = $stock_obj->stock;
						if($stock=='')
						$stock='0';
					}
					else
					{
						$stock='0';
					}
					
				}
				else
				{  
					$stock = db_fetch_array(db_query("SELECT * FROM {uc_product_stock} WHERE  sku = '".$iInfo->model."'"));
					if(!empty($stock))
					{
						$LowQtyLimit = $stock['threshold'];
						$stock = $stock['stock'];
						if($stock=='')
						$stock='0';
					}
					else
					{
						$stock='0';
					}
				}
				$Item = new WG_Item();
				$Item->setItemID($iInfo->nid);
				
				$Item->setItemCode($iInfo->model);
				
				$Item->setQuantity($stock);
				$Item->setUnitPrice($iInfo->sell_price);
				$Item->setListPrice($iInfo->list_price);
				$Item->setWeight($iInfo->weight);
				$itemCountVar++;
				$Items->setItems($Item->getItem()); 
			}
			unset($iInfo1,$iInfo);
		}
		$Items->setTotalRecordSent((int)$itemCountVar);
		return $this->response($Items->getItems());
	
	
	
	}
	
	
	function getItemsQuantity($username,$password){
		 
		global $version,$drupal_version;
		
		$status = $this->auth_user($username,$password);
		if($status !='0')
		{
			return $status;
		}
		$Items = new WG_Items();
		
		if($drupal_version > "6")
		{
			$count_query_product = db_query("SELECT  COUNT(n.nid) FROM {node}  n ,{uc_products} p, {node_type} np  where n.vid = p.vid and np.type = n.type and np.module = 'uc_product' and n.nid > $start  order by n.nid")->fetchField();
			
		}
		else
		{
			$count_query_product = db_result(db_query("SELECT  COUNT(n.nid) FROM {node}  n ,{uc_products} p, {node_type} np  where n.vid = p.vid and np.type = n.type and np.module = 'uc_product' and n.nid > $start  order by n.nid"));
		}
		
		$itemCountVar = 0;
		if($count_query_product>0)
		{
			if($drupal_version > "6")
			{
				$sql_parent = db_query("SELECT tid, parent from {taxonomy_term_hierarchy} ");
				while ($all = $sql_parent->fetchObject())
				{ 
					$all_parent[$all->tid] = $all->parent ;
				} 
				
			}
			else
			{
				$sql_parent = db_query("SELECT tid, parent from {term_hierarchy} ");
				while ($all = db_fetch_array($sql_parent))
				{
					$all_parent[$all['tid']] = $all['parent'] ;
				}
			}
			if($drupal_version<'6')
			{
				$taxes = uc_taxes_get_rates();
			}
			else
			{
				$taxes = uc_taxes_rate_load();	
			}
			if($taxes)
			{
				
				foreach ($taxes as $k=>$iInfo2) 
				{		
					foreach($iInfo2->taxed_product_types as $k2=>$value)
					{
						$taxable_arr[] = $value;	
					}
				}
			}
			
			$taxable_arr = array_unique($taxable_arr);
				
			
			$result1= db_query("SELECT DISTINCT n.nid, n.*,np.* FROM {node}  n ,{uc_products} p, {node_type} np  where n.vid = p.vid and np.type = n.type and np.module = 'uc_product' and n.nid > $start order by n.nid  limit 0,$limit");
			$Items->setStatusCode('0');
			$Items->setStatusMessage('All Ok');
			$Items->setTotalRecordFound($count_query_product?$count_query_product:'0');
			
			if($drupal_version > "6")
			{ 
				while ($iInfo1 =$result1->fetchObject()) 
				{	//$iInfo1->nid = '151';
					$iInfo_arr[] = $this->menu_get_item_v7("node/".$iInfo1->nid);	
					
					
				} 
				
				
				
			}
			else
			{ 
				while ($iInfo1 = db_fetch_object($result1)) 
				{	if($drupal_version == "5")
					{	
						
						$iInfo_arr[] = node_load($iInfo1->nid);	
					}
					else
					{
						//$iInfo_arr[] = $this->menu_get_item_v6("node/".$iInfo1->nid);
						//$iInfo1->nid = '418';
						$iInfo_arr[] = $this->menu_get_item_v6("node/".$iInfo1->nid);	
					
					}
				} //print_r($iInfo_arr);
			}
			
			foreach($iInfo_arr as $k=> $iInfo)
			{	
				if($drupal_version > "6")
				{ 
					
					$stock_obj = db_query("SELECT * FROM {uc_product_stock} WHERE  sku = '".$iInfo->model."'")->fetchObject();
					if(!empty($stock_obj))
					{

						$LowQtyLimit = $stock_obj->threshold;
						$stock = $stock_obj->stock;
						if($stock=='')
						$stock='0';
					}
					else
					{
						$stock='0';
					}
					
				}
				else
				{  
					$stock = db_fetch_array(db_query("SELECT * FROM {uc_product_stock} WHERE  sku = '".$iInfo->model."'"));
					if(!empty($stock))
					{
						$LowQtyLimit = $stock['threshold'];
						$stock = $stock['stock'];
						if($stock=='')
						$stock='0';
					}
					else
					{
						$stock='0';
					}
				}
				$Item = new WG_Item();
				$Item->setItemID($iInfo->nid);
				
				$Item->setItemCode($iInfo->model);
				
				$Item->setQuantity($stock);
				$Item->setUnitPrice($iInfo->sell_price);
				$Item->setListPrice($iInfo->list_price);
				$Item->setWeight($iInfo->weight);
				$itemCountVar++;
				$Items->setItems($Item->getItem()); 
			}
			unset($iInfo1,$iInfo);
		}
		$Items->setTotalRecordSent((int)$itemCountVar);
		return $this->response($Items->getItems());
	
	}
	
	
	function getStoreItemByIdForEcc($username,$password,$datefrom,$start,$limit,$storeid,$others)
	{ 
		global $version,$drupal_version;
		$status = $this->auth_user($username,$password);
		if($status !='0')
		{
			return $status;
		}
		$Items = new WG_Items();
		
		if($drupal_version > "6")
		{
			$count_query_product = db_query("SELECT  COUNT(n.nid) FROM {node}  n ,{uc_products} p, {node_type} np  where n.vid = p.vid and np.type = n.type and np.module = 'uc_product' and n.nid = $start  order by n.nid")->fetchField();
			
		}
		else
		{
			$count_query_product = db_result(db_query("SELECT  COUNT(n.nid) FROM {node}  n ,{uc_products} p, {node_type} np  where n.vid = p.vid and np.type = n.type and np.module = 'uc_product' and n.nid = $start  order by n.nid"));
		}
		
		$itemCountVar = 0;
		
		$Items->setStatusCode('0');
		$Items->setStatusMessage('All Ok');
		
		if($count_query_product>0)
		{
			if($drupal_version > "6")
			{
				$sql_parent = db_query("SELECT tid, parent from {taxonomy_term_hierarchy} ");
				while ($all = $sql_parent->fetchObject())
				{ 
					$all_parent[$all->tid] = $all->parent ;
				} 
				
			}
			else
			{
				$sql_parent = db_query("SELECT tid, parent from {term_hierarchy} ");
				while ($all = db_fetch_array($sql_parent))
				{
					$all_parent[$all['tid']] = $all['parent'] ;
				}
			}
			if($drupal_version<'6')
			{
				$taxes = uc_taxes_get_rates();
			}
			else
			{
				$taxes = uc_taxes_rate_load();	
			}
			if($taxes)
			{
				
				foreach ($taxes as $k=>$iInfo2) 
				{		
					foreach($iInfo2->taxed_product_types as $k2=>$value)
					{
						$taxable_arr[] = $value;	
					}
				}
			}
			
			$taxable_arr = array_unique($taxable_arr);
				
			
			$result1= db_query("SELECT DISTINCT n.nid, n.*,np.* FROM {node}  n ,{uc_products} p, {node_type} np  where n.vid = p.vid and np.type = n.type and np.module = 'uc_product' and n.nid = $start order by n.nid  limit 0,$limit");
			
			$Items->setTotalRecordFound($count_query_product?$count_query_product:'0');
			
			if($drupal_version > "6")
			{ 
				while ($iInfo1 =$result1->fetchObject()) 
				{	//$iInfo1->nid = '151';
					$iInfo_arr[] = $this->menu_get_item_v7("node/".$iInfo1->nid);	
					
					
				} 
				
				
				
			}
			else
			{ 
				while ($iInfo1 = db_fetch_object($result1)) 
				{	if($drupal_version == "5")
					{	
						
						$iInfo_arr[] = node_load($iInfo1->nid);	
					}
					else
					{
						//$iInfo_arr[] = $this->menu_get_item_v6("node/".$iInfo1->nid);
						//$iInfo1->nid = '418';
						$iInfo_arr[] = $this->menu_get_item_v6("node/".$iInfo1->nid);	
					
					}
				} //print_r($iInfo_arr);
			}
			
			foreach($iInfo_arr as $k=> $iInfo)
			{	
				if($drupal_version > "6")
				{ 
					
					$stock_obj = db_query("SELECT * FROM {uc_product_stock} WHERE  sku = '".$iInfo->model."'")->fetchObject();
					if(!empty($stock_obj))
					{

						$LowQtyLimit = $stock_obj->threshold;
						$stock = $stock_obj->stock;
						if($stock=='')
						$stock='0';
					}
					else
					{
						$stock='0';
					}
					
				}
				else
				{  
					$stock = db_fetch_array(db_query("SELECT * FROM {uc_product_stock} WHERE  sku = '".$iInfo->model."'"));
					if(!empty($stock))
					{
						$LowQtyLimit = $stock['threshold'];
						$stock = $stock['stock'];
						if($stock=='')
						$stock='0';
					}
					else
					{
						$stock='0';
					}
				}
				$Item = new WG_Item();
				$Item->setItemID($iInfo->nid);
				
				$Item->setItemCode($iInfo->model);
				$Item->setItemDescription($iInfo->title);
				if($drupal_version > "6")
				{
					$Item->setItemShortDescr($iInfo->body['und'][0]['value']);
				}
				else
				{
					$Item->setItemShortDescr($iInfo->body);
				}
				
				
				//$postion_char		=	strpos(substr($_SERVER['PHP_SELF'],1), '/');
				//$store_site_path	=	 'http://'.$_SERVER['HTTP_HOST'].'/'.substr($_SERVER['PHP_SELF'], 5, $postion_char).'/';
				
				$store_site_path	=	 'http://'.$_SERVER['HTTP_HOST'].'/';
				
				//Code to set image node
				if($drupal_version > "6")
				{
					$image_query = db_query("SELECT * FROM content_field_image_cache AS ctp INNER JOIN files AS f ON ctp.field_image_cache_fid=f.fid WHERE  ctp.nid = '".$iInfo->nid."'");
					while($image_result	=	$image_query->fetchObject()) {
						if($image_result->filename != '' && strlen($image_result->filename) > 0) {
							$site_image_url	=	trim(substr($GLOBALS['base_url'], 0, strpos($GLOBALS['base_url'],'sites')));	
							$image['ItemImages']=array('ItemID'=>$iInfo->nid,'ItemImageID'=>$image_result->fid,'ItemImageFileName'=>$image_result->filename,'ItemImageUrl'=>$site_image_url.$image_result->filepath);
							$Item->setItemImages($image['ItemImages']);
						}
					}
				}
				else
				{
					$image_query = db_query("SELECT * FROM content_field_image_cache AS ctp INNER JOIN files AS f ON ctp.field_image_cache_fid=f.fid WHERE  ctp.nid = '".$iInfo->nid."'");
					while($image_result = db_fetch_object($image_query)) {
						if($image_result->filename != '' && strlen($image_result->filename) > 0) {
							$site_image_url	=	trim(substr($GLOBALS['base_url'], 0, strpos($GLOBALS['base_url'],'sites')));	
							$image['ItemImages']=array('ItemID'=>$iInfo->nid,'ItemImageID'=>$image_result->fid,'ItemImageFileName'=>$image_result->filename,'ItemImageUrl'=>$site_image_url.$image_result->filepath);
							$Item->setItemImages($image['ItemImages']);
						}
					}
				}
				
				//End code to set image node
				
			
				$categoriesI = 0;
				if(is_array($iInfo->taxonomy) && count($iInfo->taxonomy)>0)
				{
					foreach($iInfo->taxonomy as $category)
					{ 
						$catArray['CategoryId'] = $category->tid;
						$catArray['Category'] = $category->name;
						//$catArray['ParentId'] = $all_parent[$category->tid];
						$Item->setCategories($catArray);
						$categoriesI++;
					}
					unset($category);
				} 
				
				$Item->setQuantity($stock);
				$Item->setUnitPrice($iInfo->sell_price);
				//$Item->setListPrice($iInfo->list_price);
				$Item->setListPrice($iInfo->cost);
				$Item->setWeight($iInfo->weight);
				$Item->setLowQtyLimit($LowQtyLimit);
				if($shipping>0)
				{ 
					$Item->setFreeShipping("N");
				}
				else 
				{
					$Item->setFreeShipping("Y");
				}
					
				$Item->setDiscounted($iInfo->discount? $iInfo->discount:0);
				$Item->setShippingFreight($shipping?$shipping:0);
				$Item->setWeight_Symbol($iInfo->weight_units);
				$Item->setWeight_Symbol_Grams('grams');
				unset($product_type);
				$product_type = $iInfo->type;
				if(in_array($product_type,$taxable_arr))
				{
					$Item->setTaxExempt("N");
				}
				else
				{
					$Item->setTaxExempt("Y");
				}
				
				$Item->setUpdatedAt($iInfo->changed ? date("Y-m-d H:i:s",$iInfo->changed) : date("Y-m-d H:i:s",$iInfo->created));
				
				$itemsv_query = $iInfo->attributes;			
				
				$var=0;
				$Variants = new WG_Variants();
				if($drupal_version > "6")
				{ 
					$result2 = db_query("SELECT * FROM {uc_product_adjustments} WHERE nid = ".$iInfo->nid." ");
					while($obj = $result2->fetchObject())
					{
						$default_model = $obj->model;
						$combination = unserialize($obj->combination);
						if($default_model != $iInfo->model)
						{ 
							$stock1_arr = db_query("SELECT sku, nid, stock FROM {uc_product_stock} WHERE  sku = '".$default_model." ' " );
							while($st = $stock1_arr->fetchObject())
							{
								$stock1 = $st;
							}
							$vcode = $ivInfo;
							foreach ($combination as $comb)
							{ 
								if(isset($comb) && $comb!="")
								{
									$sql = db_query("SELECT nid, oid,cost,price,weight FROM {uc_product_options} WHERE nid = ".$iInfo->nid." and oid = ".$comb." ");
									while($pricesql = $sql->fetchObject())
									{
										$price += $pricesql->price;
										$weight += $pricesql->weight;
										$cost += $pricesql->cost;
										
									}
								}
							}
							$VariantArray['ItemCode'] = $default_model;
							$VariantArray['VarientID'] = $iInfo->nid;
							$VariantArray['Quantity'] = $stock1->stock?$stock1->stock:0;
							$VariantArray['UnitPrice'] = $price+$iInfo->sell_price;
							$VariantArray['Weight'] = number_format($weight, 2 );
							$Item->setItemVariants($VariantArray);
							$var++;
							unset($price);
							unset($weight);	
							unset($cost);	
						}
					}
					unset($obj);
					$op=0;
					$Options = new WG_Options();
					if(is_array($ioInfo_new1) && count($ioInfo_new1)>0)
					{
						foreach($ioInfo_new1 as $ioInfo_new)
						{
							$optionArray['ItemOption']['ID'] = $ioInfo_new['oid'];
							$optionArray['ItemOption']['Value'] = $ioInfo_new['name'];
							$optionArray['ItemOption']['Name'] = $ioInfo_new['value'];
							$Item->setItemOptions($optionArray);
							$op++;
						}
					}
					
				}
				
				else
				{
					$result2 = db_query("SELECT * FROM {uc_product_adjustments} WHERE nid = ".$iInfo->nid." ");  
					while ($obj = db_fetch_object($result2)) 
					{
						$default_model = $obj->model;
						$combination = unserialize($obj->combination);
						if($default_model != $iInfo->model)
						{  
							$stock1 = db_fetch_array(db_query("SELECT sku, nid, stock FROM {uc_product_stock} WHERE  sku = '%s'", $default_model));
							
							$vcode = $ivInfo;
							foreach ($combination as $comb)
							{ 
								if(isset($comb) && $comb!="")
								{
									$sql = db_query("SELECT nid, oid,cost,price,weight FROM {uc_product_options} WHERE nid = ".$iInfo->nid." and oid = ".$comb." ");
									while($pricesql = db_fetch_array($sql))
									{
										$price += $pricesql['price'];
										$weight += $pricesql['weight'];
										$cost += $pricesql['cost'];
										
									}
								}
							}
							unset($combination);
							$VariantArray['ItemCode'] = $default_model;
							$VariantArray['VarientID'] = $iInfo->nid;
							$VariantArray['Quantity'] = $stock1['stock']?$stock1['stock']:0;
							$VariantArray['UnitPrice'] = $price+$iInfo->sell_price;
							$VariantArray['Weight'] = number_format($weight, 2 );
							$Item->setItemVariants($VariantArray);
							$var++;
							unset($price);
							unset($weight);	
							unset($cost);		
						}
						
					} 
					unset($obj);
					#
					# get item options if any
					#
				//	$iOptions = $xmlResponse->createTag("ItemOptions", array(), '', $itemNode, __ENCODE_RESPONSE);
					$op=0;
					$Options = new WG_Options();
					if(is_array($ioInfo_new1) && count($ioInfo_new1)>0)
					{
						foreach($ioInfo_new1 as $ioInfo_new)
						{
							$optionArray['ItemOption']['ID'] = $ioInfo_new['oid'];
							$optionArray['ItemOption']['Value'] = $ioInfo_new['name'];
							$optionArray['ItemOption']['Name'] = $ioInfo_new['value'];
							$Item->setItemOptions($optionArray);
							$op++;
						}
					}
				}
				$itemCountVar++;
				$Items->setItems($Item->getItem()); 
			}
			unset($iInfo1,$iInfo);
		}
		$Items->setTotalRecordSent((int)$itemCountVar);
		return $this->response($Items->getItems());
	}
	
	
	function menu_get_item_v6($path = NULL, $router_item = NULL) 
	{
		global $version;
	  static $router_items;
	  
	  if (!isset($path)) {
		$path = $_GET['q'];
	  }
	  
	  if (isset($router_item)) {
		$router_items[$path] = $router_item;
	  }
	  
	  
	  if (!isset($router_items[$path])) {
		$original_map = arg(NULL, $path);
		$parts = array_slice($original_map, 0, MENU_MAX_PARTS);
		list($ancestors, $placeholders) = menu_get_ancestors($parts);
	
		
		if ($router_item = db_fetch_array(db_query_range('SELECT * FROM {menu_router} WHERE path IN ('. implode (',', $placeholders) .') ORDER BY fit DESC', $ancestors, 0, 1))) 
		{
			$map = _menu_translate($router_item, $original_map);
			
			if ($map === FALSE) {
				$router_items[$path] = FALSE;
				return FALSE;
			}
			$router_item['map'] = $map;
			
			$router_item['page_arguments'] = array_merge(menu_unserialize($router_item['page_arguments'], $map), array_slice($map, $router_item['number_parts']));
		}
		$router_items[$path] = $router_item;
	  }
		return $router_items[$path]['page_arguments'][0];
	}
	
	function menu_get_item_v7($path = NULL, $router_item = NULL) 
	{
	  $router_items = &drupal_static(__FUNCTION__);
	
	  if (!isset($path)) {
		$path = $_GET['q'];
	  }
	  if (isset($router_item)) {
		$router_items[$path] = $router_item;
	  } 
	  if (!isset($router_items[$path])) {
		$original_map = arg(NULL, $path);
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
		  if ($map === FALSE) {
		 
			$router_items[$path] = FALSE;
			return FALSE;
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
	function ItemUpdatePriceQty($username,$password,$itemId,$qty,$price,$cost,$weight,$storeid=1) {
	
		global $version,$drupal_version;
		$status = $this->auth_user($username,$password);
		if($status !='0')
		{
			return $status;
		}
		$Items = new WG_Items();
		if (!isset($itemId)) {
			$Items->setStatusCode('9997');
			$Items->setStatusMessage('Unknown request or request not in proper format');				
			return $this->response($Items->getItemsNode());				
		}
		
		$Items->setStatusCode('0');
		$Items->setStatusMessage('Item successfully updated');
 
		$Item = new WG_Item();
		$productID = $itemId;
		
		if($drupal_version > "6")
		{
			$data = db_query("SELECT COUNT(nid) FROM {node} WHERE nid=".$this->mySQLSafe($productID))->fetchField();
		}
		else
		{
			$data = db_result(db_query("SELECT COUNT(nid) FROM {node} WHERE nid=".$productID));
		}
		
		if($data>0)
		{
			
			if ($qty!="")
			{	
				if($drupal_version > "6")
				{ 
					//$row = db_query("SELECT COUNT(nid) from {uc_product_stock} where  sku ='".htmlspecialchars($sku)."'")->fetchField();
					$row = db_query("SELECT COUNT(nid) from {uc_product_stock} where nid ='".$productID."'")->fetchField();
					
					if ($row>0){
						 db_update('uc_product_stock')
						  ->fields(array('stock'=>$qty))
						  ->condition('nid',$productID)
						  ->execute();
						$status ="Success";	
					}
					
					else
					{ 
						  db_insert('uc_product_stock')
						->fields(array('sku' => $row['sku'], 'nid' =>$productID,'active'=>1,'stock'=>$qty,'threshold'=>0))
						->execute();
						$status = "Success";
					}
				}
				else
				{
					
					//$row = db_result(db_query("SELECT COUNT(nid) from {uc_product_stock} where  sku =".$this->mySQLSafe($sku)." "));
					$row = db_result(db_query("SELECT COUNT(nid) from {uc_product_stock} where nid ='".$productID."'"));
					
					if ($row>0){
						db_query("UPDATE {uc_product_stock} SET stock = $qty WHERE nid ='".$productID."'");
						$status ="Success";	
					}
					
					else{
						db_query("Insert Into {uc_product_stock} (sku ,nid,active ,stock ,threshold) values (".$this->mySQLSafe($row['sku']).",".$this->mySQLSafe($productID).",'1',".$this->mySQLSafe($qty).",'0')");
						$status ="Success";
					}
					
				}
			}
			if ($price!="")
			{	
				
				if($drupal_version > "6")
				{
					//$row = db_query("SELECT COUNT(nid) from {uc_products} where  model ='".$sku."'")->fetchField();
					$row = db_query("SELECT COUNT(nid) from {uc_products} where  vid =".$this->mySQLSafe($productID)." ")->fetchField();
					
					if ($row>0){
						 db_update('uc_products')
						  ->fields(array('sell_price'=>$price))
						  ->condition('vid', $productID)
						  ->execute();
						$status ="Success";	
					}
					else{
						$status ="Price for this product not found";
					}
				}
				else
				{
					//$row = db_result(db_query("SELECT COUNT(nid) from {uc_products} where  model =".$this->mySQLSafe($sku)." "));
					$row = db_result(db_query("SELECT COUNT(nid) from {uc_products} where  vid ='".$productID."'"));
					
					if ($row>0){
						//db_query("UPDATE {uc_products} SET sell_price = ".$this->mySQLSafe($price)." WHERE model=".$this->mySQLSafe($sku)."");
						//db_query("UPDATE {uc_products} SET list_price = ".$price." WHERE vid = ".$productID." and model='".$sku."'");
						db_query("UPDATE {uc_products} SET sell_price = ".$price.", cost=".$cost.", weight=".$weight." WHERE vid = ".$productID."");
						$status ="Success";	
					}
					else{
						$status ="Price for this product not found";
					}
				}
			}	
			$itemsProcessed++;  
		}
		else
			$status ="Product ID not found";
	
		$Item->setStatus('Success');
		$Item->setProductID($v4['ProductID']);
		$Item->setSku($v4['Sku']);							
		$Items->setItems($Item->getItem());
		return $this->response($Items->getItems());
	}
	
	# Return the Count of the orders remained with specific dates and status
	function getOrdersRemained($start_date,$start_order_no)
	{
		global $version;
		$previous_orders = 0; 
		   
		if($version > "6.x-2.6")
		{
			$sql = db_query("SELECT COUNT(*) FROM {uc_orders} o LEFT JOIN {uc_order_statuses} os ON o.order_status = os.order_status_id WHERE o.created >= '".$start_date."' AND o.order_id > ".$start_order_no." and o.order_status IN (".QB_ORDERS_DOWNLOAD_EXCL_LIST.") ORDER BY o.order_id  ")->fetchField();
			return $sql;
		} 
		else
		{	 
			$sql = "SELECT COUNT(*) FROM {uc_orders} o LEFT JOIN {uc_order_statuses} os ON o.order_status = os.order_status_id WHERE o.created >= '".$start_date."' AND o.order_id > ".$start_order_no." and o.order_status IN (".QB_ORDERS_DOWNLOAD_EXCL_LIST.") ORDER BY o.order_id  " ;	
			$previous_orders = db_result(db_query($sql));
			
		}
		
		return $previous_orders;
	}
	# Return the Orders to sync with the QB according to the date and the staus and order id.
	function getOrders($username,$password,$datefrom,$start_order_no,$ecc_excl_list,$order_per_response="25")
	{
		global $version,$drupal_version;
		
		$status = $this->auth_user($username,$password);
		
		if($status !='0')
		{
			return $status;
		}
		$currency = $store_name = variable_get('uc_currency_code', NULL);
		if(!isset($datefrom) or empty($datefrom))
		{
			$datefrom=date('m-d-Y');  
		}
	
		list($mm,$dd,$yy)=explode("-",$datefrom);
		$time_from = mktime(0,0,0,$mm,$dd,$yy);  
		
		#fetch Id of Order Status	
		foreach (uc_order_status_list('general') as $orderstatus1) {
			$firstset[] =$orderstatus1;
			$array = array($statusid1 => $status1);
		}
		foreach (uc_order_status_list('specific') as $orderstatus2) {
			$secondset[] = $orderstatus2;
		}
		$orderstatus =  array_merge( $firstset, $secondset);
		$ecc_excl_list = str_replace("'","",trim($ecc_excl_list));
		
		$ecc_list = explode(",",$ecc_excl_list);
	
		$list = array();
		foreach ($orderstatus as $storeOrder)
		{ 
			foreach ($ecc_list as $status_list)
			{ 
				if($status_list == $storeOrder['title'])
				{
					$list[$storeOrder['title']] = "'".$storeOrder['id']."'" ;
				}
				
			}
		}
		/*if($orderlist!='')
		{
			foreach ($orderstatus as $storeOrder)
			{ 
				$list[$storeOrder['title']] = "'".$storeOrder['id']."'" ;
			}
		}*/
		
		
		//Temporary hard coded for all order
		$ecc_excl_list = 'All';
		if($ecc_excl_list == 'All') {
			$list = array();
			foreach ($orderstatus as $storeOrder) { 
				$list[$storeOrder['title']] = "'".$storeOrder['id']."'" ;
			}
		}
		//Temporary hard coded for all order
		
		
		unset($orderstatus,$storeOrder, $orderstatus1,$orderstatus2,$uc_order_status_list);
		$ecc_orderstatus_list = implode(",",$list);
		
		#check for authorisation
	
		//$this->auth_user($username,$password);
		//die("hhh");
		
		define("QB_ORDERS_DOWNLOAD_EXCL_LIST", $ecc_orderstatus_list);
		define("QB_ORDERS_PER_RESPONSE",$order_per_response);  
		
		$orders_remained = $this->getOrdersRemained($start_date,$start_order_no);	

		$orders_remained=$orders_remained>0?$orders_remained:0;	
	
		$orders = array();
		$Orders = new WG_Orders();
	
		$sql = "SELECT o.order_id,o.data, o.uid, o.billing_first_name, o.billing_last_name, o.order_total, o.order_status, o.created, os.title FROM {uc_orders} o LEFT JOIN {uc_order_statuses} os ON o.order_status = os.order_status_id WHERE o.created >= '".$start_date."' and o.order_id  > ".$start_order_no." and o.order_status IN (".QB_ORDERS_DOWNLOAD_EXCL_LIST.") ORDER BY o.order_id ASC  ".(QB_ORDERS_PER_RESPONSE>0?"LIMIT 0, ".QB_ORDERS_PER_RESPONSE:'');

		$result = db_query($sql);
		if($drupal_version > "6")
		{
			while ($test = $result->fetchObject())
			{ 
				$orders1[]  = $test;
				//print_r($orders1);		
				foreach($orders1 as $member=>$data)
				{
					$orders[$member]=$data;
				}
			} 
			
		}
		else
		{
			while ($test = db_fetch_array($result))
			{
				$orders[]  = $test;		
			}
		}

	$no_orders = count($orders);
	
		if($orderlist!='')
		{
		$orders_remained=$no_orders>0?$no_orders:0;	
		}
		
		if ($no_orders<=0)
		{
			$no_orders = true;
			$Orders->setStatusCode($no_orders?"9999":"0");
			$Orders->setStatusMessage($no_orders?"No Orders returned":"Total Orders:".$orders_remained);
			//return $xmlResponse->generate();
			return $this->response($Orders->getOrders());
		}
		#Fetch Zone code
		
		$zone_sql = db_query("select zone_id,zone_code from {uc_zones}");
		if($drupal_version > "6")
		{
			while ($zone = $zone_sql->fetchObject())
			{ 
				$zone_code[$zone->zone_id] = $zone->zone_code;
			}

		}
		else
		{
			while ($zone = db_fetch_array($zone_sql))
			{
				$zone_code[$zone['zone_id']] = $zone['zone_code'];
			}
		}
		
		#Fetch Country name
	
		$country_sql = db_query("select country_id, country_name from {uc_countries}");
		if($drupal_version > "6")
		{
			while($country = $country_sql->fetchObject())
			{
				$country_name[$country->country_id] = $country->country_name;
			}
		}
		else
		{
			while($country = db_fetch_array($country_sql))
			{
				$country_name[$country['country_id']] = $country['country_name'];
			}
		}
		if($drupal_version<'6')
		{
			$taxes = uc_taxes_get_rates();
		}
		else
		{
			$taxes = uc_taxes_rate_load();	
		}
		if($taxes)
		{
			
			foreach ($taxes as $k=>$iInfo2) 
			{		
				foreach($iInfo2->taxed_product_types as $k2=>$value)
				{
					$taxable_arr[] = $value;	
				}
			}
		}
		
		$taxable_arr = array_unique($taxable_arr);
		
		
		//if($orderlist!='')
		//{
/*		foreach (uc_order_status_list('general') as $orderstatus1) {
			$firstset[] =$orderstatus1;
			$array = array($statusid1 => $status1);
		}
		 foreach (uc_order_status_list('specific') as $orderstatus2) {
			$secondset[] = $orderstatus2;
		}
		
		$orderstatus =  array_merge( $firstset, $secondset);
		$i=0;
		
		foreach($orderstatus as $orderstat)
		{
		$state=$orderstat['state'];
		$title=$orderstat['title'];
		$order_stat=
		
		$i++;
		}
	print_r($order_stat);
		die;*/
		//}

		$ord_count_ecc = 0;
		if($orders)
		{  
			
			$Orders->setStatusCode(0);
			$Orders->setStatusMessage("Total Orders:".$orders_remained);
			$Orders->setTotalRecordFound($orders_remained?(int)$orders_remained:"0");
			$store_name = variable_get('uc_store_name', NULL);
			
			foreach ($orders as $order_data)
			{ 	
				
				unset($order_details); 
				unset($order);
				unset($order_details->line_items);
				unset($order_details->payment_method);
				unset($order_details->products);		
				if($drupal_version > "6")
				{
					$order_details = uc_order_load($order_data->order_id);
				}
				else
				{
					//$order_details = uc_order_load($order_data['order_id']);
					//$order_data['order_id'] = '129';
					$order_details = uc_order_load($order_data['order_id']);
				}
				
				$weightsymbol = 'lbs';
				$weight_symbol_grams ='453.6';
				
				$total = $order_details->order_total;
				
				unset($totaltax,$totalship);
				foreach($order_details->line_items as $taxes)
				{
					if($taxes['type'] == 'tax')
					{
						$totaltax += $taxes['amount']; 
					}
					
				}
				$ship_method ="";
				foreach($order_details->line_items as $shipping)
				{
					if($shipping['type'] == 'shipping')
					{
						$totalship += $shipping['amount']; 
						$ship_method =$ship_method.$shipping['title'];
					}
				}
				
				$extra = unserialize($order['extra']);
				
				if($drupal_version > "6")
				{
					$time = strtotime($order_data->created);
				}
				else
				{
					$time = strtotime($order_data['created']);
				}
				
				$Order = new WG_Order();
				
				$objOrderInfo	=	new WG_OrderInfo();
				
				if($drupal_version > "6")
				{
					$orderid = $order_data->order_id;
					$Order->setOrderId($order_data->order_id);
					$objOrderInfo->setTitle('');
					$objOrderInfo->setFirstName($order_data->billing_first_name);
					$objOrderInfo->setLastName( $order_data->billing_last_name);
					$objOrderInfo->setDate(date("Y-m-d",$order_data->created));
					//$Order->setTime(format_date($order_data['created'], 'custom', 'H:i:s'));
					$objOrderInfo->setTime(date("h:i:s A",$order_data->created));
					$objOrderInfo->setStoreID($store_name);
					$objOrderInfo->setStoreName($store_name);
					$objOrderInfo->setCurrency($currency);
					$objOrderInfo->setWeight_Symbol($weightsymbol);
					$objOrderInfo->setWeight_Symbol_Grams($weight_symbol_grams);
					$objOrderInfo->setCustomerId($order_data->uid);
					
					
				//	if($orderlist!='')
		           //  {
					//$Order->setStatus(array_search("'".$order_details->order_status."'",$statusarray));
		            //  }else{
					$objOrderInfo->setStatus(array_search("'".$order_details->order_status."'",$list));
		              //     }			
				}
				else
				{
					$orderid = $order_data['order_id'];
					$objOrderInfo->setOrderId($order_data['order_id']);
					$objOrderInfo->setTitle('');
					$objOrderInfo->setFirstName($order_data['billing_first_name']);
					$objOrderInfo->setLastName( $order_data['billing_last_name']);
					$objOrderInfo->setDate(date("Y-m-d",$order_data['created']));
					//$Order->setTime(format_date($order_data['created'], 'custom', 'H:i:s'));
					$objOrderInfo->setTime(date("h:i:s A",$order_data['created']));
					$objOrderInfo->setStoreID($store_name);
					$objOrderInfo->setStoreName($store_name);
					$objOrderInfo->setCurrency($currency);
					$objOrderInfo->setWeight_Symbol($weightsymbol);
					$objOrderInfo->setWeight_Symbol_Grams($weight_symbol_grams);
					$objOrderInfo->setCustomerId($order_data['uid']);
					//if($orderlist!='')
		            // {
					//$Order->setStatus(array_search("'".$order_details->order_status."'",$statusarray));
		             // }else{
					$objOrderInfo->setStatus(array_search("'".$order_details->order_status."'",$list));
		            //       }	
				}
				unset($admin_comments);	
				$result =db_query("select comment_id, message from {uc_order_admin_comments} oa where oa.order_id =".$orderid." ORDER BY oa.comment_id  ");
				if($drupal_version > "6")
				{
					//while ($ad_comment = db_fetch_array($result)) 
					while($ad_comment = $result->fetchObject())
					{ 
							$admin_comments = $admin_comments ." ".$ad_comment->message;
					}
				}
				else
				{
					while ($ad_comment = db_fetch_array($result)) 
					{ 
							$admin_comments = $admin_comments ." ".$ad_comment['message'];
					}
				}
				
				$objOrderInfo->setNotes(strip_tags($admin_comments));
				
				$objOrderInfo->setFax('');
				if($drupal_version > "6")
				{
					$comments = db_query("select message from {uc_order_comments} oc where oc.order_id =".$orderid." ORDER BY oc.comment_id  ")->fetchField();
				}
				else
				{
					$comments = db_result(db_query("select message from {uc_order_comments} oc where oc.order_id =".$orderid." ORDER BY oc.comment_id  "));
				}

				//$Order->setComment('');
				$Order->setOrderInfo($objOrderInfo->getOrderInfo());
				$Order->setComment($comments?$comments:"");
				# Orders/Bill info
				$payment_method = $order_details->payment_method;
				
				$payresult = db_query("SELECT * FROM {uc_payment_receipts} where order_id ='".$orderid."'");	
				if($drupal_version > "6")
				{
					while($test1 = $payresult->fetchObject())
					{
						 $payinfo = $test1;
					}
				}
				else
				{		
					$payinfo =db_fetch_object($payresult);
				}
				
				if(!empty($payinfo))
				{
					$payment_method = $payinfo->method;
				}
	
	
				$billing_first_name = $order_details->billing_first_name;
				$billing_last_name 	= $order_details->billing_last_name;
				$billing_company 	= $order_details ->billing_company;
				$billing_street1 	= $order_details->billing_street1;
				$billing_street2 	= $order_details->billing_street2;
				$billing_city 		= $order_details->billing_city;
				$billing_postal_code= $order_details->billing_postal_code;
				$primary_email		= $order_details->primary_email;
				$billing_phone		= $order_details->billing_phone;
				$zone_id 			= $order_details->billing_zone; 
				$country_id         = $order_details->billing_country; 		
				if($drupal_version > "6")
				{
					$abc = unserialize($order_data->data);
				}
				else
				{				
					$abc = unserialize($order_data['data']);
				}
				
				$key = uc_credit_encryption_key();
				
				$crypt = new uc_encryption_class;	
				
				$transaction_id = '';
				$order_details->payment_details = unserialize($crypt->decrypt($key, $abc['cc_data']));
				$Bill = new WG_Bill();
				$CreditCard = new WG_CreditCard();
				if (is_array($order_details->payment_details))
				{
					$card_type = $order_details->payment_details['cc_type'];
					$card_no =   $order_details->payment_details['cc_number'];
					$card_owner =$order_details->payment_details['cc_owner'];
					$card_cvv =  $order_details->payment_details['cc_cvv'];
					
					
					if(!empty($order_details->payment_details['cc_exp_month']) || !empty($order_details->payment_details['cc_exp_year']))
					{
						$expiration_date = ($order_details->payment_details['cc_exp_month']."/".$order_details->payment_details['cc_exp_year']);
					}
					else
					{
						$expiration_date = "";
					}
					
					if(is_array($abc['cc_txns']))
					{
						if(is_array($abc['cc_txns']['authorizations']))
							{
								$transaction_id = array_keys($abc['cc_txns']['authorizations']);
								$transaction_id = $transaction_id[0];
							}
						else {
								$transaction_id = '';
							}	
					}
					else
					{
						$transaction_id ='';
					}
						
					
					# Credit card 
					$CreditCard->setCreditCardType($card_type);
					$CreditCard->setCreditCardCharge('');
					$CreditCard->setExpirationDate($expiration_date);
					$CreditCard->setCreditCardName($card_owner);
					$CreditCard->setCreditCardNumber($card_no);
					$CreditCard->setCVV2($card_cvv);
					$CreditCard->setAdvanceInfo('');
	
					if(''==$transaction_id )
					{						
						$payinfo->data = unserialize($payinfo->data);
						$CreditCard->setTransactionId($payinfo->data['txn_id']);

					}
					else
					{
						$CreditCard->setTransactionId($transaction_id);
					}
				}
				else
				{	
					if(''==$transaction_id )
					{
						if($drupal_version > "6")
						{
							## Table {uc_payment_paypal_ipn} is not exist in versions
						}
						else
						{
							$result = db_query("SELECT txn_id FROM {uc_payment_paypal_ipn} where order_id ='".$orderid."'");			
							$shipment =db_fetch_object($result);
						}
						
						$CreditCard->getCreditCard();
						$CreditCard->setTransactionId($shipment->txn_id);
						
					}					
				}
				
				//$CreditCard->getCreditCard();
				//$Bill->setCreditCardInfo($CreditCard->getCreditCard());
				unset($card_type,$expiration_date,$card_no,$card_owner,$card_cvv,$transaction_id );
				#Bill
				$Bill->setPayMethod($payment_method);
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
				$Bill->setPONumber('');								
				$Order->setOrderBillInfo($Bill->getBill());	
				
				# Order Shipping Info
				$shipping_first_name = $order_details->delivery_first_name;
				$shipping_last_name  = $order_details->delivery_last_name;
				$shipping_company 	= $order_details ->delivery_company;
				$shipping_street1 	= $order_details->delivery_street1;
				$shipping_street2 	= $order_details->delivery_street2;
				$shipping_city 		= $order_details->delivery_city;
				$shipping_postal_code = $order_details->delivery_postal_code;
				$shipping_phone		= $order_details->delivery_phone;
				$zone_id 			= $order_details->delivery_zone; 
				$country_id         = $order_details->delivery_country; 			
				
				// Retrieve Carrier's Title using id (from associative array)
				if($drupal_version > "6")
				{
					$All_shipping_carrier = module_invoke_all('uc_shipping_method');
				}
				else
				{
					$All_shipping_carrier = module_invoke_all('shipping_method');
				}
				foreach($All_shipping_carrier as $key1 => $all_carrier)
				{
					
					$shipping_name_key[] =  $key1."-".$all_carrier['title'];
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
				}
				$Ship =new WG_Ship();
				$Ship->setShipMethod(htmlentities($ship_method, ENT_QUOTES));
				$Ship->setCarrier($carrier[0]);
			
				$result = db_query("SELECT * FROM {uc_shipments} WHERE order_id = ".$orderid);
				if($drupal_version > "6")
				{
					while($test1 = $result->fetchObject())
					{
						 $shipment = $test1;
					}
				}
				else
				{
					$shipment =db_fetch_object($result);
				}
				
				if($shipment)
				{
					$tracking_number = $shipment->tracking_number;
				}
				else
				{
					$tracking_number ='';
				}
				
				#Ship Node
				$Ship->setTrackingNumber($tracking_number);
				$Ship->setTitle('');
				$Ship->setFirstName($shipping_first_name);
				$Ship->setLastName($shipping_last_name);
				$Ship->setCompanyName($shipping_company);
				
				$Ship->setAddress1( $shipping_street1);
				$Ship->setAddress2($shipping_street2);
				$Ship->setCity($shipping_city);
				$Ship->setState($zone_code[$zone_id]);
				$Ship->setZip($shipping_postal_code);
				$Ship->setCountry($country_name[$country_id]);
				$Ship->setEmail('');
				$Ship->setPhone($shipping_phone);
				$Order->setOrderShipInfo($Ship->getShip());

				# get items of order
				
				foreach($order_details->products as $product_info)
				{ 
					$Item = new WG_Item();
					$item_code = $product_info->model;
					$item_name = $product_info->title;
					$qty 	   = $product_info->qty; 
					$unitprice = $product_info->price;	
					$weight	   = $product_info->weight;
					$cost 	   = $product_info->cost;
					//$description = db_fetch_array(db_query($sql));
					//$desc=$description['body']; 
					if($qty=='')
						$qty=0;
						
					# Itme node
					$Item->setItemCode(htmlentities($item_code, ENT_QUOTES));		
					$Item->setItemDescription(htmlentities($item_name, ENT_QUOTES));
					$Item->setItemShortDescr(htmlentities($desc, ENT_QUOTES));
					$Item->setQuantity($qty);
					
					//$Item->setCostPrice($cost);
					$cost=(float)$cost;
					//$Item->setListPrice($cost);
					//$unitprice=(float)$unitprice;
					$Item->setUnitPrice($unitprice);
                    //$weight=(float)$weight;
					$Item->setWeight($weight);
					if($drupal_version > "6")
					{	
						$Item->setFreeShipping($data->free_shipping);
						//$iInfo[$data->discount_avail]=(float)$iInfo[$data->discount_avail];
						$Item->setDiscounted($iInfo[$data->discount_avail]);
						$Item->setshippingFreight($data->shipping_freight);
					}
					else
					{
						$Item->setFreeShipping($data['free_shipping']);
						//$iInfo[$data['discount_avail']]=(float)$iInfo[$data['discount_avail']];
						$Item->setDiscounted($iInfo[$data['discount_avail']]);
						$Item->setshippingFreight($data['shipping_freight']);
					}

					
					$Item->setWeight_Symbol($weightsymbol);
					$Item->setWeight_Symbol_Grams($weight_symbol_grams);
					unset($product_type);
					$Qresult1= db_query("SELECT np.* FROM {node}  n ,{uc_products} p, {node_type} np  where n.vid = p.vid and np.type = n.type and np.module = 'uc_product' and n.nid = ".$product_info->nid."");
					if($drupal_version > "6")
					{ 
						while ($QiInfo1 =$Qresult1->fetchObject()) 
						{	
								$product_type = $QiInfo1->type;
						} 
						
					}
					else
					{ 
						while ($QiInfo1 = db_fetch_object($Qresult1)) 
						{
							$product_type = $QiInfo1->type;
						}
					}
					
				
					if(in_array($product_type,$taxable_arr))
					{
						$Item->setTaxExempt("N");
					}
					else
					{
						$Item->setTaxExempt("Y");
					}
					
					$onetimecharge = "0.00";
					$Item->setOneTimeCharge(number_format($onetimecharge,2,'.',''));
					$itemtaxamt='';
					
					$Item->setItemTaxAmount($itemtaxamt);
					//$Item->setCustomField('');
					//$responseArray['ItemOptions'] = array();
//					
					$Itemoption = new WG_Itemoption();

					if(is_array($product_info->data['attributes']) && count($product_info->data['attributes'])>0)
					{ 
						
						if($version == "6.x-2.0-rc2")
						{
							foreach($product_info->data['attributes'] as $attributes => $value)
							{ 
								//$responseArray['ItemOptions'][$optionI]['Name'] = htmlentities($attributes);
//								$responseArray['ItemOptions'][$optionI]['Value'] = htmlentities($value);
								$Itemoption->setOptionName(htmlentities($attributes));
								$Itemoption->setOptionValue(htmlentities($value));
							}
						}
						else
						{
							foreach($product_info->data['attributes'] as $attributes => $value)
							{
								foreach($value as $keyvalue )
								{ 
									//$responseArray['ItemOptions']['Name'] = htmlentities($attributes);
									//$responseArray['ItemOptions']['Value'] = htmlentities($keyvalue);
									
									
									$Itemoption->setOptionName(htmlentities($attributes));
									$Itemoption->setOptionValue( htmlentities($keyvalue));
								}
								$Item->setItemOptions($Itemoption->getItemoption());
							}
						}
					}

					
					
					$Order->setOrderItems($Item->getItem());
				} // end items 
				
				
				
				$coupon_name=0.0;
				$generic_name = 0.0;
				$uc_discounts = 0.0;
				foreach($order_details->line_items as $disc_type)
				{ 
					if($disc_type['type'] == 'coupon')
					{
						$coupon_title = $disc_type['title'];
						$coupon_amt = abs($disc_type['amount']);
						
						$Item->setItemCode($disc_type['type']);		
						$Item->setItemDescription($coupon_title);
						$Item->setQuantity('1');
						$Item->setUnitPrice('-'.$coupon_amt);
						$Item->setWeight('0');
						$Item->setFreeShipping('N');
						$Order->setOrderItems($Item->getItem());
						//break;
					} 
					if($disc_type['type'] == 'generic')
					{
						$generic_title = $disc_type['title'];
						$generic_amt = abs($disc_type['amount']);
						
						$Item->setItemCode($disc_type['type']);		
						$Item->setItemDescription($generic_title);
						$Item->setQuantity('1');
						$Item->setUnitPrice('-'.$generic_amt);
						$Item->setWeight('0');
						$Item->setFreeShipping('N');
						$Order->setOrderItems($Item->getItem());
						//break;
						
					}
					if($disc_type['type'] == 'uc_discounts')
					{
						//$uc_discounts = $disc_type['amount']; 
						$discount_title = $disc_type['title'];
						$discount_amt = abs($disc_type['amount']);
						
					}
					if($disc_type['type'] == 'gift_certificate')
					{
						$gift_title = $disc_type['title'];
						$gift_amt = abs($disc_type['amount']);
						

						$Item->setItemCode($disc_type['type']);		
						$Item->setItemDescription($gift_title);
						$Item->setQuantity('1');
						$Item->setUnitPrice('-'.$gift_amt);
						$Item->setWeight('0');
						$Item->setFreeShipping('N');
						$Order->setOrderItems($Item->getItem());
						//break;
					}
					
					
				} 
				
				$charges =new WG_Charges();
				$charges->setDiscount($discount_amt?$discount_amt:'0.00');
				$charges->setStoreCredit($storecredit?$storecredit:'0.0');
				//$totaltax=(float)$totaltax;
				$charges->setTax($totaltax);
				unset($totaltax);
				
				$charges->setShipping($totalship?$totalship:'0.0');
				unset($totalship);
				$charges->setTotal($total);
				
				$Order->setOrderChargeInfo($charges->getCharges());
				if($drupal_version > "6")
				{
					$Order->setShippedOn(date("m-d-Y",$order_data->created));
				}
				else
				{
					$Order->setShippedOn(date("m-d-Y",$order_data['created']));
				}
								

				$Order->setShippedVia($carrier[0]);
				$ord_count_ecc++;
				$Orders->setOrders($Order->getOrder());
			} 
			$Orders->setTotalRecordSent($ord_count_ecc);
			unset($order_details); 
		}
		//print_r($Orders->getOrders());
	//die("mmm");
		unset($orders);
		return $this->response($Orders->getOrders());
	}
	
	
	function getStoreOrderByIdForEcc($username,$password,$datefrom,$start_order_no,$ecc_excl_list,$order_per_response="25")
	{
		global $version,$drupal_version;
		
		$status = $this->auth_user($username,$password);
		
		if($status !='0')
		{
			return $status;
		}
		$currency = $store_name = variable_get('uc_currency_code', NULL);
		if(!isset($datefrom) or empty($datefrom))
		{
			$datefrom=date('m-d-Y');  
		}
	
		list($mm,$dd,$yy)=explode("-",$datefrom);
		$time_from = mktime(0,0,0,$mm,$dd,$yy);  
		
		#fetch Id of Order Status	
		foreach (uc_order_status_list('general') as $orderstatus1) {
			$firstset[] =$orderstatus1;
			$array = array($statusid1 => $status1);
		}
		foreach (uc_order_status_list('specific') as $orderstatus2) {
			$secondset[] = $orderstatus2;
		}
		$orderstatus =  array_merge( $firstset, $secondset);
		$ecc_excl_list = str_replace("'","",trim($ecc_excl_list));
		
		$ecc_list = explode(",",$ecc_excl_list);
	
		$list = array();
		foreach ($orderstatus as $storeOrder)
		{ 
			foreach ($ecc_list as $status_list)
			{ 
				if($status_list == $storeOrder['title'])
				{
					$list[$storeOrder['title']] = "'".$storeOrder['id']."'" ;
				}
				
			}
		}
		/*if($orderlist!='')
		{
			foreach ($orderstatus as $storeOrder)
			{ 
				$list[$storeOrder['title']] = "'".$storeOrder['id']."'" ;
			}
		}*/
		
		
		//Temporary hard coded for all order
		$ecc_excl_list = 'All';
		if($ecc_excl_list == 'All') {
			$list = array();
			foreach ($orderstatus as $storeOrder) { 
				$list[$storeOrder['title']] = "'".$storeOrder['id']."'" ;
			}
		}
		//Temporary hard coded for all order
		
		
		unset($orderstatus,$storeOrder, $orderstatus1,$orderstatus2,$uc_order_status_list);
		$ecc_orderstatus_list = implode(",",$list);
		
		#check for authorisation
	
		//$this->auth_user($username,$password);
		//die("hhh");
		
		define("QB_ORDERS_DOWNLOAD_EXCL_LIST", $ecc_orderstatus_list);
		define("QB_ORDERS_PER_RESPONSE",$order_per_response);  
		
		$orders_remained = $this->getOrdersRemained($start_date,$start_order_no);	

		$orders_remained=$orders_remained>0?$orders_remained:0;	
	
		$orders = array();
		$Orders = new WG_Orders();
		
		$sql = "SELECT o.order_id,o.data, o.uid, o.billing_first_name, o.billing_last_name, o.order_total, o.order_status, o.created, os.title FROM {uc_orders} o LEFT JOIN {uc_order_statuses} os ON o.order_status = os.order_status_id WHERE o.created >= '".$start_date."' and o.order_id  = ".$start_order_no." and o.order_status IN (".QB_ORDERS_DOWNLOAD_EXCL_LIST.") ORDER BY o.order_id ASC  ".(QB_ORDERS_PER_RESPONSE>0?"LIMIT 0, ".QB_ORDERS_PER_RESPONSE:'');

		$result = db_query($sql);
		if($drupal_version > "6")
		{
			while ($test = $result->fetchObject())
			{ 
				$orders1[]  = $test;
				//print_r($orders1);		
				foreach($orders1 as $member=>$data)
				{
					$orders[$member]=$data;
				}
			} 
			
		}
		else
		{
			while ($test = db_fetch_array($result))
			{
				$orders[]  = $test;		
			}
		}

	$no_orders = count($orders);
	
		if($orderlist!='')
		{
		$orders_remained=$no_orders>0?$no_orders:0;	
		}
		
		if ($no_orders<=0)
		{
			$no_orders = true;
			$Orders->setStatusCode($no_orders?"9999":"0");
			$Orders->setStatusMessage($no_orders?"No Orders returned":"Total Orders:".$orders_remained);
			//return $xmlResponse->generate();
			return $this->response($Orders->getOrders());
		}
		#Fetch Zone code
		
		$zone_sql = db_query("select zone_id,zone_code from {uc_zones}");
		if($drupal_version > "6")
		{
			while ($zone = $zone_sql->fetchObject())
			{ 
				$zone_code[$zone->zone_id] = $zone->zone_code;
			}

		}
		else
		{
			while ($zone = db_fetch_array($zone_sql))
			{
				$zone_code[$zone['zone_id']] = $zone['zone_code'];
			}
		}
		
		#Fetch Country name
	
		$country_sql = db_query("select country_id, country_name from {uc_countries}");
		if($drupal_version > "6")
		{
			while($country = $country_sql->fetchObject())
			{
				$country_name[$country->country_id] = $country->country_name;
			}
		}
		else
		{
			while($country = db_fetch_array($country_sql))
			{
				$country_name[$country['country_id']] = $country['country_name'];
			}
		}
		if($drupal_version<'6')
		{
			$taxes = uc_taxes_get_rates();
		}
		else
		{
			$taxes = uc_taxes_rate_load();	
		}
		if($taxes)
		{
			
			foreach ($taxes as $k=>$iInfo2) 
			{		
				foreach($iInfo2->taxed_product_types as $k2=>$value)
				{
					$taxable_arr[] = $value;	
				}
			}
		}
		
		$taxable_arr = array_unique($taxable_arr);
		
		
		//if($orderlist!='')
		//{
/*		foreach (uc_order_status_list('general') as $orderstatus1) {
			$firstset[] =$orderstatus1;
			$array = array($statusid1 => $status1);
		}
		 foreach (uc_order_status_list('specific') as $orderstatus2) {
			$secondset[] = $orderstatus2;
		}
		
		$orderstatus =  array_merge( $firstset, $secondset);
		$i=0;
		
		foreach($orderstatus as $orderstat)
		{
		$state=$orderstat['state'];
		$title=$orderstat['title'];
		$order_stat=
		
		$i++;
		}
	print_r($order_stat);
		die;*/
		//}

		$ord_count_ecc = 0;
		if($orders)
		{  
			
			$Orders->setStatusCode(0);
			$Orders->setStatusMessage("Total Orders:".$orders_remained);
			$Orders->setTotalRecordFound($orders_remained?(int)$orders_remained:"0");
			$store_name = variable_get('uc_store_name', NULL);
			
			foreach ($orders as $order_data)
			{ 	
				
				unset($order_details); 
				unset($order);
				unset($order_details->line_items);
				unset($order_details->payment_method);
				unset($order_details->products);		
				if($drupal_version > "6")
				{
					$order_details = uc_order_load($order_data->order_id);
				}
				else
				{
					//$order_details = uc_order_load($order_data['order_id']);
					//$order_data['order_id'] = '129';
					$order_details = uc_order_load($order_data['order_id']);
				}
				
				$weightsymbol = 'lbs';
				$weight_symbol_grams ='453.6';
				
				$total = $order_details->order_total;
				
				unset($totaltax,$totalship);
				foreach($order_details->line_items as $taxes)
				{
					if($taxes['type'] == 'tax')
					{
						$totaltax += $taxes['amount']; 
					}
					
				}
				$ship_method ="";
				foreach($order_details->line_items as $shipping)
				{
					if($shipping['type'] == 'shipping')
					{
						$totalship += $shipping['amount']; 
						$ship_method =$ship_method.$shipping['title'];
					}
				}
				
				$extra = unserialize($order['extra']);
				
				if($drupal_version > "6")
				{
					$time = strtotime($order_data->created);
				}
				else
				{
					$time = strtotime($order_data['created']);
				}
				
				$Order = new WG_Order();
				
				$objOrderInfo	=	new WG_OrderInfo();
				
				if($drupal_version > "6")
				{
					$orderid = $order_data->order_id;
					$Order->setOrderId($order_data->order_id);
					$objOrderInfo->setTitle('');
					$objOrderInfo->setFirstName($order_data->billing_first_name);
					$objOrderInfo->setLastName( $order_data->billing_last_name);
					$objOrderInfo->setDate(date("Y-m-d",$order_data->created));
					//$Order->setTime(format_date($order_data['created'], 'custom', 'H:i:s'));
					$objOrderInfo->setTime(date("h:i:s A",$order_data->created));
					$objOrderInfo->setStoreID($store_name);
					$objOrderInfo->setStoreName($store_name);
					$objOrderInfo->setCurrency($currency);
					$objOrderInfo->setWeight_Symbol($weightsymbol);
					$objOrderInfo->setWeight_Symbol_Grams($weight_symbol_grams);
					$objOrderInfo->setCustomerId($order_data->uid);
					
					
				//	if($orderlist!='')
		           //  {
					//$Order->setStatus(array_search("'".$order_details->order_status."'",$statusarray));
		            //  }else{
					$objOrderInfo->setStatus(array_search("'".$order_details->order_status."'",$list));
		              //     }			
				}
				else
				{
					$orderid = $order_data['order_id'];
					$objOrderInfo->setOrderId($order_data['order_id']);
					$objOrderInfo->setTitle('');
					$objOrderInfo->setFirstName($order_data['billing_first_name']);
					$objOrderInfo->setLastName( $order_data['billing_last_name']);
					$objOrderInfo->setDate(date("Y-m-d",$order_data['created']));
					//$Order->setTime(format_date($order_data['created'], 'custom', 'H:i:s'));
					$objOrderInfo->setTime(date("h:i:s A",$order_data['created']));
					$objOrderInfo->setStoreID($store_name);
					$objOrderInfo->setStoreName($store_name);
					$objOrderInfo->setCurrency($currency);
					$objOrderInfo->setWeight_Symbol($weightsymbol);
					$objOrderInfo->setWeight_Symbol_Grams($weight_symbol_grams);
					$objOrderInfo->setCustomerId($order_data['uid']);
					//if($orderlist!='')
		            // {
					//$Order->setStatus(array_search("'".$order_details->order_status."'",$statusarray));
		             // }else{
					$objOrderInfo->setStatus(array_search("'".$order_details->order_status."'",$list));
		            //       }	
				}
				unset($admin_comments);	
				$result =db_query("select comment_id, message from {uc_order_admin_comments} oa where oa.order_id =".$orderid." ORDER BY oa.comment_id  ");
				if($drupal_version > "6")
				{
					//while ($ad_comment = db_fetch_array($result)) 
					while($ad_comment = $result->fetchObject())
					{ 
							$admin_comments = $admin_comments ." ".$ad_comment->message;
					}
				}
				else
				{
					while ($ad_comment = db_fetch_array($result)) 
					{ 
							$admin_comments = $admin_comments ." ".$ad_comment['message'];
					}
				}
				
				$objOrderInfo->setNotes(strip_tags($admin_comments));
				
				$objOrderInfo->setFax('');
				if($drupal_version > "6")
				{
					$comments = db_query("select message from {uc_order_comments} oc where oc.order_id =".$orderid." ORDER BY oc.comment_id  ")->fetchField();
				}
				else
				{
					$comments = db_result(db_query("select message from {uc_order_comments} oc where oc.order_id =".$orderid." ORDER BY oc.comment_id  "));
				}

				//$Order->setComment('');
				$Order->setOrderInfo($objOrderInfo->getOrderInfo());
				$Order->setComment($comments?$comments:"");
				# Orders/Bill info
				$payment_method = $order_details->payment_method;
				
				$payresult = db_query("SELECT * FROM {uc_payment_receipts} where order_id ='".$orderid."'");	
				if($drupal_version > "6")
				{
					while($test1 = $payresult->fetchObject())
					{
						 $payinfo = $test1;
					}
				}
				else
				{		
					$payinfo =db_fetch_object($payresult);
				}
				
				if(!empty($payinfo))
				{
					$payment_method = $payinfo->method;
				}
	
	
				$billing_first_name = $order_details->billing_first_name;
				$billing_last_name 	= $order_details->billing_last_name;
				$billing_company 	= $order_details ->billing_company;
				$billing_street1 	= $order_details->billing_street1;
				$billing_street2 	= $order_details->billing_street2;
				$billing_city 		= $order_details->billing_city;
				$billing_postal_code= $order_details->billing_postal_code;
				$primary_email		= $order_details->primary_email;
				$billing_phone		= $order_details->billing_phone;
				$zone_id 			= $order_details->billing_zone; 
				$country_id         = $order_details->billing_country; 		
				if($drupal_version > "6")
				{
					$abc = unserialize($order_data->data);
				}
				else
				{				
					$abc = unserialize($order_data['data']);
				}
				
				$key = uc_credit_encryption_key();
				
				$crypt = new uc_encryption_class;	
				
				$transaction_id = '';
				$order_details->payment_details = unserialize($crypt->decrypt($key, $abc['cc_data']));
				$Bill = new WG_Bill();
				$CreditCard = new WG_CreditCard();
				if (is_array($order_details->payment_details))
				{
					$card_type = $order_details->payment_details['cc_type'];
					$card_no =   $order_details->payment_details['cc_number'];
					$card_owner =$order_details->payment_details['cc_owner'];
					$card_cvv =  $order_details->payment_details['cc_cvv'];
					
					
					if(!empty($order_details->payment_details['cc_exp_month']) || !empty($order_details->payment_details['cc_exp_year']))
					{
						$expiration_date = ($order_details->payment_details['cc_exp_month']."/".$order_details->payment_details['cc_exp_year']);
					}
					else
					{
						$expiration_date = "";
					}
					
					if(is_array($abc['cc_txns']))
					{
						if(is_array($abc['cc_txns']['authorizations']))
							{
								$transaction_id = array_keys($abc['cc_txns']['authorizations']);
								$transaction_id = $transaction_id[0];
							}
						else {
								$transaction_id = '';
							}	
					}
					else
					{
						$transaction_id ='';
					}
						
					
					# Credit card 
					$CreditCard->setCreditCardType($card_type);
					$CreditCard->setCreditCardCharge('');
					$CreditCard->setExpirationDate($expiration_date);
					$CreditCard->setCreditCardName($card_owner);
					$CreditCard->setCreditCardNumber($card_no);
					$CreditCard->setCVV2($card_cvv);
					$CreditCard->setAdvanceInfo('');
	
					if(''==$transaction_id )
					{						
						$payinfo->data = unserialize($payinfo->data);
						$CreditCard->setTransactionId($payinfo->data['txn_id']);

					}
					else
					{
						$CreditCard->setTransactionId($transaction_id);
					}
				}
				else
				{	
					if(''==$transaction_id )
					{
						if($drupal_version > "6")
						{
							## Table {uc_payment_paypal_ipn} is not exist in versions
						}
						else
						{
							$result = db_query("SELECT txn_id FROM {uc_payment_paypal_ipn} where order_id ='".$orderid."'");			
							$shipment =db_fetch_object($result);
						}
						
						$CreditCard->getCreditCard();
						$CreditCard->setTransactionId($shipment->txn_id);
						
					}					
				}
				
				//$CreditCard->getCreditCard();
				//$Bill->setCreditCardInfo($CreditCard->getCreditCard());
				unset($card_type,$expiration_date,$card_no,$card_owner,$card_cvv,$transaction_id );
				#Bill
				$Bill->setPayMethod($payment_method);
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
				$Bill->setPONumber('');								
				$Order->setOrderBillInfo($Bill->getBill());	
				
				# Order Shipping Info
				$shipping_first_name = $order_details->delivery_first_name;
				$shipping_last_name  = $order_details->delivery_last_name;
				$shipping_company 	= $order_details ->delivery_company;
				$shipping_street1 	= $order_details->delivery_street1;
				$shipping_street2 	= $order_details->delivery_street2;
				$shipping_city 		= $order_details->delivery_city;
				$shipping_postal_code = $order_details->delivery_postal_code;
				$shipping_phone		= $order_details->delivery_phone;
				$zone_id 			= $order_details->delivery_zone; 
				$country_id         = $order_details->delivery_country; 			
				
				// Retrieve Carrier's Title using id (from associative array)
				if($drupal_version > "6")
				{
					$All_shipping_carrier = module_invoke_all('uc_shipping_method');
				}
				else
				{
					$All_shipping_carrier = module_invoke_all('shipping_method');
				}
				foreach($All_shipping_carrier as $key1 => $all_carrier)
				{
					
					$shipping_name_key[] =  $key1."-".$all_carrier['title'];
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
				}
				$Ship =new WG_Ship();
				$Ship->setShipMethod(htmlentities($ship_method, ENT_QUOTES));
				$Ship->setCarrier($carrier[0]);
			
				$result = db_query("SELECT * FROM {uc_shipments} WHERE order_id = ".$orderid);
				if($drupal_version > "6")
				{
					while($test1 = $result->fetchObject())
					{
						 $shipment = $test1;
					}
				}
				else
				{
					$shipment =db_fetch_object($result);
				}
				
				if($shipment)
				{
					$tracking_number = $shipment->tracking_number;
				}
				else
				{
					$tracking_number ='';
				}
				
				#Ship Node
				$Ship->setTrackingNumber($tracking_number);
				$Ship->setTitle('');
				$Ship->setFirstName($shipping_first_name);
				$Ship->setLastName($shipping_last_name);
				$Ship->setCompanyName($shipping_company);
				
				$Ship->setAddress1( $shipping_street1);
				$Ship->setAddress2($shipping_street2);
				$Ship->setCity($shipping_city);
				$Ship->setState($zone_code[$zone_id]);
				$Ship->setZip($shipping_postal_code);
				$Ship->setCountry($country_name[$country_id]);
				$Ship->setEmail('');
				$Ship->setPhone($shipping_phone);
				$Order->setOrderShipInfo($Ship->getShip());

				# get items of order
				
				foreach($order_details->products as $product_info)
				{ 
					$Item = new WG_Item();
					$item_code = $product_info->model;
					$item_name = $product_info->title;
					$qty 	   = $product_info->qty; 
					$unitprice = $product_info->price;	
					$weight	   = $product_info->weight;
					$cost 	   = $product_info->cost;
					//$description = db_fetch_array(db_query($sql));
					//$desc=$description['body']; 
					if($qty=='')
						$qty=0;
						
					# Itme node
					$Item->setItemCode(htmlentities($item_code, ENT_QUOTES));		
					$Item->setItemDescription(htmlentities($item_name, ENT_QUOTES));
					$Item->setItemShortDescr(htmlentities($desc, ENT_QUOTES));
					$Item->setQuantity($qty);
					
					//$Item->setCostPrice($cost);
					$cost=(float)$cost;
					//$Item->setListPrice($cost);
					//$unitprice=(float)$unitprice;
					$Item->setUnitPrice($unitprice);
                    //$weight=(float)$weight;
					$Item->setWeight($weight);
					if($drupal_version > "6")
					{	
						$Item->setFreeShipping($data->free_shipping);
						//$iInfo[$data->discount_avail]=(float)$iInfo[$data->discount_avail];
						$Item->setDiscounted($iInfo[$data->discount_avail]);
						$Item->setshippingFreight($data->shipping_freight);
					}
					else
					{
						$Item->setFreeShipping($data['free_shipping']);
						//$iInfo[$data['discount_avail']]=(float)$iInfo[$data['discount_avail']];
						$Item->setDiscounted($iInfo[$data['discount_avail']]);
						$Item->setshippingFreight($data['shipping_freight']);
					}

					
					$Item->setWeight_Symbol($weightsymbol);
					$Item->setWeight_Symbol_Grams($weight_symbol_grams);
					unset($product_type);
					$Qresult1= db_query("SELECT np.* FROM {node}  n ,{uc_products} p, {node_type} np  where n.vid = p.vid and np.type = n.type and np.module = 'uc_product' and n.nid = ".$product_info->nid."");
					if($drupal_version > "6")
					{ 
						while ($QiInfo1 =$Qresult1->fetchObject()) 
						{	
								$product_type = $QiInfo1->type;
						} 
						
					}
					else
					{ 
						while ($QiInfo1 = db_fetch_object($Qresult1)) 
						{
							$product_type = $QiInfo1->type;
						}
					}
					
				
					if(in_array($product_type,$taxable_arr))
					{
						$Item->setTaxExempt("N");
					}
					else
					{
						$Item->setTaxExempt("Y");
					}
					
					$onetimecharge = "0.00";
					$Item->setOneTimeCharge(number_format($onetimecharge,2,'.',''));
					$itemtaxamt='';
					
					$Item->setItemTaxAmount($itemtaxamt);
					//$Item->setCustomField('');
					//$responseArray['ItemOptions'] = array();
//					
					$Itemoption = new WG_Itemoption();

					if(is_array($product_info->data['attributes']) && count($product_info->data['attributes'])>0)
					{ 
						
						if($version == "6.x-2.0-rc2")
						{
							foreach($product_info->data['attributes'] as $attributes => $value)
							{ 
								//$responseArray['ItemOptions'][$optionI]['Name'] = htmlentities($attributes);
//								$responseArray['ItemOptions'][$optionI]['Value'] = htmlentities($value);
								$Itemoption->setOptionName(htmlentities($attributes));
								$Itemoption->setOptionValue(htmlentities($value));
							}
						}
						else
						{
							foreach($product_info->data['attributes'] as $attributes => $value)
							{
								foreach($value as $keyvalue )
								{ 
									//$responseArray['ItemOptions']['Name'] = htmlentities($attributes);
									//$responseArray['ItemOptions']['Value'] = htmlentities($keyvalue);
									
									
									$Itemoption->setOptionName(htmlentities($attributes));
									$Itemoption->setOptionValue( htmlentities($keyvalue));
								}
								$Item->setItemOptions($Itemoption->getItemoption());
							}
						}
					}

					
					
					$Order->setOrderItems($Item->getItem());
				} // end items 
				
				
				
				$coupon_name=0.0;
				$generic_name = 0.0;
				$uc_discounts = 0.0;
				foreach($order_details->line_items as $disc_type)
				{ 
					if($disc_type['type'] == 'coupon')
					{
						$coupon_title = $disc_type['title'];
						$coupon_amt = abs($disc_type['amount']);
						
						$Item->setItemCode($disc_type['type']);		
						$Item->setItemDescription($coupon_title);
						$Item->setQuantity('1');
						$Item->setUnitPrice('-'.$coupon_amt);
						$Item->setWeight('0');
						$Item->setFreeShipping('N');
						$Order->setOrderItems($Item->getItem());
						//break;
					} 
					if($disc_type['type'] == 'generic')
					{
						$generic_title = $disc_type['title'];
						$generic_amt = abs($disc_type['amount']);
						
						$Item->setItemCode($disc_type['type']);		
						$Item->setItemDescription($generic_title);
						$Item->setQuantity('1');
						$Item->setUnitPrice('-'.$generic_amt);
						$Item->setWeight('0');
						$Item->setFreeShipping('N');
						$Order->setOrderItems($Item->getItem());
						//break;
						
					}
					if($disc_type['type'] == 'uc_discounts')
					{
						//$uc_discounts = $disc_type['amount']; 
						$discount_title = $disc_type['title'];
						$discount_amt = abs($disc_type['amount']);
						
					}
					if($disc_type['type'] == 'gift_certificate')
					{
						$gift_title = $disc_type['title'];
						$gift_amt = abs($disc_type['amount']);
						

						$Item->setItemCode($disc_type['type']);		
						$Item->setItemDescription($gift_title);
						$Item->setQuantity('1');
						$Item->setUnitPrice('-'.$gift_amt);
						$Item->setWeight('0');
						$Item->setFreeShipping('N');
						$Order->setOrderItems($Item->getItem());
						//break;
					}
					
					
				} 
				
				$charges =new WG_Charges();
				$charges->setDiscount($discount_amt?$discount_amt:'0.00');
				$charges->setStoreCredit($storecredit?$storecredit:'0.0');
				//$totaltax=(float)$totaltax;
				$charges->setTax($totaltax);
				unset($totaltax);
				
				$charges->setShipping($totalship?$totalship:'0.0');
				unset($totalship);
				$charges->setTotal($total);
				
				$Order->setOrderChargeInfo($charges->getCharges());
				if($drupal_version > "6")
				{
					$Order->setShippedOn(date("m-d-Y",$order_data->created));
				}
				else
				{
					$Order->setShippedOn(date("m-d-Y",$order_data['created']));
				}
								

				$Order->setShippedVia($carrier[0]);
				$ord_count_ecc++;
				$Orders->setOrders($Order->getOrder());
			} 
			$Orders->setTotalRecordSent($ord_count_ecc);
			unset($order_details); 
		}
		//print_r($Orders->getOrders());
	//die("mmm");
		unset($orders);
		return $this->response($Orders->getOrders());
	}
	
	
	function OrderUpdateStatus($username,$password,$orderid,$current_order_status,$order_status,$order_notes,$storeid=1,$emailAlert='N') {
		
		global $version,$drupal_version;
			
		$OrderId			=	trim($orderid); 
		//$CurrentOrderStatus	=	strtolower(str_replace(' ', '_', trim($current_order_status)));
		$CurrentOrderStatus	=	trim($current_order_status);
		$OrderStatus		=	trim($order_status); 
		$OrderNotes			=	trim($order_notes);
	
		
		$status = $this->auth_user($username,$password);
		if($status !='0') { return $status; }
		
		$Orders = new WG_Orders();		
		if(!isset($OrderId)) {
			$Orders->setStatusCode("9997");
			$Orders->setStatusMessage("Unknown request or request not in proper format");	
			return $this->response($Orders->getOrders());
		}
		
		$update_notes_flag	=	false;
		$update_status_flag	=	false;
	
		$Orders->setStatusCode("0");
		
		# Fetch All order status of Cart 
		$sql= db_query("SELECT order_status_id,title from {uc_order_statuses} ");
		if($drupal_version > "6") {
			//while($status_all = db_fetch_array($sql))
			while ($status_all =$sql->fetchObject()) {
				$statuses[$status_all->title] = $status_all->order_status_id;
			}
		} else {
			while($status_all = db_fetch_array($sql)) {
				$statuses[$status_all['title']] = $status_all['order_status_id'];
			}
		} 
		
		
		$order_id = $OrderId;
		# for fetching user id	
		
		if($drupal_version > "6")
		{	
			$user = db_query("SELECT uid,name,pass FROM {users} WHERE name = ".$this->mySQLSafe($username)." ")->fetchObject();
			$uid = $user->uid;
		
		}
		else
		{
			$user = db_fetch_array(db_query("SELECT uid,name,pass FROM {users} u WHERE LOWER(name) = LOWER(".$this->mySQLSafe($username).")"));
			$uid = $user['uid'];
		}
		
		
		if($OrderStatus == '') {
			$status = $statuses[$CurrentOrderStatus];
		} else {
			$status = $statuses[$OrderStatus];
		}
		
		$info = "";
			
		if ($OrderNotes!="") $info .=" \n".$OrderNotes;
		
		if($emailAlert=='N') { $notify = 0; }
		else if($emailAlert=='Y') { $notify = 1; }
		
		$time = time();	
		# Update  Order Status
		if($OrderStatus != '') {
			uc_order_update_status($order_id, $status);
			$update_status_flag = true;
		}	
		
		if ($OrderNotes!="") {	
			$result =db_query("select comment_id, order_status from {uc_order_comments} oc where oc.order_id =".$order_id." ORDER BY oc.comment_id  ");
			$order_comments =array();

			if($drupal_version > "6") {
				while ($comment = $result->fetchObject()) { 
					$order_comments = $comment;
				} 
			} else {
				while ($comment = db_fetch_array($result)) {
					$order_comments = $comment;
				}
			}
			
			
			
			$result =db_query("select comment_id, message from {uc_order_admin_comments} oa where oa.order_id =".$order_id." ORDER BY oa.comment_id  ");
			$admin_comments =array();
			if($drupal_version > "6") {
				while ($ad_comment = $result->fetchObject()) { 
					$admin_comments = $ad_comment;
				} 
			} else {
				while ($ad_comment = db_fetch_array($result)) {
					$admin_comments = $ad_comment;
				}
			}
			
			/*if($drupal_version > "6") {
				$status_var = $comments->order_status;
			} else {
				$status_var = $comments['order_status'];
			}*/
			/*if($order_comments['order_status']== $status && strtolower($status)!='canceled') { 	
				//db_query("UPDATE {uc_order_comments} SET message = '".$info."',order_status ='$status' , notified =$notify, created =$time where comment_id = ".$order_comments['comment_id']." ");
				db_query("UPDATE {uc_order_comments} SET order_status ='$status' , notified =$notify, created =$time where comment_id = ".$order_comments['comment_id']." ");
				
				db_query("UPDATE {uc_order_admin_comments} SET message = '".$info."' created =$time where comment_id = ".$admin_comments['comment_id']." ");
			} else*/if (strtolower($status)=='canceled') {
			
			
			
				if(isset($comments)) {
					if($drupal_version > "6") {
						db_update('uc_order_comments')
						  ->fields(array('message'=>'Order status changed to '.$status.'.','order_status' => $status,'notified'=>$notify,'created'=>$time))
						  ->condition('comment_id',$comments->comment_id)
						  ->execute();
					} else {
						db_query("UPDATE {uc_order_comments} SET message = 'Order status changed to ".$this->mySQLSafe($status).".',order_status ='$status' , notified =$notify, created =$time where comment_id = ".$this->mySQLSafe($comments['comment_id'])." ");
					}
				} else {
					if($drupal_version > "6") {
						uc_order_comment_save($order_id, $uid, 'Order status changed to '.$status.'.', $type = 'order', $status = $status, $notify);
					} else {
						db_query("INSERT INTO {uc_order_comments} (order_id, uid, message, order_status, notified, created) VALUES ($order_id, $uid, 'Order status changed to ".$this->mySQLSafe($status).".','$status', $notify , $time)");
					}
				}
			
			
				if(isset($order_comments['comment_id']) || isset($order_comments->comment_id)) {
					
					
					if($drupal_version > "6") {
						db_update('uc_order_comments')
						  ->fields(array('message'=>'Order status changed to '.$status.'.','order_status' => $status,'notified'=>$notify,'created'=>$time))
						  ->condition('comment_id',$order_comments->comment_id)
						  ->execute();
					} else {
						db_query("UPDATE {uc_order_comments} SET message = 'Order status changed to ".$status.".',order_status ='$status' , notified =$notify, created =$time where comment_id = ".$order_comments['comment_id']." ");
					}
					
					
				} else {
				
					
					if($drupal_version > "6") {
						uc_order_comment_save($order_id, $uid, 'Order status changed to '.$status.'.', $type = 'order', $status = $status, $notify);
					} else {
						//db_query("INSERT INTO {uc_order_comments} (order_id, uid, message, order_status, notified, created) VALUES ($order_id, $uid, 'Order status changed to ".$this->mySQLSafe($status).".','$status', $notify , $time)");
						
						db_query("INSERT INTO {uc_order_comments} (order_id, uid, message, order_status, notified, created) VALUES ($order_id, $uid, 'Order status changed to ".$status.".','$status', $notify , $time)");
						
					}
				
				}
			} else { 
				
				//db_query("INSERT INTO {uc_order_comments} (order_id, uid,  order_status, notified, created) VALUES ($order_id, $uid,  '$status', $notify , $time)");
				//db_query("INSERT INTO {uc_order_admin_comments} SET message = '".$info."', created = $time, order_id = ".$order_id.",uid = ".$uid."");
				
				if($drupal_version > "6") {
					uc_order_comment_save($order_id, $uid, $info, $type = 'order', $status = $status, $notify);
					uc_order_comment_save($order_id, $uid, $info, $type = 'admin', $status = $status, $notify);
				} else {
					db_query("INSERT INTO {uc_order_comments} (order_id, uid,  order_status, notified, created) VALUES ($order_id, $uid,  ".$this->mySQLSafe($status)."', $notify , ".$this->mySQLSafe($time).")");
					db_query("INSERT INTO {uc_order_admin_comments} SET message = ".$this->mySQLSafe($info).", created = ".$this->mySQLSafe($time).", order_id = ".$this->mySQLSafe($order_id).",uid = ".$this->mySQLSafe($uid)."");
				}
			}
			$update_notes_flag = true;
		}
		
		

		if(strtolower($statustype) == 'posttostore') {		
			# Fetch delivery & Billing address
			$add1 =db_query("SELECT delivery_first_name first_name ,delivery_last_name last_name,delivery_phone phone,delivery_company company,	delivery_street1 street1,delivery_street2 street2,delivery_city city,delivery_zone zone,delivery_postal_code postal_code,delivery_country country,primary_email email from {uc_orders} where order_id =".$order_id);
			
			if($drupal_version > "6") {
				while ($add = $add1->fetchObject()) { 
					$deliveryadd = $add;
				}
			} else {
				$deliveryadd = db_fetch_object($add1);	
			}	
			
			$add2 =db_query("SELECT billing_first_name first_name ,billing_last_name last_name,billing_phone phone,billing_company company,	billing_street1 street1,billing_street2 street2,billing_city city,billing_zone zone,billing_postal_code postal_code,billing_country country from {uc_orders} where order_id =".$order_id);
			
			if($drupal_version > "6") {
				while ($add_b = $add2->fetchObject()) { 
					$billingadd = $add_b;
				}
			} else {
				$billingadd = db_fetch_object($add2);
			}	
			
							
			$sql = "SELECT package_id  from {uc_packages} where order_id = ".$order_id ;		
			if($drupal_version > "6") { 
				$package_id = db_query("SELECT package_id  from {uc_packages} where order_id = ".$this->mySQLSafe($order_id))->fetchField();
			} else {
				$sql = "SELECT package_id  from {uc_packages} where order_id = ".$this->mySQLSafe($order_id) ;	
				$package_id = db_result(db_query($sql));
			}
			
			$sql =  db_query("Select order_product_id,order_id,nid,data,qty from {uc_order_products} where order_id=".$order_id);
			unset($order_products);
			
			if($drupal_version > "6") {
				while ($order_prod = $sql->fetchObject()) { 
					$order_products[] = $order_prod;	
					
					$prd['checked'] =  1; 
					$prd['qty'] =  $order_prod->qty; 
					$prd['package'] = 1;  			
					$package_wg['products'][$order_prod->order_product_id] = (object)$prd;		
				}
			} else {
				while ($order_prod = db_fetch_object($sql)) {
					$order_products[] = $order_prod;	
					
					$prd['checked'] =  1; 
					$prd['qty'] =  $order_prod->qty; 
					$prd['package'] = 1;  			
					$package_wg['products'][$order_prod->order_product_id] = (object)$prd;			
				}
			}
			
			
			
			$package_wg['order_id'] = $order_id;
			$package_wg['shipping_type'] = 'small_package';		
				
			# Insert or Update the tracking number abd other data for  Order number .
			$sql = db_query("Select * from {uc_shipments} where order_id=".$order_id);
			
			if($drupal_version > "6") {
				while ($ship = $sql->fetchObject()) {
					$record = $ship;
				}
			} else {
				$record = db_fetch_array($sql);	
			}
			
			
			//$dt = explode("/",$order_wg['ShippedOn']);
			$dt1 = gmmktime(12, 0, 0, date("Y"), date("m"), date("d"));		
			
			$order_details123 = uc_order_load($order_id);
			foreach($order_details123->line_items as $shipping123)
			{
				if($shipping123['type'] == 'shipping')
				{
					$totalship += $shipping123['amount']; 
					$ship_method =$ship_method.$shipping123['title'];
				}
			}
				
			if($record)
			{ 
				if($drupal_version > "6")
				{ 
					$shipment_id = $record->sid;
					
					db_update('uc_shipments')
					  ->fields(array('o_first_name' =>$deliveryadd->first_name ,'o_last_name' => $deliveryadd->last_name,
					'o_company' => $deliveryadd->company,
					'o_street1'=> $deliveryadd->street1,
					'o_street2'=> $deliveryadd->street2,
					'o_city'=>$deliveryadd->city,
					'o_zone'=>$deliveryadd->zone,
					'o_postal_code'=> $deliveryadd->postal_code ,
					'o_country'=> $deliveryadd->country,
					'd_first_name'=> $billingadd->first_name,
					'd_last_name'=> $billingadd->last_name ,
					'd_company'=> $billingadd->company,
					'd_street1'=> $billingadd->street1,
					'd_street2'=> $billingadd->street2,
					'd_city'=> $billingadd->city,
					'd_zone'=> $billingadd->zone,
					'd_postal_code'=> $billingadd->postal_code,
					'd_country'=> $billingadd->country,
					'shipping_method'=>$order_wg['ShippedVia'],
					'accessorials'=>'',
					'carrier'=>$order_wg['ServiceUsed'],
					'tracking_number'=>$order_wg['TrackingNumber'],
					'ship_date'=>$dt1,
					'expected_delivery'=>$dt1,
					'cost'=>$totalship))
					  ->condition('order_id', $order_id)
					  ->execute();


				//die("vvvv");
					$result1 ="Success";	
					
				}
				else
				{ 
					$shipment_id = $record['sid'];
					db_query("UPDATE {uc_shipments} SET  o_first_name = ".$this->mySQLSafe($deliveryadd->first_name)." ,o_last_name = ".$this->mySQLSafe($deliveryadd->last_name).",
					o_company = ".$this->mySQLSafe($deliveryadd->company).",
					o_street1= ".$this->mySQLSafe($deliveryadd->street1).",
					o_street2= ".$this->mySQLSafe($deliveryadd->street2).",
					o_city=".$this->mySQLSafe($deliveryadd->city).",
					o_zone=".$this->mySQLSafe($deliveryadd->zone).",
					o_postal_code= ".$this->mySQLSafe($deliveryadd->postal_code)." ,
					o_country= ".$this->mySQLSafe($deliveryadd->country).",
					d_first_name= ".$this->mySQLSafe($billingadd->first_name).",
					d_last_name= ".$this->mySQLSafe($billingadd->last_name)." ,
					d_company= ".$this->mySQLSafe($billingadd->company).",
					d_street1= ".$this->mySQLSafe($billingadd->street1).",
					d_street2= ".$this->mySQLSafe($billingadd->street2).",
					d_city= ".$this->mySQLSafe($billingadd->city).",
					d_zone= ".$this->mySQLSafe($billingadd->zone).",
					d_postal_code= ".$this->mySQLSafe($billingadd->postal_code).",
					d_country= ".$this->mySQLSafe($billingadd->country).",
					shipping_method=".$this->mySQLSafe($order_wg['ShippedVia']).",
					accessorials='',
					carrier=".$this->mySQLSafe($order_wg['ServiceUsed']).",
					tracking_number=".$this->mySQLSafe($order_wg['TrackingNumber']).",
					ship_date=".$this->mySQLSafe($dt1).",
					expected_delivery=".$this->mySQLSafe($dt1).",
					cost=".$this->mySQLSafe($totalship)."                 where order_id = ".$order_id." ");
					$result1 ="Success";
					
				} 
			}
			else
			{ 
				if($drupal_version > "6")
				{
					# if required follow to function 'uc_shipping_shipment_edit_submit'
					
					$shipment = new stdClass();
					$shipment->order_id = $order_id;
					if (isset($record['sid'])) 
					{
						$shipment->sid = $record['sid'];
					}
					
					$origin = array('first_name'=>$deliveryadd->first_name,'last_name'=>$deliveryadd->last_name,'company'=>$deliveryadd->company,'street1'=>$deliveryadd->street1 , 'street2'=>$deliveryadd->street2, 'city'=>$deliveryadd->city,'zone'=>$deliveryadd->zone,'country'=>$deliveryadd->country,'postal_code'=>$deliveryadd->postal_code,'phone'=>'');
					
					$shipment->origin = (object) $origin;

					$address = array('order_id'=>$order_id,'uid'=>$uid,'delivery_first_name'=>$billingadd->first_name ,'delivery_last_name'=>$billingadd->last_name ,'delivery_phone'=>'' ,'delivery_company'=> $billingadd->company,'delivery_street1'=>$billingadd->street1 , 'delivery_street2'=>$billingadd->street2 ,'delivery_city'=>$billingadd->city , 'delivery_zone'=>$billingadd->zone  ,'delivery_postal_code'=>$billingadd->postal_code ,'delivery_country'=>$billingadd->country,'billing_first_name'=>$deliveryadd->first_name ,'billing_last_name'=>$deliveryadd->last_name ,'billing_phone'=>'' ,'billing_company'=>$deliveryadd->company ,'billing_street1'=>$deliveryadd->street1 ,'billing_street2'=>$deliveryadd->street2 , 'billing_city'=>$deliveryadd->city ,'billing_zone'=>$deliveryadd->zone ,'billing_postal_code'=>$deliveryadd->postal_code ,'billing_country'=>$deliveryadd->country );
					
					
					
					$destination = array('address'=>(object)$address,'first_name'=>$deliveryadd->first_name ,'last_name'=>$deliveryadd->last_name ,'company'=>$deliveryadd->company ,'street1'=>$deliveryadd->street1 ,'street2'=>$deliveryadd->street2 ,'city'=>$deliveryadd->city ,'zone'=>$deliveryadd->zone ,'country'=>$deliveryadd->country ,'postal_code'=>$deliveryadd->postal_code,'phone'=>'');
					
				
					
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
					
					$shipment_id  =   db_query('SELECT MAX(sid) FROM {uc_shipments}')->fetchField();
					$result1 ="Success";	
					//die("hihihi");
					
				}
				else
				{
					if($drupal_version == "5")
					{
						
						//$sql = db_query("SELECT MAX(sid) FROM {uc_shipments} ");
//								
//								$max_sid  = db_fetch_array($sql);
//								
//								$sid=$max_sid['MAX(sid)']+1;
						
						$sid = db_next_id('{uc_shipments}_sid');
						
						db_query("INSERT INTO {uc_shipments} (sid,order_id,o_first_name,o_last_name,o_company,o_street1,o_street2,o_city,o_zone,o_postal_code,o_country,d_first_name,d_last_name,d_company,d_street1,d_street2,d_city,d_zone,d_postal_code,d_country,shipping_method,accessorials,carrier,transaction_id,tracking_number,ship_date,expected_delivery,cost) values (".$sid.",".$order_id." ,'".$deliveryadd->first_name."','".$deliveryadd->last_name."','".$deliveryadd->company."','".$deliveryadd->street1."','".$deliveryadd->street2."','".$deliveryadd->city."','".$deliveryadd->zone."','".$deliveryadd->postal_code."','".$deliveryadd->country."','".$billingadd->first_name."','".$billingadd->last_name."','".$billingadd->company."','".$billingadd->street1."','".$billingadd->street2."','".$billingadd->city."','".$billingadd->zone."','".$billingadd->postal_code."','".$billingadd->country."','".$order_wg['ShippedVia']."','','".$order_wg['ServiceUsed']."','','".$order_wg['TrackingNumber']."','".$dt1."','".$dt1."','".$totalship."')  "); 
						$shipment_id = $sid;
						
					}
				 else
				 {
					db_query("INSERT INTO {uc_shipments} (order_id,o_first_name,o_last_name,o_company,o_street1,o_street2,o_city,o_zone,o_postal_code,o_country,d_first_name,d_last_name,d_company,d_street1,d_street2,d_city,d_zone,d_postal_code,d_country,shipping_method,accessorials,carrier,transaction_id,tracking_number,ship_date,expected_delivery,cost) values (".$this->mySQLSafe($order_id)." ,".$this->mySQLSafe($deliveryadd->first_name).",".$this->mySQLSafe($deliveryadd->last_name).",".$this->mySQLSafe($deliveryadd->company).",".$this->mySQLSafe($deliveryadd->street1).",".$this->mySQLSafe($deliveryadd->street2).",".$this->mySQLSafe($deliveryadd->city).",".$this->mySQLSafe($deliveryadd->zone).",".$this->mySQLSafe($deliveryadd->postal_code).",".$this->mySQLSafe($deliveryadd->country).",".$this->mySQLSafe($billingadd->first_name).",".$this->mySQLSafe($billingadd->last_name).",".$this->mySQLSafe($billingadd->company).",".$this->mySQLSafe($billingadd->street1).",".$this->mySQLSafe($billingadd->street2).",".$this->mySQLSafe($billingadd->city).",".$this->mySQLSafe($billingadd->zone).",".$this->mySQLSafe($billingadd->postal_code).",".$this->mySQLSafe($billingadd->country).",".$this->mySQLSafe($order_wg['ShippedVia']).",'',".$this->mySQLSafe($order_wg['ServiceUsed']).",'',".$this->mySQLSafe($order_wg['TrackingNumber']).",".$this->mySQLSafe($dt1).",".$this->mySQLSafe($dt1).",".$this->mySQLSafe($totalship).")  "); 
					$shipment_id = db_last_insert_id('uc_shipments', 'sid');
				}	
					
					$result1 ="Success";	
				}
			}
			
			$package_wg['sid'] = $shipment_id;		
			if(!$package_id) {	
				uc_shipping_package_save($package_wg);
				if($drupal_version > "6") {
					$package_id = db_query('SELECT MAX(package_id) FROM {uc_packages}')->fetchField();
				} else {
				
					if($drupal_version == "5") {
						
						$sql = db_query("SELECT MAX(package_id) FROM {uc_packages} ");
						$max_pid  = db_fetch_array($sql);
						$package_id = $max_pid['MAX(package_id)'];
					} else {
						$package_id = db_last_insert_id('uc_packages', 'package_id');
					}
				}
			}		
			
			foreach($order_products as $order_product)
			{
				$order_product->data = unserialize($order_product->data);
				$order_product->data['package_id'] = intval($package_id);
				db_query("UPDATE {uc_order_products} SET data = '%s' WHERE order_product_id = %d", serialize($order_product->data),$order_product->order_product_id);
				
				if($drupal_version > "6") {
					 db_update('uc_order_products')
					  ->fields(array('data' => serialize($order_product->data)))
					  ->condition('order_product_id', $order_product->order_product_id)
					  ->execute();
				  
				} else {
					db_query("UPDATE {uc_order_products} SET data = '%s' WHERE order_product_id = %d", serialize($order_product->data),$order_product->order_product_id);
				}
			 }
			 
		} else{
			$result = "Success";
		} 
		
		# send a notify mail
		if ($result=='Success' && $emailAlert=='Y')
		{
			$order_data = uc_order_load($order_id);
			global $base_url;
			$base_url =str_replace("/sites/all/modules/ecc","",$base_url);	
			ca_pull_trigger('uc_order_status_email_update', $order_data);
			
			if($drupal_version > "6") {
				rules_invoke_event('uc_order_status_email_update', $order_data);
			} else {
				if($drupal_version != '5') {
					ca_pull_trigger('uc_order_status_email_update', $order_data);
				}
			}
		}				
		
		if($update_notes_flag && $update_status_flag) {
			$Orders->setStatusMessage("Order updated successfully");	
		} elseif($update_notes_flag) {
			$Orders->setStatusMessage("Order notes updated successfully");	
		} elseif($update_status_flag) {
			$Orders->setStatusMessage("Order status updated successfully");	
		} else {
			$StatusMessage	=	"";
			$Orders->setStatusMessage("Error in update order");
		}

	
		//return $this->response($Orders->getOrders());
		return $this->response($Orders->getOrderResponse());
	
	
	}
	
	
	#
	#
	# Update Orders shipping status method
	# Will update Order Notes and tracking number of  order
	# Input parameter Username,Password, array (OrderID,ShippedOn,ShippedVia,ServiceUsed,TrackingNumber)
	#
	function UpdateOrdersShippingStatus($username,$password,$data,$statustype='Cancel')
	{ 
		global $version,$drupal_version;
		$status = $this->auth_user($username,$password);
		if($status !='0')
		{
			return $status;
		}
		
		$Orders = new WG_Orders();		
		//$response_array = json_decode($Orders_json_array,true);
		$response_array = $data; 
		if (!is_array($response_array))
		{
			$Orders->setStatusCode("9997");
			$Orders->setStatusMessage("Unknown request or request not in proper format");	
			return $this->response($Orders->getOrders());exit();				
		}
		if (count($response_array) == 0)
		{
			$Orders->setStatusCode("9996");
			$Orders->setStatusMessage("REQUEST array(s) doesnt have correct input format");				
			return $this->response($Orders->getOrders());exit();
		}
		if(count($response_array) == 0) {
			$no_orders = true;
		}else {
			$no_orders = false;
		}
		$Orders->setStatusCode($no_orders?"1000":"0");
		$Orders->setStatusMessage($no_orders?"No new orders.":"All Ok");
		if ($no_orders){
			return json_encode($response_array);
		}
	
		# Fetch All order status of Cart 
		$sql= db_query("SELECT order_status_id,title from {uc_order_statuses} ");
		if($drupal_version > "6")
		{
			//while($status_all = db_fetch_array($sql))
			while ($status_all =$sql->fetchObject()) 
			{
				$statuses[$status_all->title] = $status_all->order_status_id;
			}
		}
		else
		{
			while($status_all = db_fetch_array($sql))
			{
				$statuses[$status_all['title']] = $status_all['order_status_id'];
			}
		} 
//
//		
//		//$ordersNode = $xmlResponse->createTag("Orders", array(), '', $root);
//		
		$i=0;	
	//print_r($response_array);
	//die("hihihihi");
		foreach($response_array as $k=>$v)//request
		{
		
					if(isset($order_wg))
					{
						unset($order_wg);
					}
				
					foreach($v as $k1=>$v1)
					{
						$order_wg[$k1] = $v1;
					} 
					
					$order_id = $order_wg['OrderID'];
					# for fetching user id	
					if($drupal_version > "6")
					{	
						$user = db_query("SELECT uid,name,pass FROM {users} WHERE name = ".$this->mySQLSafe($username)." ")->fetchObject();
						$uid = $user->uid;
					
					}
					else
					{
						$user = db_fetch_array(db_query("SELECT uid,name,pass FROM {users} u WHERE LOWER(name) = LOWER(".$this->mySQLSafe($username).")"));
						$uid = $user['uid'];
					}

					if( strtolower($statustype) == strtolower('Cancel'))
					{ 
						$status = 'canceled';
					}
					else
					{
						$status = $statuses[$order_wg['OrderStatus']];
					} 
//					
					$info = "\nOrder shipped ";
						
					if ($order_wg['ShippedOn']!="")
					$info .= " on ". substr($order_wg['ShippedOn'],0,10);
					
					if ($order_wg['ServiceUsed']!="" )
					$info .= ". ".$order_wg['ServiceUsed'];
			
					if ($order_wg['TrackingNumber']!="")
					$info .= " Tracking Number is ".$order_wg['TrackingNumber'].".";
					
					if ($order_wg['OrderNotes']!="")			
					$info .=" \n".$order_wg['OrderNotes'];
					
					
					if($order_wg['IsNotifyCustomer']=='N')
					{
						$notify = 0;
					}
					if($order_wg['IsNotifyCustomer']=='Y')
					{
						$notify = 1;
					}
					$time = time();	
//					# Update  Order Status
//			
					uc_order_update_status($order_id, $status);
					
					//$result =db_query("select * from {uc_order_comments} oc where oc.order_id = ".$order_id." ORDER BY oc.comment_id  ");
					$result =db_query("select comment_id, order_status from {uc_order_comments} oc where oc.order_id =".$this->mySQLSafe($order_id)." ORDER BY oc.comment_id  ");
					$comments =array();
					if($drupal_version > "6")
					{
						while ($comment = $result->fetchObject())
						{ 
							$comments = $comment;
						} 
					}
					else
					{
						while ($comment = db_fetch_array($result)) 
						{
							$comments = $comment;
						}
					} 
					
					$result =db_query("select comment_id, message from {uc_order_admin_comments} oa where oa.order_id =".$this->mySQLSafe($order_id)." ORDER BY oa.comment_id  ");
					$admin_comments =array();
						
					if($drupal_version > "6")
					{
						while ($ad_comment = $result->fetchObject())
						{ 
							$admin_comments = $ad_comment;
						} 
					}
					else
					{
						while ($ad_comment = db_fetch_array($result)) 
						{
							$admin_comments = $ad_comment;
						}
					}
					if($drupal_version > "6")
					{
						$status_var = $comments->order_status;
					}
					
					else
					{
						$status_var = $comments['order_status'];
					}
						
					
					if($status_var == $status && strtolower($status)!='canceled')
					{ 	
						//db_query("UPDATE {uc_order_comments} SET message = '".$info."',order_status ='$status' , notified =$notify, created =$time where comment_id = ".$order_comments['comment_id']." ");
						if($drupal_version > "6")
						{
							
							db_update('uc_order_comments')
								  ->fields(array('order_status' => $status,'notified'=>$notify,'created'=>$time))
								  ->condition('comment_id',$comments->comment_id)
								  ->execute();
							
							
							db_update('uc_order_admin_comments')
								  ->fields(array('message' => $info,'created'=>$time))
								  ->condition('comment_id',$admin_comments->comment_id)
								  ->execute();
								
						}
						else
						{
							db_query("UPDATE {uc_order_comments} SET order_status =".$this->mySQLSafe($status).", notified =$notify, created =$time where comment_id = ".$this->mySQLSafe($comments['comment_id'])." ");
						
							db_query("UPDATE {uc_order_admin_comments} SET message = ".$this->mySQLSafe($info)."' created =$time where comment_id = ".$this->mySQLSafe($admin_comments['comment_id'])." ");
						}
						
					}
					
					elseif (strtolower($status)=='canceled')
					{
						if(isset($comments))
						{
							if($drupal_version > "6")
							{
								db_update('uc_order_comments')
								  ->fields(array('message'=>'Order status changed to '.$status.'.','order_status' => $status,'notified'=>$notify,'created'=>$time))
								  ->condition('comment_id',$comments->comment_id)
								  ->execute();
							}
							else
							{
								db_query("UPDATE {uc_order_comments} SET message = 'Order status changed to ".$this->mySQLSafe($status).".',order_status ='$status' , notified =$notify, created =$time where comment_id = ".$this->mySQLSafe($comments['comment_id'])." ");
							}
						}
						else
						{
							if($drupal_version > "6")
							{
								uc_order_comment_save($order_id, $uid, 'Order status changed to '.$status.'.', $type = 'order', $status = $status, $notify);
							}
							else
							{
								db_query("INSERT INTO {uc_order_comments} (order_id, uid, message, order_status, notified, created) VALUES ($order_id, $uid, 'Order status changed to ".$this->mySQLSafe($status).".','$status', $notify , $time)");
							}
						}
					}
					else 
					{ 
						if($drupal_version > "6")
						{
							uc_order_comment_save($order_id, $uid, $info, $type = 'order', $status = $status, $notify);
							uc_order_comment_save($order_id, $uid, $info, $type = 'admin', $status = $status, $notify);
						}
						else
						{
							db_query("INSERT INTO {uc_order_comments} (order_id, uid,  order_status, notified, created) VALUES ($order_id, $uid,  ".$this->mySQLSafe($status)."', $notify , ".$this->mySQLSafe($time).")");
							db_query("INSERT INTO {uc_order_admin_comments} SET message = ".$this->mySQLSafe($info).", created = ".$this->mySQLSafe($time).", order_id = ".$this->mySQLSafe($order_id).",uid = ".$this->mySQLSafe($uid)."");
						}
					}
					
					
			
					if(strtolower($statustype) == 'posttostore')
					{	
//					# Fetch delivery & Billing address
					$add1 =db_query("SELECT delivery_first_name first_name ,delivery_last_name last_name,delivery_phone phone,delivery_company company,	delivery_street1 street1,delivery_street2 street2,delivery_city city,delivery_zone zone,delivery_postal_code postal_code,delivery_country country,primary_email email from {uc_orders} where order_id =".$this->mySQLSafe($order_id));
					if($drupal_version > "6")
					{
						while ($add = $add1->fetchObject())
						{ 
							$deliveryadd = $add;
							
						}
					}
					else
					{
						$deliveryadd = db_fetch_object($add1);	
					}	
			
					$add2 =db_query("SELECT billing_first_name first_name ,billing_last_name last_name,billing_phone phone,billing_company company,	billing_street1 street1,billing_street2 street2,billing_city city,billing_zone zone,billing_postal_code postal_code,billing_country country from {uc_orders} where order_id =".$this->mySQLSafe($order_id));
					if($drupal_version > "6")
					{
						while ($add_b = $add2->fetchObject())
						{ 
							$billingadd = $add_b;
						}
					}
					else
					{
						$billingadd = db_fetch_object($add2);
					}	
	
						
					
					if($drupal_version > "6")
					{ 
						$package_id = db_query("SELECT package_id  from {uc_packages} where order_id = ".$this->mySQLSafe($order_id))->fetchField();
					}
					else
					{
						$sql = "SELECT package_id  from {uc_packages} where order_id = ".$this->mySQLSafe($order_id) ;	
						$package_id = db_result(db_query($sql));
						
					}
							
				
//					
					$sql =  db_query("Select order_product_id,order_id,nid,data,qty from {uc_order_products} where order_id=".$this->mySQLSafe($order_id));
					
					unset($order_products);
					if($drupal_version > "6")
					{
						while ($order_prod = $sql->fetchObject())
						{ 
							$order_products[] = $order_prod;	
							
							$prd['checked'] =  1; 
							$prd['qty'] =  $order_prod->qty; 
							$prd['package'] = 1;  			
							$package_wg['products'][$order_prod->order_product_id] = (object)$prd;		
						}
					}
					else
					{
						while ($order_prod = db_fetch_object($sql))
						{
							$order_products[] = $order_prod;	
							
							$prd['checked'] =  1; 
							$prd['qty'] =  $order_prod->qty; 
							$prd['package'] = 1;  			
							$package_wg['products'][$order_prod->order_product_id] = (object)$prd;			
						}
					}
					
					$package_wg['order_id'] = $order_id;
					$package_wg['shipping_type'] = 'small_package';		
					$package_wg['tracking_number'] = $order_wg['TrackingNumber'];
						
//			
//						
//					# Insert or Update the tracking number abd other data for  Order number .
					
					$sql = db_query("Select * from {uc_shipments} where order_id=".$this->mySQLSafe($order_id));
					if($drupal_version > "6")
					{
						while ($ship = $sql->fetchObject())
						{
							$record = $ship;
						}
					}
					else
					{
						$record = db_fetch_array($sql);	
					}
					
					#$dt = explode("/",$order_wg['ShippedOn']);
					#$dt1 = gmmktime(12, 0, 0, $dt[0], $dt[1], $dt[2]);
					$dt1 = strtotime($order_wg['ShippedOn']);		
					
					$order_details123 = uc_order_load($order_id);
					
					foreach($order_details123->line_items as $shipping123)
					{ 
						if($shipping123['type'] == 'shipping')
						{
							$totalship += $shipping123['amount']; 
							$ship_method =$ship_method.$shipping123['title'];
						}
					}
//						
					
					if($record)
					{ 
						if($drupal_version > "6")
						{ 
							$shipment_id = $record->sid;
							
							db_update('uc_shipments')
							  ->fields(array('o_first_name' =>$deliveryadd->first_name ,'o_last_name' => $deliveryadd->last_name,
							'o_company' => $deliveryadd->company,
							'o_street1'=> $deliveryadd->street1,
							'o_street2'=> $deliveryadd->street2,
							'o_city'=>$deliveryadd->city,
							'o_zone'=>$deliveryadd->zone,
							'o_postal_code'=> $deliveryadd->postal_code ,
							'o_country'=> $deliveryadd->country,
							'd_first_name'=> $billingadd->first_name,
							'd_last_name'=> $billingadd->last_name ,
							'd_company'=> $billingadd->company,
							'd_street1'=> $billingadd->street1,
							'd_street2'=> $billingadd->street2,
							'd_city'=> $billingadd->city,
							'd_zone'=> $billingadd->zone,
							'd_postal_code'=> $billingadd->postal_code,
							'd_country'=> $billingadd->country,
							'shipping_method'=>$order_wg['ShippedVia'],
							'accessorials'=>'',
							'carrier'=>$order_wg['ServiceUsed'],
							'tracking_number'=>$order_wg['TrackingNumber'],
							'ship_date'=>$dt1,
							'expected_delivery'=>$dt1,
							'cost'=>$totalship))
							  ->condition('order_id', $order_id)
							  ->execute();


						//die("vvvv");
							$result1 ="Success";	
							
						}
						else
						{ 
							$shipment_id = $record['sid'];
							db_query("UPDATE {uc_shipments} SET  o_first_name = ".$this->mySQLSafe($deliveryadd->first_name)." ,o_last_name = ".$this->mySQLSafe($deliveryadd->last_name).",
							o_company = ".$this->mySQLSafe($deliveryadd->company).",
							o_street1= ".$this->mySQLSafe($deliveryadd->street1).",
							o_street2= ".$this->mySQLSafe($deliveryadd->street2).",
							o_city=".$this->mySQLSafe($deliveryadd->city).",
							o_zone=".$this->mySQLSafe($deliveryadd->zone).",
							o_postal_code= ".$this->mySQLSafe($deliveryadd->postal_code)." ,
							o_country= ".$this->mySQLSafe($deliveryadd->country).",
							d_first_name= ".$this->mySQLSafe($billingadd->first_name).",
							d_last_name= ".$this->mySQLSafe($billingadd->last_name)." ,
							d_company= ".$this->mySQLSafe($billingadd->company).",
							d_street1= ".$this->mySQLSafe($billingadd->street1).",
							d_street2= ".$this->mySQLSafe($billingadd->street2).",
							d_city= ".$this->mySQLSafe($billingadd->city).",
							d_zone= ".$this->mySQLSafe($billingadd->zone).",
							d_postal_code= ".$this->mySQLSafe($billingadd->postal_code).",
							d_country= ".$this->mySQLSafe($billingadd->country).",
							shipping_method=".$this->mySQLSafe($order_wg['ShippedVia']).",
							accessorials='',
							carrier=".$this->mySQLSafe($order_wg['ServiceUsed']).",
							tracking_number=".$this->mySQLSafe($order_wg['TrackingNumber']).",
							ship_date=".$this->mySQLSafe($dt1).",
							expected_delivery=".$this->mySQLSafe($dt1).",
							cost=".$this->mySQLSafe($totalship)."                 where order_id = ".$order_id." ");
							$result1 ="Success";
							
						} 
					}
					else
					{ 
						if($drupal_version > "6")
						{
							# if required follow to function 'uc_shipping_shipment_edit_submit'
							
							$shipment = new stdClass();
							$shipment->order_id = $order_id;
							if (isset($record['sid'])) 
							{
								$shipment->sid = $record['sid'];
							}
							
							$origin = array('first_name'=>$deliveryadd->first_name,'last_name'=>$deliveryadd->last_name,'company'=>$deliveryadd->company,'street1'=>$deliveryadd->street1 , 'street2'=>$deliveryadd->street2, 'city'=>$deliveryadd->city,'zone'=>$deliveryadd->zone,'country'=>$deliveryadd->country,'postal_code'=>$deliveryadd->postal_code,'phone'=>'');
							
							$shipment->origin = (object) $origin;

							$address = array('order_id'=>$order_id,'uid'=>$uid,'delivery_first_name'=>$billingadd->first_name ,'delivery_last_name'=>$billingadd->last_name ,'delivery_phone'=>'' ,'delivery_company'=> $billingadd->company,'delivery_street1'=>$billingadd->street1 , 'delivery_street2'=>$billingadd->street2 ,'delivery_city'=>$billingadd->city , 'delivery_zone'=>$billingadd->zone  ,'delivery_postal_code'=>$billingadd->postal_code ,'delivery_country'=>$billingadd->country,'billing_first_name'=>$deliveryadd->first_name ,'billing_last_name'=>$deliveryadd->last_name ,'billing_phone'=>'' ,'billing_company'=>$deliveryadd->company ,'billing_street1'=>$deliveryadd->street1 ,'billing_street2'=>$deliveryadd->street2 , 'billing_city'=>$deliveryadd->city ,'billing_zone'=>$deliveryadd->zone ,'billing_postal_code'=>$deliveryadd->postal_code ,'billing_country'=>$deliveryadd->country );
							
							
							
							$destination = array('address'=>(object)$address,'first_name'=>$deliveryadd->first_name ,'last_name'=>$deliveryadd->last_name ,'company'=>$deliveryadd->company ,'street1'=>$deliveryadd->street1 ,'street2'=>$deliveryadd->street2 ,'city'=>$deliveryadd->city ,'zone'=>$deliveryadd->zone ,'country'=>$deliveryadd->country ,'postal_code'=>$deliveryadd->postal_code,'phone'=>'');
							
						
							
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
							
							$shipment_id  =   db_query('SELECT MAX(sid) FROM {uc_shipments}')->fetchField();
							$result1 ="Success";	
							//die("hihihi");
							
						}
						else
						{
							if($drupal_version == "5")
							{
								
								//$sql = db_query("SELECT MAX(sid) FROM {uc_shipments} ");
//								
//								$max_sid  = db_fetch_array($sql);
//								
//								$sid=$max_sid['MAX(sid)']+1;
								
								$sid = db_next_id('{uc_shipments}_sid');
								
								db_query("INSERT INTO {uc_shipments} (sid,order_id,o_first_name,o_last_name,o_company,o_street1,o_street2,o_city,o_zone,o_postal_code,o_country,d_first_name,d_last_name,d_company,d_street1,d_street2,d_city,d_zone,d_postal_code,d_country,shipping_method,accessorials,carrier,transaction_id,tracking_number,ship_date,expected_delivery,cost) values (".$sid.",".$order_id." ,'".$deliveryadd->first_name."','".$deliveryadd->last_name."','".$deliveryadd->company."','".$deliveryadd->street1."','".$deliveryadd->street2."','".$deliveryadd->city."','".$deliveryadd->zone."','".$deliveryadd->postal_code."','".$deliveryadd->country."','".$billingadd->first_name."','".$billingadd->last_name."','".$billingadd->company."','".$billingadd->street1."','".$billingadd->street2."','".$billingadd->city."','".$billingadd->zone."','".$billingadd->postal_code."','".$billingadd->country."','".$order_wg['ShippedVia']."','','".$order_wg['ServiceUsed']."','','".$order_wg['TrackingNumber']."','".$dt1."','".$dt1."','".$totalship."')  "); 
								$shipment_id = $sid;
								
							}
						 else
						 {
							db_query("INSERT INTO {uc_shipments} (order_id,o_first_name,o_last_name,o_company,o_street1,o_street2,o_city,o_zone,o_postal_code,o_country,d_first_name,d_last_name,d_company,d_street1,d_street2,d_city,d_zone,d_postal_code,d_country,shipping_method,accessorials,carrier,transaction_id,tracking_number,ship_date,expected_delivery,cost) values (".$this->mySQLSafe($order_id)." ,".$this->mySQLSafe($deliveryadd->first_name).",".$this->mySQLSafe($deliveryadd->last_name).",".$this->mySQLSafe($deliveryadd->company).",".$this->mySQLSafe($deliveryadd->street1).",".$this->mySQLSafe($deliveryadd->street2).",".$this->mySQLSafe($deliveryadd->city).",".$this->mySQLSafe($deliveryadd->zone).",".$this->mySQLSafe($deliveryadd->postal_code).",".$this->mySQLSafe($deliveryadd->country).",".$this->mySQLSafe($billingadd->first_name).",".$this->mySQLSafe($billingadd->last_name).",".$this->mySQLSafe($billingadd->company).",".$this->mySQLSafe($billingadd->street1).",".$this->mySQLSafe($billingadd->street2).",".$this->mySQLSafe($billingadd->city).",".$this->mySQLSafe($billingadd->zone).",".$this->mySQLSafe($billingadd->postal_code).",".$this->mySQLSafe($billingadd->country).",".$this->mySQLSafe($order_wg['ShippedVia']).",'',".$this->mySQLSafe($order_wg['ServiceUsed']).",'',".$this->mySQLSafe($order_wg['TrackingNumber']).",".$this->mySQLSafe($dt1).",".$this->mySQLSafe($dt1).",".$this->mySQLSafe($totalship).")  "); 
							$shipment_id = db_last_insert_id('uc_shipments', 'sid');
						}	
							
							$result1 ="Success";	
						}
					}
//					
					 $package_wg['sid'] = $shipment_id;		
					 
					if(!$package_id)		
					{	
						uc_shipping_package_save($package_wg);
						if($drupal_version > "6")
						{
							$package_id = db_query('SELECT MAX(package_id) FROM {uc_packages}')->fetchField();
						}
						else
						{
						
							if($drupal_version == "5")
							{
								
								$sql = db_query("SELECT MAX(package_id) FROM {uc_packages} ");
								
								$max_pid  = db_fetch_array($sql);
								
								$package_id = $max_pid['MAX(package_id)'];
							}
							else
							{
								$package_id = db_last_insert_id('uc_packages', 'package_id');
							}
						}
						
					}	
					foreach($order_products as $order_product)
					{ 
						$order_product->data = unserialize($order_product->data);
						$order_product->data['package_id'] = intval($package_id);
						if($drupal_version > "6")
						{
							 db_update('uc_order_products')
							  ->fields(array('data' => serialize($order_product->data)))
							  ->condition('order_product_id', $order_product->order_product_id)
							  ->execute();
						  
						}
						else
						{
							db_query("UPDATE {uc_order_products} SET data = '%s' WHERE order_product_id = %d", serialize($order_product->data),$order_product->order_product_id);
						}
					 }
						 
					}
					else
					{
						$result1 = "Success";
					} 
						
//					# send a notify mail
					if ($result1=='Success' && $order_wg['IsNotifyCustomer']=='Y')
					{ 
						$order_data = uc_order_load($order_id);
						global $base_url;
						$base_url =str_replace("/sites/all/modules/ecc","",$base_url);	
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
						
					}		
				
					//$xmlResponse->createTag('Status',  array(), $result, $orderNode, __ENCODE_RESPONSE);
					$Order = new WG_Order();
					$Order->setOrderID($order_id);
					$Order->setStatus($result1);
					$Orders->setOrders($Order->getOrder());	
					$Order_counter++;
					
//				
		$i++;
	   }	
//print_r($Orders->getOrders());
//die("hi");
	return $this->response($Orders->getOrders());
	}
	
	public function mySQLSafe($value, $quote = "'") 
	{
		//We are going to do this to keep the functions from contantly running
		if (empty($this->magic)) 
		{
			$this->magic = (bool)get_magic_quotes_gpc();
		}
		if (empty($this->escape)) 
		{

			if (function_exists('mysql_real_escape_string')) 
			{
				$this->escape = 'mysql_real_escape_string';
			} 
			else 
			{
				$this->escape = 'mysql_escape_string';
			}
		}

		if (empty($value)) 
		{
			return $quote.$quote;
		}

		## Stripslashes
		if ($this->magic) 
		{
			$value = stripslashes($value);
		}
		
		## Strip quotes if already in
		$value = str_replace(array("\\'","'"), "&#39;", $value);

		## Quote value
		if ($this->escape == 'mysql_real_escape_string' && !empty($this->db)) 
		{
			$value = mysql_real_escape_string($value, $this->db);
		} 
		else 
		{
			$value = mysql_escape_string($value);
		}

		$value = $quote . trim($value) . $quote;

		return $value;
	}
	
	
} // Class end

ob_clean();ob_start();

if(isset($_REQUEST['request'])) 
{
	$wpObject = new Webgility_Ecc_UB();
	$wpObject->parseRequest();
}	


?>
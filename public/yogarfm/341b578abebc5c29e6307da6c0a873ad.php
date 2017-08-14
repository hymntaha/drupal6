<?php
error_reporting(E_ALL);
set_time_limit(0);
require_once("iSDK/src/isdk.php");

//file_put_contents('worked.txt', time()."\r\n", FILE_APPEND);



print '<pre>';
$_REQUEST['contactId'] = 138952;

if (!empty($_REQUEST['contactId'])) {

	$app = new iSDK;

	$app->cfgCon('yogatuneup', '87fca80817489030fba4ef87c20f2e6b');



	$contact = $app->loadCon($_REQUEST['contactId'], array('Id'));

	$contact['_OrderCount'] = 0;
	$contact['_IsCustomer'] = 0;
	$contact['_LastOrderDate'] = null;
	$contact['_LTV'] = 0.00;
	//$contact['_LTVMinusDiscounts'] = 0.00;



	$page = 0;

	do {
		$orders = $app->dsQueryOrderBy('Job', 1000, $page, array('ContactId' => $contact['Id']), array('Id', 'DateCreated', 'ShipCity', 'ShipCountry', 'ShipState', 'ShipZip'), 'DateCreated', false);

		if (!empty($orders)) {
			if ($page == 0  ) {
				$contact['_LastOrderDate'] = $orders[0]['DateCreated'];
			}	

			$contact['_OrderCount'] = $contact['_OrderCount'] + count($orders);

			foreach ($orders as $order) {
				$invoices = $app->dsQuery('Invoice', 1, 0, array('JobId' => $order['Id']), array('Id', 'InvoiceTotal', 'TotalPaid', 'TotalDue', 'PromoCode'));		
				if (!empty($invoices)) {
					foreach ($invoices as $invoice) {
						$contact['_LTV'] = sprintf( '%.2f', ltrim( $contact['_LTV'], '$' ) + $invoice['TotalPaid'] );
					}
				}
			}
		}	
	} while ( !empty($orders) && count($orders) == 1000);	

	if ( $contact['_OrderCount'] > 0 ) {
		$contact['_IsCustomer'] = 1;
	}	
	if ( $contact['_OrderCount'] > 1) {
		$app->grpAssign($contact['Id'], 2346); //Multi Purchase
	}

	$result = $app->updateCon($contact['Id'], $contact);

	var_dump($contact);
}

echo 'Done!';


?>
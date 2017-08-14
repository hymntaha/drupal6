<?php

function order_export_callback(){
	drupal_goto('admin/store/export/execute');
}

function order_fufillment_form($form,&$form_state){
	$header = array(
		'order_id' => array('data' => t('Order ID'), 'field' => 'order_id', 'sort' => 'desc'),
		'customer_name' => array('data' => t('Customer'), 'field' => 'customer_name'),
		'created' => array('data' => t('Purchase Date'), 'field' => 'created'),
		'order_status' => array('data' => t('Status'), 'field' => 'order_status'),
	);

	$options = array(
		'header' => $header,
		'status' => array(
			'payment_received',
		),
	);

	$rows = array();
	$orders = order_get_orders($options);
	foreach($orders as $order){
		$rows[$order->order_id] = array(
			'order_id' => $order->order_id,
			'customer_name' => $order->customer_name,
			'created' => date('m/d/Y',$order->created),
			'order_status' => $order->order_status,
		);
	}

	$form['orders'] = array(
		'#type' => 'tableselect',
		'#header' => $options['header'],
		'#options' => $rows,
		'#empty' => t('No Orders.'),
		'#weight' => 1,
	);

	$form['actions'] = array(
		'#type' => 'actions',
		'#weight' => 0,
	);

	$form['actions']['submit'] = array(
		'#type' => 'submit',
		'#value' => 'Set status of selected to complete',
	);

	return $form;
}

function order_fufillment_form_validate($form,&$form_state){
	if(!array_filter($form_state['values']['orders'])){
		form_set_error('','No orders selected.');
	}
}

function order_fufillment_form_submit($form,&$form_state){
	$orders = array_filter($form_state['values']['orders']);
	foreach($orders as $order_id){
		uc_order_update_status($order_id,'completed');
	}
}
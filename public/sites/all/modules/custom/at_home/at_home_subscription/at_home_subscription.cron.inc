<?php

/**
 * Sends email to Active At Home Users whose CC will expire in 1 month
 */
function at_home_subscription_cc_exp_1_month_cron_callback(){
	set_time_limit(0);

	watchdog('cron', 'Starting At Home CC Expired 1 Month Cron');

	$ah_users = at_home_subscription_get_active_users();
	foreach($ah_users as $uid => $ah_user){
		$account = user_load($uid);
		$account_info = at_home_subscription_account_info_load($account);
		if(at_home_subscription_cc_expired($account_info, strtotime('next month'))){
			at_home_subscription_send_cc_exp_1_month_email($account);
		}
	}

	watchdog('cron', 'At Home CC Expired 1 Month Cron Finished');
}

/**
 * Sends email to Active At Home Users whose next shipment is tomorrow and their CC is expired
 */
function at_home_subscription_cc_exp_cron_callback(){
	set_time_limit(0);

	watchdog('cron', 'Starting At Home CC Expired Ship Tomorrow Cron');

	$tomorrow_date = date('Y-m-d',strtotime('tomorrow'));

	$ah_users = at_home_subscription_get_active_users($tomorrow_date);
	foreach($ah_users as $uid => $ah_user){
		$account = user_load($uid);
		$account_info = at_home_subscription_account_info_load($account);
		if(at_home_subscription_cc_expired($account_info, strtotime('tomorrow'))){
			at_home_subscription_send_cc_exp_email($account);
		}	
	}

	watchdog('cron', 'At Home CC Expired Ship Tomorrow Cron Finished');
}

/**
 * Process orders for Active At Home Users whose next shipment is today
 */
function at_home_subscription_process_orders_cron_callback(){
	set_time_limit(0);

	watchdog('cron', 'Starting At Home Process Orders Cron');
	
	$now_date = date('Y-m-d',strtotime('now'));
	$ah_users = at_home_subscription_get_active_users($now_date);
	foreach($ah_users as $uid => $ah_user){
		$account = user_load($uid);
		$account_info = at_home_subscription_account_info_load($account);

		if(at_home_subscription_cc_expired($account_info)){
			at_home_subscription_set_on_hold($account);
			at_home_subscription_send_cc_exp_email($account);
		}
		else{
			at_home_subscription_process_automatic_order($account_info);
		}
	}

	watchdog('cron', 'At Home Process Orders Cron Finished');
}
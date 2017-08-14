<?php
/*
Plugin Name: Publish 2 Ping.fm
Author: François-Xavier Payet
Version: 1.1
*/

/*
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License version 3 as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/*
 * Last change : $Date: 2010-05-04 10:42:42 +0000 (Tue, 04 May 2010) $ by $Author: foux $
 * Revision : $Revision: 237374 $
 */

$publish2pingfm_plugin_url = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));

require('php/main.php');
require('php/admin.php');
require('php/prefs.php');

if (class_exists("Publish2PingFM") && 
	  class_exists("Publish2PingFMAdminPanel") &&
	  class_exists("Publish2PingFMPreferencesManager")) {
	global $wpPingPrefs;
	global $wpPing;
	global $wpPingAdmin;
	
	$wpPingPrefs = new Publish2PingFMPreferencesManager();
	$wpPing = new Publish2PingFM();
	$wpPingAdmin = new Publish2PingFMAdminPanel();
}


//Actions and Filters	
if (isset($wpPing)) {
	//Actions
	register_activation_hook( __FILE__, array(&$wpPing,'init'));
	add_action('transition_post_status', array(&$wpPing,'statusChanged'), 10, 3);
	add_action('wp_head', array(&$wpPing,'pingfm_wp_head_url'));
	add_action('admin_menu', array(&$wpPingAdmin,'registerAdminPanel'));
	add_action('admin_init',array(&$wpPingAdmin,'registerJavaScript'));
	
	//Filters
}
?>

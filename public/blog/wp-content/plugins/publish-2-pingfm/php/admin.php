<?php
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
 * Last change : $Date: 2010-04-28 19:32:08 +0000 (Wed, 28 Apr 2010) $ by $Author: foux $
 * Revision : $Revision: 234862 $
 */

if (! class_exists("Publish2PingFMAdminPanel")) {
	class Publish2PingFMAdminPanel {
	
		/**
		 * Constructor. Do nothing for the moment
		 * 
		 * @since 0.2
		 */
		public function __construct() {
		}
		
		/**
		 * Registers the panel to wordpress
		 * 
		 * @since 0.4
		 */
		public function registerAdminPanel() {
			$page = add_submenu_page('options-general.php',
									 'Publish 2 Ping.fm Settings',
									 'Publish 2 Ping.fm', 9, basename(__FILE__),
									 array(&$this, 'printAdminPage'));
			add_action('admin_print_scripts-' . $page, array(&$this, 'enqueueJavaScript'));
		}
		
		/**
		 * Register JavaScript for the admin panel
		 * 
		 * @since 1.0
		 */
		public function registerJavaScript() {
			global $publish2pingfm_plugin_url;
			
			wp_register_script('publish2pingfmAdminScript',$publish2pingfm_plugin_url.'/js/adminScript.js');
		}
		
		/**
		 * Enqueue the JavaScript each time the admin page is loaded
		 * 
		 * @since 1.0
		 */
		public function enqueueJavaScript() {
			wp_enqueue_script('publish2pingfmAdminScript');
		}
	
		/**
		 * Print the settings page
		 * 
		 * @since 0.1
		 */
		public function printAdminPage() {
			global $wpPing;
			global $wpPingPrefs;

			if (isset($_POST['update_wpPingSettings'])) {
				if (isset($_POST['wpPingPingKey']) && ($_POST['wpPingPingKey'] != $wpPingPrefs->getPingFMUserKey())) {
					$wpPingPrefs->setPingFMUserKey($_POST['wpPingPingKey']);
				}   
				if (isset($_POST['wpPingBitlyUser']) && ($_POST['wpPingBitlyUser'] != $wpPingPrefs->getBitlyUser())) {
					$wpPingPrefs->setBitlyUser($_POST['wpPingBitlyUser']);
				}
				if (isset($_POST['wpPingBitlyKey']) && ($_POST['wpPingBitlyKey'] != $wpPingPrefs->getBitlyKey())) {
					$wpPingPrefs->setBitlyKey($_POST['wpPingBitlyKey']);
				}
				// Templates
				foreach ($wpPingPrefs->getTemplates() as $key=>$template) {
					if (isset($_POST['wpTemplate'.$key])) {
						if (empty($_POST['wpTemplate'.$key])) {
							$wpPingPrefs->removeTemplate($key);
						} else if ($_POST['wpTemplate'.$key] != $template) {
							$wpPingPrefs->setTemplateMessage($key,$template);
						}
					}
				}
				if (isset($_POST['newTemplate']) && (! empty($_POST['newTemplate']))) {
					$newTemplate = $_POST['newTemplate'];
					$key = array_search($newTemplate,$wpPingPrefs->getTemplates());
					if (! $key) {
						$wpPingPrefs->addTemplate($newTemplate);
					}
				}
				// Getting each categories
				$catArgs = array(
					'orderby' => 'id',
					'hide_empty' => 0,
				);
				$categories = get_categories($catArgs);
				foreach ($categories as $cat) {
					if (isset($_POST['wpPingCat'.$cat->term_id])) {
						$wpPingPrefs->setTemplate($cat->term_id,$_POST['wpPingCat'.$cat->term_id]);
					}
					if (isset($_POST['wpPingCatRepubTemplate'.$cat->term_id])) {
						$wpPingPrefs->setRepublishTemplate($cat->term_id,$_POST['wpPingCatRepubTemplate'.$cat->term_id]);
					}
					$wpPingPrefs->setEnabled($cat->term_id,isset($_POST['wpPingEnableCat'.$cat->term_id]));
					$wpPingPrefs->setPostUpdates($cat->term_id,isset($_POST['wpPingCatRepub'.$cat->term_id]));
					
				}
				?><div class="updated"><p><strong><?php _e("Settings Updated.", "Publish2PingFM");?></strong></p></div>
				<?php
			}?>
<form method="post" name="publish2pingform" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
	<div>
		<div class="wrap">
			<h2>Wordpress 2 Ping.fm Settings</h2>
			<p>In order to get Wordpress 2 Ping.fm work, you need a 
			<a href="http://www.ping.fm">Ping.fm account</a>, a 
			<a href="http://bit.ly">bit.ly</a> account, and to fill in
			the settings.</p>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						Ping.fm API Key
					</th>
					<td>
						<input type="text" name="wpPingPingKey" value="<?php echo $wpPingPrefs->getPingFMUserKey(); ?>" size="45" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						Bit.ly user name
					</th>
					<td>
						<input type="text" name="wpPingBitlyUser" value="<?php echo $wpPingPrefs->getBitlyUser(); ?>" size="45" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						Bit.ly API Key
					</th>
					<td>
						<input type="text" name="wpPingBitlyKey" value="<?php echo $wpPingPrefs->getBitlyKey(); ?>" size="45" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						Status templates
					</th>
					<td><?php 
						// Getting each templates
						foreach ($wpPingPrefs->getTemplates() as $key=>$template) {?>
						<input type="text" name="wpTemplate<?php echo $key; ?>" value="<?php echo $template; ?>" size="45" /><br /><?php
						}?>
						<input type="text" name="newTemplate" size="45" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						Categories
					</th>
					<td>
						<table><?php 
							// Getting each categories
							$catArgs = array(
								'hide_empty' => 0,
								'hierarchical' => 0,
								'parent' => 0
							);
							$categories = get_categories($catArgs);
							foreach ($categories as $cat) {
								$this->printCategories($cat->term_id, $cat->cat_name, 0);
							}?>
						</table>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<div class="submit">
		<input type="submit" name="update_wpPingSettings" value="<?php _e('Update Settings', 'Publish2PingFM') ?>" />
	</div>
</form><?php
		}
		
		/**
		 * Prints each category, with tab spaces for recursivity
		 * 
		 * @since 0.1
		 * 
		 * @param int $catId WP Category ID
		 * @param string $catName WP Category name
		 * @param int $level Recursivity level
		 */
		private function printCategories($catId, $catName, $level) {
			global $wpPingPrefs;
			?>
<tr><td><?php for ($i=0; $i<$level; $i++) echo "&nbsp;&nbsp;&nbsp"; ?><input type="checkbox" value="<?php
			if ($wpPingPrefs->isEnabled($catId)) {
				echo "checked";
			} else {
				echo "unchecked";
			}?>
" name="<?php echo 'wpPingEnableCat'.$catId ?>" onClick="categorieCheckBoxChanges(<?php echo $catId; ?>)"<?php
			if ($wpPingPrefs->isEnabled($catId)) {
				echo "checked";
			}?>
><?php echo $catName ?></td>
<td><div id="showHide<?php echo $catId; ?>" style="visibility:<?php 
			if ($wpPingPrefs->isEnabled($catId)) {
				echo "visible";
			} else {
				echo "hidden";
			}?>"><select name="<?php echo 'wpPingCat'.$catId ?>" <?php 
			if (! $wpPingPrefs->isEnabled($catId)) {
				echo "disabled";
			}
?>><?php
			foreach ($wpPingPrefs->getTemplates() as $templateId=>$message) {
				?>
<OPTION VALUE="<?php echo $templateId; ?>"<?php 
				if ($wpPingPrefs->getTemplate($catId) == $templateId) {
					echo " selected";
				}
?>><?php echo $message; ?></OPTION><?php				
			}
?></select></div>
</td><td><div id="showHideRepub<?php echo $catId; ?>" style="visibility:<?php 
				if ($wpPingPrefs->isEnabled($catId)) {
					echo "visible";
				} else {
					echo "hidden";
				}?>">Publish post updates? <input type="checkbox" value="<?php 
				if ($wpPingPrefs->postUpdates($catId)) {
					echo "checked";
				} else {
					echo "unchecked";
				}?>
" name="<?php echo 'wpPingCatRepub'.$catId; ?>" <?php 
				if ($wpPingPrefs->postUpdates($catId)) {
					echo "checked";
				}?> onClick="categorieCheckBoxChanges(<?php echo $catId; ?>)"></div>
</td><td><div id="showHideRepubTemp<?php echo $catId; ?>" style="visibility:<?php 
				if ($wpPingPrefs->isEnabled($catId) && $wpPingPrefs->postUpdates($catId)) {
					echo "visible";
				} else {
					echo "hidden";
				}?>"><select name="<?php echo 'wpPingCatRepubTemplate'.$catId; ?>" <?php 
				if (! $wpPingPrefs->postUpdates($catId)) {
					echo "disabled";
				}
?>><?php 
				foreach ($wpPingPrefs->getTemplates() as $templateId=>$message) {
					?>
<OPTION VALUE="<?php echo $templateId ?>"<?php 
					if ($wpPingPrefs->getTemplateForUpdates($catId) == $templateId) {
						echo "selected";
					}
?>><?php echo $message; ?></OPTION><?php
				}
?></select></div></td></tr><?php
			$catArgs = array(
				'hide_empty' => 0,
				'hierarchical' => 0,
				'parent' => $catId
			);
			$categories = get_categories($catArgs);
			foreach ($categories as $cat) {
				$this->printCategories($cat->term_id, $cat->cat_name, $i+1);
			}
		}
	}
}
?>
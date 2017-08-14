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

if (! class_exists("Publish2PingFMPreferencesManager")) {
	class Publish2PingFMPreferencesManager {
	
		private $adminOptionName;
		private $options;
		
		/**
		 * Constructor. In additions to setting basic options, the constructor
		 * will read preferences from the database and update preferences if the
		 * scheme as changed since the last version.
		 * 
		 * @since 0.2
		 */
		public function __construct() {
			$this->adminOptionName = "Publish2PingFMAdminOptions";
			$this->options = get_option($this->adminOptionName);
		}
		
		/**
		 * Retrieve the options from the DB
		 * 
		 * @since 0.2
		 */
		public function retrieveOptions() {
			// First, we try to see if there's already some options.
			$this->options = get_option($this->adminOptionName);
			if (! $this->options) {
				// No options set, we initialize them and we're done
				$this->initializeOptions();
			} else {
				// We update the datas if necessary
				$version = (int) $this->options['version'];
				if ($version < 3) {
					$this->migrate02to03();
				}
				if ($version < 4) {
					$this->migrate03to04();
				}
				if ($version < 5) {
					$this->migrate4to5();
				}
			}
		}
		
		/**
		 * Initialize options
		 * 
		 * @since 0.3
		 */
		private function initializeOptions() {
			$this->options = array(
				'pingFM_key' => '',
				'bitly_user' => '',
				'bitly_key' => '',
				'categories' => array(),
				'templates' => array(
					1 => 'New blog post : [title] [short_url]',
					2 => 'Read on my blog : [title] [short_url]'
				),
				'version' => 5
			);
			
			// Getting each categories
			$catArgs = array(
				'orderby' => 'id',
				'hide_empty' => 0,
			);
			$categories = get_categories($catArgs);
			foreach ($categories as $cat) {
				$this->options['categories'][$cat->term_id]['enabled'] = false;
				$this->options['categories'][$cat->term_id]['republish'] = false;
			}
			update_option($this->adminOptionName,$this->options);
		}
		
		/**
		 * Migrate options from version 2 to 3
		 * 
		 * @since 0.3
		 */
		private function migrate02to03() {
			// We create the default templates
			$this->options['templates'] = array(
				1 => 'New blog post : [title] [short_url]',
				2 => 'Read on my blog : [title] [short_url]'
			);
			
			// We have to create new messages templates as they were introduces in this version
			foreach ($this->options['categories'] as $currentCat) {
				if (isset($currentCat['message'])) {
					$key = array_search($currentCat['message'],$this->options['templates']);
					if (! $key) {
						$message = $currentCat['message'];
						$this->options['templates'][] = $message;
						$currentCat['template'] = array_search($message);
						unset($currentCat['message']);
					}
				} 
			}
			
			// We change the version
			$this->options['version'] = 3;
			
			// And we're done!
			update_option($this->adminOptionName,$this->options);
		}
		
		/**
		 * Migrate from version 3 to 4
		 * 
		 * @since 1.0
		 */
		private function migrate03to04() {
			// In this version, we added the option to re-publish posts. We disable it by default
			foreach ($this->options['categories'] as $currentCat) {
				$currentCat['republish'] = false;
			}
			
			// We change the version
			$this->options['version'] = 4;
			
			//And we're done!
			update_option($this->adminOptionName,$this->options);
		}
		
		/**
		 * Migrate from version 4 to 5
		 * 
		 * @since 1.0
		 */
		private function migrate4to5() {
			global $wpdb;
			
			// For each post, we rename the meta so that it won't show to the user
			$wpdb->update($wpdb->postmeta,array('meta_key' => '_bitly_url'),array('meta_key' => 'bitly_url'));
			$wpdb->update($wpdb->postmeta,array('meta_key' => '_pingfm_published'),array('meta_key' => 'pingfm_published'));
			
			// We change the version
			$this->options['version'] = 5;
			
			//And we're done!
			update_option($this->adminOptionName,$this->options);
		}
		
		/**
		 * Check if post from a categorie should be published to Ping.FM
		 * 
		 * @since 0.2
		 * 
		 * @param int $catId The ID of the WP category
		 * @return TRUE if the posts from the category should be published, FALSE otherwise
		 */
		public function isEnabled($catId) {
			return $this->options['categories'][$catId]['enabled'];
		}
		/**
		 * Sets if posts from a category should be published to Ping.FM
		 * 
		 * @since 0.2
		 * 
		 * @param int $catId The ID of the WP category
		 * @param bool $enabled TRUE if the posts from the category should be published, FALSE otherwise
		 */
		public function setEnabled($catId, $enabled) {
			$this->options['categories'][$catId]['enabled'] = $enabled;
			update_option($this->adminOptionName,$this->options);
		}
		
		/**
		 * Returns the message template to post to Ping.fm
		 * 
		 * @since 0.2
		 * 
		 * @param int $template The template ID for which we want the message
		 * @return Message template to post to Ping.fm
		 */
		public function getMessage($template) {
			return $this->options['templates'][$template];
		}
		
		/**
		 * Returns the Ping.fm key of the user
		 * 
		 * @since 0.2
		 * 
		 * @return Ping.fm key of the user
		 */
		public function getPingFMUserKey() {
			return $this->options['pingFM_key'];
		}
		/**
		 * Sets the Ping.fm key of the user
		 * 
		 * @since 0.2
		 * 
		 * @param $key
		 */
		public function setPingFMUserKey($key) {
			$this->options['pingFM_key'] = $key;
			update_option($this->adminOptionName,$this->options); 
		}
		
		/**
		 * Returns the Ping.fm API key
		 * 
		 * @since 0.2
		 * 
		 * @return Ping.fm API key
		 */
		public function getPingFMApiKey() {
			return 'b23045984268225cdb1ec4ad68b94cd9';
		}
		/**
		 * This method is reserved for future use. Do nothing for the moment
		 * 
		 * @since 0.2
		 * 
		 * @param string $key API Key of the program
		 */
		public function setPingFMApiKey($key) {
		}
		
		/**
		 * Returns the Bitly user
		 * 
		 * @since 0.2
		 * 
		 * @return The Bitly user
		 */
		public function getBitlyUser() {
			return $this->options['bitly_user'];
		}
		/**
		 * Sets the Bit.ly username
		 * 
		 * @since 0.2
		 * 
		 * @param string $user Name of the Bit.ly user
		 */
		public function setBitlyUser($user) {
			$this->options['bitly_user'] = $user;
			update_option($this->adminOptionName,$this->options);
		}
		
		/**
		 * Returns the key of the Bit.ly user
		 * 
		 * @since 0.2
		 * 
		 * @return Key of the Bit.ly user
		 */
		public function getBitlyKey() {
			return $this->options['bitly_key'];
		}
		/**
		 * Sets the key of the Bit.ly user
		 * 
		 * @since 0.2
		 * 
		 * @param string $key Key of the Bit.ly user
		 */
		public function setBitlyKey($key) {
			$this->options['bitly_key'] = $key;
			update_option($this->adminOptionName,$this->options);
		}
		
		/**
		 * Return all of the templates
		 * 
		 * @since 0.3
		 */
		public function getTemplates() {
			return $this->options['templates'];
		}
		
		/**
		 * Return the template ID for a specific category
		 * 
		 * @since 0.3
		 * 
		 * @param int $catId Id of the category for wich you want the template
		 * @return Id of the template for this category
		 */
		public function getTemplate($catId) {
			return (int) $this->options['categories'][$catId]['template'];
		}
		
		/**
		 * Return the template ID for a specific category updates
		 * 
		 * @since 1.0
		 * 
		 * @param int $catId Id of the category for wich you want the updates template
		 * @return Id of the template for this category's updates
		 */
		public function getTemplateForUpdates($catId) {
			return (int) $this->options['categories'][$catId]['templateForUpdates'];
		}
		
		/**
		 * Sets the template ID for a specific category
		 * 
		 * @since 0.3
		 * 
		 * @param int $catId Id of the category
		 * @param int $templateId Id of the template
		 */
		public function setTemplate($catId,$templateId) {
			$this->options['categories'][$catId]['template'] = $templateId;
			update_option($this->adminOptionName,$this->options);
		}
		
		/**
		 * Sets the template ID for a specific category updates
		 * 
		 * @since 1.0
		 * 
		 * @param int $catId Id of the category
		 * @param int $templateId Id of the template
		 */
		public function setRepublishTemplate($catId,$templateId) {
			$this->options['categories'][$catId]['templateForUpdates'] = $templateId;
			update_option($this->adminOptionName,$this->options);
		}
		
		/**
		 * Changes the message for a specific template
		 * 
		 * @since 0.3
		 * 
		 * @param int $templateId Template ID
		 * @param string $message Template message
		 */
		public function setTemplateMessage($templateId, $message) {
			$this->options['templates'][$templateId] = $message;
			update_option($this->adminOptionName,$this->options);
		}
		
		/**
		 * Adds a new template
		 * 
		 * @since 0.3
		 * 
		 * @param string $message Message
		 */
		public function addTemplate($message) {
			$this->options['templates'][] = $message;
			update_option($this->adminOptionName,$this->options);
		}
		
		/**
		 * Removes a template
		 * 
		 * @since 0.3
		 * 
		 * @param int $templateId ID of the template to remove
		 */
		public function removeTemplate($templateId) {
			unset($this->options['templates'][$templateId]);
			update_option($this->adminOptionName,$this->options);
		}
		
		/**
		 * Check if updates to post from a category should be published
		 * 
		 * @since 1.0
		 * 
		 * @param int $catId ID of the post's category
		 * @return true if post updates from this category should be published, false otherwise
		 */
		public function postUpdates($catId) {
			return $this->options['categories'][$catId]['republish'];
		}
		
		/**
		 * Sets if updates to post from a category should be published
		 * 
		 * @since 1.0
		 * 
		 * @param int $catId Category ID
		 * @param bool $enabled True if post's updates from this category should be published
		 */
		public function setPostUpdates($catId, $enabled) {
			$this->options['categories'][$catId]['republish'] = $enabled;
			update_option($this->adminOptionName,$this->options);
		}
	}
}
?>

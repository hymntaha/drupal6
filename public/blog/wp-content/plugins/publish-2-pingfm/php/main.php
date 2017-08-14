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
 * Last change : $Date: 2010-05-04 10:42:42 +0000 (Tue, 04 May 2010) $ by $Author: foux $
 * Revision : $Revision: 237374 $
 */

if (!class_exists("Publish2PingFM")) {
	class Publish2PingFM {
		// setting some internal information
		private $pingfm_published;
		private $pingfm_short_meta;
		
		/**
		 * Constructor. Defines some key values, but otherwise do nothing
		 * 
		 * @since 0.3
		 */
		public function __construct() {
			$this->pingfm_short_meta = '_bitly_url';
			$this->pingfm_published = '_pingfm_published';
		}
		
		/**
		 * Called when the plugin is activated. Initialized preferences
		 * 
		 * @since 0.1
		 */
		public function init() {
			global $wpPingPrefs;
			
			$wpPingPrefs->retrieveOptions();
		}
		
		/**
		 * Called when the status of a post is updated. If the post is now published,
		 * we publish it to ping.fm if it has never been publish or the configuration
		 * says that we must publish it for updates.
		 * 
		 * @param string $oldStatus Old status of the post
		 * @param string $newStatus New status of the post
		 * @param post $post Wordpress Post
		 * 
		 * @since 0.3
		 */
		public function statusChanged($newStatus, $oldStatus, $post) {
			global $wpPingPrefs;
			
			// We do stuff only if the new status is published, and only for post
			if (($newStatus == 'publish') && ($post->post_type == 'post')) {
				$postid = $post->ID;
				$publishedMeta = get_post_meta($postid,$this->pingfm_published);
				$neverPublished = empty($publishedMeta);
				// We check the configuration to know if we must publish the post
				foreach (get_the_category($postid) as $category) {
					$currentCat = $category->term_id;
					while ($currentCat != 0) {
						// Should we post messages for this category?
						if ($wpPingPrefs->isEnabled($currentCat)) {
							// If the category is enabled, we post the message only
							// if it's a first time update or if we should post updates
							// for this category
							if ($neverPublished || ($wpPingPrefs->postUpdates($currentCat))) {
								// Ok, we know we should post this message
								$template = 0;
								if ($neverPublished) {
									$template = $wpPingPrefs->getTemplate($currentCat);
								} else {
									$template = $wpPingPrefs->getTemplateForUpdates($currentCat);
								}
								$message = $wpPingPrefs->getMessage($template);
								$this->publishToPingfm($post,$message);
								
								// And we're done
								return;
							}
						}
						
						// We haven't found a category that match, we look at the parent
						$currentCat = get_category($currentCat)->category_parent;
					}
				}
			}
		}
		
		/**
		 * Called when a post is published.
		 * This is the main function of the plugin, it will shorten
		 * the URL of the permalink to the post, and post it to Ping.fm
		 * according to configuration
		 * 
		 * @since 0.3
		 * 
		 * @param post $post WordPress post
		 */
		private function publishToPingfm($post, $message) {
			global $wpPingPrefs;
			
			$post_id = $post->ID;
			$post_url = get_permalink($post_id);
			$post_title = strip_tags($post->post_title);

			$short_url = get_post_meta($post_id, $this->pingfm_short_meta, true);
			
			if (empty($message)) return;
			
			// URL shortening
			if (empty($short_url)) {
				$short_url = $this->pingfm_make_bitly_url($post);               
			}
			
			// We transform the message template into a real message
			$message = str_replace('[title]',$post_title,$message);
			$message = str_replace('[url]',$post_url,$message);
			$message = str_replace('[short_url]',$short_url,$message);
  
			$post_data = Array(
				'api_key' => $wpPingPrefs->getPingFMApiKey(),
				'user_app_key' => $wpPingPrefs->getPingFMUserKey(),
				'post_method'  => 'status',
				'body'  => $message,
			);

			// send data to ping.fm
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Publish 2 Pingfm');
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_URL, 'http://api.ping.fm/v1/'. 'user.post');
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
			$output = curl_exec($ch);

			// if ok, we mark the post as allready published
			if (preg_match('/OK/', $output)) {
				preg_match('/\<transaction\>([^\<]*)\<\/transaction\>/', $output, $match);
				$ping_result = addslashes(trim($match[1]));
				add_post_meta($post_id,$this->pingfm_published,1,true);
			} else {
				preg_match('/\<message\>([^\<]*)\<\/message\>/', $output, $match);
				$ping_result = addslashes(trim($match[1]));

				add_post_meta($post_id, 'pingfm_error', $ping_result);
			}
		}

		/**
		 * This method will check if a post as already got a short URL,
		 * and if it has, put it in the rel link of the pasge HEAD
		 * 
		 * @since 0.1
		 */
		public function pingfm_wp_head_url() {
			global $post;
  
			$short_permalink = get_post_meta($post->ID, $this->pingfm_short_meta, 'true');
  
			if ((is_single($post->ID)) && (!empty($short_permalink))) {
				echo "<!-- Short URL -->\n";
				echo "<link rel=\"shorturl\" href=\"$short_permalink\" />\n";
			}
		}

		/**
		 * Creates the bit.ly URL, and store it in the database
		 * 
		 * @since 0.1
		 * 
		 * @param post $post The WP post
		 */
		private function pingfm_make_bitly_url($post) { 
			global $wpPingPrefs;
			
			$login = $wpPingPrefs->getBitlyUser();
			$appKey = $wpPingPrefs->getBitlyKey();

			$post_id = $post->ID;
  
			$short_permalink = get_post_meta($post_id, $this->pingfm_short_meta, 'true');
  
			// We do nothing if we already have a short url
			if (empty($short_permalink)) {
				//create the URL
				$url = get_permalink($post_id);
				$bitly = 'http://api.bit.ly/v3/shorten';
				$param = 'login='.$login.'&apiKey='.$appKey.'&uri='.urlencode($url);
				//get the url
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_USERAGENT, 'Publish 2 Ping.fm');
				curl_setopt($ch, CURLOPT_URL, $bitly . "?" . $param);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$response = curl_exec($ch); 
				curl_close($ch);

				// Decode bit.ly answer
				$json = json_decode($response,true);

				// check if all goes ok, if not, return error message
				if ($json['status_code'] == '200') {
					add_post_meta($post_id, $this->pingfm_short_meta, $json['data']['url']);
					return $json['data']['url'];
				} else {
					add_post_meta($post_id,'bitly_error',$response.'\\n'.$bitly.$param);
				}
			}
		}
	}
}
?>
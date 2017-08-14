<?php
/*
Plugin Name: Shorten2Ping
Plugin URI: http://www.samuelaguilera.com/archivo/shorten2ping-notifies-pingfm-bitly.xhtml
Description: Sends <strong>status</strong> updates to Ping.fm or Twitter everytime you publish a post, using your own domain, Bit.ly, Tr.im, and others for shortened permalinks.
Author: Samuel Aguilera
Version: 1.4.4
Author URI: http://www.samuelaguilera.com
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


// setting some internal information
$shorten2ping_dirname = plugin_basename(dirname(__FILE__));
$shorten2ping_url = WP_PLUGIN_URL . '/' . $shorten2ping_dirname;

//load translation file if any for the current language
load_plugin_textdomain('shorten2ping', PLUGINDIR . '/' . $shorten2ping_dirname . '/locale');


// simple function to use in your theme if you want to show the short url for the current post

function short_permalink($linktext="") {

      global $post;
      
      $short_permalink = get_post_meta($post->ID, 'short_url', 'true');   

      if ($linktext == 'linktext') {
          
          $linktext = $short_permalink;
          
          } elseif (empty($linktext)) {
                            $linktext = __('Short URL','shorten2ping');
          }
          
      $post_title = strip_tags($post->post_title);
      
      // Using rel="shorturl" as proposed at http://wiki.snaplog.com/short_url
      if (!empty($short_permalink)) echo "<a href=\"$short_permalink\" rel=\"shorturl\" title=\"$post_title\">" . $linktext . "</a>";
    }
    
function short_url_head() {

    global $post;
    
    $short_url = get_post_meta($post->ID, 'short_url', 'true');    
    
    if (is_single($post->ID) && !empty($short_url)) {
    
          echo "<!-- Shorturl added by shorten2ping -->\n";
          echo "<link rel=\"shortlink\" href=\"$short_url\" />\n";    
    }

}


    function fb_thumb_in_head() {
    
     global $post;
     
            if ( function_exists( 'has_post_thumbnail' ) ) {
        	   $already_has_thumb = has_post_thumbnail($post->ID);           
            }     

             $attached_image = get_children( "post_parent=$post->ID&post_type=attachment&post_mime_type=image&numberposts=1" );        
        	   $custom_fb_thumbnail = get_post_meta($post->ID, 'fb_img', true);
	
            if (is_single($post->ID) && $already_has_thumb) {
          
                  $post_thumb_ID = get_post_thumbnail_id($post->ID);
                  $thumbnail_permalink = wp_get_attachment_url($post_thumb_ID);
                                        	     
                  echo "<!-- Img for Facebook thumbnail added by Shorten2Ping -->\n";
                  echo "<link rel=\"image_src\" href=\"$thumbnail_permalink\" />\n";     
                         
             } elseif (is_single($post->ID) && $attached_image) {
                            	
                  foreach ($attached_image as $attachment) {
                       $fb_thumbnail = wp_get_attachment_url( $attachment->ID );                            	                                
                   }     
      
                  echo "<!-- Img for Facebook thumbnail added by Shorten2Ping -->\n";
                  echo "<link rel=\"image_src\" href=\"$fb_thumbnail\" />\n";                                                                                                        
                        
             } elseif (is_single($post->ID) && !empty($custom_fb_thumbnail)) {

                  echo "<!-- Img for Facebook thumbnail added by Shorten2Ping -->\n";
                  echo "<link rel=\"image_src\" href=\"$custom_fb_thumbnail\" />\n";          
          
            }        
         
         }


function shorten2ping_published_post($post) {

      if ( $post->post_type != 'post' ) return;  // dont ping pages
      	
	$post_id = $post->ID;
	
	  $s2p_options = get_option('s2p_options');	
	  
    $pingfm_user_key = $s2p_options['pingfm_key'];
      
    $post_url = get_permalink($post_id);
    $post_title = strip_tags($post->post_title);

    $short_url_exists = get_post_meta($post_id, 'short_url', true);
   
             if (empty($short_url_exists)) {
             
                  if ($s2p_options['shorten_service'] == 'bitly') {
              
                    //acortamos la url del post con bit.ly            
                      $bitly_user = $s2p_options['bitly_user'];
                      $bitly_key = $s2p_options['bitly_key'];
                      $bitly_flavour = 'bitly';
                      $short_url = make_bitly($post_id,$post_url,$bitly_user,$bitly_key,$bitly_flavour);
                      
                      } elseif ($s2p_options['shorten_service'] == 'trim') {
                      
                      $trim_user = $s2p_options['trim_user'];
                      $trim_pass = $s2p_options['trim_pass'];
                                            
                      $short_url = make_trim($post_id,$post_url,$trim_user,$trim_pass);
                                            
                      } elseif ($s2p_options['shorten_service'] == 'wpme') {
                      
                      // $wp_api_key = $s2p_options['wp_api_key'];
                      $wp_blog_id = $s2p_options['wp_blog_id'];                                          
                      
                              if (empty($wp_blog_id)) {
                              
                                  add_post_meta($post_id, 'wpme_error', 'Check your API key in S2P options page...');
        
                              } else {
                              
                              $short_url = make_wpme($post_id,$post_url,$wp_blog_id);
                              
                              }                    
                                            
                      } elseif ($s2p_options['shorten_service'] == 'yourls') {
                                     
                      $yourls_api = $s2p_options['yourls_api'];
                      $yourls_user = $s2p_options['yourls_user'];
                      $yourls_pass = $s2p_options['yourls_pass'];
                                            
                      $short_url = make_yourls($post_id,$post_url,$yourls_api,$yourls_user,$yourls_pass);
                      
                      } elseif ($s2p_options['shorten_service'] == 'none') {
                                     
                      $short_url = $post_url;
                      
                      } elseif ($s2p_options['shorten_service'] == 'supr') {
                                     
                      $supr_key = $s2p_options['supr_key'];
                      $supr_user = $s2p_options['supr_user'];
                                            
                      $short_url = make_supr($post_id,$post_url,$supr_key,$supr_user);
                      
                      } elseif ($s2p_options['shorten_service'] == 'cligs') {
                                     
                      $cligs_key = $s2p_options['cligs_key'];
                                            
                      $short_url = make_cligs($post_id,$post_url,$cligs_key);
                                           
                      } elseif ($s2p_options['shorten_service'] == 'isgd') {
                                     
                      $short_url = make_isgd($post_id,$post_url);
                      
                      } elseif ($s2p_options['shorten_service'] == 'jmp') {
              
                      $bitly_user = $s2p_options['bitly_user'];
                      $bitly_key = $s2p_options['bitly_key'];
                      $bitly_flavour = 'jmp';
                      $short_url = make_bitly($post_id,$post_url,$bitly_user,$bitly_key,$bitly_flavour);

                      } elseif ($s2p_options['shorten_service'] == 'selfdomain') {
                      
                      $s2p_blog_url = get_bloginfo(url);
                                            
                      $short_url = $s2p_blog_url . '/?p=' . $post_id;
                      
                      add_post_meta($post_id, 'short_url', $short_url);          
            
            } else {
            
              $short_url = $short_url_exists;
            
            }
            
            //get message from settings and process title and link
            $message = $s2p_options['message'];
            $message = str_replace('[title]', $post_title, $message);
			      $message = str_replace('[link]', $short_url, $message);
                        
            if ($s2p_options['ping_service'] == 'pingfm'){

               send_pingfm($pingfm_user_key,$post_id,$message);
                            
            } elseif ($s2p_options['ping_service'] == 'twitter') {
                                
               send_twit($post_id,$s2p_options['twitter_user'], $s2p_options['twitter_pass'], $message);           
            
            } elseif ($s2p_options['ping_service'] == 'none') {
            
            return;
            
            } elseif ($s2p_options['ping_service'] == 'both') {
            
              send_pingfm($pingfm_user_key,$post_id,$message);
              send_twit($post_id,$s2p_options['twitter_user'], $s2p_options['twitter_pass'], $message);
            
            }                     

    }

} // end function

function s2p_init_options() {

// create options array. if options already exists add_option function does nothing.

  $s2p_options['message'] = "[title] [link]";
  $s2p_options['ping_service'] = "pingfm";
  $s2p_options['pingfm_key'] = "";
  $s2p_options['twitter_user'] = "";
  $s2p_options['twitter_pass'] = "";
  $s2p_options['shorten_service'] = "bitly";
  $s2p_options['bitly_user'] = "";
  $s2p_options['bitly_key'] = "";
  $s2p_options['trim_user'] = "";
  $s2p_options['trim_pass'] = "";
  $s2p_options['yourls_api'] = "";
  $s2p_options['yourls_user'] = "";
  $s2p_options['yourls_pass'] = "";
  $s2p_options['supr_user'] = "";
  $s2p_options['supr_key'] = "";
  $s2p_options['wp_api_key'] = "";
  $s2p_options['wp_blog_id'] = "";
  $s2p_options['cligs_key'] = "";     

     // check if plugin installed is previous to 1.4.1, and add new options in that case

  $existing_s2p_options = get_option('shorten2ping_options');
  
  
  if (empty($existing_s2p_options)) {
  
      delete_option('shorten2ping_options');
      add_option('s2p_options', $s2p_options );

     } else {

          $s2p_new_options = array ("wp_api_key" => "", "wp_blog_id" => "", "cligs_key" => "");
          $merged_options = array_merge($existing_s2p_options, $s2p_new_options);
          
          delete_option ('shorten2ping_options');
          add_option('s2p_options', $merged_options );  
     }


}

function shorten2ping_options_subpanel() {
  
  $s2p_options = get_option('s2p_options');  
  

  	if (isset($_POST['info_update'])) 
	     {
	
      // if we have the wp api key, ask for the blog_id to store it for future use
      $no_blog_id = empty($s2p_options['wp_blog_id']);
      $has_wp_api_key = !empty($s2p_options['wp_api_key']);
      
          if ($no_blog_id && $has_wp_api_key) {
              $s2p_wp_blog_id = '';  
              $s2p_wp_blog_id = s2p_get_blog_id($s2p_options['wp_api_key']);
    
          }

		// update_option( 'shorten2ping_options', $s2p_options );
		echo '<div id="message" class="updated fade"><p><strong>' . __('Settings saved.') . '</strong></p></div>';

    	} 

	?>
<div class="wrap">
	 <div id="icon-options-general" class="icon32"><br /></div>
	 
	 <h2><?php _e('Shorten2Ping Options','shorten2ping') ?></h2>
 
		<p><?php _e('Shorten2Ping allows you to update status at Ping.fm or Twitter (only) whenever a new blog entry is published.  To start using it, simply enter the required information below, and press update information button. You only need to fill data for the services you want to use (i.e. if you want to use Ping.fm and not Twitter, you dont need to fill Twitter information).','shorten2ping') ?>
    </p><p>
		<?php _e('You can also customize the message for the status notification by using the "message" field below.  You can use [title] to represent the title of the blog entry, and [link] to represent the permalink.','shorten2ping') ?>
    </p>

  <div id="tabs">
  
  <ul>
    <li><a href="#tabs-1"><?php _e('General','shorten2ping'); ?></a> |</li>
    <li><a href="#tabs-2"><?php _e('Notification','shorten2ping'); ?></a> |</li>
    <li><a href="#tabs-3"><?php _e('Shorteners','shorten2ping'); ?></a></li>
  </ul>
  
  <form method="post" name="options" action="options.php">
    <?php if (!empty($s2p_wp_blog_id)) { ?>
    <input type="hidden" name="s2p_options[message]" value="<?php echo $s2p_wp_blog_id; ?>" />
    <?php } ?>
    	<?php settings_fields('s2p_plugin_options'); ?>
    <div id="tabs-1">    
      <br />
          
      <table width="100%" cellspacing="0" class="widefat">
        <thead>
          <tr>
            <th width="140"><?php _e('Setting','shorten2ping'); ?></th>
            <th width="450">&nbsp;</th>
            <th><?php _e('Description','shorten2ping'); ?></th>
          </tr>
        </thead>
        
			<tr><th><?php _e('Status Message','shorten2ping') ?></th>
      <td><input type="text" name="s2p_options[message]" class="widefat" value="<?php echo(htmlentities(utf8_decode($s2p_options['message']))); ?>" /></td>
      <td class="description"><?php _e('Use [title] for the post title, and [link] for the short link. Example: New post, [title] [link]','shorten2ping') ?>
      </td>
      </tr>        
        
      <tr>
	    <th><?php _e('Shorten Permalinks With:','shorten2ping') ?></th>
      <td><select name="s2p_options[shorten_service]">
	         <option value='bitly' <?php if ($s2p_options['shorten_service'] == 'bitly') echo 'selected="selected"'; ?> ><?php _e('bit.ly','shorten2ping') ?></option>
	         <option value='trim' <?php if ($s2p_options['shorten_service'] == 'trim') echo 'selected="selected"'; ?> ><?php _e('tr.im','shorten2ping') ?></option>
	         <option value='wpme' <?php if ($s2p_options['shorten_service'] == 'wpme') echo 'selected="selected"'; ?> ><?php _e('wp.me','shorten2ping') ?></option>
	         <option value='supr' <?php if ($s2p_options['shorten_service'] == 'supr') echo 'selected="selected"'; ?> ><?php _e('su.pr','shorten2ping') ?></option>
	         <option value='isgd' <?php if ($s2p_options['shorten_service'] == 'isgd') echo 'selected="selected"'; ?> ><?php _e('is.gd','shorten2ping') ?></option>
	         <option value='jmp' <?php if ($s2p_options['shorten_service'] == 'jmp') echo 'selected="selected"'; ?> ><?php _e('j.mp','shorten2ping') ?></option>
	         <option value='cligs' <?php if ($s2p_options['shorten_service'] == 'cligs') echo 'selected="selected"'; ?> ><?php _e('cli.gs','shorten2ping') ?></option>
	         <option value='yourls' <?php if ($s2p_options['shorten_service'] == 'yourls') echo 'selected="selected"'; ?> ><?php _e('YOURLS','shorten2ping') ?></option>
	         <option value='selfdomain' <?php if ($s2p_options['shorten_service'] == 'selfdomain') echo 'selected="selected"'; ?> ><?php _e('Self domain','shorten2ping') ?></option>
	         <option value='none' <?php if ($s2p_options['shorten_service'] == 'none') echo 'selected="selected"'; ?> ><?php _e('None','shorten2ping') ?></option>
	         </select></td>
      <td class="description"><?php _e('Choose to make short URLs using any of the shorteners or turn off this feature.','shorten2ping') ?></td>
      </tr>             

      <tr>
	    <th><?php _e('Send Notification To:','shorten2ping') ?></th>
      <td><select name="s2p_options[ping_service]">
	         <option value='pingfm' <?php if ($s2p_options['ping_service'] == 'pingfm') echo 'selected="selected"'; ?> ><?php _e('Ping.fm','shorten2ping') ?></option>
	         <option value='twitter' <?php if ($s2p_options['ping_service'] == 'twitter') echo 'selected="selected"'; ?> ><?php _e('Twitter','shorten2ping') ?></option>
	         <option value='both' <?php if ($s2p_options['ping_service'] == 'both') echo 'selected="selected"'; ?>><?php _e('Both','shorten2ping') ?></option>
	         <option value='none' <?php if ($s2p_options['ping_service'] == 'none') echo 'selected="selected"'; ?>><?php _e('None','shorten2ping') ?></option>
           </select></td>
      <td class="description"><?php _e('Choose to send notification to Ping.fm (default), Twitter, both services, or turn off this feature.','shorten2ping') ?></td>
      </tr>
      
      </table>
    
    </div>
    
    <div id="tabs-2">

      <br />
    
      <table width="100%" cellspacing="0" class="widefat">
        <thead>
          <tr>
            <th width="140"><?php _e('Setting','shorten2ping'); ?></th>
            <th width="450">&nbsp;</th>
            <th><?php _e('Description','shorten2ping'); ?></th>
          </tr>
        </thead>
        
  		<tr><th><?php _e('Ping.fm','shorten2ping') ?></th>
      <td><?php _e('API Key','shorten2ping') ?> <input type="text" class="widefat" name="s2p_options[pingfm_key]" value="<?php echo($s2p_options['pingfm_key']); ?>" /></td>
      <td class="description"><?php _e('Put your Ping.fm <a href="http://ping.fm/key/">API key</a> here.','shorten2ping') ?></td>
      </tr>

      
			<tr><th><?php _e('Twitter','shorten2ping') ?></th><td><?php _e('Username','shorten2ping') ?> <input type="text" name="s2p_options[twitter_user]" class="widefat" value="<?php echo($s2p_options['twitter_user']); ?>" />
      <?php _e('Password','shorten2ping') ?> <input type="password" name="s2p_options[twitter_pass]" class="widefat" value="<?php echo($s2p_options['twitter_pass']); ?>" />
      </td><td class="description"><?php _e('Unfortunately <a href="http://twitter.com">Twitter</a> doesn\'t have API keys for users, so you must put here your user login and password if you want to use this service.','shorten2ping') ?>
      </td>
      </tr>

      </table>
      
    </div>
    
    <div id="tabs-3">
    
      <br />
      
      <table width="100%" cellspacing="0" class="widefat">
        <thead>
          <tr>
            <th width="140"><?php _e('Setting','shorten2ping'); ?></th>
            <th width="450">&nbsp;</th>
            <th><?php _e('Description','shorten2ping'); ?></th>
          </tr>
        </thead>

      <tr><th><?php _e('bit.ly or j.mp','shorten2ping') ?></th>
      <td><?php _e('API Login','shorten2ping') ?> <input type="text" class="widefat" name="s2p_options[bitly_user]" value="<?php echo($s2p_options['bitly_user']); ?>" />
			<?php _e('API Key','shorten2ping') ?> <input type="text" class="widefat" name="s2p_options[bitly_key]" value="<?php echo($s2p_options['bitly_key']); ?>"  />
      </td>
      <td class="description"><?php _e('Put here your API login and <a href="http://bit.ly/account/">Bit.ly</a> API key. This is the same for <a href="http://j.mp/account/">J.mp</a>.','shorten2ping') ?>
      </td>
      </tr>
			
      <tr><th><?php _e('tr.im','shorten2ping') ?></th><td><?php _e('Username','shorten2ping') ?> <input type="text" class="widefat" name="s2p_options[trim_user]" value="<?php echo($s2p_options['trim_user']); ?>"  />
      <?php _e('Password','shorten2ping') ?> <input type="password" class="widefat" name="s2p_options[trim_pass]" value="<?php echo($s2p_options['trim_pass']); ?>"  /></td>
      <td class="description"><?php _e('Unfortunately <a href="http://tr.im">Tr.im</a> doesn\'t have API keys for users, so you must put here your user login and password if you want to use this service.','shorten2ping') ?></td>
      </tr>
      
      <tr><th><?php _e('wp.me','shorten2ping') ?></th>
      <td><?php _e('WordPress API Key','shorten2ping') ?> <input type="text" class="widefat" name="s2p_options[wp_api_key]" value="<?php echo($s2p_options['wp_api_key']); ?>" />
      </td>
      <td class="description"><?php _e('Put here your <a href="http://en.support.wordpress.com/api-keys/">API key from wordpress.com</a> account.','shorten2ping') ?>
      </td>
      </tr>
      
			<tr><th><?php _e('YOURLS','shorten2ping') ?></th>
      <td><?php _e('Username','shorten2ping') ?> <input type="text" class="widefat" name="s2p_options[yourls_user]" value="<?php echo($s2p_options['yourls_user']); ?>"  />
      <?php _e('Password','shorten2ping') ?> <input type="password" class="widefat" name="s2p_options[yourls_pass]" value="<?php echo($s2p_options['yourls_pass']); ?>" />
      </td><td class="description"><?php _e('Put here your username and password for <a href="http://yourls.org/">YOURLS</a>.','shorten2ping') ?>
      </td></tr>
      
      <tr><th>&nbsp;</th>
      <td><?php _e('YOURLS API URL','shorten2ping') ?> <input type="text" name="s2p_options[yourls_api]" class="widefat" value="<?php echo($s2p_options['yourls_api']); ?>" />
      </td><td class="description"><?php _e('Example: http://example.com/yourls-api.php','shorten2ping') ?>
      </td>
      </tr>
      
      <tr><th><?php _e('su.pr','shorten2ping') ?></th>
      <td><?php _e('API Login','shorten2ping') ?> <input type="text" name="s2p_options[supr_user]" class="widefat" value="<?php echo($s2p_options['supr_user']); ?>" />
			<?php _e('API Key','shorten2ping') ?> <input type="text" name="s2p_options[supr_key]" class="widefat" value="<?php echo($s2p_options['supr_key']); ?>" />
      </td><td class="description"><?php _e('Put here your API login and <a href="http://su.pr/settings/">Su.pr</a> API key.','shorten2ping') ?>
      </td>
      </tr>

      <tr><th><?php _e('cli.gs','shorten2ping') ?></th>
      <td><?php _e('cli.gs API Key','shorten2ping') ?> <input type="text" class="widefat" name="s2p_options[cligs_key]" value="<?php echo($s2p_options['cligs_key']); ?>" />
      </td>
      <td class="description"><?php _e('Put here your <a href="http://cli.gs/user/api">API key from cli.gs</a> account.','shorten2ping') ?>
      </td>
      </tr>
           
      </table>

    </div>

   		<div class="submit"><input type="submit" class="button-primary" name="info_update" value="<?php _e('Save settings','shorten2ping') ?>" /></div>

     </form>
     
    <p>
		<?php _e("If you find this plugin useful, please consider to make a donation to Shorten2Ping's author (any amount will be appreciated).",'shorten2ping') ?>
    </p>

    <form action="https://www.paypal.com/cgi-bin/webscr" method="post"><div class="paypal-donations"><input type="hidden" name="cmd" value="_donations" /><input type="hidden" name="business" value="&#x64;&#x6f;&#x6e;&#x61;&#x74;&#x65;&#x40;&#x73;&#x61;&#x6d;&#x75;&#x65;&#x6c;&#x61;&#x67;&#x75;&#x69;&#x6c;&#x65;&#x72;&#x61;&#x2e;&#x63;om" /><input type="hidden" name="item_name" value="Shorten2Ping WordPress Plugin" /><input type="hidden" name="item_number" value="shorten2ping" /><input type="hidden" name="currency_code" value="EUR" /><input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" name="submit" alt="PayPal - The safer, easier way to pay online." /><img alt="" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" /></div></form>   
    
    <?php

    ?>    
    </div>

</div>
	<?php
}

function shorten2ping_add_plugin_option()
{
	$shorten2ping_plugin_name = 'Shorten2Ping';

	if (function_exists('add_options_page')) 
	{
		$s2p_options_page = add_options_page($shorten2ping_plugin_name, $shorten2ping_plugin_name, 'manage_options', basename(__FILE__), 'shorten2ping_options_subpanel');
    }
    
    add_action("admin_print_scripts-$s2p_options_page", 's2p_admin_js');
    add_action("admin_print_styles-$s2p_options_page", 's2p_admin_css');	
}

function shorten2ping_add_settings_link($links) {
	$links[] = '<a class="edit" href="options-general.php?page=shorten2ping.php" title="'. __('Go to settings page','shorten2ping') .'">' . __('Settings','shorten2ping') . '</a>';
	return $links;
}


// Funtion to send 'status' to Ping.fm. Based on the one by Sold Out Activist for the pingPressFM

function send_pingfm($pingfm_user_key,$post_id,$message) {
	if (!$pingfm_user_key) return false;
                 	
            		$post_data = Array(
			           'api_key' => '6f604abd220a79bcd443a4824354734d',
			           'user_app_key' => $pingfm_user_key,
			           'post_method'  => 'status',
			           'body'  => $message,
                 // 'debug' => 1
		            );

	// send data to ping.fm
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Shorten2Ping');
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_URL, 'http://api.ping.fm/v1/'. 'user.post');
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	$output = curl_exec($ch);

	// if ok, stores the ping_id
	if (preg_match('/OK/', $output)) {
		preg_match('/\<transaction\>([^\<]*)\<\/transaction\>/', $output, $match);
		$ping_result = addslashes(trim($match[1]));

    //only for debugging. not needed to work.
    // add_post_meta($post_id, 'pinged', $ping_result);
			     
	// if not ok, stores the error message
	} else {
		preg_match('/\<message\>([^\<]*)\<\/message\>/', $output, $match);
		$ping_result = addslashes(trim($match[1]));
		
		add_post_meta($post_id, 'pingfm_error', $ping_result);
	}
           	
}

function send_twit ($post_id,$twitter_user,$twitter_pass,$message) {
	
    $twitter_host = "http://twitter.com/statuses/update.json?status=" . urlencode(stripslashes(urldecode($message))); 
		$ch = curl_init();
	
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Shorten2Ping');
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); //Use basic authentication
		curl_setopt($ch, CURLOPT_USERPWD, "$twitter_user:$twitter_pass");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //Do not check SSL certificate (but use SSL).
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_URL, $twitter_host);

		$result = curl_exec($ch);	
		$json = json_decode($result,true);
				
		if ($json['error']) {
    
       add_post_meta($post_id, 'twitter_error', $json['error']);
    
    } 

}


// Original code by David Walsh (http://davidwalsh.name/bitly-php), improved by Jason Lengstorf (http://www.ennuidesign.com/).

      function make_bitly($post_id, $url, $login, $appkey, $bitly_flavour, $history=1, $version='2.0.1') {
                //create the URL
                $bitly_api = 'http://api.bit.ly/v3/shorten';
                
                if ($bitly_flavour == 'bitly') {
                
                $param = 'login='.$login.'&apiKey='.$appkey.'&uri='.urlencode($url).'&format=json';
                
                } elseif ($bitly_flavour == 'jmp') {
                
                $param = 'login='.$login.'&apiKey='.$appkey.'&uri='.urlencode($url).'&format=json&domain=j.mp';
                
                } 

                //get the url
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_USERAGENT, 'Shorten2Ping');
                curl_setopt($ch, CURLOPT_URL, $bitly_api . "?" . $param);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $response = curl_exec($ch);
                curl_close($ch);

                $json = json_decode($response,true);
                
                // check if all goes ok, if not, return error message
                
                    if ($json['status_txt'] == 'OK') {
                                           
                      add_post_meta($post_id, 'short_url', $json['data']['url']);
                      
                      return $json['data']['url'];                   
                    
                    } elseif ($json['status_txt'] == 'INVALID_LOGIN') {
                    
                      add_post_meta($post_id, 'bitly_error', 'INVALID_LOGIN');                 

                    } elseif ($json['status_txt'] == 'INVALID_APIKEY') {
                    
                      add_post_meta($post_id, 'bitly_error', 'INVALID_APIKEY'); 
                    
                    } else {
                    
                       add_post_meta($post_id, 'bitly_error', $response);
                       
                    }               

          }
          
// Function to shorten post URL using tr.im
      function make_trim($post_id, $url, $trim_user, $trim_pass)
          {        
         
                //create the URL
                $trim = 'http://api.tr.im/api/trim_url.json';
                $param = '?url='.urlencode($url);

                //get the url
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_USERAGENT, 'Shorten2Ping');
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); //Use basic authentication
                curl_setopt($ch, CURLOPT_USERPWD,$trim_user . ":" . $trim_pass);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //Do not check SSL certificate (but use SSL).
                curl_setopt($ch, CURLOPT_URL, $trim . $param);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $response = curl_exec($ch);
                curl_close($ch);

                $json = json_decode($response,true);
                
                // check if all goes ok, if not, return error message
                
                    if ($json['status']['result'] == 'OK') {
                                           
                        add_post_meta($post_id, 'short_url', $json['url']);
                        
                        return $json['url'];                    
                    
                    } else {
                    
                      add_post_meta($post_id, 'trim_error', $json['status']['message']);                  
                    
                    }           
                       
          }

 
          function make_yourls ($post_id,$post_url,$yourls_api,$yourls_user,$yourls_pass) {
          
                  $ch = curl_init();
                  curl_setopt($ch, CURLOPT_USERAGENT, 'Shorten2Ping');
                  curl_setopt($ch, CURLOPT_URL, $yourls_api);
                  curl_setopt($ch, CURLOPT_HEADER, 0);            // No header in the result
                  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return, do not echo result
                  curl_setopt($ch, CURLOPT_POST, 1);              // This is a POST request
                  curl_setopt($ch, CURLOPT_POSTFIELDS, array(     // Data to POST
                  		'url'      => $post_url,
                  		'format'   => 'json',
                  		'action'   => 'shorturl',
                  		'username' => $yourls_user,
                  		'password' => $yourls_pass
                  	));

                  $response = curl_exec($ch);
                  curl_close($ch);

                  $json = json_decode($response,true);
                  
                  // check if all goes ok, if not, return error message
                  
                      if ($json['status'] == 'success') {
                                             
                          add_post_meta($post_id, 'short_url', $json['shorturl']);
                          
                          return $json['shorturl'];                    
                      
                      } else {
                      
                        add_post_meta($post_id, 'yourls_error', $json['message']);                  
                      
                      } 
               
          
          }
 
        function make_supr($post_id,$post_url,$supr_key,$supr_user) {
                            
          	     // create API URL
          		  $supr_result = 'http://su.pr/api/shorten?longUrl='.$post_url.'&login='.$supr_user.'&apiKey='.$supr_key;
          
                // get the supr URL
          		  $ch = curl_init();
          		  curl_setopt($ch, CURLOPT_USERAGENT, 'Shorten2PING');
          		  curl_setopt($ch, CURLOPT_URL, $supr_result);
          		  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
          		  curl_setopt($ch, CURLOPT_TIMEOUT, 30);
          		  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          		  $response = curl_exec($ch);
          		  curl_close($ch);

                $json = json_decode($supr_result,true);
                  
                  // check if all goes ok, if not, return error message
                  
                      if ($json['statusCode'] == 'OK') {
                                             
                          add_post_meta($post_id, 'short_url', $json['results'][$post_url]['shortUrl']);
                          
                          return $json['results'][$post_url]['shortUrl'];                    
                      
                      } else {
                      
                        add_post_meta($post_id, 'supr_error', $json['errorMessage']);                  
                      
                      }           
          
          } 
 
 // Another function for making tr.im, but using simple method  -NOT USED, only for testing-
 
          function make_simple_trim($url,$user,$pass) {
              $trim_url = file_get_contents('http://api.tr.im/api/trim_simple?url='.urlencode($url).'&username='.$user.'&password='.$pass);
              return $trim_url;
            }
	
    function make_isgd($post_id, $url) {
    
    	// create API URL
    		$isgd = 'http://is.gd/api.php?longurl='.urlencode($url);
    	
    	// get the surl
    		$ch = curl_init();
    		curl_setopt($ch, CURLOPT_USERAGENT, 'Shorten2Ping');
    		curl_setopt($ch, CURLOPT_URL, $isgd);
    		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    		$response = curl_exec($ch);
    		curl_close($ch);
    		
        $pos = strpos($response,'http://');
    
          if($pos === true) {
            add_post_meta($post_id, 'isgd_error', $response);
            return $response;
          }
          else {
            add_post_meta($post_id, 'short_url', $response);
            return $response;
          }
      }


 function make_cligs($post_id,$url,$apikey) {

	// create API URL
		$cligs = 'http://cli.gs/api/v1/cligs/create?url='.urlencode($url).'&key='.$apikey.'&appid=s2p';

	// get the surl
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_USERAGENT, 'Shorten2Ping');
		curl_setopt($ch, CURLOPT_URL, $cligs);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);

    $pos = strpos($response,'http://');

      if($pos === true) {
        add_post_meta($post_id, 'cligs_error', $response);
        return $response;
      }
      else {
        add_post_meta($post_id, 'short_url', $response);
        return $response;
      }
  }

    // función para averiguar el blog_id, dato obligatorio para usar el api de stats/wp.me
    function s2p_get_blog_id($api_key) {
    
    	require_once( ABSPATH . WPINC . '/class-IXR.php' );
    
    	$client = new IXR_Client( STATS_XMLRPC_SERVER );
    
    	extract( parse_url( get_option( 'home' ) ) );
    
    	$path = rtrim( $path, '/' );
    
    	if ( empty( $path ) )
    		$path = '/';
    
    	$client->query( 'wpStats.get_blog_id', $api_key, s2p_stats_get_blog() );
    
      // check for errors. if any will add to custom field, if not, nothing is stored at this step
    	if ( $client->isError() ) {
    		if ( $client->getErrorCode() == -32300 )
    		$wp_error = 'Your blog was unable to connect to WordPress.com. Please ask your host for help. (' . $client->getErrorMessage() . ')\n';
    		add_post_meta($post_id, 'wpme_error', $wp_error);
        } else {
    		$wp_error = "Something was wrong: ". $client->getErrorMessage()."\n";
    		add_post_meta($post_id, 'wpme_error', $wp_error);
    	}
    
    	$response = $client->getResponse();
    
    	$blog_id = isset($response['blog_id']) ? (int) $response['blog_id'] : false;
    
    	return $blog_id;
    }

    function s2p_stats_get_blog( ) {
    	$home = parse_url( get_option('home') );
    	$blog = array(
    		'host' => $home['host'],
    		'path' => $home['path'],
    		'name' => get_option('blogname'),
    		'description' => get_option('blogdescription'),
    		'siteurl' => get_option('siteurl'),
    		'gmt_offset' => get_option('gmt_offset'),
    		'version' => STATS_VERSION
    	);
    	return array_map('wp_specialchars', $blog);
    }


      function s2p_dec2sixtwo( $num ) {
    		$index = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    		$out = "";
    
    		for ( $t = floor( log10( $num ) / log10( 62 ) ); $t >= 0; $t-- ) {
    			$a = floor( $num / pow( 62, $t ) );
    			$out = $out . substr( $index, $a, 1 );
    			$num = $num - ( $a * pow( 62, $t ) );
    		}
    
    		return $out;
    	}
    
    
    function make_wpme( $post_id, $post_url, $wp_blog_id, $force_numeric = false ) {
    
    	// Return link to blog home if no post id
    	if ( empty($post_id) )
    	  //echo "El enlace es: http://wp.me/" . s2p_dec2sixtwo($wp_blog_id);
        add_post_meta($post_id, 'wpme_error', 'No post ID...');
       
        // return 'http://wp.me/' . s2p_dec2sixtwo($wp_blog_id);
    
    	$post = get_post($post_id);
    	$type = '';
    
    	if ( !$force_numeric && 'publish' == $post->post_status && 'post' == $post->post_type && strlen($post->post_name) <= 8 && false === strpos($post->post_name, '%')
    		&& false === strpos($post->post_name, '-') ) {
    		$id = $post->post_name;
    		$type = 's';
    	} else {
    		$id = s2p_dec2sixtwo($post_id);
    		if ( 'page' == $post->post_type )
    			$type = 'P';
    		elseif ( 'post' == $post->post_type )
    			$type = 'p';
    		elseif ( 'attachment' == $post->post_type )
    			$type = 'a';
    	}
    
    	if ( empty($type) )
    		return '';
      $wpme_shorlink = 'http://wp.me/' . $type . s2p_dec2sixtwo($wp_blog_id) . '-' . $id;
      add_post_meta($post_id, 'short_url', $wpme_shorlink);
    	return $wpme_shorlink;
    }
	

// tabs for options page

function s2p_admin_js() { // options js
	global $shorten2ping_url;
	wp_enqueue_script('jquery-ui-tabs');
	wp_enqueue_script('s2p_tabs_js', $shorten2ping_url . '/includes/s2p_admin.js', array('jquery-ui-tabs'));
}

function s2p_admin_css() { // options css
	global $shorten2ping_url;
	wp_enqueue_style('s2p_tabs_css', $shorten2ping_url . '/includes/s2p_admin.css');
}

// remove wordpress stats wp.me shorlink creation if present.

    if ( !function_exists('remove_wpme') ) {

      add_action( 'plugins_loaded', 'remove_wpme' );
      
      function remove_wpme() {                       
      
          if ( ! function_exists('wp_get_shortlink') ) {
          	// Register these only for WP < 3.0.
          	remove_action('wp_head', 'wpme_shortlink_wp_head');
          	remove_action('wp', 'wpme_shortlink_header');
          	remove_filter( 'get_sample_permalink_html', 'wpme_get_shortlink_html', 10, 2 );
          } else {
          	// Register a shortlink handler for WP >= 3.0.
          	remove_filter('get_shortlink', 'wpme_get_shortlink_handler', 10, 4);
          	remove_action('wp_head', 'wp_shortlink_wp_head');
          	remove_action('wp', 'wp_shortlink_header');
          }
      }

    }
    
function s2p_register_init(){                                                         
  register_setting( 's2p_plugin_options', 's2p_options', 's2p_validate' );

//  register_setting( 's2p_plugin_options', 's2p_options' );
}

function s2p_unregister_settings(){                                                         
  
    unregister_setting( 's2p_plugin_options', 's2p_options', 's2p_validate' );
//    unregister_setting( 's2p_plugin_options', 's2p_options' );
}

function s2p_validate($user_settings) {
	
	$user_settings['message'] =  stripslashes($user_settings['message']);
	
	return $user_settings;
}

register_activation_hook( __FILE__, 's2p_init_options' );
//register_deactivation_hook( __FILE__, 's2p_unregister_settings' );
add_action('admin_init', 's2p_register_init' );
add_action('new_to_publish', 'shorten2ping_published_post');
add_action('draft_to_publish', 'shorten2ping_published_post');
add_action('pending_to_publish', 'shorten2ping_published_post');
add_action('future_to_publish', 'shorten2ping_published_post');
add_action('admin_menu', 'shorten2ping_add_plugin_option');
add_filter( 'plugin_action_links_'.plugin_basename(__FILE__), 'shorten2ping_add_settings_link', -10);
add_action('wp_head', 'short_url_head');
add_action('wp_head', 'fb_thumb_in_head');

define( 'STATS_VERSION', '3' );
define( 'STATS_XMLRPC_SERVER', 'http://wordpress.com/xmlrpc.php' );

?>
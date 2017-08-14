<?php
/**
 * @file messages.api.inc
 * By ben
 * Date: 1/23/12 3:32 PM
 */

/**
 * @return array
 *
 *
 * EXAMPLE: function account_messages_info(){
                          return array(
                            'order_return_pdf'=>array(
                              'title'=>"Order Return PDF",
                              'type'=>"file",
                              'group'=>"account",
                              'fieldset'=>"",
                            ),
                            'order_return_body'=>array(
                              'title'=>"Order Return Body",
                              'type'=>"textarea",
                              'group'=>"account",
                              'fieldset'=>"order_return",
                            )
                          );
                    }
 */
function hook_messages_info(){

  return array(
    'key_name'=>array(
      'title'=>"Form Element title", //passes through to the form element
      'description'=>"Form Element description", //passes through to the form element (optional)
      'type'=>"textfield", // textarea, textfield, richtext, file are supported right now (NEW OPTIONS), image_link
      'group'=>"somegroup", // MUST BE URL FRIENDLY  used for preloading and which admin page. there will be a way to preload a whole group for performance help, also determines what admin page it shows up on.
      'fieldset'=>"somefieldset", // if you specify a field set, we will put them in that collapsible fieldset on the admin page
			'contextual_links'=>TRUE, //TRUE to allow _mm() to wrap output in contextual links (can be overridden in _mm(force_no_context) param, FALSE to always prevent context links in output
			'tokens'=>FALSE, //true to support tokens, false to disable. only works with text type inputs
		),

  );


}

function hook_messages_alter(&$val, $key){

}
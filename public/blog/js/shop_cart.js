$(document).ready(function(){
	$('input.add_to_cart').click(function(){
		add_to_cart($(this));
	});
});

$(document).ready(function(){
	$('img.add_to_cart_2').click(function(){
		add_to_cart($(this));
	});

	$('img.add_to_cart_sug').click(function(){
		add_to_cart_sug($(this));
	});
});

function add_to_cart(clicked_button){
	show_overlay(true);
	show_loading(true);
	
	var quantity = clicked_button.attr('quantity');
	var product_id = clicked_button.attr('product_id');
	var type_id = clicked_button.attr('type_id');
	var product_size = clicked_button.attr('product_size');
	var disc_item = clicked_button.attr('disc_item');
	var in_Kit = clicked_button.attr('in_Kit');
	var rmv_pid = clicked_button.attr('rmv_pid');
	var rmv_pcatid = clicked_button.attr('rmv_pcatid');

	$.ajax({
			type: "POST",
			url: "unorth/ajax_processes/add_to_cart.ajax.php",
			data: "submitted=1&quantity="+quantity+'&product_id='+product_id+'&type_id='+type_id+'&product_size='+product_size+'&disc_item='+disc_item+'&sesid='+Math.random(),
			success: function(result){
										show_loading(false);
										show_overlay(false);
										alert(result);
										window.location.href = "add-to-cart.php";
									  }
	});
}

function add_to_cart_sug(button) {

	var ids = ($(button).attr('in_kit') != '') ? $(button).attr('in_kit').split(',') : null;
	var sug_product_name = $(button).attr('full_product_name');
	var sug_product_img = $(button).parents('td').find('div:first').html();

	var product_ids = new Array();
	if (ids) {

		for (i=0; i<ids.length; i++) {
			product_ids.push(ids[i]);
		}
	}

	if (product_ids.length > 0) {
		customPrompt.button = $(button);
		customPrompt.product_ids = product_ids;
		customPrompt.sug_product_name = sug_product_name;
		customPrompt.sug_product_img = sug_product_img;
		customPrompt.showPrompt();
	}
	else {
		add_to_cart($(button));
	}
}

function product_name(name) {
	if (name.indexOf('-') === -1) return name;
	parts = name.split("-");
	return '<strong>'+parts[0]+'</strong><br />'+parts[1];
}
function show_overlay(show){
	if(show){
		var body_height = $(document).height();
		$('body').prepend("<div id='overlay'></div>");
		$('div#overlay').height(body_height);
	}
	else{
		$('div#overlay').remove();
	}
}

var customPrompt = {
	  current_count: 0,
	  cart_counter: 0,
	  product_ids: new Array(),
	  button: {},
	  sug_product_name: '',
	  sug_product_img: '',
	  showPrompt: function () {
		    var cart_item = $('.cart_item').eq(customPrompt.cart_counter);
			var u_key = $(cart_item).attr('u_key');
				
			var p_name = $(cart_item).parent('td').next().text();
			var p_image = $(cart_item).attr('p_image');
			if ($.inArray(u_key, customPrompt.product_ids) !== -1) {
				var u_parts = u_key.split('_');
				

				customPrompt.generateHTML();
				var body_width = $('body').width() - 80;
				var body_height = $(document).height();
				var left = (body_width / 2) -  (400 /2);
				var top = (window.pageYOffset || document.documentElement.scrollTop || 0) + 200;

				$('div#prompt_overlay').height(body_height);
				$('div#prompt_overlay').show();
				$('div#prompt_box').css('left', left);
				$('div#prompt_box').css('top', top);
				$('div#prompt_box').find('div#prompt_message').html('<table ><tr><td><img src="'+p_image+'" /></td><td style="font-size:15px;padding-left:10px">'+product_name(p_name) + "</td></tr><tr><td colspan=2 style='font-size:15px;padding-top:10px;padding-bottom:10px;'>is included in:</td></tr><tr><td>"+customPrompt.sug_product_img+'</td><td style="font-size:15px;padding-left:10px">'+product_name(customPrompt.sug_product_name)+"</td><tr><tr><td colspan='2' style='font-size:15px;padding-top:10px;'>Do you want to remove it from the cart?</td></tr></table>");
				$('div#prompt_box').show();
				$('div#prompt_box').find('input#prompt_button_yes').unbind('click');
				$('div#prompt_box').find('input#prompt_button_no').unbind('click');

				$('div#prompt_box').find('input#prompt_button_yes').bind('click', function() {
					var url = 'cart_item_delete.php?inkit=1&rmvpid='+u_parts[1]+'&rmvpcat='+u_parts[0];
					$.get(url, function(response) {
						customPrompt.hidePrompt();
					});
				});

				$('div#prompt_box').find('input#prompt_button_no').bind('click', function() {
					customPrompt.hidePrompt();			
				});
			}
			else {
				customPrompt.hidePrompt();
			}
		},
		hidePrompt: function () {
			customPrompt.cart_counter++;
			$('div#prompt_box').hide();
			$('div#prompt_overlay').hide();	
			if (customPrompt.cart_counter <  $('.cart_item').size()) {
				customPrompt.showPrompt();
			}
			else if (customPrompt.cart_counter == $('.cart_item').size()) {
				add_to_cart($(customPrompt.button));
			}
		},
		generateHTML: function() {
				if (!$('div#prompt_box').size()) {
					var html = "<div id='prompt_box' style='background-color:#fdfdfd;position:absolute;display:none;border:3px solid #5d6c7a;position:absolute;width:400px;padding:10px;z-index:99999;'>	<div id='prompt_message' style='font-size:10pt;padding:10px;text-align:left'></div>		<div id='prompt_buttons' style='padding:10px;text-align:center'>			<input type='button' id='prompt_button_yes' value='Yes' />			<input type='button' id='prompt_button_no' value='No' />		</div>	</div>	<div id='prompt_overlay' style='background:none repeat scroll 0 0 #000000;height:100%;left:0;opacity:0.5;position:absolute;top:0;width:100%;z-index:99998;display:none;'></div>";
					$('body').append(html);
				}
		}
	}

function show_loading(show){
	if(show){
		var body_width = $('body').width();
		var left = (body_width / 2) - 100;
		var top = (window.pageYOffset || document.documentElement.scrollTop || 0) + 200;
		$('body').prepend("<div id='loading'><div>Please Wait ...</div></div>");
		$('div#loading').css({'top':top+'px','left':left+'px'});
	}
	else{
		$('div#loading').remove();
	}
}

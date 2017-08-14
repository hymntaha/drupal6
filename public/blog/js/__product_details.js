$(document).ready(function(){
	$('a.main_picture').lightBox({
		imageLoading: '../../images/lightbox-ico-loading.gif',
		imageBtnClose: '../../images/lightbox-btn-close.gif',
		imageBtnPrev: '../../images/lightbox-btn-prev.gif',
		imageBtnNext: '../../images/lightbox-btn-next.gif'
	});
	
	$('div#product_details table input[type=image]').click(function(){
		add_to_cart($(this));
	});
});

function add_to_cart(clicked_button){
	show_overlay(true);
	show_loading(true);
	var quantity = clicked_button.siblings('.quantity').val();
	var product_id = $('input#product_id').val();
	var type_id = $('input#type_id').val();
	var product_size = clicked_button.siblings('input.product_size').val();
	
	$.ajax({
		type: "POST",
		url: "unorth/ajax_processes/add_to_cart.ajax.php",
		data: "submitted=1&quantity="+quantity+'&product_id='+product_id+'&type_id='+type_id+'&product_size='+product_size,
		success: function(result){
			if(clicked_button.siblings('select.quantity').length == 0){
				clicked_button.after('Already on Cart!');
				clicked_button.remove();
			}
			show_loading(false);
			show_overlay(false);
			alert(result);
		}
	});
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
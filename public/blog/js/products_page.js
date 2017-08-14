$(document).ready(function(){
	$('input.add_to_cart').click(function(){
		add_to_cart($(this));
	});
});

var show = {};
show.success = function(txt){
	$('div#loading').html(
		'<div class=success>' +
			'<p>' + txt + '</p>' +
			'<button onclick=show.loading(false)>Continue Shopping</button><button onclick=tocart() class=mblue>Go to Checkout</button>' +
		'</div>'
	).css({
		width: ($.browser.msie & $.browser.version < 8) ? 450 : 350,
		height: 105,
		border: '3px solid #aaa'
	});
}

function tocart(){
	window.location = '/add-to-cart.php';
}
show.loading = function(show){
	if(show){
		var 	body_height= $(document).height(),

			top = (window.pageYOffset || document.documentElement.scrollTop || 0) + 200,
			
			overlay = $("<div id=overlay />")
				.height(body_height)
				.prependTo(document.body);

		$("<div id=overlay-grey />")
			.css('opacity', 0.5)
			.appendTo(overlay);

		$("<div id='loading'><div>Please Wait ...</div></div>")
			.css('top', top + 'px')
			.appendTo(overlay);	
	} else{
		$('#overlay').remove();
	}
}

function add_to_cart(clicked_button){
	show.loading(true);
	
	$.ajax({
		type: "POST",
		url: "../unorth/ajax_processes/add_to_cart.ajax.php",
		data: {
			submitted: 1,
			quantity: clicked_button.siblings('.quantity').val(),
			product_id: clicked_button.attr('product_id'),
			type_id: clicked_button.attr('type_id'),
			product_size: clicked_button.attr('product_size')
		},

		success: function(result){
			if(clicked_button.siblings('select.quantity').length == 0){
				clicked_button.after('Already on Cart!');
				clicked_button.remove();
			}
			show.success(result);
		}
	});
}


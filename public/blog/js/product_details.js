$(document).ready(function(){
	try{
		$('a.main_picture').lightBox({
			imageLoading: '../../images/lightbox-ico-loading.gif',
			imageBtnClose: '../../images/lightbox-btn-close.gif',
			imageBtnPrev: '../../images/lightbox-btn-prev.gif',
			imageBtnNext: '../../images/lightbox-btn-next.gif'
		});
	} catch(ex) {}
	
	$('div#product_details table input[type=image]').click(function(){
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
function add_to_cart(clicked_button){
	show.loading(true);

	$.ajax({
		type: "POST",
		url: "/unorth/ajax_processes/add_to_cart.ajax.php",
		data: {
			submitted: 1,
			quantity: clicked_button.siblings('.quantity').val(),
			product_id: $('input#product_id').val(),
			type_id: $('input#type_id').val(),
			product_size: clicked_button.siblings('input.product_size').val()
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

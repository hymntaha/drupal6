$(document).ready(function(){
	$('div#popup div.close').click(function(){
		$('div#popup').fadeOut('slow',SetClosedCookie);
	});
});

function SetClosedCookie(){
	var today = new Date();
	var exp_date = new Date(today.getTime() + parseInt($('div#popup').attr('expire')));
	document.cookie = "popup_closed=1; expires="+exp_date.toGMTString()+";";
}
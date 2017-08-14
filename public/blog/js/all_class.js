// Showing the class details when clicked on
	$(document).ready(function(){
		
		$('a.class_details').click(function(){
			show_class_details($(this));
			return false;
		});
	});
	
	function show_class_details(a_object){
		var parent_text_div = a_object.parents('div.vidtext');
		var class_image = parent_text_div.prev('div.vidpic').children('img').attr('src');
		var class_title = a_object.text();
		var class_description = a_object.parents('li').siblings('li.description').html();
		var class_instructor = a_object.parents('li').siblings('li.instructor').children('a').text();
		var class_schedules = a_object.parents('li').siblings('li.schedules');
		
		var class_sch=class_schedules.children('span.sd').text();
		
		var class_location = a_object.parents('li').siblings('li.location').text().replace('Location:','');
		var class_venue = a_object.parents('li').siblings('li.venue');
		var venue_name = class_venue.children('span.venue_name').text();
		var venue_location = class_venue.children('span.venue_location').text();
		var venue_link = class_venue.children('a.venue_link').attr('href');
		var venue_phone = class_venue.children('span.venue_phone').text();
		var class_category = a_object.parents('li').siblings('li.category').children('span:eq(1)').text();
		var class_tempnewdate = a_object.parents('li').siblings('li.newTempDate').html();
			var body_width = $('body').width();
			var body_height = $(document).height();
			var left = (body_width / 2) - 250;
			var top = $(window).height() / 2 - 150;
			
		// ------------------------------------------
		
		var box_content = '',overlay = "<div id='overlay' style='height: "+body_height+"px;'></div>";
		box_content += ["<div id='light_box' style='left: "+left+"px; top:"+top+"px;'>",
			"<div class='picture'><img src='"+class_image+"'/></div>",
			"<div class='text'>",
			"<h2>"+class_title+"</h2>"].join('');

		if(class_category) {
			box_content += "	<p><label>Category:</label> "+class_category+"</p>";
		}

		box_content += [
			"<p><label>Instructor:</label> "+class_instructor+"</p>",
			"<p>"+class_schedules.html()+"</p>",
			"<p style='clear:both;'><label>Location:</label> "+class_location+"</p>",
			"<p><label>Venue:</label> "+venue_name+'<br/>'+venue_location+"<br/>",
			"<a href='"+venue_link+"'>"+venue_link+"</a></p>",
			"<p><label>Phone:</label> "+venue_phone+"</p>"].join('');

		if(class_description) {
			box_content += "	<p><label>Description:</label><br/>"+class_description+"</p>";
		}

		box_content += "	</div>";
		box_content += "	<img src='/images/close.png' id='close_box'/>";
		box_content += "	<p style='float: left;'><strong>Please contact phone number above for further information.</strong></p>";

		$(".dl", class_venue).each(function(){
			box_content += '<a style=float:left;margin-top:10px;clear:both href="' + this.href + '" class="button mblue">Download Application</a>';
		});

		box_content += "</div>";
		
		$('div#light_box,div#overlay').remove();
		$('body').prepend(overlay);
		$('body').prepend(box_content);
		$('div#light_box').slideDown('slow');
		
		$("div#light_box img#close_box").click(function(){
			$('div#light_box, div#overlay').remove();
		});
	}
// -------------------------------------------

function validate()
{
	var emailRegEx = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	var str = document.getElementById('emailfld').value;
	var str1 = document.getElementById('confemail').value;
	var flag = 'g';

	$("#errfname, #erremail, #errconfemail1").css('display','none');

	if (document.getElementById('fname').value == "") {
		$("#errfname").css({
			display: 'block',
			color: '#3776C0'
		}).html("Please enter first name");

		flag = 'f';
	}

	if (!str.match(emailRegEx)) {
		$("#erremail").css({
			display: 'block',
			color: '#3776C0'
		}).html("Please enter email address");

		flag = 'f';
	}

	if (!str1.match(emailRegEx)) {
		$("#errconfemail1").css({
			display: 'block',
			color: '#3776C0'
		}).html("Please enter valid email address.");

		flag = 'f';
	}

	if (str != str1 && str1.match(emailRegEx)) {
		$("#errconfemail1").css({
			display: 'block',
			color: '#3776C0'
		}).html("Email and confirm email do not match.");

		flag = 'f';
	}

	return flag != 'f';
}

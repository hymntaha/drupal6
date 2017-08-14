$(document).ready(function()
{
$('#exercise1').click(function(){
val = $('#exercise1');	
if(val.attr('checked') == true)
{
	
	$('#exercise2').removeAttr('checked');	
	$('#exercise3').removeAttr('checked');	
	$('#exercise4').removeAttr('checked');	
	$('#exercise5').removeAttr('checked');	
	$('#exercise6').removeAttr('checked');	
	$('#exercise7').removeAttr('checked');	
	$('#exercise8').removeAttr('checked');	
	$('#exercise9').removeAttr('checked');	
	$('#exercise10').removeAttr('checked');	
	$('#exercise11').removeAttr('checked');	
	var blank='';
	$('#exercise12').val(blank);	
	
	$('#exercise2').attr('disabled', 'disabled');
	$('#exercise3').attr('disabled', 'disabled');
	$('#exercise4').attr('disabled', 'disabled');
	$('#exercise5').attr('disabled', 'disabled');
	$('#exercise6').attr('disabled', 'disabled');
	$('#exercise7').attr('disabled', 'disabled');
	$('#exercise8').attr('disabled', 'disabled');
	$('#exercise9').attr('disabled', 'disabled');
	$('#exercise10').attr('disabled', 'disabled');
	$('#exercise11').attr('disabled', 'disabled');
	$('#exercise12').attr('disabled', 'disabled');
}
else
{
	$('#exercise2').removeAttr('disabled');
	$('#exercise3').removeAttr('disabled');
	$('#exercise4').removeAttr('disabled');
	$('#exercise5').removeAttr('disabled');
	$('#exercise6').removeAttr('disabled');
	$('#exercise7').removeAttr('disabled');
	$('#exercise8').removeAttr('disabled');
	$('#exercise9').removeAttr('disabled');
	$('#exercise10').removeAttr('disabled');
	$('#exercise11').removeAttr('disabled');
	$('#exercise12').removeAttr('disabled');
}
});

$('#neck').click(function(){
val = $('#neck');	
if(val.attr('checked') == true)
{
	$('#pain11').removeAttr('disabled');
	$('#pain12').removeAttr('disabled');
	$('#painext11').removeAttr('disabled');
	$('#painext12').removeAttr('disabled');
}
else
{
	$('#pain11').removeAttr('checked');
	$('#pain12').removeAttr('checked');
	$('#painext11').removeAttr('checked');
	$('#painext12').removeAttr('checked');
	$('#pain11').attr('disabled', 'disabled');
	$('#pain12').attr('disabled', 'disabled');
	$('#painext11').attr('disabled', 'disabled');
	$('#painext12').attr('disabled', 'disabled');
}
});


$('#shoulder').click(function(){
val = $('#shoulder');	
if(val.attr('checked') == true)
{
	$('#pain21').removeAttr('disabled');
	$('#pain22').removeAttr('disabled');
	$('#painext21').removeAttr('disabled');
	$('#painext22').removeAttr('disabled');
}
else
{

	$('#pain21').removeAttr('checked');
	$('#pain22').removeAttr('checked');
	$('#painext21').removeAttr('checked');
	$('#painext22').removeAttr('checked');

	$('#pain21').attr('disabled', 'disabled');
	$('#pain22').attr('disabled', 'disabled');
	$('#painext21').attr('disabled', 'disabled');
	$('#painext22').attr('disabled', 'disabled');
}
});

$('#upperback').click(function(){
val = $('#upperback');	
if(val.attr('checked') == true)
{
	$('#pain31').removeAttr('disabled');
	$('#pain32').removeAttr('disabled');
	$('#painext31').removeAttr('disabled');
	$('#painext32').removeAttr('disabled');
}
else
{
	$('#pain31').removeAttr('checked');
	$('#pain32').removeAttr('checked');
	$('#painext31').removeAttr('checked');
	$('#painext32').removeAttr('checked');
	
	
	$('#pain31').attr('disabled', 'disabled');
	$('#pain32').attr('disabled', 'disabled');
	$('#painext31').attr('disabled', 'disabled');
	$('#painext32').attr('disabled', 'disabled');
}
});

$('#lowerback').click(function(){
val = $('#lowerback');	
if(val.attr('checked') == true)
{
	$('#pain41').removeAttr('disabled');
	$('#pain42').removeAttr('disabled');
	$('#painext41').removeAttr('disabled');
	$('#painext42').removeAttr('disabled');
}
else
{

	$('#pain41').removeAttr('checked');
	$('#pain42').removeAttr('checked');
	$('#painext41').removeAttr('checked');
	$('#painext42').removeAttr('checked');

	$('#pain41').attr('disabled', 'disabled');
	$('#pain42').attr('disabled', 'disabled');
	$('#painext41').attr('disabled', 'disabled');
	$('#painext42').attr('disabled', 'disabled');
}
});

$('#hip').click(function(){
val = $('#hip');	
if(val.attr('checked') == true)
{
	$('#pain51').removeAttr('disabled');
	$('#pain52').removeAttr('disabled');
	$('#painext51').removeAttr('disabled');
	$('#painext52').removeAttr('disabled');
}
else
{
	$('#pain51').removeAttr('checked');
	$('#pain52').removeAttr('checked');
	$('#painext51').removeAttr('checked');
	$('#painext52').removeAttr('checked');
	
	
	$('#pain51').attr('disabled', 'disabled');
	$('#pain52').attr('disabled', 'disabled');
	$('#painext51').attr('disabled', 'disabled');
	$('#painext52').attr('disabled', 'disabled');
}
});

$('#wrist').click(function(){
val = $('#wrist');	
if(val.attr('checked') == true)
{
	$('#pain61').removeAttr('disabled');
	$('#pain62').removeAttr('disabled');
	$('#painext61').removeAttr('disabled');
	$('#painext62').removeAttr('disabled');
}
else
{
	$('#pain61').removeAttr('checked');
	$('#pain62').removeAttr('checked');
	$('#painext61').removeAttr('checked');
	$('#painext62').removeAttr('checked');
	
	
	$('#pain61').attr('disabled', 'disabled');
	$('#pain62').attr('disabled', 'disabled');
	$('#painext61').attr('disabled', 'disabled');
	$('#painext62').attr('disabled', 'disabled');
}
});

$('#knee').click(function(){
val = $('#knee');	
if(val.attr('checked') == true)
{
	$('#pain71').removeAttr('disabled');
	$('#pain72').removeAttr('disabled');
	$('#painext71').removeAttr('disabled');
	$('#painext72').removeAttr('disabled');
}
else
{
	$('#pain71').removeAttr('checked');
	$('#pain72').removeAttr('checked');
	$('#painext71').removeAttr('checked');
	$('#painext72').removeAttr('checked');
	
	
	$('#pain71').attr('disabled', 'disabled');
	$('#pain72').attr('disabled', 'disabled');
	$('#painext71').attr('disabled', 'disabled');
	$('#painext72').attr('disabled', 'disabled');
}
});

$('#foot').click(function(){
val = $('#foot');	
if(val.attr('checked') == true)
{
	$('#pain81').removeAttr('disabled');
	$('#pain82').removeAttr('disabled');
	$('#painext81').removeAttr('disabled');
	$('#painext82').removeAttr('disabled');
}
else
{
	$('#pain81').removeAttr('checked');
	$('#pain82').removeAttr('checked');
	$('#painext81').removeAttr('checked');
	$('#painext82').removeAttr('checked');
	
	
	$('#pain81').attr('disabled', 'disabled');
	$('#pain82').attr('disabled', 'disabled');
	$('#painext81').attr('disabled', 'disabled');
	$('#painext82').attr('disabled', 'disabled');
}
});

//var other2txt = document.getElementById('other2').value;
$('#other1').focus(function(){
var other1txt = document.getElementById('other1').value;

	$('#pain91').removeAttr('disabled');
	$('#pain92').removeAttr('disabled');
	$('#painext91').removeAttr('disabled');
	$('#painext92').removeAttr('disabled');


});

$('#other2').focus(function(){

	$('#pain101').removeAttr('disabled');
	$('#pain102').removeAttr('disabled');
	$('#painext101').removeAttr('disabled');
	$('#painext102').removeAttr('disabled');


});


$('#other1').blur(function(){
var other1txt = document.getElementById('other1').value;
if(other1txt != "" )
{
	$('#pain91').removeAttr('disabled');
	$('#pain92').removeAttr('disabled');
	$('#painext91').removeAttr('disabled');
	$('#painext92').removeAttr('disabled');
}
else
{
	$('#pain91').removeAttr('checked');
	$('#pain92').removeAttr('checked');
	$('#painext91').removeAttr('checked');
	$('#painext92').removeAttr('checked');
	
	
	$('#pain91').attr('disabled', 'disabled');
	$('#pain92').attr('disabled', 'disabled');
	$('#painext91').attr('disabled', 'disabled');
	$('#painext92').attr('disabled', 'disabled');
}
});




$('#other2').blur(function(){
var other1txt = document.getElementById('other2').value;
if(other1txt != "" )
{
	$('#pain101').removeAttr('disabled');
	$('#pain102').removeAttr('disabled');
	$('#painext101').removeAttr('disabled');
	$('#painext102').removeAttr('disabled');
}
else
{
	$('#pain101').removeAttr('checked');
	$('#pain102').removeAttr('checked');
	$('#painext101').removeAttr('checked');
	$('#painext102').removeAttr('checked');
	
	
	$('#pain101').attr('disabled', 'disabled');
	$('#pain102').attr('disabled', 'disabled');
	$('#painext101').attr('disabled', 'disabled');
	$('#painext102').attr('disabled', 'disabled');
}
});




});

function validate(section)
{
	var emailRegEx = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	var flag = 'g';
	var fname = document.getElementById('fname').value;
	var lname = document.getElementById('lname').value;
	var email = document.getElementById('email').value;
	var gender = document.getElementById('gender').value;
	var age = document.getElementById('age').value;
	var height = document.getElementById('height').value;
	var weight = document.getElementById('weight').value;

	$(".panel." + section + " .error").css('display','none');

	if(section == "info") {
		if (document.getElementById('fname').value == "")
		{
			document.getElementById('errfname').innerHTML = "Please enter your first name";
			document.getElementById('errfname').style.display='block';
			flag = 'f';
		}
		if (document.getElementById('lname').value == "")
		{
			document.getElementById('errlname').innerHTML = "Please enter your last name";
			document.getElementById('errlname').style.display='block';
			flag = 'f';
		}
		if (!email.match(emailRegEx))
		{
			document.getElementById('erremail').innerHTML = "Please enter a valid email";
			document.getElementById('erremail').style.display = 'block';
			flag = 'f';
		}
		if (document.getElementById('gender').value == "")
		{
			document.getElementById('errgender').innerHTML = "Please tell us your gender";
			document.getElementById('errgender').style.display = 'block';
			flag = 'f';
		}
		
		if (document.getElementById('gender').value == "f" && (document.getElementById('preg1').checked == false && document.getElementById('preg2').checked == false ) )
		{
			document.getElementById('errpreg').innerHTML = "Please select yes or no.";
			document.getElementById('errpreg').style.display = 'block';
			flag = 'f';
		}

		if (document.getElementById('age').value == "")
		{
			document.getElementById('errage').innerHTML = "Please tell us your age.";
			document.getElementById('errage').style.display = 'block';
			flag = 'f';
		}
		if (document.getElementById('height').value == "")
		{
			document.getElementById('errheight').innerHTML = "Please tell us your height.";
			document.getElementById('errheight').style.display = 'block';
			flag = 'f';
		}
		if (document.getElementById('weight').value == "")
		{
			document.getElementById('errweight').innerHTML = "Please tell us your weight.";
			document.getElementById('errweight').style.display = 'block';
			flag = 'f';
		}
	}
	
	if(section== 'experience') {
		if (document.getElementById('exp1').checked == false && document.getElementById('exp2').checked == false && document.getElementById('exp3').checked == false)
		{
			document.getElementById('errexp').innerHTML = "Please select your experience.";
			document.getElementById('errexp').style.display = 'block';
			flag = 'f';
		}
		if (document.getElementById('exercise1').checked == false && document.getElementById('exercise2').checked == false && document.getElementById('exercise3').checked == false && document.getElementById('exercise4').checked == false && document.getElementById('exercise5').checked == false && document.getElementById('exercise6').checked == false && document.getElementById('exercise7').checked == false && document.getElementById('exercise8').checked == false && document.getElementById('exercise9').checked == false &&document.getElementById('exercise10').checked == false && document.getElementById('exercise11').checked == false && document.getElementById('exercise12').checked == false)
		{
			document.getElementById('errexercise').innerHTML = "Please select your exercise.";
			document.getElementById('errexercise').style.display = 'block';
			flag = 'f';
		}
	}

	if(section == 'goals') {
		if (document.getElementById('goal1').checked == false && document.getElementById('goal2').checked == false && document.getElementById('goal3').checked == false && document.getElementById('goal4').checked == false && document.getElementById('goal5').checked == false && document.getElementById('goal6').checked == false  )
		{
			document.getElementById('errgoal').innerHTML = "Please select your goal.";
			document.getElementById('errgoal').style.display = 'block';
			flag = 'f';
		}

		var neck = $('#neck');	
		var shoulder = $('#shoulder');	
		var upperback = $('#upperback');	
		var lowerback = $('#lowerback');	
		var hip = $('#hip');	
		var wrist = $('#wrist');	
		var knee = $('#knee');	
		var foot = $('#foot');	
		var other = document.getElementById('other1').value;
		var other2 = document.getElementById('other2').value;

		$('#p_ailments').find('input[type=checkbox], input[type=text]').each(function() {
			var value;
			var ms = $(this).parents('tr.p_row').find('.ms:checked');
			var oc = $(this).parents('tr.p_row').find('.oc:checked');
			var error = $(".error", this.parentNode);
			var check = false;
			switch ($(this).attr('type')) {
				case 'checkbox':
					if ($(this).attr('checked') == true) {
						check = true;
					}
				break;
				case 'text':
					if ($.trim($(this).val()) != '') {
						check = true;
					}
				break;
			}

			if (check == true)	{
				var err = new Array();
				var size = 0;
				if ($(ms).length == 0) {
					err.push('"Moderate or Severe"');
				}
				if ($(oc).length == 0) {
					err.push('"Occasional or Chronic"');
				}
				if (err.length != 0) {
					flag = 'f';
					var p_err = "Please select one from" + ((err.length>1) ? ' both ' : ' ');
					p_err += err.join(' AND ');
					$("td", error.parents('tr')).css({verticalAlign: 'top'});
					$(error).css({
						position:'absolute'
					}).html(p_err)
					.fadeIn();

					$(error.parent()).animate({
						height: $(error).height() + 20
					});
				} else {
					$(error.parent()).css('height', 'auto');
					$(error).hide();
				}

			}
		});
	}

	if(flag == 'f') {
		$(".error." + section).css('display','block');
	}
		
	return flag == 'g';
}

var show = (function(){
	var last;
	return function (id){
		if(last) {
			// only validate if trying to move forward in the process.
			// Otherwise we may be going backward, which is ok.
			if(id == 'finish' && !validate('info')) {
				id = 'info';
			}
			if(id == 'info' && !validate('experience')) {
				id = 'experience';
			}
			if(id == 'experience' && !validate('goals')) {
				id = 'goals';
			}

			if(!validate('goals')) return;

			if(id == 'finish') {
				$("form[name=frm1]").submit();
			}

			$("." + last).removeClass('active');
			$("#intro").animate({opacity: 0.25},1000);

			if(last != id) {
				$(".panel." + last).fadeOut(function(){
					$(".panel." + id).fadeIn();
					window.scrollTo(0,0);
				});
			}
		} else {
			$(".panel." + id).css('display','block');
		}

		$("." + id).addClass('active');

		if(last != id) {
			last = id;
		}
		window.location.hash = id;
	}
})();

$(document).ready(function(){
	show('goals');
	var lastHash = window.location.hash;

	setInterval(function(){
		if(window.location.hash == lastHash) {
			return;
		}

		// fix the initial hash problem.
		if(window.location.hash.length == 0) {
			history.go(-1);
		} else {
			show(window.location.hash.substr(1));
		}		
		lastHash = window.location.hash;
	}, 150);
});


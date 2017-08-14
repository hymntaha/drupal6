$(document).ready(function()
{

var chkmonday = document.getElementById('chkmonday').checked;
var chktuesday = document.getElementById('chktuesday').checked;
var chkwednesday = document.getElementById('chkwednesday').checked;
var chkthusday = document.getElementById('chkthusday').checked;
var chkfriday = document.getElementById('chkfriday').checked;
var chksaturday = document.getElementById('chksaturday').checked;
var chksunday = document.getElementById('chksunday').checked;  

if (chkmonday == false)
{
	$('#montime1').attr('disabled', 'disabled');
	$('#montime2').attr('disabled', 'disabled');
	$('#reminderMon').attr('disabled', 'disabled');
	$('#timeleeadMon').attr('disabled', 'disabled');
}
if (chktuesday == false)
{
	$('#tuetime1').attr('disabled', 'disabled');
	$('#tuetime2').attr('disabled', 'disabled');
	$('#reminderTue').attr('disabled', 'disabled');
	$('#timeleadTue').attr('disabled', 'disabled');
}
if (chkwednesday == false)
{
	$('#wedtime1').attr('disabled', 'disabled');
	$('#wedtime2').attr('disabled', 'disabled');
	$('#reminderWed').attr('disabled', 'disabled');
	$('#leadtimeWed').attr('disabled', 'disabled');
}
if (chkthusday == false)
{
	$('#thustime1').attr('disabled', 'disabled');
	$('#thustime2').attr('disabled', 'disabled');
	$('#reminderThus').attr('disabled', 'disabled');
	$('#leadtimeThus').attr('disabled', 'disabled');
}
if (chkfriday == false)
{
	$('#fritime1').attr('disabled', 'disabled');
	$('#fritime2').attr('disabled', 'disabled');
	$('#reminderFri').attr('disabled', 'disabled');
	$('#leadtimeFri').attr('disabled', 'disabled');
}
if (chksaturday == false)
{
	$('#sattime1').attr('disabled', 'disabled');
	$('#sattime2').attr('disabled', 'disabled');
	$('#reminderSat').attr('disabled', 'disabled');
	$('#leadtimeSat').attr('disabled', 'disabled');
}
if (chksunday == false)
{
	$('#suntime1').attr('disabled', 'disabled');
	$('#suntime2').attr('disabled', 'disabled');
	$('#reminderSun').attr('disabled', 'disabled');
	$('#leadtimeSun').attr('disabled', 'disabled');
}


$('#chkmonday').click(function(){
	var val = $('#chkmonday');
	if(val.attr('checked') == true)
	{
		$('#montime1').attr('disabled', '');
		$('#montime2').attr('disabled', '');
		$('#reminderMon').attr('disabled', '');
		$('#timeleeadMon').attr('disabled', '');
	}
	else
	{
		$('#montime1').attr('disabled', 'disabled');
		$('#montime2').attr('disabled', 'disabled');
		$('#reminderMon').attr('disabled', 'disabled');
		$('#timeleeadMon').attr('disabled', 'disabled');
	}
});

$('#chktuesday').click(function(){
	var val = $('#chktuesday');
	if(val.attr('checked') == true)
	{
		$('#tuetime1').attr('disabled', '');
		$('#tuetime2').attr('disabled', '');
		$('#reminderTue').attr('disabled', '');
		$('#timeleadTue').attr('disabled', '');
	}
	else
	{
		$('#tuetime1').attr('disabled', 'disabled');
		$('#tuetime2').attr('disabled', 'disabled');
		$('#reminderTue').attr('disabled', 'disabled');
		$('#timeleadTue').attr('disabled', 'disabled');
	}
});


$('#chkwednesday').click(function(){
	var val = $('#chkwednesday');
	if(val.attr('checked') == true)
	{
		$('#wedtime1').attr('disabled', '');
		$('#wedtime2').attr('disabled', '');
		$('#reminderWed').attr('disabled', '');
		$('#leadtimeWed').attr('disabled', '');
	}
	else
	{
		$('#wedtime1').attr('disabled', 'disabled');
		$('#wedtime2').attr('disabled', 'disabled');
		$('#reminderWed').attr('disabled', 'disabled');
		$('#leadtimeWed').attr('disabled', 'disabled');
	}
});

$('#chkthusday').click(function(){
	var val = $('#chkthusday');
	if(val.attr('checked') == true)
	{
		$('#thustime1').attr('disabled', '');
		$('#thustime2').attr('disabled', '');
		$('#reminderThus').attr('disabled', '');
		$('#leadtimeThus').attr('disabled', '');
	}
	else
	{
		$('#thustime1').attr('disabled', 'disabled');
		$('#thustime2').attr('disabled', 'disabled');
		$('#reminderThus').attr('disabled', 'disabled');
		$('#leadtimeThus').attr('disabled', 'disabled');
	}
});

$('#chkfriday').click(function(){
	var val = $('#chkfriday');
	if(val.attr('checked') == true)
	{
		$('#fritime1').attr('disabled', '');
		$('#fritime2').attr('disabled', '');
		$('#reminderFri').attr('disabled', '');
		$('#leadtimeFri').attr('disabled', '');
	}
	else
	{
		$('#fritime1').attr('disabled', 'disabled');
		$('#fritime2').attr('disabled', 'disabled');
		$('#reminderFri').attr('disabled', 'disabled');
		$('#leadtimeFri').attr('disabled', 'disabled');
	}
});

$('#chksaturday').click(function(){
	var val = $('#chksaturday');
	if(val.attr('checked') == true)
	{
		$('#sattime1').attr('disabled', '');
		$('#sattime2').attr('disabled', '');
		$('#reminderSat').attr('disabled', '');
		$('#leadtimeSat').attr('disabled', '');
	}
	else
	{
		$('#sattime1').attr('disabled', 'disabled');
		$('#sattime2').attr('disabled', 'disabled');
		$('#reminderSat').attr('disabled', 'disabled');
		$('#leadtimeSat').attr('disabled', 'disabled');
	}
});

$('#chksunday').click(function(){
	var val = $('#chksunday');
	if(val.attr('checked') == true)
	{
		$('#suntime1').attr('disabled', '');
		$('#suntime2').attr('disabled', '');
		$('#reminderSun').attr('disabled', '');
		$('#leadtimeSun').attr('disabled', '');
	}
	else
	{
		$('#suntime1').attr('disabled', 'disabled');
		$('#suntime2').attr('disabled', 'disabled');
		$('#reminderSun').attr('disabled', 'disabled');
		$('#leadtimeSun').attr('disabled', 'disabled');
	}
});

});

function gotomon(chk,time1,time2,remind,lead)
{
	var chkmonday = document.getElementById(chk).checked;
	if (chkmonday == false)
	{
		document.getElementById(time1).value ="";
		document.getElementById(time2).value ="";
		document.getElementById(remind).value ="";
		document.getElementById(lead).value ="";
	}
}

function validate()
{
	var emailRegEx = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	var flag = 'g';
	
	document.getElementById('errmonday').style.display = 'none';
	/* document.getElementById('erremail').style.display = 'none';
	document.getElementById('errcountry').style.display = 'none';
	document.getElementById('errstate').style.display = 'none';
	document.getElementById('errphone').style.display = 'none';


	var email = document.getElementById('email').value;
	var phone = document.getElementById('phone').value;
	var country = document.getElementById('country').value;
	var state = document.getElementById('state').value; */
	
	var chkmonday = document.getElementById('chkmonday').checked;
	var chktuesday = document.getElementById('chktuesday').checked;
	 var chkwednesday = document.getElementById('chkwednesday').checked;
	 var chkthusday = document.getElementById('chkthusday').checked;
	var chkfriday = document.getElementById('chkfriday').checked;
	var chksaturday = document.getElementById('chksaturday').checked;
	var chksunday = document.getElementById('chksunday').checked;  

	var montime1 = document.getElementById('montime1').value;
	var montime2 = document.getElementById('montime2').value;
	var reminderMon = document.getElementById('reminderMon').value;
	var timeleeadMon = document.getElementById('timeleeadMon').value;

	 var tuetime1 = document.getElementById('tuetime1').value;
	var tuetime2 = document.getElementById('tuetime2').value;
	var reminderTue = document.getElementById('reminderTue').value;
	var timeleadTue = document.getElementById('timeleadTue').value;
	
	var wedtime1 = document.getElementById('wedtime1').value;
	var wedtime2 = document.getElementById('wedtime2').value;
	var reminderWed = document.getElementById('reminderWed').value;
	var leadtimeWed = document.getElementById('leadtimeWed').value;

	var thustime1 = document.getElementById('thustime1').value;
	var thustime2 = document.getElementById('thustime2').value;
	var reminderThus = document.getElementById('reminderThus').value;
	var leadtimeThus = document.getElementById('leadtimeThus').value;

	var fritime1 = document.getElementById('fritime1').value;
	var fritime2 = document.getElementById('fritime2').value;
	var reminderFri = document.getElementById('reminderFri').value;
	var leadtimeFri = document.getElementById('leadtimeFri').value;

	var sattime1 = document.getElementById('sattime1').value;
	var sattime2 = document.getElementById('sattime2').value;
	var reminderSat = document.getElementById('reminderSat').value;
	var leadtimeSat = document.getElementById('leadtimeSat').value;
	
	var suntime1 = document.getElementById('suntime1').value;
	var suntime2 = document.getElementById('suntime2').value;
	var reminderSun = document.getElementById('reminderSun').value;
	var leadtimeSun = document.getElementById('leadtimeSun').value;  
	
	/* if (!email.match(emailRegEx))
	{
			document.getElementById('erremail').innerHTML = "Please enter valid email";
			document.getElementById('erremail').style.display = 'block';
			document.getElementById('erremail').style.color = '#FF0000';
			flag = 'f';
	}

	if (country == "")
	{
		document.getElementById('errcountry').innerHTML = "Please select country.";
		document.getElementById('errcountry').style.display = 'block';
		document.getElementById('errcountry').style.color = '#FF0000';
		flag = 'f';
	}
	if (state == "" )
	{
		document.getElementById('errstate').innerHTML = "Please select state.";
		document.getElementById('errstate').style.display = 'block';
		document.getElementById('errstate').style.color = '#FF0000';
		flag = 'f';
	}
	if (reminderMon == "text" ||  reminderTue == "text" || reminderWed == "text" || reminderThus == "text" || reminderFri == "text" || reminderSat == "text"  || reminderSun == "text" || reminderMon == "call" ||  reminderTue == "call" || reminderWed == "call" || reminderThus == "call" || reminderFri == "call" || reminderSat == "call"  || reminderSun == "call" )
	{
	if (phone == "")
	{
		document.getElementById('errphone').innerHTML = "Please enter phone no.";
		document.getElementById('errphone').style.display = 'block';
		document.getElementById('errphone').style.color = '#FF0000';
		flag = 'f';
	}
	else if(isNaN(phone))
	{
		document.getElementById('errphone').innerHTML = "Please enter phone no in numeric.";
		document.getElementById('errphone').style.display = 'block';
		document.getElementById('errphone').style.color = '#FF0000';
		flag = 'f';
	}
	} */
	/* Commented by amber as requested by Ozlem *//*
	if (chkmonday == false  && chktuesday == false && chkwednesday == false && chkthusday == false && chkfriday == false && chksaturday == false && chksunday == false)
	{ 
		document.getElementById('errmonday').innerHTML = "Please select at least one checkbox.";
		document.getElementById('errmonday').style.display = 'block';
		document.getElementById('errmonday').style.color = '#FF0000';
		flag = 'f'; 
	}*/
	if( chkmonday == true && ( montime1 == ""   || reminderMon == "" || timeleeadMon == ""    ) )
	{
		document.getElementById('errmonday').innerHTML = "Please complete the item you checked above.";
		document.getElementById('errmonday').style.display = 'block';
		document.getElementById('errmonday').style.color = '#FF0000';
		flag = 'f'; 
	}
	 if( chktuesday == true && ( tuetime1 == ""   || reminderTue == "" || timeleadTue == ""    ) )
	{ 
		document.getElementById('errmonday').innerHTML = "Please complete the item you checked above.";
		document.getElementById('errmonday').style.display = 'block';
		document.getElementById('errmonday').style.color = '#FF0000';
		flag = 'f'; 
	}
	if( chkwednesday == true && ( wedtime1 == "" || reminderWed == "" || leadtimeWed == ""    ) )
	{
		document.getElementById('errmonday').innerHTML = "Please complete the item you checked above.";
		document.getElementById('errmonday').style.display = 'block';
		document.getElementById('errmonday').style.color = '#FF0000';
		flag = 'f'; 
	}
	if( chkthusday == true && ( thustime1 == "" ||  reminderThus == "" || leadtimeThus == ""    ) )
	{
		document.getElementById('errmonday').innerHTML = "Please complete the item you checked above.";
		document.getElementById('errmonday').style.display = 'block';
		document.getElementById('errmonday').style.color = '#FF0000';
		flag = 'f'; 
	}
	if( chkfriday == true && ( fritime1 == "" || reminderFri == "" || leadtimeFri == ""    ) )
	{
		document.getElementById('errmonday').innerHTML = "Please complete the item you checked above.";
		document.getElementById('errmonday').style.display = 'block';
		document.getElementById('errmonday').style.color = '#FF0000';
		flag = 'f'; 
	}
	if( chksaturday == true && ( sattime1 == "" || reminderSat == "" || leadtimeSat == ""    ) )
	{
		document.getElementById('errmonday').innerHTML = "Please complete the item you checked above.";
		document.getElementById('errmonday').style.display = 'block';
		document.getElementById('errmonday').style.color = '#FF0000';
		flag = 'f'; 
	}
	 if( chksunday == true && ( suntime1 == "" ||  reminderSun == "" || leadtimeSun == ""    ) )
	{ 
		document.getElementById('errmonday').innerHTML = "Please complete the item you checked above.";
		document.getElementById('errmonday').style.display = 'block';
		document.getElementById('errmonday').style.color = '#FF0000';
		flag = 'f'; 
	} 
	if(flag == 'f' )
	{
		return false;
	}
	else
	{
		return true;
	}

}
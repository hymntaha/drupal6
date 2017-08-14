$(document).ready(function()
{
$('#exercise1').click(function(){
val = $('#exercise1');	
if(val.attr('checked') == true)
{
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

});
function validate()
{
	var emailRegEx = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	var flag = 'g';
	var fname = document.getElementById('fname').value;
	var email = document.getElementById('email').value;
	var gender = document.getElementById('gender').value;
	var age = document.getElementById('age').value;
	document.getElementById('errfname').style.display = 'none';
	document.getElementById('erremail').style.display = 'none';
	document.getElementById('errgender').style.display = 'none';
	document.getElementById('errage').style.display = 'none';
	document.getElementById('errexp').style.display = 'none';
	document.getElementById('errexercise').style.display = 'none';
	document.getElementById('errgoal').style.display = 'none';
	if (document.getElementById('fname').value == "")
	{
		document.getElementById('errfname').innerHTML = "Please enter first name";
		document.getElementById('errfname').style.display = 'block';
		document.getElementById('errfname').style.color = '#3776C0';
		flag = 'f';
	}
	if (!email.match(emailRegEx))
	{
		document.getElementById('erremail').innerHTML = "Please enter valid email";
		document.getElementById('erremail').style.display = 'block';
		document.getElementById('erremail').style.color = '#3776C0';
		flag = 'f';
	}
	if (document.getElementById('gender').value == "")
	{
		document.getElementById('errgender').innerHTML = "Please select gender";
		document.getElementById('errgender').style.display = 'block';
		document.getElementById('errgender').style.color = '#3776C0';
		flag = 'f';
	}
	if (document.getElementById('age').value == "")
	{
		document.getElementById('errage').innerHTML = "Please select age.";
		document.getElementById('errage').style.display = 'block';
		document.getElementById('errage').style.color = '#3776C0';
		flag = 'f';
	}
	if (document.getElementById('exp1').checked == false && document.getElementById('exp2').checked == false && document.getElementById('exp3').checked == false)
	{
		document.getElementById('errexp').innerHTML = "Please select experience.";
		document.getElementById('errexp').style.display = 'block';
		document.getElementById('errexp').style.color = '#3776C0';
		flag = 'f';
	}
	if (document.getElementById('exercise1').checked == false && document.getElementById('exercise2').checked == false && document.getElementById('exercise3').checked == false && document.getElementById('exercise4').checked == false && document.getElementById('exercise5').checked == false && document.getElementById('exercise6').checked == false && document.getElementById('exercise7').checked == false && document.getElementById('exercise8').checked == false && document.getElementById('exercise9').checked == false &&document.getElementById('exercise10').checked == false && document.getElementById('exercise11').checked == false && document.getElementById('exercise12').checked == false)
	{
		document.getElementById('errexercise').innerHTML = "Please select exercise.";
		document.getElementById('errexercise').style.display = 'block';
		document.getElementById('errexercise').style.color = '#3776C0';
		flag = 'f';
	}
	if (document.getElementById('goal1').checked == false && document.getElementById('goal2').checked == false && document.getElementById('goal3').checked == false && document.getElementById('goal4').checked == false && document.getElementById('goal5').checked == false && document.getElementById('goal6').checked == false  )
	{
		document.getElementById('errgoal').innerHTML = "Please select your goal.";
		document.getElementById('errgoal').style.display = 'block';
		document.getElementById('errgoal').style.color = '#3776C0';
		flag = 'f';
	}
	if (flag == 'f')
	{
		return false;
	}
	else
	{
		return true;
	}

}

$(document).ready(function()
{
$('#country').change(function(){	
var prodId = $('#country').val();
//Call Ajax
			$.ajax({
				type: "GET", 
				url: "getStates.php?q="+prodId,
				 success : function (data) {
				 $("#state").html(data);
				}
			});
});

});
function validateletter()
{
	var emailRegEx = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	var str = document.getElementById('nemailfld').value;
	var str1 = document.getElementById('nconfemail').value;
	var flag = 'g';
	document.getElementById('nerrfname').style.display = 'none';
	document.getElementById('nerremail').style.display = 'none';
	document.getElementById('nerrconfemail1').style.display = 'none';

	if (document.getElementById('nfname').value == "")
	{
		document.getElementById('nerrfname').innerHTML = "Please enter first name";
		document.getElementById('nerrfname').style.display = 'block';
		document.getElementById('nerrfname').style.color = '#FF0000';
		flag = 'f';
	}
	if (!str.match(emailRegEx))
	{
		document.getElementById('nerremail').innerHTML = "Please enter email address";
		document.getElementById('nerremail').style.display = 'block';
		document.getElementById('nerremail').style.color = '#FF0000';
		flag = 'f';
	}
	if (!str1.match(emailRegEx))
	{
		document.getElementById('nerrconfemail1').innerHTML = "Please enter valid email address";
		document.getElementById('nerrconfemail1').style.display = 'block';
		document.getElementById('nerrconfemail1').style.color = '#FF0000';
		flag = 'f';
	}
	if (str != str1 && str1.match(emailRegEx))
	{
		document.getElementById('nerrconfemail1').innerHTML = "Email and confirm email do not match. ";
		document.getElementById('nerrconfemail1').style.display = 'block';
		document.getElementById('nerrconfemail1').style.color = '#FF0000';
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

function validate()
{
	var emailRegEx = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	var interest1 = document.getElementById('interest1');
	var interest2 = document.getElementById('interest2');
	var interest3 = document.getElementById('interest3');
	var youare = document.getElementById('youare').value;
	var fname = document.getElementById('fname').value;
	var lname = document.getElementById('lname').value;
	var email = document.getElementById('email').value;
	var cemail = document.getElementById('cemail').value;
	var phone = document.getElementById('phone').value;
	var city = document.getElementById('city').value;
	var country = document.getElementById('country').value;
	var state = document.getElementById('state').value; 
	var member = document.getElementById('member').value;
	var flag = 'g';
	document.getElementById('erinterest').style.display = 'none';
	document.getElementById('erryouare').style.display = 'none';
	document.getElementById('errfname').style.display = 'none';
	document.getElementById('errlname').style.display = 'none';
	document.getElementById('erremail').style.display = 'none';
	document.getElementById('errcemail').style.display = 'none';
	document.getElementById('errphone').style.display = 'none';
	document.getElementById('errcity').style.display = 'none';
	document.getElementById('errcountry').style.display = 'none';
	document.getElementById('errstate').style.display = 'none';
	document.getElementById('errmember').style.display = 'none';
	if (interest1.checked == false && interest2.checked == false && interest3.checked == false)
	{
		document.getElementById('erinterest').innerHTML = "Please select Interest.";
		document.getElementById('erinterest').style.display = 'block';
		document.getElementById('erinterest').style.color = '#FF0000';
		flag = 'f';
	}
	if (youare == "" )
	{
		document.getElementById('erryouare').innerHTML = "Please select your are.";
		document.getElementById('erryouare').style.display = 'block';
		document.getElementById('erryouare').style.color = '#FF0000';
		flag = 'f';
	} 
	if (fname == "" )
	{
		document.getElementById('errfname').innerHTML = "Please enter first name.";
		document.getElementById('errfname').style.display = 'block';
		document.getElementById('errfname').style.color = '#FF0000';
		flag = 'f';
	}
	if (lname == "" )
	{
		document.getElementById('errlname').innerHTML = "Please enter last name.";
		document.getElementById('errlname').style.display = 'block';
		document.getElementById('errlname').style.color = '#FF0000';
		flag = 'f';
	}
	if (!email.match(emailRegEx) )
	{
		document.getElementById('erremail').innerHTML = "Please enter valid email.";
		document.getElementById('erremail').style.display = 'block';
		document.getElementById('erremail').style.color = '#FF0000';
		flag = 'f';
	}
	if (!cemail.match(emailRegEx) )
	{
		document.getElementById('errcemail').innerHTML = "Please enter valid confirm email.";
		document.getElementById('errcemail').style.display = 'block';
		document.getElementById('errcemail').style.color = '#FF0000';
		flag = 'f';
	}
	if (cemail.match(emailRegEx) && email.match(emailRegEx) && email != cemail)
	{
		document.getElementById('errcemail').innerHTML = "email and confirm email do not match.";
		document.getElementById('errcemail').style.display = 'block';
		document.getElementById('errcemail').style.color = '#FF0000';
		flag = 'f';
	}
	
	if (phone == "" )
	{
		document.getElementById('errphone').innerHTML = "Please enter phone number.";
		document.getElementById('errphone').style.display = 'block';
		document.getElementById('errphone').style.color = '#FF0000';
		flag = 'f';
	}
	if (phone != "" )
	{
		var ValidChars = "0123456789.-";
   		var IsNumber=true;
   		var Char;
		for (i = 0; i < phone.length && IsNumber == true; i++) 
      	{ 
     		Char = phone.charAt(i); 
      		if (ValidChars.indexOf(Char) == -1) 
         	{
         		IsNumber = false;
         	}
      	}
   		if (IsNumber == false)
		{
			document.getElementById('errphone').innerHTML = "Please enter phone number in numeric.";
			document.getElementById('errphone').style.display = 'block';
			document.getElementById('errphone').style.color = '#FF0000';
			flag = 'f';
		}	
	}
	
	if (city == "" )
	{
		document.getElementById('errcity').innerHTML = "Please enter city.";
		document.getElementById('errcity').style.display = 'block';
		document.getElementById('errcity').style.color = '#FF0000';
		flag = 'f';
	}
	if (country == "" )
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
	 if (member == "" )
	{
		document.getElementById('errmember').innerHTML = "Please select member.";
		document.getElementById('errmember').style.display = 'block';
		document.getElementById('errmember').style.color = '#FF0000';
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

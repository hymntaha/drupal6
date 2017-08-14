function validateheader()
{
	var emailRegEx = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	var flag = 'f';
	var str = document.getElementById('headeremail').value; 
	document.getElementById('errheaderpwd').style.display = 'none';  
	document.getElementById('errheaderemail').style.display = 'none';
	var str1 = document.getElementById('headerpassword').value;
	if (!str.match(emailRegEx))
	{
		document.getElementById('errheaderemail').innerHTML = "Please enter valid email and password."; 
		document.getElementById('errheaderemail').style.display = 'block';
		flag = 'g';	
	}
	if ( document.getElementById('headerpassword').value == "" )
	{
		document.getElementById('errheaderemail').innerHTML = "Please enter valid email and password.";
		document.getElementById('errheaderemail').style.display = 'block';
		flag = 'g';	
	} 
	if (flag == 'f')
	{
		return true;
	}
	else
	{
		return false;
	} 
}
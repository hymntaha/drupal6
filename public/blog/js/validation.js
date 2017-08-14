
function trim(str) 
{ 
   if (str == null)
   {
	   return;
   }
	return str.replace(/^\s+|\s+$/g,''); 
}

// Check whether the value of an object is empty/null 
function isEmpty(frm, ctrl, msg)
{
	var obj = levelInDep(frm,ctrl);
	with (obj)
	{
		if (value == null || trim(value) == "")
		{
			alertMSG(msg, ctrl);
			return true;
		}
		return false;
	}
}

// Check whether the value of an object is numeric
function isNumeric(frm, ctrl, msg)
{	
	var obj = levelInDep(frm,ctrl);
	with (obj)
	{
		if (isNaN(trim(value)) == true)
		{
			alertMSG(msg, ctrl);
			return false;
		}
		return true;
	}
}

// Check whether the length of characters entered is equal to specified length
function isOfExactLength(frm, ctrl, num, msg)
{	
	var obj = levelInDep(frm,ctrl);
	with (obj)
	{
		if (value.length < num || value.length > num)
		{
			alertMSG(msg, ctrl);
			return false;
		}
		return true;
	}
}

// Check whether the length of characters entered is equal to or greater than specified length.
function isOfMinLength(frm, ctrl, num, msg)
{	
	var obj = levelInDep(frm,ctrl);
	
	with (obj)
	{
		if (value.length < num)
		{
			alertMSG(msg, ctrl);
			return false;
		}
		return true;
	}
}

// Check whether the length of characters entered is equal to or less than specified length
function isOfMaxLength(level,entered, alertbox,num)
{	
	var obj = levelInDep(frm, ctrl);
	with (obj)
	{
		if (value.length > num)
		{
			alertMSG(msg, ctrl);
			return false;
		}
		return true;
	}
}


// Check whether the value of either of the two control blank or not
function isAtleastOneNotEmpty(frm, ctrl1, ctrl2, msg)
{
	var obj1 = levelInDep(frm,ctrl1);
	var obj2 = levelInDep(frm,ctrl2);
	with (obj1)
	{
		if (trim(value) == "" && trim(obj2.value) == "")
		{
			alertMSG(msg, ctrl1);
			return false;
		}
		return true;
	}
}

// Check whether the value of two control equals or not
function isNotEqual(frm, ctrl1, ctrl2, msg)
{
	var obj1 = levelInDep(frm,ctrl1);
	var obj2 = levelInDep(frm,ctrl2);
	with (obj2)
	{
		if (trim(value) != trim(obj1.value))
		{
			alertMSG(msg, ctrl1);
			return true;
		}
		return false;
	}
}


// Check whether an Email address is valid
function isValidEmail(frm,ctrl,msg)
{	
	var obj = levelInDep(frm, ctrl);
	with (obj)
	{
		var regexp =  /^\w(\.?\w)*@\w(\.?[-\w])*\.[a-z]{2,4}$/i;
		if (regexp.test(trim(value)) != true)
		{
			alertMSG(msg, ctrl);
			return false;
		}
		return true;
	}
}


// Check whether the something is selected in the list or not
function isSelected(frm, ctrl, msg) 
{ 
	var obj = levelInDep(frm, ctrl);
	with (obj)
	{
		if (selectedIndex != 0)
		{
			return true;
		}
		else 
		{
			alertMSG(msg, ctrl);
			return false;
		}
	}
} 


// Check whether the something is selected in the multi select list or not
function isMultipleSel(level,entered, alertbox) 
{ 
	var obj = levelInDep(frm, ctrl);
	with (obj)
	{
		if (selectedIndex != -1)
		{
			return true;
		}
		else 
		{
			alertMSG(msg, ctrl);
			return false;
		}
	}
} 

function isValidURL(frm,ctrl, msg)
{ 
	var obj = levelInDep(frm,ctrl);
	with (obj)
	{
 		var regexp =  /(http|ftp|https):\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?$/i;
		if (regexp.test(trim(value)) != true)
		{
			alertMSG(msg, ctrl);
			return false;
		}
		return true;
	}
}

function isChecked(frm, ctrl, msg)
{
	var obj = levelInDep(frm,ctrl);
	with (obj)
	{
 		if (checked == true)
		{
			return true;
		}
		else
		{
			alertMSG(msg, ctrl);
			return false;
		}
	}
}

function toggleCheckbox(element)
{
	if(document.getElementById(element).checked==true)
	{
		document.getElementById(element).checked=false;
	}
	else
	{
		document.getElementById(element).checked=true;
	}
}

function selectRadioButton(element)
{
	document.getElementById(element).checked=true;
}

function setFocus(element)
{
	document.getElementById(element).focus();
}

//Level indepenncy
function levelInDep(le,en)
{
	var res = eval('document.'+ le + '.' + en);
	return res;	
}

function alertMSG(message, ctrl)
{
	if (message == null || trim(message) == "") 
	{
		return;
	}
	alert(message);
	setFocus(ctrl);
}
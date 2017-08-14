
function addMore()
{
	var counter = document.getElementById('counter');
	var counterValue = parseInt(counter.value) + 1;
	var counterIncrement = counterValue + 1;
	counter.value = counterValue;
	//Check Not more than 20
	if(counterValue == 20)
	{
		document.getElementById('moreLink').style.display = 'none';
	}
	
	if((counterValue%2) == 0)
	str1 = '<tr class="oddRow">';
	else
	str1 = '<tr class="evenRow">';
	
	var id = document.getElementById('addMore'+counterValue);
	//alert(id);
	var str = '<table width="100%"  border="0" cellspacing="1" cellpadding="4" align="center"  bgcolor="#D8D8D8">';
  	str += str1;
	str += '<td align="right" class="field_name"  style="padding-right:15px" width="33%">Awarded To:</td>';
    str += '<td align="left"> <select name="awardedto'+counterValue+'" id="awardedto'+counterValue+'" style="width:160px"><option value="">-- Awarded To -- </option> </select> &nbsp;<span  class="field_name">Amount: </span>&nbsp; <input type="text" name="amount'+counterValue+'" size="20" id="amount'+counterValue+'"    disabled /></td>';
    str += '</tr>';
    str += '</table>';
	str += '<span id="addMore'+counterIncrement+'"></span>';
	id.innerHTML = str;
}

function onlynumeric(e)
{
	$(document).ready(function(){
		if(window.event)
			keyPressed = e.keyCode; // IE hack
		else
			keyPressed = e.which; // standard method for other
		
		//alert('key '+keyPressed);
		if(keyPressed<48 || keyPressed>58)
		{
			if(keyPressed != 8 && keyPressed != 0 && keyPressed != 46)
			{
				if(window.event)
					e.returnValue = false;
				else {
					e.preventDefault();
				}
			}
		}
	});
}

 function addMorePortfolio()
{
	var counter = document.getElementById('counter');
	var counterValue = parseInt(counter.value) + 1;
	var counterIncrement = counterValue + 1;
	counter.value = counterValue;
	//Check Not more than 20
	if(counterValue == 5)
	{
		document.getElementById('moreLink').style.display = 'none';
	}
	
	if((counterValue%2) == 0)
	str1 = '<tr class="oddRow">';
	else
	str1 = '<tr class="evenRow">';
	
	var id = document.getElementById('addMore'+counterValue);
	//alert(id);
	var str = '<table width="100%"  border="0" cellspacing="1" cellpadding="4" align="center"  bgcolor="D8D8D8">';
  	str += str1;
	str += '<td  class="field_name" width="40%" align="right" >Upload Image:</td>';
    str += '<td  align="left" valign="top" ><input type="file" name="portfolioImg'+counterValue+'" id="portfolioImg'+counterValue+'"></td>';
    str += '</tr>';
  	str += str1;
	str += '<td class="field_name" width="40%" align="right" valign="top">Text:</td>';
    str += '<td align="left"><textarea name="portfolioDesc'+counterValue+'" id="portfolioDesc'+counterValue+'" rows="7" cols="24"></textarea></td>';
    str += '</tr>';
    str += '</table>';
	str += '<span id="addMore'+counterIncrement+'"></span>';
	id.innerHTML = str;
} 

function addMoreFriend()
{
	var str = '';
	var str1 = '';
	var counter = document.getElementById('counter');
	var counterValue = parseInt(counter.value) + 1;
	var counterIncrement = counterValue + 1;
	counter.value = counterValue;
	if(counterValue == 15)
	{
		document.getElementById('moreLink').style.display = 'none';
	}
	var id = document.getElementById('addMore'+counterValue);

  str +='<table width="518"  border="0" align="center" cellpadding="0" cellspacing="0" >';	
  str +='<tr>';
  str +='<td align="right" valign="top" class="txtalign ">&nbsp;</td>';
  str+='<td class=" padd-le10">&nbsp;</td>';
  str +='</tr>';
  
  str +='<tr>';
  str +='<td align="right" valign="top" class="txtalign ">&nbsp;</td>';
  str+='<td class=" padd-le10">&nbsp;</td>';
  str +='</tr>';
 
  if((counterValue%2) == 0)
	{
		str='<tr bgcolor="#FFFFFF">';
	}
	else
	{
		str=' <tr bgcolor="#F6F6F6">';
	} 
  //str +='<tr>';
  str += '<td align="right" valign="top" class="txtalign " width="25%"><strong>Friend/Family '+ counterValue +':</strong></td>';
  str += '<td class=" padd-le10"><table width="380" border="0" cellpadding="0" cellspacing="1">'
  str += '<tr valign="middle">';
  str += '<td width="64" align="left" nowrap="nowrap">First Name:</td>';
  str += '<td width="75" align="left"><input name="friname[]" type="text" class="inputauto"  id="friname'+counterValue+'" size="13"/></td>';
  str += '<td width="33" align="right">Email:</td>';
  str += '<td width="203" height="28" align="left">';
  str += '<input type="text" name="email1[]"  id="email'+counterValue+'" class="inputauto"  size="26"/></td>';
  str += '</tr>';
  str += '<tr>';
  str += '<td colspan="4" align="left"><input type="radio" name="videoreq'+counterValue+'" id="videoreq'+counterValue+'0" value="NK" align="absmiddle" />NK &nbsp&nbsp;';
  str += '<input type="radio" name="videoreq'+counterValue+'"  id="videoreq'+counterValue+'1" value="SH" align="absmiddle"/>SH &nbsp;&nbsp;';
  str += '<input type="radio" name="videoreq'+counterValue+'" id="videoreq'+counterValue+'2" value="UB" align="absmiddle"/>UB &nbsp;&nbsp;';
  str += '<input type="radio" name="videoreq'+counterValue+'" id="videoreq'+counterValue+'3" value="LB" align="absmiddle"/>LB &nbsp;&nbsp;';
  str += '<input type="radio" name="videoreq'+counterValue+'" id="videoreq'+counterValue+'4" value="HP" align="absmiddle"/>HP &nbsp;&nbsp;';
  str += ' <input type="radio" name="videoreq'+counterValue+'" id="videoreq'+counterValue+'5" value="HWE" align="absmiddle"/>H/W/E &nbsp;&nbsp;';
  str += '<input type="radio" name="videoreq'+counterValue+'" id="videoreq'+counterValue+'6" value="AF" align="absmiddle"/>AF &nbsp;&nbsp;';
  str += '<input type="radio" name="videoreq'+counterValue+'" id="videoreq'+counterValue+'7" value="SR" align="absmiddle"/>SR &nbsp; <br><span style="display:none" id="erremail'+counterValue+'">sdfgsd</span></td>';
  str += ' </tr>';
  str += ' </table></td>';
  str += '</tr>';
  str += '</table>';	
  str +='<span id="addMore'+counterIncrement+'" ></span>';
  id.innerHTML = str; 


}

function addMoreContect()
{
	var str = '';
	var str1 = '';
	var counter = document.getElementById('counter');
	var counterValue = parseInt(counter.value) + 1;
	var counterIncrement = counterValue + 1;
	counter.value = counterValue;
	if(counterValue == 15)
	{
		document.getElementById('moreLink').style.display = 'none';
	}
	var id = document.getElementById('addMore'+counterValue);
	str += str1;
	str += '<table>';
	str +='<tr>';
	str	+='<td>User Name or Email:<input type="text" name="uname[]" class="inputauto"></td>';
	str += '</tr>';
	str +='</table>';
	
	str +='<span id="addMore'+counterIncrement+'" ></span>';
	id.innerHTML = str; 
}


function addMoreprofilePhoto()
{
	var str = '';
	var str1 = '';
	var counter = document.getElementById('counter');
	var counterValue = parseInt(counter.value) + 1;
	var counterIncrement = counterValue + 1;
	counter.value = counterValue;
	if(counterValue == 4)
	{
		document.getElementById('moreLink').style.display = 'none';
	}
	if((counterValue%2) == 0)
	{
		str1='<div class="wid590 brdrbtm litegr">';
	}
	else
	{
		str1=' <div class="wid590 brdrbtm ">';
	}
	var id = document.getElementById('addMore'+counterValue);
	str += str1;
	str +='<div class="txtalign padtb5 width200 formtxt"><strong>Upload Additional images:</strong></div>';
	str +='<div class="width315 padtb5">';
    str +='<input name="Image'+counterValue+'" type="file" size="41" class="input_txtbx" />';        
	str +='</div>';        
	str +='</div>';        
	str +='<div id="addMore'+counterIncrement+'" ></div>';
	id.innerHTML = str;
	
}

function keys(obj) {
  var ret = [];
  for(var key in obj) {
    ret.push(key);
  }
  return ret;
}

function level_select(map){
  var html = Array.prototype.slice.call(
    $.map(keys(map), function(which) {
      return "<option value='" + which + "'>" + which + "</option>";
    })
  );

  $("#level-select").html(
     '<option value="">Select Level</option>' +
     html.join(''));

  $("#level-select").bind('change', function() {
    if(map[this.value]) {
      var
        cityList = map[this.value],
        ix,
        city,
        len = cityList.length,
        dom = [];

      for(ix = 0; ix < len; ix++) {
        city = cityList[ix];
        dom.push( 
          '<option value="' + city + '">' + city + '</option>'
        );
      }

      $("#city-select").each(function(){
        this.innerHTML = '<option value="">Select City</option>' + dom.join('');
        this.selectedIndex = 0;
      });
    }
  });
}


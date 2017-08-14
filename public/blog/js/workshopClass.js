function ValidateInsertDate(){
	if($('#StartDate').val() == ''){
		alert('Please select date.');
		return false;
	}
	if($('#StartTime').val() == ''){
		alert('Please select start time.');
		return false;
	}
	if($('#EndTime').val() == ''){
		alert('Please select end time.');
		return false;
	}

	if($('#StartTime').val() > $('#EndTime').val()){
		alert('End time must be greater than start time.');
		return false;
	}

	var DateValue = $('#StartDate').val();
	var StartTimeValue = $('#StartTime').val();
	var EndTimeValue = $('#EndTime').val();


	$.ajax({
			type: "POST",
			url: 'AjaxProcess.php',
			data: "Date="+DateValue+"&STime="+StartTimeValue+"&ETime="+EndTimeValue,
			async: false,
			success: function(html)
			{ 	
				//so, if data is retrieved, store it in html
				$("#ClassTimeNonRecurring").html(html);
				$('#StartDate').val('');
				$('#StartTime').val('');
				$('#EndTime').val('');
			}
		  });
}


function frmvalidate1(mode)
{
	
	var RegExp = /^(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?a)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?$/;

	var str = '';
	var flag = 'g';
	if (document.getElementById('name').value == "")
	{
		str += "Please enter name.\n";
		flag = 'f';
	}
	if (document.getElementById('workshopid').checked == true)
	{
		if (document.getElementById('area1').checked == false && document.getElementById('area2').checked == false && document.getElementById('area3').checked == false && document.getElementById('area4').checked == false && document.getElementById('area5').checked == false && document.getElementById('area6').checked == false &&document.getElementById('area7').checked == false &&document.getElementById('area8').checked == false &&document.getElementById('area9').checked == false &&document.getElementById('area10').checked == false )
		{
			str += "Please select category.\n";
			flag = 'f';
		}
	}
	if (document.getElementById('teachertrainingid').checked == true)
	{
		if (document.getElementById('level1').checked == false && document.getElementById('level2').checked == false && document.getElementById('level3').checked == false && document.getElementById('level4').checked == false  )
		{
			str += "Please select level.\n";
			flag = 'f';
		}
	}
	
	if (document.getElementById('country').value == "")
	{
		str += "Please select country.\n";
		flag = 'f';
	}
	if (document.getElementById('states').value == "")
	{
		str += "Please select state.\n";
		flag = 'f';
	}
	if (document.getElementById('city').value == "")
	{
		str += "Please enter city.\n";
		flag = 'f';
	}
	if (document.getElementById('teacher').value == "")
	{
		str += "Please select teacher.\n";
		flag = 'f';
	}
	if (document.getElementById('url').value != "" && !RegExp.test(document.getElementById('url').value))
	{
		str += "Please enter valid url.\n";
		flag = 'f';
	}
	
	if (mode == "Add")
	{
	
		if (document.getElementById('selectnonrecuring').checked == false && document.getElementById('selectrecuring').checked == false && document.getElementById('selectextended').checked == false)
		{
				str += "Please select Time.\n";
				flag = 'f';
		}
		if ( document.getElementById('selectextended').checked == true )
		{
			if($('#startdate').val() == '')
			{
				str += "Please select start date.\n";
				flag = 'f';
			}
			if($('#enddate').val() == '')
			{
				str += "Please select end date.\n";
				flag = 'f';
			}
		}
		else if ( document.getElementById('selectnonrecuring').checked == true )
		{
			if($('#Date1').val() == ''){
				str += "Please select date1\n";
				flag = 'f';
			}
			if ($('#Date1').val() != '' && (  $('#startminutes1').val()  == '' || $('#starthours1').val()  == '' ||  $('#endminutes1').val()  == '' || $('#endhours1').val()  == '' ) )
			{
				str += "Please select hours and minutes for date1\n";
				flag = 'f';
			}

			if ($('#Date2').val() != '' && (  $('#startminutes2').val()  == '' || $('#starthours2').val()  == '' ||  $('#endminutes2').val()  == '' || $('#endhours2').val()  == '' ) )
			{
				str += "Please select hours and minutes for date2\n";
				flag = 'f';
			}
			if ($('#Date3').val() != '' && (  $('#startminutes3').val()  == '' || $('#starthours3').val()  == '' ||  $('#endminutes3').val()  == '' || $('#endhours3').val()  == '' ) )
			{
				str += "Please select hours and minutes for date3\n";
				flag = 'f';
			}

			if ($('#Date4').val() != '' && (  $('#startminutes4').val()  == '' || $('#starthours4').val()  == '' ||  $('#endminutes4').val()  == '' || $('#endhours4').val()  == '' ) )
			{
				str += "Please select hours and minutes for date4\n";
				flag = 'f';
			}
			if ($('#Date5').val() != '' && (  $('#startminutes5').val()  == '' || $('#starthours5').val()  == '' ||  $('#endminutes5').val()  == '' || $('#endhours5').val()  == '' ) )
			{
				str += "Please select hours and minutes for date5\n";
				flag = 'f';
			}
			if ($('#Date6').val() != '' && (  $('#startminutes6').val()  == '' || $('#starthours6').val()  == '' ||  $('#endminutes6').val()  == '' || $('#endhours6').val()  == '' ) )
			{
				str += "Please select hours and minutes for date6\n";
				flag = 'f';
			}
			
			if ($('#Date7').val() != '' && (  $('#startminutes7').val()  == '' || $('#starthours7').val()  == '' ||  $('#endminutes7').val()  == '' || $('#endhours7').val()  == '' ) )
			{
				str += "Please select hours and minutes for date7\n";
				flag = 'f';
			}
		}
		else if ( document.getElementById('selectrecuring').checked == true )
		{
			if (document.getElementById('selweak1').checked == false && document.getElementById('selweak2').checked == false &&document.getElementById('selweak3').checked == false &&document.getElementById('selweak4').checked == false &&document.getElementById('selweak5').checked == false &&document.getElementById('selweak6').checked == false && document.getElementById('selweak7').checked == false )
			{
				str += "Please select atleast one schedule\n";
				flag = 'f';
			}
			if ( document.getElementById('selweak1').checked == true && ( $('#selhour1').val()  == '' || $('#selmin1').val()  == '' ||  $('#selhour11').val()  == '' || $('#selmin11').val()  == '' ) )
			{
				str += "Please select hours and minutes for monday schedule.\n";
				flag = 'f';
			}
			if ( document.getElementById('selweak2').checked == true && ( $('#selhour2').val()  == '' || $('#selmin2').val()  == '' ||  $('#selhour22').val()  == '' || $('#selmin22').val()  == '' ) )
			{
				str += "Please select hours and minutes for tuesday schedule.\n";
				flag = 'f';
			}
			if ( document.getElementById('selweak3').checked == true && ( $('#selhour3').val()  == '' || $('#selmin3').val()  == '' ||  $('#selhour33').val()  == '' || $('#selmin33').val()  == '' ) )
			{
				str += "Please select hours and minutes for wednesday schedule.\n";
				flag = 'f';
			}
			if ( document.getElementById('selweak4').checked == true && ( $('#selhour4').val()  == '' || $('#selmin4').val()  == '' ||  $('#selhour44').val()  == '' || $('#selmin44').val()  == '' ) )
			{
				str += "Please select hours and minutes for thusday schedule.\n";
				flag = 'f';
			}
			if ( document.getElementById('selweak5').checked == true && ( $('#selhour5').val()  == '' || $('#selmin5').val()  == '' ||  $('#selhour55').val()  == '' || $('#selmin55').val()  == '' ) )
			{
				str += "Please select hours and minutes for friday schedule.\n";
				flag = 'f';
			}
			if ( document.getElementById('selweak6').checked == true && ( $('#selhour6').val()  == '' || $('#selmin6').val()  == '' ||  $('#selhour66').val()  == '' || $('#selmin66').val()  == '' ) )
			{
				str += "Please select hours and minutes for saturday schedule.\n";
				flag = 'f';
			}
			if ( document.getElementById('selweak7').checked == true && ( $('#selhour7').val()  == '' || $('#selmin7').val()  == '' ||  $('#selhour77').val()  == '' || $('#selmin77').val()  == '' ) )
			{
				str += "Please select hours and minutes for sunday schedule.\n";
				flag = 'f';
			} 
		
		}
	}
	if (flag == 'f')
	{
		alert(str);
		return false;
	}
	if (flag == 'g')
	{
		return true;
	}

}


function frmvalidate11()
{
	
	var str = '';
	var flag = 'g';
	if (document.getElementById('selectnonrecuring').checked == false && document.getElementById('selectrecuring').checked == false && document.getElementById('selectextended').checked == false)
	{
			str += "Please select Time..\n";
			flag = 'f';
	}
	if ( document.getElementById('selectextended').checked == true )
	{
		if($('#startdate').val() == '')
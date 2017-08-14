// Load the options in variables (God damn Internet Explorer)

	var states_list_1,cities_list_1,states_list_2,cities_list_2;

	$(document).ready(function(){
		
		states_list_1 = $("select#statesworkshop").html();
		cities_list_1 = $("select#cityworkshop").html();
		states_list_2 = $("select#statesclass").html();
		cities_list_2 = $("select#city2class").html();
		
	});

// ---------------------------------

// Workshop search criteria control

	$(document).ready(function(){

		refine_states_combo('');
		$("select#countryworkshop").change(function(){
			refine_states_combo($(this).val());
		});
		
		$("select#statesworkshop").change(function(){
			refine_cities_combo($(this).val());
		});

	});

	function refine_states_combo(country_id){

		$("select#statesworkshop").html(states_list_1);
		$("select#statesworkshop").children('option[country_id != '+country_id+']').not(':first-child').remove();
		refine_cities_combo(0);

		if(country_id == ''){
			$("span#states_combo").css('display','none');
		}
		else{
			$("span#states_combo").css('display','inline');
		}
	}

	function refine_cities_combo(state_id){
		$("select#cityworkshop").html(cities_list_1);
		$("select#cityworkshop").children('option[state_id != '+state_id+']').not(':first-child').remove();

		if(state_id == '' || state_id == 0){
			$("span#cities_combo").css('display','none');
		}
		else{
			$("span#cities_combo").css('display','inline');
		}
	}

// --------------------------------------------------------------

// Class search criteria control

	$(document).ready(function(){

		refine_states_combo_2('');
		$("select#countryclass").change(function(){
			refine_states_combo_2($(this).val());
		});
		
		$("select#statesclass").change(function(){
			refine_cities_combo_2($(this).val());
		});

	});

	function refine_states_combo_2(country_id){
		$("select#statesclass").html(states_list_2);
		$("select#statesclass").children('option[country_id != '+country_id+']').not(':first-child').remove();
		refine_cities_combo_2(0);

		if(country_id == ''){
			$("span#states_combo_2").css('display','none');
		}
		else{
			$("span#states_combo_2").css('display','inline');
		}
	}

	function refine_cities_combo_2(state_id){
		$("select#city2class").html(cities_list_2);
		$("select#city2class").children('option[state_id != '+state_id+']').not(':first-child').remove();

		if(state_id == '' || state_id == 0){
			$("span#cities_combo_2").css('display','none');
		}
		else{
			$("span#cities_combo_2").css('display','inline');
		}
	}

// --------------------------------------------------------------


$(document).ready(function()
{
$('#country').change(function(){
var prodId = $('#country').val();
//Call Ajax
			$.ajax({
				type: "GET", 
				url: "getStates1.php?q="+prodId,
				 success : function (data) {
				 $("#states").html(data);
				}
			});
});

});
$(document).ready(function()
{
$('#country1').change(function(){	
var prodId = $('#country1').val();
//Call Ajax
			$.ajax({
				type: "GET", 
				url: "getStates2.php?q="+prodId,
				 success : function (data) {
				 $("#states1").html(data);
				}
			});
});

});
$(document).ready(function()
{
$('#country2').change(function(){	
var prodId = $('#country2').val();
//Call Ajax
			$.ajax({
				type: "GET", 
				url: "getStates3.php?q="+prodId,
				 success : function (data) {
				 $("#states2").html(data);
				}
			});
});

});

$(document).ready(function()
{
$('#states').change(function(){	
var prodId = $('#states').val();
//Call Ajax
			$.ajax({
				type: "GET", 
				url: "getCity.php?q="+prodId,
				 success : function (data) {
				 $("#city").html(data);
				}
			});
});

});

$(document).ready(function()
{
$('#states1').change(function(){	
var prodId = $('#states1').val();
//Call Ajax
			$.ajax({
				type: "GET", 
				url: "getCity1.php?q="+prodId,
				 success : function (data) {
				 $("#city1").html(data);
				}
			});
});

});

$(document).ready(function()
{
$('#states2').change(function(){	
var prodId = $('#states2').val();
//Call Ajax
			$.ajax({
				type: "GET", 
				url: "getCity2.php?q="+prodId,
				 success : function (data) {
				 $("#city2").html(data);
				}
			});
});

});





function validate()
{
	var emailRegEx = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	var str = document.getElementById('emailfld').value;
	var str1 = document.getElementById('confemail').value;
	var flag = 'g';
	document.getElementById('errfname').style.display = 'none';
	document.getElementById('erremail').style.display = 'none';
	document.getElementById('errconfemail1').style.display = 'none';

	if (document.getElementById('fname').value == "")
	{
		document.getElementById('errfname').innerHTML = "Please enter first name";
		document.getElementById('errfname').style.display = 'block';
		document.getElementById('errfname').style.color = '#FF0000';
		flag = 'f';
	}
	if (!str.match(emailRegEx))
	{
		document.getElementById('erremail').innerHTML = "Please enter email address";
		document.getElementById('erremail').style.display = 'block';
		document.getElementById('erremail').style.color = '#FF0000';
		flag = 'f';
	}
	if (!str1.match(emailRegEx))
	{
		document.getElementById('errconfemail1').innerHTML = "Please enter valid email address";
		document.getElementById('errconfemail1').style.display = 'block';
		document.getElementById('errconfemail1').style.color = '#FF0000';
		flag = 'f';
	}
	if (str != str1 && str1.match(emailRegEx))
	{
		document.getElementById('errconfemail1').innerHTML = "Email and confirm email do not match. ";
		document.getElementById('errconfemail1').style.display = 'block';
		document.getElementById('errconfemail1').style.color = '#FF0000';
		flag = 'f';
	}
	if ($.trim($("input[@name=verificationCode]").val()) == '') {

		document.getElementById('errvarify').innerHTML = "Please enter the verification code. ";
		document.getElementById('errvarify').style.display = 'block';
		document.getElementById('errvarify').style.color = '#FF0000';
		flag = 'f';

	}else{
		var thecode =$("input[@name=verificationCode]").val();
		
		$.ajax({
		url: "gateway/captchaCheck.php",
		data: "code="+thecode,
		//beforeSend: function(){$("#profile_html_joining_reason").show("slow");}, //show loading just when link is clicked
		//complete: function(){ $("#profile_html_joining_reason").hide("slow");}, 
		async: false,
		success: function(html){
			
			if(html == "0"){
			//errStr += '* Please enter correct verification code.\n';
			document.getElementById('errvarify').innerHTML = "Please enter correct verification code. ";
			document.getElementById('errvarify').style.display = 'block';
			document.getElementById('errvarify').style.color = '#FF0000';
			flag = 'f';
			
			}else{
			//document.getElementById('errvarify').innerHTML = "Please enter correct verification code. ";
			document.getElementById('errvarify').style.display = 'none';
			//document.getElementById('errvarify').style.color = '#FF0000';
				//flag = '';
			}
		}
		
		});
		
		
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

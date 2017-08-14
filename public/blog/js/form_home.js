// JavaScript Document form for home page.

   
    var myFunction = function(serialized){
        alert(serialized);
    }
    
	jQuery(document).ready(function () {
	   $("#myform").abform({
	       attributes :'id="myform" action="https://yogatuneup.infusionsoft.com/AddForms/processFormSecure.jsp" method="POST"',
           //pluggable : false,
//           serialized : true,
//           multipart : true,
//           clickonce : true,
           convert : '{Contact0FirstName|text|style="border-style:solid;border-width:1px;width:250px;color:#000000;font-size:18px;height:25px;line-height:normal;padding:5px 8px 0 6px;vertical-align:top; margin-bottom:10px; float:right;"}{Contact0Email|text|style="border-style:solid;border-width:1px;width:250px;color:#000000;font-size:18px;height:25px;line-height:normal;padding:5px 8px 0 6px;vertical-align:top; margin-bottom:10px; float:right;"}{mybutton|button|class="absubmit"}'
	   });    
    });
        
  

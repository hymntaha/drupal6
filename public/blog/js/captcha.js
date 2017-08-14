function Inint_AJAX() {
  try { return new ActiveXObject("Msxml2.XMLHTTP");  } catch(e) {} //IE
  try { return new ActiveXObject("Microsoft.XMLHTTP"); } catch(e) {} //IE
  try { return new XMLHttpRequest();          } catch(e) {} //Native Javascript
  alert("XMLHttpRequest not supported");
  return null;
};
 
//==================================================
function new_captcha()
{
var c_currentTime = new Date();
var c_miliseconds = c_currentTime.getTime();
document.getElementById('captcha').src = 'gateway/captchaImage.php?x='+ c_miliseconds;
}
//===============================check captcha================================
/*function checkcode(src) {
var req = Inint_AJAX();
     req.onreadystatechange = function () { 
          if (req.readyState==4) {
               if (req.status==200) {
			   		
			   		if(req.responseText=="0"){
							
								alert("Security code does not match");
								document.getElementById('security_code').value='';
								document.getElementById('security_code').focus();
								return false
					} else { return true;}  
               } 
          }
     };
     req.open("GET", "checkcode.php?val="+src);
     req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;charset=tis-620"); // set Header
     req.send(null); //ส่งค่า
}*/
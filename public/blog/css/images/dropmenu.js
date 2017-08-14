// JavaScript Document
<!--
/***********************************************
* AnyLink Drop Down Menu- © Dynamic Drive (www.dynamicdrive.com)
* This notice MUST stay intact for legal use
* Visit http://www.dynamicdrive.com/ for full source code
***********************************************/

//Contents for menu 1
var menu1=new Array()
menu1[0]='<a href="http://beta.yogatuneup.com/yoga-at-home-fitness">"At Home" Program</a>'
menu1[1]='<a href="http://beta.yogatuneup.com/products/self-massage-therapy-balls">Therapy Ball Programs</a>'
menu1[2]='<a href="http://beta.yogatuneup.com/products/quickfix-yoga-dvds">Quickfix DVDs</a>'
menu1[3]='<a href="http://beta.yogatuneup.com/products/online-quickfix-videos">Quickfix Online Videos</a>'
menu1[4]='<a href="http://beta.yogatuneup.com/beginners-yoga"  class="lm">YTU for Beginners</a>'

//Contents for menu 2
var menu2=new Array()
menu2[0]='<a href="http://beta.yogatuneup.com/neck-pain-exercises">Neck & Upper Back </a>'
menu2[1]='<a href="http://beta.yogatuneup.com/shoulder-pain-exercises">Shoulders </a>'
menu2[2]='<a href="http://beta.yogatuneup.com/lower-back-pain-exercises">Low Back </a>'
menu2[3]='<a href="http://beta.yogatuneup.com/hip-pain-exercises">Hips</a>'
menu2[4]='<a href="http://beta.yogatuneup.com/tight-hamstrings">Hamstrings </a>'
menu2[5]='<a href="http://beta.yogatuneup.com/hand-exercises">Hands & Wrist</a>'
menu2[6]='<a href="http://beta.yogatuneup.com/ankle-foot-exercises">Feet & Ankles</a>'
menu2[7]='<a href="http://beta.yogatuneup.com/meditation-stress-relief" class="lm">Stress Relief</a>'

//Contents for menu 3
var menu3=new Array()
menu3[0]='<a href="http://beta.yogatuneup.com/quickfix-yoga-videos">Online Quickfix Videos</a>'
menu3[1]='<a href="http://beta.yogatuneup.com/weekly-yoga-pose" class="lm">Pose of the Week </a>'

//Contents for menu 4
var menu4=new Array()
menu4[0]='<a href="http://beta.yogatuneup.com/blog/">Blog</a>'
menu4[1]='<a href="http://beta.yogatuneup.com/newsletter-registration">Newsletter Sign Up </a>'
menu4[2]='<a href="http://beta.yogatuneup.com/community/temp">Forum</a>'
menu4[3]='<a href="http://beta.yogatuneup.com/tellafriend">Tell a Friend</a>'
menu4[4]='<a href="http://beta.yogatuneup.com/ambassador-program" class="lm">Ambassador Program</a>'

//Contents for menu 5
var menu5=new Array()
menu5[0]='<a href="http://beta.yogatuneup.com/yoga-workshops-classes">Class/Workshop Search</a>'
menu5[1]='<a href="http://beta.yogatuneup.com/yoga-teacher-training">Teacher Trainings </a>'
menu5[2]='<a href="http://beta.yogatuneup.com/yoga-teacher-training-schedule/Level%201">Jill’s Schedule</a>'
menu5[3]='<a href="http://beta.yogatuneup.com/meet-all-teachers.php" class="lm">Find a Teacher</a>'

//Contents for menu 6
var menu6=new Array()
menu6[0]='<a href="http://beta.yogatuneup.com/about-yogatuneup">About Yoga Tune Up®</a>'
menu6[1]='<a href="http://beta.yogatuneup.com/teachers/jill-miller">About Jill Miller </a>'
menu6[2]='<a href="http://beta.yogatuneup.com/faqs" class="lm"> FAQs</a>'
/*menu1[3]='<a href="#">Uber-Rad Years</a>'
menu1[4]='<a href="#">The Whole Shabang</a>'*/


//Contents for menu 6, and so on

		
var menuwidth='165px' //default menu width
var menubgcolor='#'  //menu bgcolor
var disappeardelay=250  //menu disappear speed onMouseout (in miliseconds)
var hidemenu_onclick="yes" //hide menu when user clicks within menu?

/////No further editting needed

var ie4=document.all
var ns6=document.getElementById&&!document.all

if (ie4||ns6)
document.write('<div id="dropmenudiv" style="visibility:hidden;width:'+menuwidth+';background-color:'+menubgcolor+'" onMouseover="clearhidemenu()" onMouseout="dynamichide(event)"></div>')

function getposOffset(what, offsettype){
var totaloffset=(offsettype=="left")? what.offsetLeft+21 : what.offsetTop;
var parentEl=what.offsetParent;
while (parentEl!=null){
totaloffset=(offsettype=="left")? totaloffset+parentEl.offsetLeft : totaloffset+parentEl.offsetTop;
parentEl=parentEl.offsetParent;
}
return totaloffset;
}


function showhide(obj, e, visible, hidden, menuwidth){
if (ie4||ns6)
dropmenuobj.style.left=dropmenuobj.style.top="-500px"
if (menuwidth!=""){
dropmenuobj.widthobj=dropmenuobj.style
dropmenuobj.widthobj.width=menuwidth
}
if (e.type=="click" && obj.visibility==hidden || e.type=="mouseover")
obj.visibility=visible
else if (e.type=="click")
obj.visibility=hidden
}

function iecompattest(){
return (document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body
}

function clearbrowseredge(obj, whichedge){
var edgeoffset=0
if (whichedge=="rightedge"){
var windowedge=ie4 && !window.opera? iecompattest().scrollLeft+iecompattest().clientWidth-15 : window.pageXOffset+window.innerWidth-15
dropmenuobj.contentmeasure=dropmenuobj.offsetWidth
if (windowedge-dropmenuobj.x < dropmenuobj.contentmeasure)
edgeoffset=dropmenuobj.contentmeasure-obj.offsetWidth
}
else{
var topedge=ie4 && !window.opera? iecompattest().scrollTop : window.pageYOffset
var windowedge=ie4 && !window.opera? iecompattest().scrollTop+iecompattest().clientHeight-15 : window.pageYOffset+window.innerHeight-18
dropmenuobj.contentmeasure=dropmenuobj.offsetHeight
if (windowedge-dropmenuobj.y < dropmenuobj.contentmeasure){ //move up?
edgeoffset=dropmenuobj.contentmeasure+obj.offsetHeight
if ((dropmenuobj.y-topedge)<dropmenuobj.contentmeasure) //up no good either?
edgeoffset=dropmenuobj.y+obj.offsetHeight-topedge
}
}
return edgeoffset
}

function populatemenu(what){
if (ie4||ns6)
dropmenuobj.innerHTML=what.join("")
}


function dropdownmenu(obj, e, menucontents, menuwidth){
if (window.event) event.cancelBubble=true
else if (e.stopPropagation) e.stopPropagation()
clearhidemenu()
dropmenuobj=document.getElementById? document.getElementById("dropmenudiv") : dropmenudiv
populatemenu(menucontents)

if (ie4||ns6){
showhide(dropmenuobj.style, e, "visible", "hidden", menuwidth)

dropmenuobj.x=getposOffset(obj, "left")
dropmenuobj.y=getposOffset(obj, "top")
dropmenuobj.style.left=dropmenuobj.x-clearbrowseredge(obj, "rightedge")+"px"
dropmenuobj.style.top=dropmenuobj.y-clearbrowseredge(obj, "bottomedge")+obj.offsetHeight+"px"
}

return clickreturnvalue()
}

function clickreturnvalue(){
if (ie4||ns6) return false
else return true
}

function contains_ns6(a, b) {
while (b.parentNode)
if ((b = b.parentNode) == a)
return true;
return false;
}

function dynamichide(e){
if (ie4&&!dropmenuobj.contains(e.toElement))
delayhidemenu()
else if (ns6&&e.currentTarget!= e.relatedTarget&& !contains_ns6(e.currentTarget, e.relatedTarget))
delayhidemenu()
}

function hidemenu(e){
if (typeof dropmenuobj!="undefined"){
if (ie4||ns6)
dropmenuobj.style.visibility="hidden"
}
}

function delayhidemenu(){
if (ie4||ns6)
delayhide=setTimeout("hidemenu()",disappeardelay)
}

function clearhidemenu(){
if (typeof delayhide!="undefined")
clearTimeout(delayhide)
}

if (hidemenu_onclick=="yes")
document.onclick=hidemenu

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
//-->
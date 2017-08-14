<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Yogatuneup - Live Long. Stay Healthy. Enjoy Life.</title>

		<link href="<?=HTTP_BASE_URLS?>css/main.css" rel="stylesheet" type="text/css" />
		<link href="<?=HTTP_BASE_URLS?>css/mod_yoo_carousel.css" rel="stylesheet" type="text/css" />
	<link href="<?=HTTP_BASE_URLS?>css/pages.css" rel="stylesheet" type="text/css" />
		<script src="js/jquery-1.2.6.min.js"></script>
		<script src="js/all_class.js"></script>
<link href="<?=HTTP_BASE_URL?>images/style_div.css" rel="stylesheet" type="text/css" />
		
		
		<script>
			function pointto(){
				window.scrollTo(0, '<?php echo $scroll; ?>');
			}
		</script>
	</head>
<body onload="pointto();">

<div align="center">

<div id="container">
	<?php include("header.inc.php") ; ?>	


<div id="mainflash">
<br /><br />

<div id="main-contner">

  <div class="body-sect">
    <!--<div class="banner-sec">
      <div class="banner-img"><img src="images/class-workshop-head.gif" width="337" height="38" /></div>
    </div>-->
    <div class="wid987" style="margin-top:10px;">
      <div class="width657_m">
	  <div class="width657"><img src="images/corner-left.gif" width="15" height="15" align="left" /></div>
	  <div class="width657_center">
	    <div class="width600_r">
		<div class="wid590 heading18 brdrbtm padbtm_10" style="width:610px;">
			<?php if($headText != "" && $headText != "YTU Upcoming Class") { print $headText."s"; } elseif ( $headText != "" && $headText == "YTU Upcoming Class") { print $headText."es";} else {echo "YTU Upcoming Classes and Workshops"; } ?>
			<div class="floatright" style="padding-top:6px;"><span class="floatright" style="padding-top:6px;"><a href='teachers/<?=$teacher_info[0]['ytuURL'];?>'><img src="images/back-btn.gif" alt=""  border="0"/></a></span></div>
		</div>
		<? 
			if (trim($messageString) == "")
			{
				
				//#################RECURRING START HERE 
				
				foreach($results as $key => $val)
		   		{
				$rsschedule = getschedule($val['ClassID']);
				
				//if($rsschedule[0]['ScheduleType'] == "recuring" ){
		?>
		<div id="video-list">
			<div class="vidpic" style="width:150px;">
				<?php
					if($val['Image'] != ""){
						$arrImg = explode(".",$val['Image']); $img = $arrImg[0]."_thumb.".$arrImg[1]; 
						?>
						<img src="upload/workshop/thumb_small/<?=$img;?>"  border="0" class="vidbrdr" />
						<?php
					}
					else{
						?>
						<img src="images/logo.gif"  height="100" border="0" class="vidbrdr" style="margin-right:15px;" />
						<?php
					}
				?>
			</div>
			<div class="vidtext classes_workshop" style="width:350px">
				<ul>
					<li style="list-style-image:none;">
						<span>
							<a href='#' class='class_details'>
								<strong style="font-size:14px;"><?=ucfirst(html_entity_decode($val['Name'])); ?></strong>
							</a>
						</span>
					</li>
					<?php
						$clsid = base64_encode($val['ClassID']);
						if(trim($_POST['category']))
							print("<li>".ucfirst($_POST['category'])."</li>");

						if(trim(strip_tags($val['Description'])))
							print('<li class="hidden description">'.html_entity_decode($val['Description']).'</li>');
						$encid = base64_encode($val['UserID']);
					?>
					<li class='instructor hidden' style="list-style-image:none;">
						<span style='font-size: 11px!important;'><strong>Instructor:</strong></span>
						<a href="teachers/<?=$val['ytuURL']?>"><?=$val['FName']; ?>&nbsp;<?=$val['LName']; ?></a>
					</li>
					<li class='category' style="list-style-image:none;">
						<span style='font-size: 11px!important;'><strong>Category:</strong></span>
						<span><?=ucfirst($val['Type']);?></span>
					</li>
					<li class='location' style="list-style-image:none;">
						<strong>Location:</strong>
						<span class='city'><?=$val['City']?></span>, 
						<span class='state'><?=getstate($val['StateID'])?></span>, 
						<span class='country'><?=getcountry($val['CountryID'])?></span>
					</li>
					<li  class='schedules' style="list-style-image:none;">
						<strong style='float: left;'>Schedule:</strong>
						<span class='sd' style='float: left; margin: 0; margin-left: 5px; font-size: 12px;'>
						<?php
							$rsschedule = getschedule($val['ClassID']);
							if($rsschedule[0]['ScheduleType'] == "extended")
								if($val['Type'] != "Class"){
									$temp_start_time = strtotime($rsschedule[0]['Date']);
									$temp_end_time = strtotime($rsschedule[0]['EndDate']);
									if(date('F Y',$temp_start_time) == date('F Y',$temp_end_time)){
										$start_date = date('jS',$temp_start_time);
										$end_date = date('jS',$temp_end_time);
										print(date('F',$temp_end_time)." $start_date - $end_date, ".date('Y',$temp_end_time));
									}
									else{
										$start_date = date('F jS',$temp_start_time);
										$end_date = date('F jS',$temp_end_time);
										print("$start_date - $end_date, ".date('Y',$temp_end_time));
									}
								}
								else
									print(date('l, F d, Y',strtotime($rsschedule[0]['Date']))." - ".date('l, F d, Y',strtotime($rsschedule[0]['EndDate'])).'<br/>');
						
							$ctn = 0;
							foreach ($rsschedule as $key1 => $val1 ){
								$ctn++;
								$listtimestart = explode(":",$val1['StartTime']);
								$listtimeend = explode(":",$val1['EndTime']);	

								if($val1['ScheduleType'] == "nonrecuring" )
									print(date('l, F d',strtotime($val1['Date']))."<br/>");
								elseif($val1['ScheduleType'] == "recuring")
									print($val1['Day']."s, ".$listtimestart[0].":".$listtimestart[1]." ".$val1['StartAMPM']." - ".$listtimeend[0].":".$listtimeend[1]." ".$val1['EndAMPM'].'<br/>');
							}
						?>
						</span>
					</li>
					<?php
						($val['Url'] != '' and strpos($val['Url'],'http://') === false)? 
							$val['Url'] = 'http://'.$val['Url']:
							null;
					?>
					<li class=' hidden venue' style="list-style-image:none;">
						<span><strong>Venue:</strong></span>
						<span class='venue_name'><?=$val['VName']?></span><br/>
						<span class='venue_location'><?=$val['Location']?></span><br/>
						<a class='venue_link' href="<?=$val['Url']?>" target="_blank"><?=$val['Url']?></a><br/>
						<span><strong>Phone: </strong></span>
						<span class='venue_phone'><?=$val['Phone']?></span><br/>
					</li>
				</ul>
		  <div class="floatleft vidtext">

		      <? if ($_GET['from'] != "public" ) { ?>
			  <div class="floatleft pad_RIGHT10 pad_top"><a href="edit-teacher-scheduleTest.php?cid=<?=$clsid;?>"><img src="images/update-schedule.gif" border="0" /></a> &nbsp; <a href="teacher-classes.php?cid=<?=$clsid;?>&do=delclass" onclick="return confirm('Are you sure you want to delete this class?')"><img src="images/delete-class.gif" border="0"></a></div>
				<? } ?>	 
		      </div>
		    </div>
		  </div> 
		<? 	//}
			//else
			//{
				// $db->update('classes', array("Status"=>'0'), array("ClassID"=>$val['ClassID']));
			//}
			  
			  } 

		?>
			
			<div class="paging" align="right"><strong>
		 <?  	$pag1 = str_replace('<div class="pagination">',"",$pagingStr);
				$pag2 = str_replace("</div>","",$pag1); ?>
				<div class="pagination11"><? echo $pag2; ?></div>
		
		 </strong>
		 </div>	
		 		
			<? }
			else
			{ ?>
				<div id="video-list"><?=$messageString?></div>
		<? }?>
	    
		 <!-- <div class="paging" align="right"><strong><a href="#" class="page">Previous</a></strong><a href="#" class="page-on">1</a><a href="#" class="page">2</a><a href="#" class="page">3</a><a href="#" class="page">4</a><a href="#" class="page">5</a><a href="#" class="page">6</a><a href="#" class="page">7</a><a href="#" class="page">8</a><a href="#" class="page">9</a><strong><a href="#" class="page">Next</a></strong></div> -->
	    </div>
	  </div>
	  
	  <div class="width657_btm"><img src="images/corner-left-btm.gif" width="15" height="15" align="left" /></div>
	  </div>
      <div class="width307_m">
        <div class="width307_m">
          <div class="width307"><img src="images/corner-01.gif" width="14" height="14" align="left" /></div>
        </div>
        <div class="width307_center">
          <div class="width250">
					<? if ($_GET['userid'] != "" && $_GET['from'] == "dash") { ?>
					<div class="heading18 width250 padbtm_10">Quick Links </div>
					
					<div class="width250 padbtm_10"><a href="teacher-profile.php?userid=<?=base64_encode($_SESSION['UserID']);?>&from=dash"><img src="images/view-webpage.gif" width="243" height="33" border="0" /></a></div>
					<div class="width250 padbtm_10"><a href="edit-teacher-scheduleTest.php"><img src="images/add-class-1.gif"  border="0" /></a></div>
					<div class="width250 padbtm_10"><a href="teacher-classes.php"><img src="images/my-classes-1.gif" width="243" height="33" border="0" /></a></div>
					<div class="width250 padbtm_10"><a href="ytu-groups.php"><img src="images/my-groups-icon.gif"  border="0" width="243" height="33"/></a></div>
					<div class="width250 padbtm_10"><a href="teacher-community.php"><img src="images/teacher-forum-1.gif" width="243" height="33" border="0" /></a></div>
					<div class="width250 padbtm_10"><a href="teacher-teleconferences.php"><img src="images/tele-button.gif" width="243" height="33" border="0" /></a></div>
					<div class="width250 padbtm_10"><a href="ytu-dashboard.php"><img src="images/my-dashboard-button.gif" width="243" height="33" border="0" /></a></div>
					<? } else if ($_GET['userid'] == "" && $_GET['from'] != "public") { ?>
					<div class="heading18 width250 padbtm_10">Quick Links </div>
					
					<div class="width250 padbtm_10"><a href="teacher-profile.php?userid=<?=base64_encode($_SESSION['UserID']);?>&from=dash"><img src="images/view-webpage.gif" width="243" height="33" border="0" /></a></div>
					<div class="width250 padbtm_10"><a href="edit-teacher-scheduleTest.php"><img src="images/add-class-1.gif"  border="0" /></a></div>
					<div class="width250 padbtm_10"><a href="teacher-classes.php"><img src="images/my-classes-1.gif" width="243" height="33" border="0" /></a></div>
					<div class="width250 padbtm_10"><a href="ytu-groups.php"><img src="images/my-groups-icon.gif"  border="0" width="243" height="33"/></a></div>
					<div class="width250 padbtm_10"><a href="teacher-community.php"><img src="images/teacher-forum-1.gif" width="243" height="33" border="0" /></a></div>
					<div class="width250 padbtm_10"><a href="teacher-teleconferences.php"><img src="images/tele-button.gif" width="243" height="33" border="0" /></a></div>
					<div class="width250 padbtm_10"><a href="ytu-dashboard.php"><img src="images/my-dashboard-button.gif" width="243" height="33" border="0" /></a></div>
					<? } else { ?>
			<form name="serchteach" method="request" action="teacher-profile.php">
			<div class="heading18 width250">Instructor Search</div>
			<!--<div class="heading18 width250 pad_top10">Search for Yoga Tune Up<sup class="blue14"><span style="font-size:12px;">&reg;</span></sup> Instructors</div>-->
			<div class="pad_b3 width250 pad_top">Search for Workshops and Classes by Instructor</div>
            <div class="width250 pad_b5">
              <select name="userid" class="inputdd_search">
                <option value="">- Select Teacher -</option>	
				<option value='<?=base64_encode(28);?>'>Jill Miller, Creator of Yoga Tune Up<span style="font-size:12px;">&reg;</span></option>
				<? foreach($rsteacher as $key => $val) {
					if($val['UserID'] != 28){?>
				<option value="<?=base64_encode($val['UserID'])?>"><?=$val['FName']." ".$val['LName']?></option>
                <? }} ?>
              </select>
            </div>
            <div class="width250 pad_top br_gr padbtm_10">
              <input type="image" src="images/search-button.gif" align="absmiddle" width="91" height="21" border="0" /> <a href="meet-all-teachers.php"><img src="images/meet-all-teachers.gif" border="0" align="top"></a>
            </div>
			</form>		  


            <div class="heading18 width250 pad_top10">Can&rsquo;t Find a Workshop or Class Near You?</div>
            <div class="width250 gr11 pad_b5">Try the Yoga Tune Up<span style="font-size:12px;">&reg;</span> &ldquo;At Home Program and get a customized program just for you!</div>
            <div class="width250 br_gr padbtm_10"><a href="at-home-series.php"><img src="images/learn-btn.gif" width="83" height="21" border="0" align="absmiddle" /></a>
			</div>
			
            <div class="pad_top10 width250"><strong class="bluetext11">Register here to receive the monthly Yoga Tune Up<span style="font-size:12px;">&reg;</span> newsletter! </strong></div>
            <form name="frm5" method="post" onsubmit="return validate();">
              <div class="pad_b3 width250 pad_top">
                
                <div class="floatleft gray11bold pad_top pad_r10" style="width:107px;">First Name: </div>
                <div class="floatleft pad_b5">
                  <input name="fname" id="fname" type="text" class="inputauto" size="19" />
                  <div  id="errfname" style="padding-top:4px; display:none" class="bluetext11"></div>
                </div>
                <div class="floatleft gray11bold pad_top pad_r10" style="width:107px;">Email Address: </div>
                <div class="floatleft pad_b5">
                  <input name="emailfld"  id="emailfld" type="text" class="inputauto" size="19"  value=""/>
                  <div  id="erremail" style="padding-top:4px; display:none"></div>
                </div>
                <div class="floatleft gray11bold pad_top pad_r10" style="width:107px;">Email Confirmation: </div>
                <div class="floatleft">
                  <input name="confemail"  id="confemail" type="text" class="inputauto" size="19"  value=""/>
                </div>
				<div  id="errconfemail1" style="padding-top:4px; padding-left:85px; display:none"></div>
              </div>
              <div class="pad_top width250 padbtm_10">
			<input type="hidden" name="register" value="newsletter" />
			<input type="image" src="images/register.gif" alt="Register" width="76" height="21" border="0" />
			</div>
			<div class="floatleft redtxt" style="padding-bottom:4px;"><?php print $messegeString; ?></div>
			</form>					<? } ?>
			<!-- <div class="width250 gr10 padtb_10"><a href="teacher-classes.php"><img src="images/my-classes.gif"  border="0"  alt="My Classes" width="112" height="21"/></a></div> -->
		  
		  </div>

        </div>
        <div class="width307_btm"><img src="images/corner-left-btm.gif" width="15" height="15" align="left" /></div>
      </div>
    </div>
  </div>
</div>


<br /><br />
</div>
	  
<?php include ("footer.inc.php") ; ?>	  
	  
  </div>
</div>	




</body>
</html>

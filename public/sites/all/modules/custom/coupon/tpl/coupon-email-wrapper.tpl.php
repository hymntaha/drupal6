<?php

$base_url =  "http://".$_SERVER['HTTP_HOST']."/";

?><html  lang="en">
<head>
  <title><?= $title; ?></title>
</head>
<body>
<?php foreach($body as $text):?>
<div style="background-color:#FFFFFF;background-repeat:no-repeat;">
  <table height="428" width="624" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:50px;">
    <tr>
      <td bgcolor="#FFFFFF" width="624" height="468" valign="top">
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
          <tr style="line-height:0;">
            <td colspan="3">
              <img src="<?=file_create_url(drupal_get_path('module', 'coupon').'/bg-coupon-email-top.jpg');?>" alt="Gift Certificate" />
            </td>
          </tr>
          <tr style="line-height:0;">
            <td width="50">
              <img src="<?=file_create_url(drupal_get_path('module', 'coupon').'/bg-coupon-email-left.jpg');?>" alt="">
            </td>
            <td style="text-align:center;line-height:32px;" align="center" bgcolor="#E8E8E8">
              <?php
                if(strlen($text) != strlen(strip_tags($text))){
                  echo $text;
                }else{
                  echo nl2br($text);
                }
              ?>
            </td>
            <td width="50">
              <img src="<?=file_create_url(drupal_get_path('module', 'coupon').'/bg-coupon-email-right.jpg');?>" alt="">
            </td>
          </tr>
          <tr>
            <td colspan="3">
              <img src="<?=file_create_url(drupal_get_path('module', 'coupon').'/bg-coupon-email-bottom.jpg');?>" alt="" />
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</div>
<?php endforeach;?>
</body>
</html>

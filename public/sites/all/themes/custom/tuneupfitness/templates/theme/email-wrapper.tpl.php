<?php

$base_url =  "http://".$_SERVER['HTTP_HOST']."/";

?><html  lang="en">
<head>
  <title><?= $title; ?></title>
</head>
<body>
<style>

	a{ color: #3776C0;text-decoration:none; }
	a:hover{ color: #3776C0; text-decoration: none; }

</style>
		<table class="container" width="100%" style="font-family: Arial, Helvetica, sans-serif;font-size: 13px;color:#333333;">
			<tr>
				<td align="left">
					<table width="600">
						<tr>
							<td width="600"><a href="<?= $base_url; ?>"><img src="http://<?= $_SERVER['HTTP_HOST']."/".drupal_get_path("theme", "tuneupfitness");?>/images/logo-email2.jpg" alt="Tune Up Fitness" /></a></td>
						</tr>
						<tr>
							<td align="left" style="font-family: Arial, Helvetica, sans-serif;font-size: 13px;color:#333333;padding: 0 22px;">
								<div style="font-family: Arial, Helvetica, sans-serif;font-size: 13px;color:#333333;">
									<?php
										if(strlen($body) != strlen(strip_tags($body))){
											echo $body;
										}else{
											echo nl2br($body);
										}
									?>
								</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>

</body>
</html>

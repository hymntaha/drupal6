<?php

$base_url =  "https://".$_SERVER['HTTP_HOST']."/";

?><html  lang="en">
<head>
  <title>Yoga Tune Up&copy; Group Message</title>
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
                            <td width="600"><a href="<?= $base_url; ?>"><img src="https://<?= $_SERVER['HTTP_HOST']."/".drupal_get_path("theme", "tuneupfitness");?>/images/logo-email.jpg" alt="Tune Up Fitness" /></a></td>
						</tr>
						<tr>
							<td align="left" style="font-family: Arial, Helvetica, sans-serif;font-size: 13px;color:#333333;padding: 0 22px;">
								<div style="font-family: Arial, Helvetica, sans-serif;font-size: 13px;color:#333333;">
									<?php
										echo $body;
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

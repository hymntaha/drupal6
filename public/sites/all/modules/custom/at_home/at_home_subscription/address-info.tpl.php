<div class="address">
	<p class="title"><strong><?=$type?> Address</strong></p>
	<p class="name"><span class="first-name"><?=$first_name?></span> <span class="last-name"><?=$last_name?></span></p>
	<p class="street1"><?=$street1?></p>
	<?php if(!empty($street2)): ?>
	<p class="street2"><?=$street2?></p>
	<?php endif;?>
	<p class="city-zone-postal"><span class="city"><?=$city?></span>, <span class="zone"><?=$zone?></span> <span class="postal-code"><?=$postal_code?></span></p>
	<p class="country"><?=$country?></p>
	<p class="edit-link"><?=$edit_link?></p>
</div>
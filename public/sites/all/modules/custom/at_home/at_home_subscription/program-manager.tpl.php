<div class="program-manager">
	<header>
		<div class="header-copy">
            <?=$header?>
            <div class="change-type-link">
                <p>You are currently set to receive <strong><?=$account_info['account']->type == AH_TYPE_DVD ? 'DVD' : 'Online Video'?>s</strong><br />
                <?php
                $link_name = 'Change your subscription to ';
                if($account_info['account']->type == AH_TYPE_DVD){
                    $link_name .= 'Online Videos';
                }
                else{
                    $link_name .= 'DVDs';
                }
                ?>
                <?=l($link_name,'user/'.$account_info['account']->uid.'/at-home-type-switch')?>
                </p>
            </div>
        </div>
		<div class="account-info">
			<span>Welcome <strong><?=$name?></strong></span><br />
			<span>Last Shipment: <strong><?=$last_shipment?></strong></span><br />
			<span>Status: <strong><?=$status?></strong></span><br />
			<span>Joined: <strong><?=$joined?></strong></span>
            <div class="cancel-link"><?=$cancel_link?></div>
		</div>
	</header>
	<div class="program-wrapper">
		<?=render($next_shipment_info)?>
		<div class="info-wrapper">
			<ul class="tabs">
				<li class="shipping-options active">
					Subscription Options
					<div class="tab-content">
						<?=render($shipping_options)?>
					</div>
				</li>
				<li class="shipping-address">
					Shipping Address
					<div class="tab-content">
						<?=render($shipping_address)?>
					</div>
				</li>
				<li class="billing-address">
					Billing Address
					<div class="tab-content">
						<?=render($billing_address)?>
					</div>
				</li>
				<li class="credit-card">
					Credit Card Information
					<div class="tab-content">
						<?=render($credit_card_info)?>
					</div>
				</li>
			</ul>
		</div>
		<div class="queue">
			<div class="queue-header"><?=$queue_header?></div>
			<?=render($queue_form)?>
		</div>
	</div>
</div>
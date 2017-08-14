<div id="cart-login-page" class="row">
	<div class="col-md-6">
		<h2>Sign In &amp; Checkout</h2>
		<div class="contents">
            <?=_mm('cart_sign_in_and_checkout','')?>
			<?= drupal_render($login_form); ?>
		</div>
	</div>
    <div class="col-md-6">
        <h2>Checkout as a Guest</h2>
        <div class="contents">
            <?=_mm('cart_login_as_guest','')?>
            <?= render($anon_login_form); ?>
        </div>
    </div>
	<div class="col-sm-12">
		<div class="fb-signin">
			<h2>OR</h2>
			<a href="/user/simple-fb-connect"><img class="img-responsive" src="/<?=drupal_get_path('theme','tuneupfitness')?>/images/ico-fb-login.png" /></a>
		</div>
	</div>
</div>

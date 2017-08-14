<div id="cart-login-page" class="row">
	<div class="col-md-4">
		<h2>Sign In &amp; Checkout</h2>
		<div class="contents">
			<?= drupal_render($login_form); ?>
		</div>
	</div>
	<div class="col-md-4 extra-margin">
		<h2>Register &amp; Checkout</h2>
		<div class="contents">
			Create an account for quick checkout with the ability to track your order and print receipts.<br />
            <?=l('Register & Checkout','user/register', array('query' => array("destination"=>"cart/checkout"), 'attributes' => array('class' => array('register-action'))))?>
		</div>
	</div>
    <div class="col-md-4">
        <h2>Checkout as a Guest</h2>
        <div class="contents">
            <?= render($anon_login_form); ?>
        </div>
    </div>
	<div class="col-sm-12">
		<div class="fb-signin">
			<h2>OR</h2>
			<a href="/user/simple-fb-connect"><img class="img-responsive" src="/sites/all/themes/custom/yogatuneup/images/ico-fb-login.png" /></a>
		</div>
	</div>
</div>
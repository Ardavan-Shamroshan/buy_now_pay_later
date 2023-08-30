<div class="wrap">
    <h1>درگاه پرداخت اقساطی</h1>
	<?php settings_errors(); ?>

    <ul class="nav nav-tabs">
        <li class="active"><a href="#tab-1">تنظیمات</a></li>
        <li><a href="#tab-2">بروزرسانی</a></li>
        <li><a href="#tab-3">درباره ما</a></li>
    </ul>

    <div class="tab-content">
        <div id="tab-1" class="tab-pane active">
            <form action="options.php" method="POST">
				<?php
				settings_fields( 'buy_now_pay_later_settings' );
				do_settings_sections('buy-now-pay-later' );
				submit_button();
				?>
            </form>
        </div>

        <div id="tab-2" class="tab-pane">
            <h3>بروزرسانی</h3>
        </div>
        <div id="tab-3" class="tab-pane">
            <h3>درباره ما</h3>
        </div>
    </div>

</div>
<?php

namespace Inc\Controllers;

class BuyNowPayLaterController extends BaseController {

	public function register() {
		require_once $this->plugin_path . '/include/Api/Gateways/WC_Gateway_Themedoni_Buy_Now_Pay_Later.php';
	}

}
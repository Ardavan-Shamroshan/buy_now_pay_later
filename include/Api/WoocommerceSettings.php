<?php

namespace Inc\Api;

use Inc\Api\Gateways\WC_Gateway_Themedoni_Buy_Now_Pay_Later;

class WoocommerceSettings
{
	public function register()
	{
		/**
		 * Payment gateways should be created as additional plugins that hook into WooCommerce.
		 * Inside the plugin, you need to create a class after plugins are loaded.
		 */
		add_action('plugins_loaded', [$this, 'init_themedoni_buy_now_pay_later']);
	}

	/**
	 * It is also important that your gateway class extends the WooCommerce base gateway class,
	 * so you have access to important methods and the settings API:
	 */
	public function init_themedoni_buy_now_pay_later()
	{
		/**
		 * As well as defining your class, you need to also tell WooCommerce (WC) that it exists.
		 * Do this by filtering woocommerce_payment_gateways:
		 */
		add_filter('woocommerce_payment_gateways', [$this, 'WC_Add_Themedoni_Buy_Now_Pay_Later']);

		(new WC_Gateway_Themedoni_Buy_Now_Pay_Later());
	}

	public function WC_Add_Themedoni_Buy_Now_Pay_Later($methods)
	{
		$methods[] = (WC_Gateway_Themedoni_Buy_Now_Pay_Later::class);

		return $methods;
	}
}
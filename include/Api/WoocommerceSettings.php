<?php

namespace Inc\Api;

use WC_Payment_Gateway;

/**
 * It is also important that your gateway class extends the WooCommerce base gateway class,
 * so you have access to important methods and the settings API:
 */
function init_themedoni_buy_now_pay_later()
{
	class WC_Gateway_Themedoni_Buy_Now_Pay_Later extends WC_Payment_Gateway
	{
		function add_your_gateway_class($methods)
		{
			$methods[] = 'WC_Gateway_Your_Gateway';
			return $methods;
		}
	}
}

/**
 * Payment gateways should be created as additional plugins that hook into WooCommerce.
 * Inside the plugin, you need to create a class after plugins are loaded.
 */
add_action('plugins_loaded', 'init_themedoni_buy_now_pay_later');

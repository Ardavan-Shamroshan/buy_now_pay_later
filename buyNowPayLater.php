<?php

/**
 * @package PeachCore
 *
 * Plugin Name: درگاه پرداخت اقساطی
 * Plugin URI: http://themedoni.com
 * Description: با این افزونه، شما می توانید پرداخت اقساطی با چک را در فروشگاه خود راه اندازی کنید.
 * Version: 1.0.0
 * Author: Ardavan Shamroshan
 * Author URI: http://ardavanshamroshan.com
 * License: GPLv2 or later
 * Text Domain: http://themedoni.com
 */

define( "BuyNowPayLater", "1.0.0" );

// Die if accessed externally
defined( 'ABSPATH' ) || die;

// Dump autoload
if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

// Define constants
define( 'BNPL_PATH', plugin_dir_path( __FILE__ ) );
define( 'BNPL_URL', plugin_dir_url( __FILE__ ) );
define( 'BNPL', plugin_basename( __FILE__ ) );

// Plugin on activation
function activate_buy_now_pay_later() {
	\Inc\Base\Activate::activate();
}

register_activation_hook( __FILE__, 'activate_buy_now_pay_later' );

// Plugin on deactivation
function deactivate_buy_now_pay_later() {
	\Inc\Base\Deactivate::deactivate();
}

register_deactivation_hook( __FILE__, 'deactivate_buy_now_pay_later' );


add_filter( 'wc_order_statuses', 'themedoni_bnpl_wc_add_order_statuses' );

// Add New Order Statuses to WooCommerce
function themedoni_bnpl_wc_add_order_statuses( $order_statuses ) {
	$new_order_statuses = array();
	foreach ( $order_statuses as $key => $status ) {
		$new_order_statuses[ $key ] = $status;
		if ( 'wc-processing' === $key ) {
			$new_order_statuses['wc-cheque-progress'] = 'در انتظار تایید چک';
		}
	}
	return $new_order_statuses;
}

// Initialization

if ( class_exists( 'Inc\Init' ) ) {
	\Inc\Init::register_services();
}
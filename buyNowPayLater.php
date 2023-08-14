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

// Initialization

if ( class_exists( 'Inc\Init' ) ) {
	\Inc\Init::register_services();
}
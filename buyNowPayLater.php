<?php

/**
 * @package BuyNowPayLater
 *
 * Plugin Name: درگاه پرداخت اقساطی
 * Description: با این افزونه، شما می توانید پرداخت اقساطی با چک را در فروشگاه خود راه اندازی کنید.
 * Version: 3.4.0
 * Author: Ardavan Shamroshan
 * Author URI: ardavanshamroshan@yahoo.com
 * License: GPLv2 or later
 */

define( "BuyNowPayLaterVersion", "3.4.0" );

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

// action hooks
add_action( 'plugins_loaded', 'init_buy_now_pay_later' );

add_action( 'wp_ajax_bnpl_get_data', 'bnpl_get_term_of_installment' );
add_action( 'wp_ajax_nopriv_bnpl_get_data', 'bnpl_get_term_of_installment' );

function bnpl_get_term_of_installment() {
	// if (!wp_verify_nonce($_POST['nonce'])) {
	// 	wp_die('Access Denied!');
	// }

	$installment_name = $_POST['name'];

	$installments = get_option( 'buy_now_pay_later_cheque_conditions' );

	$key = array_search( $installment_name, array_column( $installments, 'condition_name' ) );

	if ( $key === false ) {

		echo json_encode( [
			'status'   => 'error',
			'response' => 'عملیات با خطا مواجه شد',
		] );
	} else {
		echo json_encode( [
			'status'      => 'success',
			'response'    => $installments[ $key ] ?? [],
			'order_total' => $_POST['orderTotal']
		] );
	}

	wp_die();
}


// Initialization
if ( class_exists( 'Inc\Init' ) ) {
	\Inc\Init::register_services();
}

<?php

namespace Inc\Controllers;

class ShortCodeController extends BaseController {
	/**
	 * 'condition_name'       => نام شرط,
	 * 'prepayment'           => پیش پرداخت,
	 * 'installments'         => اقساط,
	 * 'term_of_installments' => مدت اقساط,
	 * 'commission_rate'      => نرخ کارمزد
	 *
	 * @var false|mixed|null
	 */
	public $cheque_conditions;

	public function register() {
		$gateway_settings = get_option( 'woocommerce_WC_Gateway_Buy_Now_Pay_Later_settings' );

		if ( $gateway_settings['calculator'] !== 'yes' ) {
			return;
		}

		$this->cheque_conditions = get_option( 'buy_now_pay_later_cheque_conditions' );

		$this->add_bnpl_calculator_shortcode();


		add_action( 'woocommerce_after_add_to_cart_form', function () {
			do_shortcode( '[bnpl-calculator]' );
		}, 20 );
	}

	public function add_bnpl_calculator_shortcode() {
		add_shortcode( 'bnpl-calculator', [ $this, 'bnpl_calculator_form' ] );
	}


	public function bnpl_calculator_form() {
		global $product;
		extract( [ $this->cheque_conditions, $product ] );
		require_once $this->plugin_path . '/templates/shortcodes/calculator.php';
	}


}
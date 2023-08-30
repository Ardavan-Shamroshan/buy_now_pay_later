<?php

namespace Inc\Controllers;

class MyAccountHooksController extends BaseController {
	public function register() {
		add_action( 'woocommerce_view_order', [ $this, 'action_woocommerce_account_content' ], 11 );
	}

	public function action_woocommerce_account_content( $order_id ) {


		$order = wc_get_order( $order_id ); // Get the WC_Order object


		if ( $order->get_status() == 'cancelled' ) {
			return;
		}

		if ( $order->get_payment_method() !== 'WC_Gateway_Buy_Now_Pay_Later' ) {
			return;
		}

		$extra_fields             = get_option( 'buy_now_pay_later_extra_fields' );
		$cheque_conditions        = get_option( 'buy_now_pay_later_cheque_conditions' );
		$order_extra_fields_value = get_post_meta( $order_id, 'bnpl_extra_fields', true );
		$order_cheques            = get_post_meta( $order_id, 'bnpl_cheque', true );
		$order_cheque_condition   = get_post_meta( $order_id, 'bnpl_cheque_condition', true );

		list( $final_price, $every_installment_price, $prepayment_price ) = $this->gateway_calculator( $order->get_total(), $order_cheque_condition );

		extract( [ $order, $extra_fields, $cheque_conditions, $order_extra_fields_value, $order_cheques, $order_cheque_condition, $final_price, $prepayment_price, $every_installment_price ] );
		include_once $this->plugin_path . '/templates/order-metabox.php';
	}

	public function gateway_calculator( $order_total, $order_cheque_condition ) {
		$prepayment      = (int) $order_cheque_condition['prepayment'];
		$installments    = (int) $order_cheque_condition['installments'];
		$commission_rate = (int) $order_cheque_condition['commission_rate'];

		$prepayment_price               = $order_total * ( $prepayment / 100 );
		$remained                       = $order_total - $prepayment_price;
		$commission_price               = $remained * ( $commission_rate / 100 );
		$remained_with_commission_price = $remained + $commission_price;
		$every_installment_price        = $remained_with_commission_price / $installments;

		return [ $remained_with_commission_price + $prepayment_price, $every_installment_price, $prepayment_price ];
	}
}

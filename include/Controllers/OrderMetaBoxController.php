<?php

namespace Inc\Controllers;


class OrderMetaBoxController extends BaseController {

	public function register() {
		// Adding Meta container admin shop_order pages
		add_action( 'add_meta_boxes', [ $this, 'check_page_and_post_type' ], 10, 2 );
	}

	public function check_page_and_post_type() {
		global $pagenow, $post;


		$post_type = get_post_type( $post->ID );

		if ( $pagenow != 'post.php' && $_GET['action'] != 'edit' ) {
			return;
		} // Exit

		if ( $post_type != 'shop_order' ) {
			return;
		} // Exit

		$this->add_shop_order_meta_boxes( $post_type, $post );
	}

	private function add_shop_order_meta_boxes( $post_type, $post ) {
		$order = wc_get_order( $post->ID ); // Get the WC_Order object

		if ( $order->get_payment_method() != 'WC_Gateway_Buy_Now_Pay_Later' ) {
			return;
		}


		if ( $order->get_status() == 'cancelled' ) {
			return;
		}

		add_meta_box( 'cheque_order_field', __( 'اطلاعات چک ها' ), [ $this, 'add_cheque_order_field_content' ], 'shop_order' );
	}

	public function add_cheque_order_field_content( $post ) {

		$order = wc_get_order( $post->ID ); // Get the WC_Order object


		$extra_fields             = get_option( 'buy_now_pay_later_extra_fields' );
		$cheque_conditions        = get_option( 'buy_now_pay_later_cheque_conditions' );
		$order_extra_fields_value = get_post_meta( $post->ID, 'bnpl_extra_fields', true );
		$order_cheques            = get_post_meta( $post->ID, 'bnpl_cheque', true );
		$order_cheque_condition   = get_post_meta( $post->ID, 'bnpl_cheque_condition', true );


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

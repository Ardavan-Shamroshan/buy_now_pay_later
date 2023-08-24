<?php

namespace Inc\Controllers;

class OrderMetaBoxController extends BaseController {

	public function register() {
		// Adding Meta container admin shop_order pages
		add_action( 'add_meta_boxes', [ $this, 'add_shop_order_meta_boxes' ] );
	}

	public function add_shop_order_meta_boxes( $post ) {
		add_meta_box( 'cheque_order_field', __( 'اطلاعات چک ها', 'woocommerce' ), [ $this, 'add_cheque_order_field_content' ], 'shop_order' );
	}

	public function add_cheque_order_field_content( $post ) {
		$order = wc_get_order( $post->ID ); // Get the WC_Order object
		if ( $order->get_payment_method() != 'WC_Gateway_Themedoni_Buy_Now_Pay_Later' ) {
			return;
		}

		$extra_fields      = get_option( 'themedoni_buy_now_pay_later_extra_fields' );
		$cheque_conditions = get_option( 'themedoni_buy_now_pay_later_cheque_conditions' );

		$order_extra_fields_value    = get_post_meta( $post->ID, 'themedoni_bnpl_extra_fields', true );
		$order_cheques               = get_post_meta( $post->ID, 'themedoni_bnpl_cheque', true );
		$order_cheque_condition_name = get_post_meta( $post->ID, 'themedoni_bnpl_cheque_condition', true );

		$key                    = array_search( $order_cheque_condition_name, array_column( $cheque_conditions, 'condition_name' ) );
		$order_cheque_condition = $cheque_conditions[ $key ];

		extract( [ $order, $extra_fields, $cheque_conditions, $order_extra_fields_value, $order_cheques, $order_cheque_condition ] );
		include_once $this->plugin_path . '/templates/order-metabox.php';
	}
}
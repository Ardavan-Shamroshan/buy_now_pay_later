<?php

namespace Inc\Api\Callbacks;

use WC_Order;

class GatewayCallbacks {
	public function redirect_to_cheque_payment_page( $order_id, $extra_fields ) {
		$order = new WC_Order( $order_id );

		global $woocommerce;

		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			$files            = [];
			$wp_upload_path   = wp_upload_dir();
			$bnpl_upload_path = $wp_upload_path['basedir'] . '/bnpl_uploads/' . date( 'Y' ) . '/' . date( 'm' ) . '/';
			$bnpl_upload_url  = '/bnpl_uploads/' . date( 'Y' ) . '/' . date( 'm' ) . '/';

			if ( ! file_exists( $bnpl_upload_path ) ) {
				wp_mkdir_p( $bnpl_upload_path );
			}

			$input = [];
			foreach ( $this->extra_fields as $extra_field ) {
				$input[ $extra_field['field_id'] ] = $_POST[ $extra_field['field_id'] ];

				// if extra field has an input:file
				if ( $extra_field['field_type'] == 'file' ) {
					if ( isset( $_FILES ) && $_FILES[ $extra_field['field_id'] ] ) {
						$file_name     = explode( '.', $_FILES[ $extra_field['field_id'] ]['name'] );
						$new_file_name = rand( 100000000, 9999999999 ) . '.' . $file_name[1];
						$result        = move_uploaded_file( $_FILES[ $extra_field['field_id'] ]['tmp_name'], $bnpl_upload_path . $new_file_name );
						if ( $result ) {
							$input[ $extra_field['field_id'] ] = $bnpl_upload_url . $new_file_name;
						}
					}
				}

			}

			update_post_meta( $order_id, 'themedoni_bnpl_extra_fields', $input );
			update_post_meta( $order_id, 'themedoni_bnpl_cheque_condition', $_POST['name'] );

			// upload cheque images
			if ( is_array( $_FILES ) ) {
				foreach ( $_FILES as $file ) {
					$file_name     = explode( '.', $file['name'] );
					$new_file_name = rand( 100000000, 9999999999 ) . '.' . $file_name[1];
					$result        = move_uploaded_file( $file['tmp_name'], $bnpl_upload_path . $new_file_name );
					$files[]       = $bnpl_upload_url . $new_file_name;
					if ( $result ) {
						update_post_meta( $order_id, 'themedoni_bnpl_cheque', $files );
					}
				}
			} else {
				$file_name     = explode( '.', $_FILES['name'] );
				$new_file_name = rand( 100000000, 9999999999 ) . '.' . $file_name[1];
				$result        = move_uploaded_file( $_FILES['tmp_name'], $bnpl_upload_url . $new_file_name );
				if ( $result ) {
					update_post_meta( $order_id, 'themedoni_bnpl_cheque', $bnpl_upload_url . $new_file_name );
				}
			}

			$order->update_status( 'on-hold', 'در انتظار تایید چک' ); // order note is optional, if you want to  add a note to order
			$woocommerce->cart->empty_cart();
		}
	}


	public function return_from_cheque_payment_page( $order_id ) {
		wp_redirect( home_url() );
		$order = new WC_Order( $order_id );
		extract( [ $order ] );
		include_once BNPL_PATH . 'templates/gateways/cheque-paid-page.php';
	}
}
<?php

namespace Inc\Api\Callbacks;

use WC_Order;

class GatewayCallbacks {
	public function redirect_to_cheque_payment_page( $order_id, $extra_fields ) {
		$order = new WC_Order( $order_id );
		global $woocommerce;

		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			$all_conditions     = get_option( 'buy_now_pay_later_cheque_conditions' );
			$key                = array_search( $_POST['bnpl_order_condition_name'], array_column( $all_conditions, 'condition_name' ) );
			$selected_condition = $all_conditions[ $key ];

			if ( ! wp_verify_nonce( $_POST['_wpnonce'] ) ) {
				wp_die( 'Access Denied!' );
			}

			if ( empty( $selected_condition ) ) {
				alert( sprintf( "عملیات با خطا مواجه شد" ), 'error');

				return false;

			}

			if ( count( $_FILES['bnpl_cheque_image']['name'] ) != $selected_condition['installments'] ) {
				alert( "لطفا به تعداد چک های خواسته شده تصاویر را ارسال کنید", 'error');
				return false;
			}

			$files = [];
			$input = [];
			foreach ( $extra_fields as $extra_field ) {
				$input[ $extra_field['field_id'] ] = $_POST[ $extra_field['field_id'] ];

				if ( empty( $input[ $extra_field['field_id'] ] ) && $extra_field['field_type'] != 'file' ) {
					alert( "همه موارد را با دقت پر کنید", 'error');

					return false;
				}

				// if extra field has an input:file
				if ( $extra_field['field_type'] == 'file' ) {
					if ( isset( $_FILES ) && $_FILES[ $extra_field['field_id'] ] ) {
						$attachment = $this->uploadFile( $_FILES[ $extra_field['field_id'] ] );

						if ( $attachment ) {
							$input[ $extra_field['field_id'] ] = $attachment['url'];
						} else {
							alert( "با عرض پوزش شما مجاز به بارگزاری این نوع پرونده نیستید" , 'error' );

							return false;
						}
					}
				}
			}

			update_post_meta( $order_id, 'bnpl_extra_fields', $input );
			update_post_meta( $order_id, 'bnpl_cheque_condition', $selected_condition );

			// upload cheque images

			if ( isset( $_FILES['bnpl_cheque_image'] ) ) {
				$cheque_files = [];
				$totalFile    = count( $_FILES['bnpl_cheque_image']['name'] );

				// create array of uploaded files
				for ( $i = 0; $i < $totalFile; $i ++ ) {
					$cheque_files["bnpl_cheque_image_$i"]['name']     = $_FILES['bnpl_cheque_image']['name'][ $i ];
					$cheque_files["bnpl_cheque_image_$i"]['type']     = $_FILES['bnpl_cheque_image']['type'][ $i ];
					$cheque_files["bnpl_cheque_image_$i"]['tmp_name'] = $_FILES['bnpl_cheque_image']['tmp_name'][ $i ];
					$cheque_files["bnpl_cheque_image_$i"]['error']    = $_FILES['bnpl_cheque_image']['error'][ $i ];
					$cheque_files["bnpl_cheque_image_$i"]['size']     = $_FILES['bnpl_cheque_image']['size'][ $i ];
				}

				foreach ( $cheque_files as $cheque_file ) {
					$cheque_attachment = $this->uploadFile( $cheque_file );
					if ( $cheque_attachment ) {
						$files[] = $cheque_attachment['url'];
						update_post_meta( $order_id, 'bnpl_cheque', $files );
					}
					else {
						alert( "با عرض پوزش شما مجاز به بارگزاری این نوع پرونده نیستید" , 'error' );

						return false;
					}
				}
			}

			$order->update_status('cheque_approval', 'در انتظار تایید چک'); // order note is optional, if you want to  add a note to order
			$woocommerce->cart->empty_cart();
			return true;
		}
	}


	public function uploadFile( $file ) {
		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}

		$overrides = [ 'test_form' => false, 'mimes' => [ 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png' ] ];
		$file      = wp_handle_upload( $file, $overrides );
		if ( isset( $file['error'] ) ) {
			return false;
		}

		// Construct the attachment array.
		return $file;
	}
}

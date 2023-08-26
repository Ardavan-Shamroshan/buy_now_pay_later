<?php

namespace Inc\Api\Callbacks;

use WC_Order;

class GatewayCallbacks
{
	public function redirect_to_cheque_payment_page($order_id, $extra_fields)
	{
		$order = new WC_Order($order_id);

		global $woocommerce;

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$files            = [];
			$wp_upload_path   = wp_upload_dir();
			$bnpl_upload_path = $wp_upload_path['basedir'] . '/bnpl_uploads/' . date('Y') . '/' . date('m') . '/';
			$bnpl_upload_url  = '/bnpl_uploads/' . date('Y') . '/' . date('m') . '/';

			if (!file_exists($bnpl_upload_path)) {
				wp_mkdir_p($bnpl_upload_path);
			}


			$input = [];
			foreach ($extra_fields as $extra_field) {
				$input[$extra_field['field_id']] = $_POST[$extra_field['field_id']];

				// if extra field has an input:file
				if ($extra_field['field_type'] == 'file') {
					if (isset($_FILES) && $_FILES[$extra_field['field_id']]) {
						$file_name     = explode('.', $_FILES[$extra_field['field_id']]['name']);
						$new_file_name = rand(100000000, 9999999999) . '.' . $file_name[1];
						$result        = move_uploaded_file($_FILES[$extra_field['field_id']]['tmp_name'], $bnpl_upload_path . $new_file_name);
						if ($result) {
							$input[$extra_field['field_id']] = $bnpl_upload_url . $new_file_name;
						}
					}
				}
			}

			// validate national code

			update_post_meta($order_id, 'themedoni_bnpl_extra_fields', $input);
			update_post_meta($order_id, 'themedoni_bnpl_cheque_condition', $_POST['name']);

			// upload cheque images
			// if ( is_array( $_FILES ) ) {
				var_dump($_FILES['themedoni_bnpl_cheque_image']);
				die;
			if (isset($_FILES['themedoni_bnpl_cheque_image'])) {
				$cheque_files = $_FILES['themedoni_bnpl_cheque_image'];
				$totalFile = count($_FILES['themedoni_bnpl_cheque_image']['name']);   //line 25

				foreach ($_FILES['themedoni_bnpl_cheque_image']['name'] as $key => $name) {
					var_dump($_FILES['themedoni_bnpl_cheque_image']);
					
					// $this->uploadFile($file);
					// $file_name     = explode('.', $file['name']);
					// $new_file_name = rand(100000000, 9999999999) . '.' . $file_name[1];
					// $result        = move_uploaded_file($file['tmp_name'], $bnpl_upload_path . $new_file_name);
					// $files[]       = $bnpl_upload_url . $new_file_name;
					// if ($result) {
					// 	update_post_meta($order_id, 'themedoni_bnpl_cheque', $files);
					// }
				}
				die;

			}
			// } else {	
			// 	$file_name     = explode( '.', $_FILES['name'] );
			// 	$new_file_name = rand( 100000000, 9999999999 ) . '.' . $file_name[1];
			// 	$result        = move_uploaded_file( $_FILES['tmp_name'], $bnpl_upload_url . $new_file_name );
			// 	if ( $result ) {
			// 		update_post_meta( $order_id, 'themedoni_bnpl_cheque', $bnpl_upload_url . $new_file_name );
			// 	}
			// }

			$order->update_status('cheque_approval', 'در انتظار تایید چک'); // order note is optional, if you want to  add a note to order
			$woocommerce->cart->empty_cart();
		}
	}

	public function uploadFile($file)
	{
		if (!function_exists('wp_handle_upload')) {
			require_once(ABSPATH . 'wp-admin/includes/file.php');
		}

		$overrides = array('test_form' => false);
		$file = wp_handle_upload($file, $overrides);

		if (isset($file['error'])) {
			return 0;
		}

		$url = $file['url'];
		$type = $file['type'];
		$file = $file['file'];
		$filename = wp_basename($file);

		// Construct the attachment array.
		$attachment = array(
			'post_title' => $filename,
			'post_content' => $url,
			'post_mime_type' => $type,
			'guid' => $url,
			'context' => 'custom-background',
		);

		$id = wp_insert_attachment($attachment, $file);
		return $id;
	}


	public function return_from_cheque_payment_page($order_id)
	{
		wp_redirect(home_url());
		$order = new WC_Order($order_id);
		extract([$order]);
		include_once BNPL_PATH . 'templates/gateways/cheque-paid-page.php';
	}
}

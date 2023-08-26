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


			// validate national code
			$input = [];
			foreach ($extra_fields as $extra_field) {
				$input[$extra_field['field_id']] = $_POST[$extra_field['field_id']];

				// if extra field has an input:file
				if ($extra_field['field_type'] == 'file') {
					if (isset($_FILES) && $_FILES[$extra_field['field_id']]) {
						$this->uploadFile($_FILES[$extra_field['field_id']]);
					}
				}
			}

			update_post_meta($order_id, 'themedoni_bnpl_extra_fields', $input);
			update_post_meta($order_id, 'themedoni_bnpl_cheque_condition', $_POST['name']);

			// upload cheque images

			if (isset($_FILES['themedoni_bnpl_cheque_image'])) {
				$cheque_files = [];
				$totalFile = count($_FILES['themedoni_bnpl_cheque_image']['name']);

				// create array of uploaded files
				for ($i = 0; $i < $totalFile; $i++) {
					$cheque_files["themedoni_bnpl_cheque_image_$i"]['name'] = $_FILES['themedoni_bnpl_cheque_image']['name'][$i];
					$cheque_files["themedoni_bnpl_cheque_image_$i"]['type'] = $_FILES['themedoni_bnpl_cheque_image']['type'][$i];
					$cheque_files["themedoni_bnpl_cheque_image_$i"]['tmp_name'] = $_FILES['themedoni_bnpl_cheque_image']['tmp_name'][$i];
					$cheque_files["themedoni_bnpl_cheque_image_$i"]['error'] = $_FILES['themedoni_bnpl_cheque_image']['error'][$i];
					$cheque_files["themedoni_bnpl_cheque_image_$i"]['size'] = $_FILES['themedoni_bnpl_cheque_image']['size'][$i];
				}

				foreach ($cheque_files as $cheque_file) {
					$this->uploadFile($cheque_file);
				}
			}

			$order->update_status('cheque_approval', 'در انتظار تایید چک'); // order note is optional, if you want to  add a note to order
			$woocommerce->cart->empty_cart();
		}
	}

	public function uploadFile($file)
	{
		if (!function_exists('wp_handle_upload')) {
			require_once(ABSPATH . 'wp-admin/includes/file.php');
		}

		$overrides = ['test_form' => false, 'mimes' => ['jpg' => 'image/jpeg, image/pjpeg', 'jpeg' => 'image/jpeg, image/pjpeg', 'png' => 'image/png']];
		$file = wp_handle_upload($file, $overrides,);

		if (isset($file['error'])) {
			return 0;
		}

		$url = $file['url'];
		$type = $file['type'];
		$file = $file['file'];
		$filename = wp_basename($file);

		// Construct the attachment array.
		$attachment = [
			'post_title' => $filename,
			'post_content' => $url,
			'post_mime_type' => $type,
			'guid' => $url,
			'context' => 'custom-background',
		];

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

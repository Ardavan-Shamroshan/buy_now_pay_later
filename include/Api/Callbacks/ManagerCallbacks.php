<?php

namespace Inc\Api\Callbacks;

use Inc\Controllers\BaseController;

class ManagerCallbacks extends BaseController {
	/**
	 * Custom fields callbacks
	 */

	public function checkbox_sanitize( $input ): array {
		$output = [];
		foreach ( $this->setting_managers as $key => $value ) {
			$output[ $key ] = isset( $input[ $key ] );
		}

		return $output;
	}

	public function admin_section_manager() {
		echo 'تنظیمات عمومی درگاه پرداخت اقساطی ';
	}

	public function checkbox_field( $args ) {
		$name  = $args['label_for'];
		$class = $args['class'];

		// initialize input's name like 'option_name[cpt_manager]'
		$option_name = $args['option_name'];
		$checkbox    = get_option( $option_name );
		// if there was an option with the option_name ($checkbox), then if there was an option with option_name value check the checkbox
		$checked = $checkbox ? ( $checkbox[ $name ] ? 'checked' : '' ) : '';

		echo "<input type='checkbox' id='$name' name='$option_name" . "[$name]' value='1' class='$class' $checked><label for='$name'></label>";
	}
}
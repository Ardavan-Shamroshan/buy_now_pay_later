<?php

namespace Inc\Api\Callbacks;

class CustomPostTypeCallbacks {
	public function cpt_section_manager() {
		echo 'نوع پست اختصاصی را از طریق این صفحه ایجاد کنید';
	}

	public function cpt_sanitize( $input ) {
		$output = get_option( 'peach_core_plugin_cpt' ) ?: [];

		// delete record
		if ( isset( $_POST['remove'] ) ) {
			unset( $output[ $_POST['remove'] ] );

			return $output;
		}

		// if output was an empty array
		if ( empty( $output ) ) {
			$output[ $input['post_type'] ] = $input;

			return $output;
		}

		// if there was the post type update it, otherwise create the new one
		foreach ( $output as $key => $value ) {
			if ( $input['post_type'] === $key ) {
				$output[ $key ] = $input;
			} else {
				$output[ $input['post_type'] ] = $input;
			}
		}

		return $output;
	}

	public function text_field( $args ) {
		$name        = $args['label_for'];
		$placeholder = $args['placeholder'];
		$option_name = $args['option_name'];
		// if there was an option with the option_name, then if there was an option with option_name value fill the input
		$value = '';
		if ( isset( $_POST['edit_post'] ) ) {
			$input = get_option( $option_name );
			$value = $input[ $_POST['edit_post'] ][ $name ];
		}

		echo "<input type='text' class='regular-text' id='$name'  name='$option_name" . "[$name]' value='$value' placeholder='$placeholder' required>";
	}

	public function checkbox_field( $args ) {
		$name  = $args['label_for'];
		$class = $args['class'];

		// initialize input's name like 'option_name[cpt_manager]'
		$option_name = $args['option_name'];

		// if there was an option with the option_name ($checkbox), then if there was an option with option_name value check the checkbox

		$checked = false;
		if ( isset( $_POST['edit_post'] ) ) {
			$checkbox = get_option( $option_name );
			$checked  = isset( $checkbox[ $_POST['edit_post'] ][ $name ] );
		}
		$checked =  $checked ? 'checked' : '';

		echo "<input type='checkbox' id='$name' name='$option_name" . "[$name]' value='1' class='$class' $checked><label for='$name'></label>";
	}
}
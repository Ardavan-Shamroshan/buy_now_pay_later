<?php

namespace Inc\Api\Callbacks;

class CustomTaxonomyCallbacks {
	public function taxonomy_section_manager() {
		echo "دسته بندی های اختصاصی خود را از این صفحه مدیریت کنید";
	}

	public function taxonomy_sanitize( $input ) {
		$output = get_option( 'peach_core_plugin_taxonomy' ) ?: [];

		// delete record
		if ( isset( $_POST['remove'] ) ) {
			unset( $output[ $_POST['remove'] ] );

			return $output;
		}

		// if output was an empty array
		if ( empty( $output ) ) {
			$output[ $input['taxonomy'] ] = $input;

			return $output;
		}

		// if there was the taxonomy update it, otherwise create the new one
		foreach ( $output as $key => $value ) {
			if ( $input['taxonomy'] === $key ) {
				$output[ $key ] = $input;
			} else {
				$output[ $input['taxonomy'] ] = $input;
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
		if ( isset( $_POST['edit_taxonomy'] ) ) {
			$input = get_option( $option_name );
			$value = $input[ $_POST['edit_taxonomy'] ][ $name ];
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
		if ( isset( $_POST['edit_taxonomy'] ) ) {
			$checkbox = get_option( $option_name );
			$checked  = isset( $checkbox[ $_POST['edit_taxonomy'] ][ $name ] );
		}
		$checked = $checked ? 'checked' : '';

		echo "<input type='checkbox' id='$name' name='$option_name" . "[$name]' value='1' class='$class' $checked><label for='$name'></label>";
	}

	public function checkbox_post_type_field( $args ) {
		$name  = $args['label_for'];
		$class = $args['class'];

		// initialize input's name like 'option_name[cpt_manager]'
		$option_name = $args['option_name'];

		// if there was an option with the option_name ($checkbox), then if there was an option with option_name value check the checkbox

		$checked = false;
		if ( isset( $_POST['edit_taxonomy'] ) ) {
			$checkbox = get_option( $option_name );
		}


		// get all post types as objects
		$post_types = get_post_types( [ 'show_ui' => true ], 'object' );

		$output = '';
		foreach ( $post_types as $key => $type ) {

			if ( isset( $_POST['edit_taxonomy'] ) ) {
				$checked = isset( $checkbox[ $_POST['edit_taxonomy'] ][ $name ][$key] );
			}
			$checked = $checked ? 'checked' : '';


			$output .= "<div class='mb-10'><input type='checkbox' id='$key' name='$option_name" . "[$name][$key]' value='1' class='$class' $checked><label for='$name'>$key ($type->label)</label></div> <br>";
		}
		echo $output;
	}
}
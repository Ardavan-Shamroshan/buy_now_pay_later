<?php

namespace Inc\Base;

use Inc\Controllers\BaseController;

class Enqueue extends BaseController {
	public function register() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ] );
	}

	/**
	 * Enqueue scripts
	 */
	public function enqueue() {
		// wp_enqueue_script( 'peachCoreCkeditorScript', 'https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js' );

		// enable wp builtin media upload used in MediaWidget.php
		wp_enqueue_script('media - upload');
		wp_enqueue_media();

		// override
		wp_enqueue_style( 'peachCoreStyle', $this->plugin_url . 'assets/override.css', [], null );
		wp_enqueue_script( 'peachCoreScript', $this->plugin_url . 'assets/override.js', [], null );
	}
}
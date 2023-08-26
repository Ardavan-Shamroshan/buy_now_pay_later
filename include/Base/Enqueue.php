<?php

namespace Inc\Base;

use Inc\Controllers\BaseController;

class Enqueue extends BaseController {
	public function register() {
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue' ] );
	}

	/**
	 * Admin Enqueue scripts
	 */
	public function admin_enqueue() {
		// enable wp builtin media upload used in MediaWidget.php
		wp_enqueue_script( 'media-upload' );
		wp_enqueue_media();

		// override
		wp_enqueue_style( 'peachCoreStyle', $this->plugin_url . 'assets/override.css', [], null );
		wp_enqueue_script( 'peachCoreScript', $this->plugin_url . 'assets/override.js', [], null );
	}

	/**
	 * WP Enqueue scripts
	 */
	public function enqueue()
	{
		wp_register_style('bnplChequePaymentCss', BNPL_URL . '/assets/cheque-payment.css', [], BuyNowPayLaterVersion);
		wp_register_style('bnplTailwindCss', BNPL_URL . '/assets/dist/output.css', [], BuyNowPayLaterVersion);
		wp_register_script('chequePaymentScript', BNPL_URL . '/assets/cheque-payment.js', ['jquery'], BuyNowPayLaterVersion);

		wp_enqueue_script('bnplTailwindCssCdn', 'https://cdn.tailwindcss.com', [], null);
		wp_enqueue_style('bnplTailwindCss');
		// wp_enqueue_style('bnplChequePaymentCss');
		wp_enqueue_script('chequePaymentScript');
		wp_localize_script(
			'chequePaymentScript',
			'ajax_obj',
			['ajax_url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce()]
		);
	}
}
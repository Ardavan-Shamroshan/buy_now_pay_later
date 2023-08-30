<?php

namespace Inc\Controllers;

class BaseController {
	public string $plugin_path;
	public string $plugin_url;
	public string $plugin;
	public array $setting_managers = [];

	public function __construct() {
		$this->plugin_path = plugin_dir_path( dirname( __FILE__, 2 ) );
		$this->plugin_url  = plugin_dir_url( dirname( __FILE__, 2 ) );
		$this->plugin      = plugin_basename( dirname( __FILE__, 3 ) . '/buyNowPayLater.php' );

		// settings manager for dynamically create custom checkbox fields
		$this->setting_managers = [];
	}

	public function activated( string $option_name ) {
		$option = get_option( 'buy_now_pay_later' );

		// if there was an option with the option_name ($option), then if there was an option with option_name value check the checkbox
		return $option && $option[ $option_name ];
	}
}
<?php

namespace Inc;

use Inc\Controllers\BuyNowPayLaterController;

final class Init {
	/**
	 * Loop through the classes, initialize them, and call the register method if exists
	 * @return void
	 */
	public static function register_services() {
		foreach ( self::get_services() as $class ) {
			$service = self::instance( $class );
			if ( method_exists( $service, 'register' ) ) {
				$service->register();
			}
		}
	}

	/**
	 * Store all the classes in an array
	 * @return string[]
	 */
	public static function get_services(): array {
		return [
			Pages\Dashboard::class,
			Base\Enqueue::class,
			Base\SettingsLink::class,

			Controllers\BuyNowPayLaterController::class
		];
	}

	/**
	 * Initialize the class
	 *
	 * @param $class
	 *
	 * @return mixed
	 */
	private static function instance( $class ) {
		return new $class;
	}

}
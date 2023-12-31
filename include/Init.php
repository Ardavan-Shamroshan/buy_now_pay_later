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
			Base\Enqueue::class,
			Base\SettingsLink::class,

			Controllers\GatewayHooksController::class,
			Controllers\BuyNowPayLaterController::class,
			Controllers\OrderMetaBoxController::class,
			Controllers\MyAccountHooksController::class,
			Controllers\ShortCodeController::class
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
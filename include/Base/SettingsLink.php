<?php

namespace Inc\Base;

use Inc\Controllers\BaseController;

class SettingsLink extends BaseController {
	public function register() {
		add_filter("plugin_action_links_$this->plugin", [$this, 'settings_link']);
	}

	public function settings_link( $links ) {
		$settings_link = "<a href='admin.php?page=themedoni-buy-now-pay-later'>تنظیمات</a>";
		$links[]       = $settings_link;

		return $links;
	}

}
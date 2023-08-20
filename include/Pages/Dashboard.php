<?php

namespace Inc\Pages;

use Inc\Api\Callbacks\AdminCallbacks;
use Inc\Api\Callbacks\ManagerCallbacks;
use Inc\Api\Settings;
use Inc\Controllers\BaseController;

class Dashboard extends BaseController
{
	public Settings $settings;
	public AdminCallbacks $callbacks;
	public ManagerCallbacks $callbacks_manager;
	public array $pages;

	public function register()
	{
		$this->settings          = new Settings();
		$this->callbacks         = new AdminCallbacks();
		$this->callbacks_manager = new ManagerCallbacks();

		// menu, submenu pages
		$this->set_pages();

		// custom fields
		$this->set_settings();
		$this->set_sections();
		$this->set_fields();

		// $this->settings
		// 	->add_pages($this->pages)
		// 	->with_subpage('درگاه پرداخت اقساطی')
		// 	->register();
	}

	/**
	 * set admin menu, submenu pages
	 */

	// initialize (array) pages
	public function set_pages()
	{
		$this->pages = [
			[
				'page_title' => 'درگاه پرداخت اقساطی',
				'menu_title' => 'درگاه پرداخت اقساطی',
				'capability' => 'manage_options',
				'menu_slug'  => 'themedoni-buy-now-pay-later',
				'callback'   => [$this->callbacks, 'admin_dashboard'],
				'icon_url'   => 'dashicons-store',
				'position'   => 25
			]
		];
	}

	/**
	 * Register custom fields
	 */

	// set custom fields settings
	public function set_settings()
	{
		$args[] = [
			'option_group' => 'themedoni_buy_now_pay_later_settings', // option group name
			'option_name'  => 'themedoni_buy_now_pay_later', // option_name stores in wp_options
			'callback'     => [$this->callbacks_manager, 'checkbox_sanitize'],
		];

		$this->settings->set_settings($args);
	}

	// set custom fields sections
	public function set_sections()
	{
		$args = [
			[
				'id'       => 'themedoni_buy_now_pay_later_admin_section',
				'title'    => 'تنظیمات افزونه',
				'callback' => [$this->callbacks_manager, 'admin_section_manager'],
				'page'     => 'themedoni-buy-now-pay-later' // based on page slug
			]
		];

		$this->settings->set_sections($args);
	}

	// set custom fields input fields
	public function set_fields()
	{
		$args = [];
		foreach ($this->setting_managers as $key => $value) {
			$args[] = [
				'id'       => $key,
				'title'    => $value,
				'callback' => [$this->callbacks_manager, 'checkbox_field'],
				'page'     => 'themedoni-buy-now-pay-later', // based on page slug
				'section'  => 'themedoni_buy_now_pay_later_admin_section', // based on section id
				'args'     => ['option_name' => 'themedoni_buy_now_pay_later', 'label_for' => $key, 'class' => 'ui-toggle']
			];
		}

		$this->settings->set_fields($args);
	}
}

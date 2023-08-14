<?php

namespace Inc\Api;

class Settings {
	public array $admin_pages = [];
	public array $admin_subpages = [];
	public array $settings = [];
	public array $sections = [];
	public array $fields = [];

	public function register() {
		// admin menu
		if ( ! empty( $this->admin_pages ) || ! empty( $this->admin_subpages ) ) {
			add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
		}

		// admin init, register custom fields
		if ( ! empty( $this->settings ) ) {
			add_action( 'admin_init', [ $this, 'register_custom_fields' ] );
		}
	}

	/**
	 * WP Admin menus management
	 */

	// initialize admin menu pages
	public function add_pages( array $pages ): Settings {
		$this->admin_pages = $pages;

		return $this;
	}

	// initialize the first submenu
	public function with_subpage( string $title = null ): Settings {
		// return with no corruption if there is no admin page
		if ( empty( $this->admin_pages ) ) {
			return $this;
		}

		// get the first admin menu page
		$admin_page = $this->admin_pages[0];

		// initial submenu page
		$subpages = [
			[
				'parent_slug' => $admin_page['menu_slug'],
				'page_title'  => $admin_page['page_title'],
				'menu_title'  => $title ?? $admin_page['menu_title'],
				'capability'  => $admin_page['capability'],
				'menu_slug'   => $admin_page['menu_slug'],
				'callback'    => $admin_page['callback'],
			]
		];

		// add it to admin submenu pages
		$this->admin_subpages = $subpages;

		return $this;
	}

	// add submenu pages
	public function add_subpages( array $pages ): Settings {
		$this->admin_subpages = array_merge( $this->admin_subpages, $pages );

		return $this;
	}

	// add menu page and submenu page dynamically from (arrays) admin_pages, admin_subpages
//	private function add_admin_menu() {
	public function add_admin_menu() {
		foreach ( $this->admin_pages as $page ) {
			add_menu_page(
				$page['page_title'],
				$page['menu_title'],
				$page['capability'],
				$page['menu_slug'],
				$page['callback'],
				$page['icon_url'],
				$page['position'],
			);
		}

		foreach ( $this->admin_subpages as $page ) {
			add_submenu_page(
				$page['parent_slug'],
				$page['page_title'],
				$page['menu_title'],
				$page['capability'],
				$page['menu_slug'],
				$page['callback'],
			);
		}
	}

	/**
	 * Register WP Admin Custom fields
	 */

	// set settings
	public function set_settings( array $settings ): Settings {
		$this->settings = $settings;

		return $this;
	}

	// set form sections
	public function set_sections( array $sections ): Settings {
		$this->sections = $sections;

		return $this;
	}

	// set forms fields
	public function set_fields( array $fields ): Settings {
		$this->fields = $fields;

		return $this;
	}

	// register settings, sections, fields
	public function register_custom_fields() {

		// register setting
		foreach ( $this->settings as $setting ) {
			register_setting( $setting["option_group"], $setting["option_name"], ( $setting["callback"] ?? '' ) );
		}

		// add settings section
		foreach ( $this->sections as $section ) {
			add_settings_section( $section["id"], $section["title"], ( $section["callback"] ?? '' ), $section["page"] );
		}

		// add settings field
		foreach ( $this->fields as $field ) {
			add_settings_field( $field["id"], $field["title"], ( $field["callback"] ?? '' ), $field["page"], $field["section"], ( $field["args"] ?? '' ) );
		}
	}
}
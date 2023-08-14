<?php

namespace Inc\Controllers;

use Inc\Api\Callbacks\AdminCallbacks;
use Inc\Api\Callbacks\CustomTaxonomyCallbacks;
use Inc\Api\Settings;

class CustomTaxonomyController extends BaseController {
	public Settings $settings;
	public AdminCallbacks $callbacks;
	public CustomTaxonomyCallbacks $taxonomy_callbacks;

	public array $subpages = [];
	public array $taxonomies = [];

	public function register() {
		if ( ! $this->activated( 'taxonomy_manager' ) ) {
			return;
		}

		$this->settings           = new Settings();
		$this->callbacks          = new AdminCallbacks();
		$this->taxonomy_callbacks = new CustomTaxonomyCallbacks();

		// menu, submenu pages
		$this->set_subpages();

		$this->set_settings();
		$this->set_sections();
		$this->set_fields();

		$this->settings
			->add_subpages( $this->subpages )
			->register();

		$this->store_custom_taxonomies();

		if ( ! empty( $this->taxonomies ) ) {
			add_action( 'init', [ $this, 'register_custom_taxonomy' ] );
		}
	}

	public function set_subpages() {
		$this->subpages = [
			[
				'parent_slug' => 'peach-core',
				'page_title'  => 'دسته بندی اختصاصی',
				'menu_title'  => 'دسته بندی اختصاصی',
				'capability'  => 'manage_options',
				'menu_slug'   => 'peach-core-custom-taxonomy-submenu',
				'callback'    => [ $this->callbacks, 'custom_taxonomy_type' ]
			]
		];
	}

	public function set_settings() {
		$args = [
			[
				'option_group' => 'peach_core_plugin_taxonomy_settings',
				'option_name'  => 'peach_core_plugin_taxonomy',
				'callback'     => [ $this->taxonomy_callbacks, 'taxonomy_sanitize' ]
			]
		];

		$this->settings->set_settings( $args );
	}

	public function set_sections() {
		$args = [
			[
				'id'       => 'peach_taxonomy_index',
				'title'    => 'دسته بندی اختصاصی',
				'callback' => [ $this->taxonomy_callbacks, 'taxonomy_section_manager' ],
				'page'     => 'peach-core-custom-taxonomy-submenu',
			]
		];


		$this->settings->set_sections( $args );
	}

	// set custom fields sections
	public function set_fields() {

		// post type id, singular name, plural name, public, has_archive
		$args = [
			[
				'id'       => 'taxonomy',
				'title'    => 'شناسه دسته بندی اختصاصی',
				'callback' => [ $this->taxonomy_callbacks, 'text_field' ],
				'page'     => 'peach-core-custom-taxonomy-submenu', // based on menu/submenu slug
				'section'  => 'peach_taxonomy_index', // based on section id
				'args'     => [
					'option_name' => 'peach_core_plugin_taxonomy', // based on setting option_name
					'label_for'   => 'taxonomy',
					'placeholder' => 'مثال: genre',
					'array'       => 'taxonomy'
				]
			],
			[
				'id'       => 'singular_name',
				'title'    => 'نام مفرد',
				'callback' => [ $this->taxonomy_callbacks, 'text_field' ],
				'page'     => 'peach-core-custom-taxonomy-submenu', // based on menu/submenu slug
				'section'  => 'peach_taxonomy_index', // based on section id
				'args'     => [
					'option_name' => 'peach_core_plugin_taxonomy', // based on setting option_name
					'label_for'   => 'singular_name',
					'placeholder' => 'مثال: ژانر',
					'array'       => 'taxonomy'
				]
			],
			[
				'id'       => 'hierarchical',
				'title'    => 'سلسله مراتب',
				'callback' => [ $this->taxonomy_callbacks, 'checkbox_field' ],
				'page'     => 'peach-core-custom-taxonomy-submenu', // based on menu/submenu slug
				'section'  => 'peach_taxonomy_index', // based on section id
				'args'     => [
					'option_name' => 'peach_core_plugin_taxonomy', // based on setting option_name
					'label_for'   => 'hierarchical',
					'class'       => 'ui-toggle',
					'array'       => 'taxonomy'
				]
			],
			[
				'id'       => 'objects',
				'title'    => 'نوع پست های اختصاصی',
				'callback' => [ $this->taxonomy_callbacks, 'checkbox_post_type_field' ],
				'page'     => 'peach-core-custom-taxonomy-submenu', // based on menu/submenu slug
				'section'  => 'peach_taxonomy_index', // based on section id
				'args'     => [
					'option_name' => 'peach_core_plugin_taxonomy', // based on setting option_name
					'label_for'   => 'objects',
					'class'       => 'ui-toggle',
					'array'       => 'taxonomy'
				]
			],
		];

		$this->settings->set_fields( $args );
	}

	public function store_custom_taxonomies() {
		$options = get_option( 'peach_core_plugin_taxonomy' ) ?: [];

		foreach ( $options as $option ) {
			$labels = [
				'name'              => $option['singular_name'],
				'singular_name'     => $option['singular_name'],
				'search_items'      => 'Search ' . $option['singular_name'],
				'all_items'         => 'All ' . $option['singular_name'],
				'parent_item'       => 'Parent ' . $option['singular_name'],
				'parent_item_colon' => 'Parent ' . $option['singular_name'] . ':',
				'edit_item'         => 'Edit ' . $option['singular_name'],
				'update_item'       => 'Update ' . $option['singular_name'],
				'add_new_item'      => 'Add New ' . $option['singular_name'],
				'new_item_name'     => 'New ' . $option['singular_name'] . ' Name',
				'menu_name'         => $option['singular_name'],
			];

			$this->taxonomies[] = [
				'hierarchical'      => isset( $option['hierarchical'] ),
				'labels'            => $labels,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'show_in_rest'      => true,
				'rewrite'           => [ 'slug' => $option['taxonomy'] ],
				'objects'           => $option['objects'] ?? null
			];
		}
	}

	public function register_custom_taxonomy() {
		foreach ( $this->taxonomies as $taxonomy ) {
			// register taxonomy args: name of taxonomy, [post types], taxonomy args
			$objects = isset( $taxonomy['objects'] ) ? array_keys( $taxonomy['objects'] ) : null;

			register_taxonomy( $taxonomy['rewrite']['slug'], $objects, $taxonomy );
		}
	}
}
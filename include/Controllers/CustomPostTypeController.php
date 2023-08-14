<?php

namespace Inc\Controllers;

use Inc\Api\Callbacks\AdminCallbacks;
use Inc\Api\Callbacks\CustomPostTypeCallbacks;
use Inc\Api\Settings;

class CustomPostTypeController extends BaseController {
	public Settings $settings;
	public AdminCallbacks $callbacks;
	public CustomPostTypeCallbacks $cpt_callbacks;

	public array $subpages = [];

	public array $custom_post_types = [];

	public function register() {
		if ( ! $this->activated( 'cpt_manager' ) ) {
			return;
		}

		$this->settings      = new Settings();
		$this->callbacks     = new AdminCallbacks();
		$this->cpt_callbacks = new CustomPostTypeCallbacks();

		// menu, submenu pages
		$this->set_subpages();

		// create input fields
		$this->set_settings();
		$this->set_sections();
		$this->set_fields();


		$this->settings
			->add_subpages( $this->subpages )
			->register();

		// store POST
		$this->store_custom_post_type();

		if ( ! empty( $this->custom_post_types ) ) {
			add_action( 'init', [ $this, 'register_custom_post_type' ] );
		}
	}


	public function set_settings() {
		$args = [
			[
				'option_group' => 'peach_core_plugin_cpt_settings',
				'option_name'  => 'peach_core_plugin_cpt',
				'callback'     => [ $this->cpt_callbacks, 'cpt_sanitize' ]
			]
		];

		$this->settings->set_settings( $args );
	}

	/**
	 * Register custom fields
	 */

	// set custom fields settings
	public function set_sections() {
		$args = [
			[
				'id'       => 'peach_cpt_index',
				'title'    => 'نوع پست اختصاصی',
				'callback' => [ $this->cpt_callbacks, 'cpt_section_manager' ],
				'page'     => 'peach-core-custom-post-type-submenu' // based on menu/submenu slug
			]
		];

		$this->settings->set_sections( $args );
	}

	// set custom fields sections
	public function set_fields() {

		// post type id, singular name, plural name, public, has_archive
		$args = [
			[
				'id'       => 'post_type',
				'title'    => 'شناسه نوع پست اختصاصی',
				'callback' => [ $this->cpt_callbacks, 'text_field' ],
				'page'     => 'peach-core-custom-post-type-submenu', // based on menu/submenu slug
				'section'  => 'peach_cpt_index', // based on section id
				'args'     => [
					'option_name' => 'peach_core_plugin_cpt', // based on setting option_name
					'label_for'   => 'post_type',
					'placeholder' => 'مثال: product',
					'array'       => 'post_type'
				]
			],
			[
				'id'       => 'name',
				'title'    => 'نام جمع',
				'callback' => [ $this->cpt_callbacks, 'text_field' ],
				'page'     => 'peach-core-custom-post-type-submenu',
				'section'  => 'peach_cpt_index',
				'args'     => [
					'option_name' => 'peach_core_plugin_cpt',
					'label_for'   => 'name',
					'placeholder' => 'مثال: محصولات',
					'array'       => 'post_type'
				]
			],
			[
				'id'       => 'singular_name',
				'title'    => 'نام مفرد',
				'callback' => [ $this->cpt_callbacks, 'text_field' ],
				'page'     => 'peach-core-custom-post-type-submenu',
				'section'  => 'peach_cpt_index',
				'args'     => [
					'option_name' => 'peach_core_plugin_cpt',
					'label_for'   => 'singular_name',
					'placeholder' => 'مثال: محصول',
					'array'       => 'post_type'
				]
			],
			[
				'id'       => 'public',
				'title'    => 'عمومی',
				'callback' => [ $this->cpt_callbacks, 'checkbox_field' ],
				'page'     => 'peach-core-custom-post-type-submenu',
				'section'  => 'peach_cpt_index',
				'args'     => [
					'option_name' => 'peach_core_plugin_cpt',
					'label_for'   => 'public',
					'class'       => 'ui-toggle',
					'array'       => 'post_type'
				]
			],
			[
				'id'       => 'has_archive',
				'title'    => 'دارای آرشیو',
				'callback' => [ $this->cpt_callbacks, 'checkbox_field' ],
				'page'     => 'peach-core-custom-post-type-submenu',
				'section'  => 'peach_cpt_index',
				'args'     => [
					'option_name' => 'peach_core_plugin_cpt',
					'label_for'   => 'has_archive',
					'class'       => 'ui-toggle',
					'array'       => 'post_type'
				]
			]
		];

		$this->settings->set_fields( $args );
	}

	// set custom fields input fields

	/**
	 * Define submenu page
	 */
	public function set_subpages() {
		$this->subpages = [
			[
				'parent_slug' => 'peach-core',
				'page_title'  => 'پست اختصاصی',
				'menu_title'  => 'پست اختصاصی',
				'capability'  => 'manage_options',
				'menu_slug'   => 'peach-core-custom-post-type-submenu',
				'callback'    => [ $this->callbacks, 'custom_post_type' ]
			]
		];
	}

	public function store_custom_post_type() {

		$options = get_option( 'peach_core_plugin_cpt' ) ?: [];

		foreach ( $options as $option ) {
			$this->custom_post_types[] = [
				'post_type'             => $option['post_type'],
				'name'                  => $option['name'],
				'singular_name'         => $option['singular_name'],
				'menu_name'             => $option['name'],
				'name_admin_bar'        => $option['singular_name'],
				'archives'              => $option['singular_name'] . ' Archives',
				'attributes'            => $option['singular_name'] . ' Attributes',
				'parent_item_colon'     => 'Parent ' . $option['singular_name'],
				'all_items'             => 'All ' . $option['name'],
				'add_new_item'          => 'Add New ' . $option['singular_name'],
				'add_new'               => 'Add New',
				'new_item'              => 'New ' . $option['singular_name'],
				'edit_item'             => 'Edit ' . $option['singular_name'],
				'update_item'           => 'Update ' . $option['singular_name'],
				'view_item'             => 'View ' . $option['singular_name'],
				'view_items'            => 'View ' . $option['name'],
				'search_items'          => 'Search ' . $option['name'],
				'not_found'             => 'No ' . $option['singular_name'] . ' Found',
				'not_found_in_trash'    => 'No ' . $option['singular_name'] . ' Found in Trash',
				'featured_image'        => 'Featured Image',
				'set_featured_image'    => 'Set Featured Image',
				'remove_featured_image' => 'Remove Featured Image',
				'use_featured_image'    => 'Use Featured Image',
				'insert_into_item'      => 'Insert into ' . $option['singular_name'],
				'uploaded_to_this_item' => 'Upload to this ' . $option['singular_name'],
				'items_list'            => $option['name'] . ' List',
				'items_list_navigation' => $option['name'] . ' List Navigation',
				'filter_items_list'     => 'Filter' . $option['name'] . ' List',
				'label'                 => $option['singular_name'],
				'description'           => $option['name'] . 'Custom Post Type',
				'supports'              => [ 'title', 'editor', 'thumbnail' ],
				'show_in_rest'          => true,
				'taxonomies'            => [ 'category', 'post_tag' ],
				'hierarchical'          => false,
				'public'                => isset( $option['public'] ) || false,
				'show_ui'               => true,
				'show_in_menu'          => true,
				'menu_position'         => 5,
				'show_in_admin_bar'     => true,
				'show_in_nav_menus'     => true,
				'can_export'            => true,
				'has_archive'           => isset( $option['has_archive'] ) || false,
				'exclude_from_search'   => false,
				'publicly_queryable'    => true,
				'capability_type'       => 'post'
			];

		}

	}

	public function register_custom_post_type() {
		foreach ( $this->custom_post_types as $post_type ) {
			register_post_type( $post_type['post_type'],
				[
					'labels'              => [
						'name'                  => $post_type['name'],
						'singular_name'         => $post_type['singular_name'],
						'menu_name'             => $post_type['menu_name'],
						'name_admin_bar'        => $post_type['name_admin_bar'],
						'archives'              => $post_type['archives'],
						'attributes'            => $post_type['attributes'],
						'parent_item_colon'     => $post_type['parent_item_colon'],
						'all_items'             => $post_type['all_items'],
						'add_new_item'          => $post_type['add_new_item'],
						'add_new'               => $post_type['add_new'],
						'new_item'              => $post_type['new_item'],
						'edit_item'             => $post_type['edit_item'],
						'update_item'           => $post_type['update_item'],
						'view_item'             => $post_type['view_item'],
						'view_items'            => $post_type['view_items'],
						'search_items'          => $post_type['search_items'],
						'not_found'             => $post_type['not_found'],
						'not_found_in_trash'    => $post_type['not_found_in_trash'],
						'featured_image'        => $post_type['featured_image'],
						'set_featured_image'    => $post_type['set_featured_image'],
						'remove_featured_image' => $post_type['remove_featured_image'],
						'use_featured_image'    => $post_type['use_featured_image'],
						'insert_into_item'      => $post_type['insert_into_item'],
						'uploaded_to_this_item' => $post_type['uploaded_to_this_item'],
						'items_list'            => $post_type['items_list'],
						'items_list_navigation' => $post_type['items_list_navigation'],
						'filter_items_list'     => $post_type['filter_items_list']
					],
					'label'               => $post_type['label'],
					'description'         => $post_type['description'],
					'supports'            => $post_type['supports'],
					'show_in_rest'        => true,
					'taxonomies'          => $post_type['taxonomies'],
					'hierarchical'        => $post_type['hierarchical'],
					'public'              => $post_type['public'],
					'show_ui'             => $post_type['show_ui'],
					'show_in_menu'        => $post_type['show_in_menu'],
					'menu_position'       => $post_type['menu_position'],
					'show_in_admin_bar'   => $post_type['show_in_admin_bar'],
					'show_in_nav_menus'   => $post_type['show_in_nav_menus'],
					'can_export'          => $post_type['can_export'],
					'has_archive'         => $post_type['has_archive'],
					'exclude_from_search' => $post_type['exclude_from_search'],
					'publicly_queryable'  => $post_type['publicly_queryable'],
					'capability_type'     => $post_type['capability_type']
				]
			);
		}
	}
}
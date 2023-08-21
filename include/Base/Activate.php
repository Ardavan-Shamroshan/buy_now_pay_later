<?php

namespace Inc\Base;

class Activate {
	public static function activate() {
		flush_rewrite_rules();

		// if there was no option with the given key, update the key with an empty array
		if ( ! get_option( 'themedoni_buy_now_pay_later' ) ) {
			update_option( 'themedoni_buy_now_pay_later', [] );
		}

		add_filter( 'init', 'themedoni_bnpl_wc_register_post_statuses' );
	}

	// Register New Order Statuses
	public function themedoni_bnpl_wc_register_post_statuses() {
		register_post_status( 'wc-cheque-progress', array(
			'label'                     => _x( 'در انتظار تایید چک', 'WooCommerce Order status', 'text_domain' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Approved (%s)', 'Approved (%s)', 'text_domain' )
		) );
	}
}

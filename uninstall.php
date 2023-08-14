<?php

/**
 * Trigger this file on plugin uninstall
 *
 * @package PeachCore
 */

// Die if accessed externally
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Clear Database
//$books = get_posts( [ 'post_type' => 'book', 'numberposts' => - 1 ] );
//foreach ( $books as $book ) {
//	wp_delete_post( $book->ID, true );
//}

// WordPress database abstraction object.
global $wpdb;
$wpdb->prepare(
	"DELETE FROM `wp_posts` WHERE `post_type` = %s",
	'book'
);

$wpdb->query( "DELETE FROM `wp_postmeta` WHERE `post_id` NOT IN (SELECT `id` FROM wp_posts)" );

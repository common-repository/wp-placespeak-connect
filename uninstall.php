<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       http://tempranova.com
 * @since      1.0.0
 *
 * @package    comment-load-more
 */

// exit if WordPress is not uninstalling the plugin.
if ( ! defined( 'ABSPATH' ) && ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

/* 
 * Making WPDB as global
 * to access database information.
 */
global $wpdb;

/* 
 * @var $table_name 
 * name of table to be dropped
 * prefixed with $wpdb->prefix from the database
 */

if ( is_multisite() ) {
    // Note: if there are more than 10,000 blogs or
    // if `wp_is_large_network` filter is set, then this may fail.
    $sites = wp_get_sites();
    foreach ( $sites as $site ) {
        switch_to_blog( $site['blog_id'] );
        $table_name = $wpdb->prefix . 'placespeak';
        // drop the table from the database.
        $wpdb->query( "DROP TABLE IF EXISTS $table_name" );
        restore_current_blog();
    }
} else {
    $table_name = $wpdb->prefix . 'placespeak';
    // drop the table from the database.
    $wpdb->query( "DROP TABLE IF EXISTS $table_name" );
}




$option_commenter_metadata = 'placespeak_commenter_metadata';
$option_user_storage = 'placespeak_user_storage';

if ( !is_multisite() ) 
{
    delete_option( $option_commenter_metadata );
    delete_option( $option_user_storage );
} 
else 
{
    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
    $original_blog_id = get_current_blog_id();

    foreach ( $blog_ids as $blog_id ) 
    {
        switch_to_blog( $blog_id );
        delete_option( $option_commenter_metadata );
        delete_option( $option_user_storage );     

    }

    switch_to_blog( $original_blog_id );
}
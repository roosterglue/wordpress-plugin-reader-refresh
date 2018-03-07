<?php
/*
 * Plugin Name: Reader Refresh
 * Version: 1.0
 * Plugin URI: http://www.google.com/
 * Description: Automatically refresh your site's page
 * Author: Rooster Glue
 * Author URI: http://www.google.com/
 * Requires at least: 4.0
 * Tested up to: 4.0
 *
 * Text Domain: reader-refresh
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Rooster Glue
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Load plugin class files
require_once( 'includes/class-reader-refresh.php' );
require_once( 'includes/class-reader-refresh-settings.php' );

// Load plugin libraries
require_once( 'includes/lib/class-reader-refresh-admin-api.php' );
require_once( 'includes/lib/class-reader-refresh-post-type.php' );
require_once( 'includes/lib/class-reader-refresh-taxonomy.php' );

/**
 * Returns the main instance of Reader_Refresh to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Reader_Refresh
 */
function Reader_Refresh () {
	$instance = Reader_Refresh::instance( __FILE__, '1.0.0' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = Reader_Refresh_Settings::instance( $instance );
	}

	return $instance;
}

Reader_Refresh();

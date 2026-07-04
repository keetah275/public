<?php
/**
 * Plugin Name: Gymcast Marketing Tools
 * Description: Site-specific Gutenberg patterns and styling for Gymcast marketing resources.
 * Version: 0.1.0
 * Author: Gymcast
 * Text Domain: gymcast-marketing-tools
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GYMCAST_MARKETING_TOOLS_VERSION', '0.1.0' );
define( 'GYMCAST_MARKETING_TOOLS_PATH', plugin_dir_path( __FILE__ ) );
define( 'GYMCAST_MARKETING_TOOLS_URL', plugin_dir_url( __FILE__ ) );

require_once GYMCAST_MARKETING_TOOLS_PATH . 'includes/patterns.php';
require_once GYMCAST_MARKETING_TOOLS_PATH . 'includes/schema.php';

/**
 * Enqueue shared pattern styling.
 */
function gymcast_marketing_tools_enqueue_styles() {
	$asset_path = GYMCAST_MARKETING_TOOLS_PATH . 'assets/css/gymcast-marketing.css';
	$asset_url  = GYMCAST_MARKETING_TOOLS_URL . 'assets/css/gymcast-marketing.css';
	$version    = file_exists( $asset_path ) ? filemtime( $asset_path ) : GYMCAST_MARKETING_TOOLS_VERSION;

	wp_enqueue_style(
		'gymcast-marketing-tools',
		$asset_url,
		array(),
		$version
	);
}
add_action( 'wp_enqueue_scripts', 'gymcast_marketing_tools_enqueue_styles' );
add_action( 'enqueue_block_editor_assets', 'gymcast_marketing_tools_enqueue_styles' );

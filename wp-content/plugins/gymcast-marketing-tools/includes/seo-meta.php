<?php
/**
 * SEO title and meta description fields for Gymcast Resource Guides.
 *
 * @package GymcastMarketingTools
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check whether the current user may edit a post's Gymcast SEO fields.
 *
 * @param bool   $allowed Whether access is currently allowed.
 * @param string $meta_key Meta key.
 * @param int    $post_id Post ID.
 * @return bool
 */
function gymcast_marketing_tools_can_edit_seo_meta( $allowed, $meta_key, $post_id ) {
	return current_user_can( 'edit_post', $post_id );
}

/**
 * Register SEO fields so Gutenberg can read and save them through REST.
 */
function gymcast_marketing_tools_register_seo_meta() {
	$fields = array(
		'_gymcast_meta_title'       => 'sanitize_text_field',
		'_gymcast_meta_description' => 'sanitize_textarea_field',
	);

	foreach ( array( 'post', 'page' ) as $post_type ) {
		foreach ( $fields as $key => $sanitize_callback ) {
			register_post_meta(
				$post_type,
				$key,
				array(
					'type'              => 'string',
					'single'            => true,
					'default'           => '',
					'show_in_rest'      => true,
					'sanitize_callback' => $sanitize_callback,
					'auth_callback'     => 'gymcast_marketing_tools_can_edit_seo_meta',
				)
			);
		}
	}
}
add_action( 'init', 'gymcast_marketing_tools_register_seo_meta' );

/**
 * Enqueue the Resource Guide SEO sidebar panel.
 */
function gymcast_marketing_tools_enqueue_seo_editor() {
	$script_path = GYMCAST_MARKETING_TOOLS_PATH . 'assets/js/seo-meta.js';

	wp_enqueue_script(
		'gymcast-marketing-tools-seo-editor',
		GYMCAST_MARKETING_TOOLS_URL . 'assets/js/seo-meta.js',
		array( 'wp-components', 'wp-data', 'wp-edit-post', 'wp-element', 'wp-i18n', 'wp-plugins' ),
		file_exists( $script_path ) ? filemtime( $script_path ) : GYMCAST_MARKETING_TOOLS_VERSION,
		true
	);
}
add_action( 'enqueue_block_editor_assets', 'gymcast_marketing_tools_enqueue_seo_editor' );

/**
 * Use the optional Resource Guide SEO title as the document title.
 *
 * @param string $title Existing pre-generated title.
 * @return string
 */
function gymcast_marketing_tools_filter_document_title( $title ) {
	if ( ! is_singular( array( 'post', 'page' ) ) ) {
		return $title;
	}

	$post = get_queried_object();
	if ( ! $post instanceof WP_Post || ! gymcast_marketing_tools_is_resource_article( $post ) ) {
		return $title;
	}

	$meta_title = trim( (string) get_post_meta( $post->ID, '_gymcast_meta_title', true ) );
	return '' !== $meta_title ? $meta_title : $title;
}
add_filter( 'pre_get_document_title', 'gymcast_marketing_tools_filter_document_title' );

/**
 * Output the optional Resource Guide meta description.
 */
function gymcast_marketing_tools_output_meta_description() {
	if ( ! is_singular( array( 'post', 'page' ) ) ) {
		return;
	}

	$post = get_queried_object();
	if ( ! $post instanceof WP_Post || ! gymcast_marketing_tools_is_resource_article( $post ) ) {
		return;
	}

	$description = trim( (string) get_post_meta( $post->ID, '_gymcast_meta_description', true ) );
	if ( '' !== $description ) {
		printf( "\n<meta name=\"description\" content=\"%s\" />\n", esc_attr( $description ) );
	}
}
add_action( 'wp_head', 'gymcast_marketing_tools_output_meta_description', 1 );

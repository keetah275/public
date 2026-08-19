<?php
/**
 * Plugin Name: Gymcast Marketing Tools
 * Description: Site-specific Gutenberg patterns and styling for Gymcast marketing resources.
 * Version: 0.4.0
 * Author: Gymcast
 * Text Domain: gymcast-marketing-tools
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GYMCAST_MARKETING_TOOLS_VERSION', '0.4.0' );
define( 'GYMCAST_MARKETING_TOOLS_PATH', plugin_dir_path( __FILE__ ) );
define( 'GYMCAST_MARKETING_TOOLS_URL', plugin_dir_url( __FILE__ ) );

require_once GYMCAST_MARKETING_TOOLS_PATH . 'includes/patterns.php';
require_once GYMCAST_MARKETING_TOOLS_PATH . 'includes/schema.php';
require_once GYMCAST_MARKETING_TOOLS_PATH . 'includes/related-guides.php';
require_once GYMCAST_MARKETING_TOOLS_PATH . 'includes/faqs.php';

/**
 * Register the automatic table of contents block used by resource guides.
 */
function gymcast_marketing_tools_register_blocks() {
	$script_path = GYMCAST_MARKETING_TOOLS_PATH . 'assets/js/table-of-contents.js';

	wp_register_script(
		'gymcast-marketing-tools-toc-editor',
		GYMCAST_MARKETING_TOOLS_URL . 'assets/js/table-of-contents.js',
		array( 'wp-block-editor', 'wp-blocks', 'wp-components', 'wp-data', 'wp-element', 'wp-i18n' ),
		file_exists( $script_path ) ? filemtime( $script_path ) : GYMCAST_MARKETING_TOOLS_VERSION,
		true
	);

	register_block_type(
		'gymcast/table-of-contents',
		array(
			'api_version'     => 3,
			'editor_script'   => 'gymcast-marketing-tools-toc-editor',
			'render_callback' => 'gymcast_marketing_tools_render_table_of_contents',
		)
	);
}
add_action( 'init', 'gymcast_marketing_tools_register_blocks' );

/**
 * Render a placeholder which is populated when the enclosing article is rendered.
 *
 * @return string
 */
function gymcast_marketing_tools_render_table_of_contents() {
	return '<div class="gc-toc" data-gymcast-toc="true"></div>';
}

/**
 * Build unique anchors for a collection of heading elements.
 *
 * @param DOMNodeList<DOMElement> $headings Heading nodes.
 * @return array<int, array{title:string, anchor:string}>
 */
function gymcast_marketing_tools_prepare_toc_headings( $headings ) {
	$items = array();
	$used  = array();

	foreach ( $headings as $heading ) {
		$title = trim( wp_strip_all_tags( $heading->textContent ) );
		if ( '' === $title ) {
			continue;
		}

		$base   = $heading->getAttribute( 'id' );
		$base   = $base ? sanitize_title( $base ) : sanitize_title( $title );
		$base   = $base ? $base : 'section';
		$anchor = $base;
		$suffix = 2;

		while ( isset( $used[ $anchor ] ) ) {
			$anchor = $base . '-' . $suffix;
			++$suffix;
		}

		$used[ $anchor ] = true;
		$heading->setAttribute( 'id', $anchor );
		$items[] = array(
			'title'  => $title,
			'anchor' => $anchor,
		);
	}

	return $items;
}

/**
 * Keep contents lists and H2 anchors in sync on the front end.
 *
 * This also upgrades resource guides created with the original static pattern.
 *
 * @param string $block_content Rendered block markup.
 * @param array  $block         Parsed block data.
 * @return string
 */
function gymcast_marketing_tools_sync_resource_guide_toc( $block_content, $block ) {
	if (
		'core/group' !== $block['blockName'] ||
		false === strpos( $block_content, 'gc-resource-article' ) ||
		false === strpos( $block_content, 'gc-toc' ) ||
		! class_exists( 'DOMDocument' )
	) {
		return $block_content;
	}

	$previous = libxml_use_internal_errors( true );
	$document = new DOMDocument( '1.0', 'UTF-8' );
	$loaded   = $document->loadHTML(
		'<?xml encoding="utf-8" ?><div id="gc-render-root">' . $block_content . '</div>',
		LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
	);
	libxml_clear_errors();
	libxml_use_internal_errors( $previous );

	if ( ! $loaded ) {
		return $block_content;
	}

	$xpath = new DOMXPath( $document );
	$main  = $xpath->query( '//*[contains(concat(" ", normalize-space(@class), " "), " gc-main-content ")]' )->item( 0 );

	if ( $main ) {
		$headings = $xpath->query( './/h2', $main );
	} else {
		// Original guides stored article H2s as direct children, before the separator.
		$article  = $xpath->query( '//*[contains(concat(" ", normalize-space(@class), " "), " gc-resource-article ")]' )->item( 0 );
		$legacy   = array();
		$past_toc = false;
		if ( $article ) {
			foreach ( $article->childNodes as $child ) {
				if ( XML_ELEMENT_NODE !== $child->nodeType ) {
					continue;
				}
				if ( false !== strpos( ' ' . $child->getAttribute( 'class' ) . ' ', ' gc-toc ' ) ) {
					$past_toc = true;
					continue;
				}
				if ( $past_toc && 'hr' === strtolower( $child->nodeName ) ) {
					break;
				}
				if ( $past_toc && 'h2' === strtolower( $child->nodeName ) ) {
					$legacy[] = $child;
				}
			}
		}
		$headings = $legacy;
	}

	$items = gymcast_marketing_tools_prepare_toc_headings( $headings );
	$toc   = $xpath->query( '//*[contains(concat(" ", normalize-space(@class), " "), " gc-toc ")]' )->item( 0 );

	if ( ! $toc || empty( $items ) ) {
		return $block_content;
	}

	while ( $toc->firstChild ) {
		$toc->removeChild( $toc->firstChild );
	}

	$heading = $document->createElement( 'h2' );
	$heading->appendChild( $document->createTextNode( __( 'Contents', 'gymcast-marketing-tools' ) ) );
	$heading->setAttribute( 'class', 'wp-block-heading' );
	$toc->appendChild( $heading );
	$list = $document->createElement( 'ul' );

	foreach ( $items as $item ) {
		$list_item = $document->createElement( 'li' );
		$link      = $document->createElement( 'a' );
		$link->appendChild( $document->createTextNode( $item['title'] ) );
		$link->setAttribute( 'href', '#' . $item['anchor'] );
		$list_item->appendChild( $link );
		$list->appendChild( $list_item );
	}
	$toc->appendChild( $list );

	$root   = $document->getElementById( 'gc-render-root' );
	$output = '';
	foreach ( $root->childNodes as $child ) {
		$output .= $document->saveHTML( $child );
	}

	return $output;
}
add_filter( 'render_block', 'gymcast_marketing_tools_sync_resource_guide_toc', 20, 2 );

/**
 * Normalise the main-content Group wrapper to Gutenberg's canonical markup.
 *
 * Version 0.2.0 briefly inserted whitespace between the Group wrapper and its
 * first inner block. Browsers ignore it, but Gutenberg correctly reports the
 * saved Group as invalid. Filtering both edit reads and saves repairs affected
 * posts without requiring users to recover the block manually.
 *
 * @param string $content Post content.
 * @return string
 */
function gymcast_marketing_tools_normalize_main_content_group( $content ) {
	if ( false === strpos( $content, 'gc-main-content' ) ) {
		return $content;
	}

	return preg_replace(
		'/(<div\b[^>]*class=(?:"[^"]*\bgc-main-content\b[^"]*"|\'[^\']*\bgc-main-content\b[^\']*\')[^>]*>)\s+(<!--\s+wp:)/i',
		'$1$2',
		$content
	);
}
add_filter( 'content_edit_pre', 'gymcast_marketing_tools_normalize_main_content_group' );
add_filter( 'content_save_pre', 'gymcast_marketing_tools_normalize_main_content_group' );

/**
 * Normalise raw REST content before Gutenberg parses an existing post.
 *
 * @param WP_REST_Response $response REST response.
 * @return WP_REST_Response
 */
function gymcast_marketing_tools_normalize_rest_post_content( $response ) {
	$data = $response->get_data();

	if ( isset( $data['content']['raw'] ) ) {
		$data['content']['raw'] = gymcast_marketing_tools_normalize_main_content_group( $data['content']['raw'] );
		$response->set_data( $data );
	}

	return $response;
}
add_filter( 'rest_prepare_post', 'gymcast_marketing_tools_normalize_rest_post_content' );
add_filter( 'rest_prepare_page', 'gymcast_marketing_tools_normalize_rest_post_content' );

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

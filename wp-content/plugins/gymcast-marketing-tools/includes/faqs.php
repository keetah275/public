<?php
/**
 * Dynamic FAQ block backed by posts in the FAQ category.
 *
 * @package GymcastMarketingTools
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get or create the FAQ category.
 *
 * @return int FAQ category ID, or zero on failure.
 */
function gymcast_marketing_tools_get_faq_category_id() {
	static $category_id = null;
	if ( null !== $category_id ) {
		return $category_id;
	}

	$faq = get_term_by( 'slug', 'faq', 'category' );
	if ( ! $faq ) {
		$created = wp_insert_term( __( 'FAQ', 'gymcast-marketing-tools' ), 'category', array( 'slug' => 'faq' ) );
		if ( is_wp_error( $created ) ) {
			return 0;
		}
		$faq = get_term( $created['term_id'], 'category' );
	}

	$category_id = $faq && ! is_wp_error( $faq ) ? (int) $faq->term_id : 0;
	return $category_id;
}

/**
 * Ensure the FAQ category exists.
 */
function gymcast_marketing_tools_ensure_faq_category() {
	if ( taxonomy_exists( 'category' ) ) {
		gymcast_marketing_tools_get_faq_category_id();
	}
}
add_action( 'init', 'gymcast_marketing_tools_ensure_faq_category', 20 );

/**
 * Select FAQs using manual choices first and shared tags second.
 *
 * @param int        $post_id         Current article ID.
 * @param array<int> $manual_post_ids Manually selected FAQ IDs.
 * @param bool       $automatic       Whether shared-tag matching is enabled.
 * @param int        $limit           Maximum FAQs.
 * @return array<int>
 */
function gymcast_marketing_tools_get_faq_ids( $post_id, $manual_post_ids = array(), $automatic = true, $limit = 4 ) {
	$category_id = gymcast_marketing_tools_get_faq_category_id();
	if ( ! $category_id ) {
		return array();
	}

	$selected = array();
	$manual   = array_values( array_unique( array_filter( array_map( 'absint', $manual_post_ids ) ) ) );
	foreach ( $manual as $candidate_id ) {
		if (
			$candidate_id !== $post_id &&
			'publish' === get_post_status( $candidate_id ) &&
			has_category( $category_id, $candidate_id )
		) {
			$selected[] = $candidate_id;
		}
		if ( count( $selected ) >= $limit ) {
			return $selected;
		}
	}

	if ( ! $automatic ) {
		return $selected;
	}

	$tag_ids = wp_get_post_tags( $post_id, array( 'fields' => 'ids' ) );
	$tag_ids = is_wp_error( $tag_ids ) ? array() : $tag_ids;
	if ( empty( $tag_ids ) ) {
		return $selected;
	}

	$candidates = get_posts(
		array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => 30,
			'category__in'        => array( $category_id ),
			'tag__in'             => $tag_ids,
			'post__not_in'        => array_merge( array( $post_id ), $selected ),
			'ignore_sticky_posts' => true,
		)
	);

	usort(
		$candidates,
		static function ( $left, $right ) use ( $tag_ids ) {
			$left_tags  = wp_get_post_tags( $left->ID, array( 'fields' => 'ids' ) );
			$right_tags = wp_get_post_tags( $right->ID, array( 'fields' => 'ids' ) );
			$left_score = is_wp_error( $left_tags ) ? 0 : count( array_intersect( $tag_ids, $left_tags ) );
			$right_score = is_wp_error( $right_tags ) ? 0 : count( array_intersect( $tag_ids, $right_tags ) );
			return $left_score === $right_score ? strcmp( $right->post_date, $left->post_date ) : $right_score - $left_score;
		}
	);

	foreach ( $candidates as $candidate ) {
		$selected[] = $candidate->ID;
		if ( count( $selected ) >= $limit ) {
			break;
		}
	}

	return $selected;
}

/**
 * Render FAQ answers from their source posts.
 *
 * @param array         $attributes Block attributes.
 * @param string        $content    Saved content.
 * @param WP_Block|null $block      Block instance.
 * @return string
 */
function gymcast_marketing_tools_render_faq_section( $attributes, $content, $block = null ) {
	$post_id   = $block && isset( $block->context['postId'] ) ? (int) $block->context['postId'] : (int) get_the_ID();
	$automatic = ! isset( $attributes['automaticMatching'] ) || (bool) $attributes['automaticMatching'];
	$ids       = gymcast_marketing_tools_get_faq_ids( $post_id, $attributes['manualPostIds'] ?? array(), $automatic, 4 );

	if ( empty( $ids ) ) {
		return '';
	}

	$output = '<div class="wp-block-group gc-faq"><h2 class="wp-block-heading">' . esc_html__( 'Frequently Asked Questions', 'gymcast-marketing-tools' ) . '</h2>';
	foreach ( $ids as $faq_id ) {
		$answer  = apply_filters( 'the_content', get_post_field( 'post_content', $faq_id ) );
		$output .= '<h3 class="wp-block-heading">' . esc_html( get_the_title( $faq_id ) ) . '</h3>';
		$output .= '<div class="gc-faq-answer">' . $answer . '</div>';
	}

	return $output . '</div>';
}

/**
 * Render legacy static FAQ groups dynamically until the editor migrates them.
 *
 * @param string $block_content Rendered content.
 * @param array  $block         Parsed block.
 * @return string
 */
function gymcast_marketing_tools_render_legacy_faq( $block_content, $block ) {
	$class_name = isset( $block['attrs']['className'] ) ? $block['attrs']['className'] : '';

	if (
		'core/group' === $block['blockName'] &&
		in_array( 'gc-faq', preg_split( '/\s+/', $class_name ), true )
	) {
		return gymcast_marketing_tools_render_faq_section( array(), '', null );
	}
	return $block_content;
}
add_filter( 'render_block', 'gymcast_marketing_tools_render_legacy_faq', 15, 2 );

/**
 * Register the dynamic FAQ block.
 */
function gymcast_marketing_tools_register_faq_block() {
	$script_path = GYMCAST_MARKETING_TOOLS_PATH . 'assets/js/faqs.js';
	wp_register_script(
		'gymcast-marketing-tools-faq-editor',
		GYMCAST_MARKETING_TOOLS_URL . 'assets/js/faqs.js',
		array( 'wp-block-editor', 'wp-blocks', 'wp-components', 'wp-core-data', 'wp-data', 'wp-element', 'wp-i18n' ),
		file_exists( $script_path ) ? filemtime( $script_path ) : GYMCAST_MARKETING_TOOLS_VERSION,
		true
	);

	register_block_type(
		'gymcast/faq-section',
		array(
			'api_version'     => 3,
			'attributes'      => array(
				'manualPostIds'    => array( 'type' => 'array', 'default' => array(), 'items' => array( 'type' => 'number' ) ),
				'automaticMatching' => array( 'type' => 'boolean', 'default' => true ),
			),
			'uses_context'    => array( 'postId', 'postType' ),
			'editor_script'   => 'gymcast-marketing-tools-faq-editor',
			'render_callback' => 'gymcast_marketing_tools_render_faq_section',
		)
	);
}
add_action( 'init', 'gymcast_marketing_tools_register_faq_block' );

<?php
/**
 * Dynamic related-guide block and resource taxonomy management.
 *
 * @package GymcastMarketingTools
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ensure the Resources > Guide category hierarchy exists.
 *
 * @return array{resources:int,guide:int}|array{}
 */
function gymcast_marketing_tools_get_resource_category_ids() {
	static $category_ids = null;
	if ( null !== $category_ids ) {
		return $category_ids;
	}

	$resources = get_term_by( 'slug', 'resources', 'category' );
	if ( ! $resources ) {
		$created = wp_insert_term( __( 'Resources', 'gymcast-marketing-tools' ), 'category', array( 'slug' => 'resources' ) );
		if ( is_wp_error( $created ) ) {
			return array();
		}
		$resources = get_term( $created['term_id'], 'category' );
	}

	$guide = get_term_by( 'slug', 'guide', 'category' );
	if ( ! $guide ) {
		$created = wp_insert_term(
			__( 'Guide', 'gymcast-marketing-tools' ),
			'category',
			array(
				'slug'   => 'guide',
				'parent' => (int) $resources->term_id,
			)
		);
		if ( is_wp_error( $created ) ) {
			return array();
		}
		$guide = get_term( $created['term_id'], 'category' );
	} elseif ( (int) $guide->parent !== (int) $resources->term_id ) {
		$updated = wp_update_term( $guide->term_id, 'category', array( 'parent' => (int) $resources->term_id ) );
		if ( is_wp_error( $updated ) ) {
			return array();
		}
		$guide = get_term( $guide->term_id, 'category' );
	}

	$category_ids = array(
		'resources' => (int) $resources->term_id,
		'guide'     => (int) $guide->term_id,
	);

	return $category_ids;
}

/**
 * Create resource categories after the category taxonomy is registered.
 */
function gymcast_marketing_tools_ensure_resource_categories() {
	if ( taxonomy_exists( 'category' ) ) {
		gymcast_marketing_tools_get_resource_category_ids();
	}
}
add_action( 'init', 'gymcast_marketing_tools_ensure_resource_categories', 20 );

/**
 * Assign the resource categories whenever a Gymcast guide is saved.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 */
function gymcast_marketing_tools_assign_resource_categories( $post_id, $post ) {
	if (
		'post' !== $post->post_type ||
		wp_is_post_revision( $post_id ) ||
		( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ||
		false === strpos( $post->post_content, 'gc-resource-article' )
	) {
		return;
	}

	$categories = gymcast_marketing_tools_get_resource_category_ids();
	if ( ! empty( $categories ) ) {
		wp_set_post_categories( $post_id, array_values( $categories ), true );
	}
}
add_action( 'save_post', 'gymcast_marketing_tools_assign_resource_categories', 20, 2 );

/**
 * Get related guide IDs, prioritising manual choices and shared tags.
 *
 * @param int        $post_id         Current post ID.
 * @param array<int> $manual_post_ids Manually selected post IDs.
 * @param int        $limit           Maximum results.
 * @return array<int>
 */
function gymcast_marketing_tools_get_related_guide_ids( $post_id, $manual_post_ids = array(), $limit = 3 ) {
	$categories = gymcast_marketing_tools_get_resource_category_ids();
	if ( empty( $categories['guide'] ) ) {
		return array();
	}

	$selected = array();
	$manual   = array_values( array_unique( array_filter( array_map( 'absint', $manual_post_ids ) ) ) );
	foreach ( $manual as $candidate_id ) {
		if (
			$candidate_id !== $post_id &&
			'publish' === get_post_status( $candidate_id ) &&
			has_category( $categories['guide'], $candidate_id )
		) {
			$selected[] = $candidate_id;
		}
		if ( count( $selected ) >= $limit ) {
			return $selected;
		}
	}

	$tag_ids = wp_get_post_tags( $post_id, array( 'fields' => 'ids' ) );
	$tag_ids = is_wp_error( $tag_ids ) ? array() : $tag_ids;
	if ( $tag_ids ) {
		$candidates = get_posts(
			array(
				'post_type'        => 'post',
				'post_status'      => 'publish',
				'posts_per_page'   => 30,
				'category__in'     => array( $categories['guide'] ),
				'tag__in'          => $tag_ids,
				'post__not_in'     => array_merge( array( $post_id ), $selected ),
				'ignore_sticky_posts' => true,
			)
		);

		usort(
			$candidates,
			static function ( $left, $right ) use ( $tag_ids ) {
				$left_score  = count( array_intersect( $tag_ids, wp_get_post_tags( $left->ID, array( 'fields' => 'ids' ) ) ) );
				$right_score = count( array_intersect( $tag_ids, wp_get_post_tags( $right->ID, array( 'fields' => 'ids' ) ) ) );
				return $left_score === $right_score ? strcmp( $right->post_date, $left->post_date ) : $right_score - $left_score;
			}
		);

		foreach ( $candidates as $candidate ) {
			$selected[] = $candidate->ID;
			if ( count( $selected ) >= $limit ) {
				return $selected;
			}
		}
	}

	return $selected;
}

/**
 * Render the Related Guides block.
 *
 * @param array $attributes Block attributes.
 * @param string $content    Saved content.
 * @param WP_Block $block    Block instance.
 * @return string
 */
function gymcast_marketing_tools_render_related_guides( $attributes, $content, $block = null ) {
	$post_id = $block && isset( $block->context['postId'] ) ? (int) $block->context['postId'] : (int) get_the_ID();
	$ids     = gymcast_marketing_tools_get_related_guide_ids( $post_id, $attributes['manualPostIds'] ?? array(), 3 );

	if ( empty( $ids ) ) {
		return '';
	}

	$output = '<div class="wp-block-group gc-related-guides"><h2 class="wp-block-heading">' . esc_html__( 'Related Guides', 'gymcast-marketing-tools' ) . '</h2><ul>';
	foreach ( $ids as $related_id ) {
		$output .= sprintf(
			'<li><a href="%s">%s</a></li>',
			esc_url( get_permalink( $related_id ) ),
			esc_html( get_the_title( $related_id ) )
		);
	}

	return $output . '</ul></div>';
}

/**
 * Render old static Related Guides groups dynamically until they are migrated
 * by the editor and saved as the dedicated block.
 *
 * @param string $block_content Rendered content.
 * @param array  $block         Parsed block.
 * @return string
 */
function gymcast_marketing_tools_render_legacy_related_guides( $block_content, $block ) {
	$class_name = isset( $block['attrs']['className'] ) ? $block['attrs']['className'] : '';

	if (
		'core/group' === $block['blockName'] &&
		in_array( 'gc-related-guides', preg_split( '/\s+/', $class_name ), true )
	) {
		return gymcast_marketing_tools_render_related_guides( array(), '', null );
	}

	return $block_content;
}
add_filter( 'render_block', 'gymcast_marketing_tools_render_legacy_related_guides', 15, 2 );

/**
 * Register the dynamic Related Guides block.
 */
function gymcast_marketing_tools_register_related_guides_block() {
	$script_path = GYMCAST_MARKETING_TOOLS_PATH . 'assets/js/related-guides.js';
	wp_register_script(
		'gymcast-marketing-tools-related-guides-editor',
		GYMCAST_MARKETING_TOOLS_URL . 'assets/js/related-guides.js',
		array( 'wp-block-editor', 'wp-blocks', 'wp-components', 'wp-core-data', 'wp-data', 'wp-element', 'wp-i18n' ),
		file_exists( $script_path ) ? filemtime( $script_path ) : GYMCAST_MARKETING_TOOLS_VERSION,
		true
	);

	register_block_type(
		'gymcast/related-guides',
		array(
			'api_version'     => 3,
			'attributes'      => array(
				'manualPostIds' => array(
					'type'    => 'array',
					'default' => array(),
					'items'   => array( 'type' => 'number' ),
				),
			),
			'uses_context'    => array( 'postId', 'postType' ),
			'editor_script'   => 'gymcast-marketing-tools-related-guides-editor',
			'render_callback' => 'gymcast_marketing_tools_render_related_guides',
		)
	);
}
add_action( 'init', 'gymcast_marketing_tools_register_related_guides_block' );

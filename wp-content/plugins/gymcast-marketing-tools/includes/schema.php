<?php
/**
 * JSON-LD schema output for Gymcast marketing content.
 *
 * @package GymcastMarketingTools
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Output Article/BlogPosting JSON-LD on Gymcast resource posts and pages.
 */
function gymcast_marketing_tools_output_article_schema() {
	if ( ! is_singular( array( 'post', 'page' ) ) || post_password_required() ) {
		return;
	}

	/**
	 * Allows schema output to be disabled if another SEO plugin handles it.
	 *
	 * @param bool $enabled Whether Gymcast schema output should be enabled.
	 */
	if ( ! apply_filters( 'gymcast_marketing_tools_enable_schema', true ) ) {
		return;
	}

	$post = get_post();

	if ( ! $post instanceof WP_Post ) {
		return;
	}

	if ( ! gymcast_marketing_tools_is_resource_article( $post ) ) {
		return;
	}

	$schema = gymcast_marketing_tools_get_article_schema( $post );

	if ( empty( $schema ) ) {
		return;
	}

	printf(
		"\n<script type=\"application/ld+json\" class=\"gymcast-marketing-schema\">%s</script>\n",
		wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
	);
}
add_action( 'wp_head', 'gymcast_marketing_tools_output_article_schema', 20 );

/**
 * Check whether a post uses the Gymcast resource article pattern.
 *
 * @param WP_Post $post Post object.
 * @return bool
 */
function gymcast_marketing_tools_is_resource_article( WP_Post $post ) {
	return false !== strpos( $post->post_content, 'gc-resource-article' );
}

/**
 * Build Article/BlogPosting schema for a post object.
 *
 * @param WP_Post $post Post object.
 * @return array
 */
function gymcast_marketing_tools_get_article_schema( WP_Post $post ) {
	$permalink   = get_permalink( $post );
	$title       = wp_strip_all_tags( get_the_title( $post ) );
	$description = gymcast_marketing_tools_get_schema_description( $post );
	$author_id   = (int) $post->post_author;
	$schema_type = 'post' === get_post_type( $post ) ? 'BlogPosting' : 'Article';

	$schema = array(
		'@context'         => 'https://schema.org',
		'@type'            => $schema_type,
		'mainEntityOfPage' => array(
			'@type' => 'WebPage',
			'@id'   => $permalink,
		),
		'headline'         => $title,
		'description'      => $description,
		'url'              => $permalink,
		'datePublished'    => get_the_date( DATE_W3C, $post ),
		'dateModified'     => get_the_modified_date( DATE_W3C, $post ),
		'author'           => array(
			'@type' => 'Person',
			'name'  => get_the_author_meta( 'display_name', $author_id ),
			'url'   => get_author_posts_url( $author_id ),
		),
		'publisher'        => gymcast_marketing_tools_get_publisher_schema(),
	);

	$image = gymcast_marketing_tools_get_featured_image_schema( $post );

	if ( ! empty( $image ) ) {
		$schema['image'] = $image;
	}

	return array_filter( $schema );
}

/**
 * Get a short plain-text schema description.
 *
 * @param WP_Post $post Post object.
 * @return string
 */
function gymcast_marketing_tools_get_schema_description( WP_Post $post ) {
	$description = get_the_excerpt( $post );

	if ( '' === trim( $description ) ) {
		$description = wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 32 );
	}

	return html_entity_decode( wp_strip_all_tags( $description ), ENT_QUOTES, get_bloginfo( 'charset' ) );
}

/**
 * Get publisher Organization schema.
 *
 * @return array
 */
function gymcast_marketing_tools_get_publisher_schema() {
	$publisher = array(
		'@type' => 'Organization',
		'name'  => get_bloginfo( 'name' ),
		'url'   => home_url( '/' ),
	);

	$custom_logo_id = get_theme_mod( 'custom_logo' );

	if ( $custom_logo_id ) {
		$logo = wp_get_attachment_image_src( $custom_logo_id, 'full' );

		if ( $logo ) {
			$publisher['logo'] = array(
				'@type'  => 'ImageObject',
				'url'    => $logo[0],
				'width'  => $logo[1],
				'height' => $logo[2],
			);
		}
	}

	return $publisher;
}

/**
 * Get featured image schema when a post has one.
 *
 * @param WP_Post $post Post object.
 * @return array
 */
function gymcast_marketing_tools_get_featured_image_schema( WP_Post $post ) {
	if ( ! has_post_thumbnail( $post ) ) {
		return array();
	}

	$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post ), 'full' );

	if ( ! $image ) {
		return array();
	}

	return array(
		'@type'  => 'ImageObject',
		'url'    => $image[0],
		'width'  => $image[1],
		'height' => $image[2],
	);
}

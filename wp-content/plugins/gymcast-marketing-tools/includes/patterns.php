<?php
/**
 * Gutenberg block patterns for Gymcast marketing resources.
 *
 * @package GymcastMarketingTools
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the Gymcast pattern category and patterns.
 */
function gymcast_marketing_tools_register_patterns() {
	if ( function_exists( 'register_block_pattern_category' ) ) {
		register_block_pattern_category(
			'gymcast-resources',
			array(
				'label' => __( 'Gymcast Resources', 'gymcast-marketing-tools' ),
			)
		);
	}

	if ( ! function_exists( 'register_block_pattern' ) ) {
		return;
	}

	register_block_pattern(
		'gymcast/resource-guide',
		array(
			'title'       => __( 'Gymcast Resource Guide', 'gymcast-marketing-tools' ),
			'description' => __( 'A full editable article scaffold for evergreen Gymcast resource content.', 'gymcast-marketing-tools' ),
			'categories'  => array( 'gymcast-resources' ),
			'keywords'    => array( 'gymcast', 'resource', 'guide', 'article' ),
			'blockTypes'  => array( 'core/post-content' ),
			'postTypes'   => array( 'post', 'page' ),
			'inserter'    => true,
			'content'     => gymcast_marketing_tools_get_resource_guide_pattern(),
		)
	);

	register_block_pattern(
		'gymcast/related-guides',
		array(
			'title'       => __( 'Related Guides', 'gymcast-marketing-tools' ),
			'description' => __( 'A reusable internal-linking section for Gymcast resource articles.', 'gymcast-marketing-tools' ),
			'categories'  => array( 'gymcast-resources' ),
			'keywords'    => array( 'gymcast', 'related', 'guides', 'links' ),
			'blockTypes'  => array( 'core/post-content' ),
			'postTypes'   => array( 'post', 'page' ),
			'inserter'    => true,
			'content'     => gymcast_marketing_tools_get_related_guides_pattern(),
		)
	);

	register_block_pattern(
		'gymcast/article-cta',
		array(
			'title'       => __( 'Gymcast Article CTA', 'gymcast-marketing-tools' ),
			'description' => __( 'A simple end-of-article call to action for Gymcast resources.', 'gymcast-marketing-tools' ),
			'categories'  => array( 'gymcast-resources' ),
			'keywords'    => array( 'gymcast', 'cta', 'demo', 'article' ),
			'blockTypes'  => array( 'core/post-content' ),
			'postTypes'   => array( 'post', 'page' ),
			'inserter'    => true,
			'content'     => gymcast_marketing_tools_get_article_cta_pattern(),
		)
	);

	register_block_pattern(
		'gymcast/faq-section',
		array(
			'title'       => __( 'FAQ Section', 'gymcast-marketing-tools' ),
			'description' => __( 'A simple editable FAQ section using headings and paragraphs.', 'gymcast-marketing-tools' ),
			'categories'  => array( 'gymcast-resources' ),
			'keywords'    => array( 'gymcast', 'faq', 'questions', 'answers' ),
			'blockTypes'  => array( 'core/post-content' ),
			'postTypes'   => array( 'post', 'page' ),
			'inserter'    => true,
			'content'     => gymcast_marketing_tools_get_faq_section_pattern(),
		)
	);

	register_block_pattern(
		'gymcast/comparison-table',
		array(
			'title'       => __( 'Gymcast Comparison Table', 'gymcast-marketing-tools' ),
			'description' => __( 'A simple editable comparison table for resource articles.', 'gymcast-marketing-tools' ),
			'categories'  => array( 'gymcast-resources' ),
			'keywords'    => array( 'gymcast', 'comparison', 'table', 'options' ),
			'blockTypes'  => array( 'core/post-content' ),
			'postTypes'   => array( 'post', 'page' ),
			'inserter'    => true,
			'content'     => gymcast_marketing_tools_get_comparison_table_pattern(),
		)
	);
}
add_action( 'init', 'gymcast_marketing_tools_register_patterns' );

/**
 * Get the full resource guide article pattern.
 *
 * @return string
 */
function gymcast_marketing_tools_get_resource_guide_pattern() {
	return '<!-- wp:group {"className":"gc-resource-article","layout":{"type":"constrained"}} -->
<div class="wp-block-group gc-resource-article"><!-- wp:group {"className":"gc-article-intro","layout":{"type":"constrained"}} -->
<div class="wp-block-group gc-article-intro"><!-- wp:paragraph {"className":"gc-kicker"} -->
<p class="gc-kicker">Gymcast Resource Guide</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"gc-standfirst"} -->
<p class="gc-standfirst">Placeholder introduction — replace this with a short summary of the article.</p>
<!-- /wp:paragraph -->

<!-- wp:group {"className":"gc-guide-meta","layout":{"type":"constrained"}} -->
<div class="wp-block-group gc-guide-meta"><!-- wp:list -->
<ul><!-- wp:list-item -->
<li><strong>Best for:</strong> Replace with the intended audience</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Reading time:</strong> Replace with estimated reading time</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Last updated:</strong> Replace with month and year</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:gymcast/table-of-contents /-->

<!-- wp:group {"className":"gc-main-content","metadata":{"name":"Main Article Content"},"templateLock":false,"layout":{"type":"constrained"}} -->
<div class="wp-block-group gc-main-content"><!-- wp:heading -->
<h2 class="wp-block-heading">Placeholder H2 section 1</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Replace this paragraph with your article content.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Placeholder H2 section 2</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Replace this paragraph with your article content.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Placeholder H2 section 3</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Replace this paragraph with your article content.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Placeholder H2 section 4</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Replace this paragraph with your article content.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Placeholder H2 section 5</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Replace this paragraph with your article content.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Placeholder H2 section 6</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Replace this paragraph with your article content.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:separator -->
<hr class="wp-block-separator has-alpha-channel-opacity"/>
<!-- /wp:separator -->

' . gymcast_marketing_tools_get_faq_section_pattern() . '

' . gymcast_marketing_tools_get_related_guides_pattern() . '

' . gymcast_marketing_tools_get_article_cta_pattern() . '</div>
<!-- /wp:group -->';
}

/**
 * Get the related guides pattern.
 *
 * @return string
 */
function gymcast_marketing_tools_get_related_guides_pattern() {
	return '<!-- wp:gymcast/related-guides /-->';
}

/**
 * Get the article CTA pattern.
 *
 * @return string
 */
function gymcast_marketing_tools_get_article_cta_pattern() {
	return '<!-- wp:group {"className":"gc-article-cta","layout":{"type":"constrained"}} -->
<div class="wp-block-group gc-article-cta"><!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Want an Easier Way to Manage Gym TV Displays?</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Gymcast helps gyms automatically display the right class on the right screen at the right time.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/book-demo/">Book a Demo</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->';
}

/**
 * Get the FAQ section pattern.
 *
 * @return string
 */
function gymcast_marketing_tools_get_faq_section_pattern() {
	return '<!-- wp:gymcast/faq-section /-->';
}

/**
 * Get the comparison table pattern.
 *
 * @return string
 */
function gymcast_marketing_tools_get_comparison_table_pattern() {
	return '<!-- wp:group {"className":"gc-comparison-table","layout":{"type":"constrained"}} -->
<div class="wp-block-group gc-comparison-table"><!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Quick Comparison</h2>
<!-- /wp:heading -->

<!-- wp:table {"hasFixedLayout":false} -->
<figure class="wp-block-table"><table><thead><tr><th>Option</th><th>Best for</th><th>Pros</th><th>Watch out for</th></tr></thead><tbody><tr><td>Smart TV app or browser</td><td>Single-screen gyms</td><td>Low cost and simple to start</td><td>Harder to manage across multiple screens</td></tr><tr><td>Laptop or casting device</td><td>Temporary or occasional displays</td><td>Flexible and familiar for staff</td><td>Needs manual setup and staff attention</td></tr><tr><td>Dedicated gym display software</td><td>Multi-room gyms with changing schedules</td><td>Central control, scheduling, and room-specific displays</td><td>Usually has a monthly software cost</td></tr></tbody></table></figure>
<!-- /wp:table --></div>
<!-- /wp:group -->';
}

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
<p class="gc-standfirst">Use this guide to compare practical ways to show workouts, timetables, class names, and member information across your gym TVs.</p>
<!-- /wp:paragraph -->

<!-- wp:group {"className":"gc-guide-meta","layout":{"type":"constrained"}} -->
<div class="wp-block-group gc-guide-meta"><!-- wp:list -->
<ul><!-- wp:list-item -->
<li><strong>Best for:</strong> Gym owners and operators planning TV displays</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Reading time:</strong> 6 minutes</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Last updated:</strong> Replace with current month and year</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"gc-toc","layout":{"type":"constrained"}} -->
<div class="wp-block-group gc-toc"><!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Contents</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul><!-- wp:list-item -->
<li>Why this matters</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Option 1</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Option 2</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Option 3</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>When dedicated software makes sense</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Which option is right for your gym?</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:group -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Why this matters</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Gym TV displays are often one of the first things members notice when they walk into a training space. A clear setup helps members understand what is happening now, where they need to be, and what is coming next.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Option 1</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Describe the simplest setup, when it works well, and where it starts to become awkward for a busy gym.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Option 2</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Explain the middle-ground option. Include the operational tradeoffs for updating screens, keeping content accurate, and managing multiple rooms.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Option 3</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Cover the more advanced setup. Focus on what the gym gains, what it needs to maintain, and who this approach is best suited for.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">When dedicated software makes sense</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Dedicated gym display software starts to make sense when staff are updating several screens manually, classes change often, or each area of the gym needs different content at different times.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Which option is right for your gym?</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Summarise the decision in practical terms. Help readers match the setup to their gym size, number of displays, timetable complexity, and available staff time.</p>
<!-- /wp:paragraph -->

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
	return '<!-- wp:group {"className":"gc-related-guides","layout":{"type":"constrained"}} -->
<div class="wp-block-group gc-related-guides"><!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Related Guides</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul><!-- wp:list-item -->
<li><a href="/resources/portrait-vs-landscape-gym-tv/">Portrait vs Landscape TVs for Gyms</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="/resources/best-tv-size-for-gym/">Best TV Size for a Gym</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="/resources/gym-digital-signage/">Digital Signage for Gyms</a></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:group -->';
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
	return '<!-- wp:group {"className":"gc-faq","layout":{"type":"constrained"}} -->
<div class="wp-block-group gc-faq"><!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Frequently Asked Questions</h2>
<!-- /wp:heading -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Can I display gym workouts on any Smart TV?</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>In many cases, yes. The best setup depends on the TV model, browser support, network reliability, and how you want to control what appears on each screen.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Do I need a laptop connected to the TV?</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>No, not always. A laptop can work for a simple setup, but many gyms prefer a cleaner arrangement using a Smart TV, small media device, or dedicated display software.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Is portrait or landscape better for gym workout displays?</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Landscape is usually easier for timetables and class information, while portrait can work well for workout stations, narrow walls, and single-focus content.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">What is the easiest way to manage multiple gym TVs?</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>The easiest option is usually a central system that lets staff schedule content once and send the right display to each screen automatically.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->';
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

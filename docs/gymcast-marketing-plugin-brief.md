# Gymcast Marketing Plugin Brief

## Purpose

Create a small site-specific WordPress plugin for the Gymcast marketing site (`gymcast.fit`).

The plugin should provide reusable Gutenberg block patterns and supporting CSS for publishing polished resource articles, without relying heavily on the current theme.

The plugin is not intended to be distributed publicly. It is a private marketing-site helper plugin.

## Plugin Name

Suggested name:

```text
Gymcast Marketing Tools
```

Suggested folder:

```text
gymcast-marketing-tools/
```

## Goals

The plugin should:

1. Register a custom Gutenberg pattern category for Gymcast resources.
2. Register reusable block patterns for SEO/resource articles.
3. Enqueue a small CSS file for styling those patterns on the front end and editor.
4. Keep the article design consistent even if the theme is basic.
5. Output simple Article/BlogPosting schema for resource posts and pages using the Gymcast Resource Guide pattern.
6. Avoid creating custom post types for now.
7. Avoid over-engineering.

## Non-goals

Do not build:

- Custom Gutenberg blocks.
- A full page builder.
- A custom post type for resources.
- Admin settings pages.
- A complex design system.
- JavaScript interactions unless absolutely necessary.

This plugin should be simple PHP + CSS.

## Suggested File Structure

```text
gymcast-marketing-tools/
  gymcast-marketing-tools.php
  assets/
    css/
      gymcast-marketing.css
  includes/
    patterns.php
    schema.php
```

Optional later:

```text
  includes/
    faq-schema.php
```

## Main Plugin File

`gymcast-marketing-tools.php` should:

- Define plugin metadata.
- Prevent direct access.
- Define useful constants such as plugin path and plugin URL.
- Require `includes/patterns.php`.
- Require `includes/schema.php`.
- Enqueue CSS on the front end.
- Enqueue CSS in the block editor.

Example responsibilities:

```php
/**
 * Plugin Name: Gymcast Marketing Tools
 * Description: Site-specific Gutenberg patterns and styling for Gymcast marketing resources.
 * Version: 0.1.0
 * Author: Gymcast
 */
```

Use `plugins_url()` or `plugin_dir_url()` for asset paths.

## Pattern Category

Register a pattern category called:

```text
gymcast-resources
```

User-facing label:

```text
Gymcast Resources
```

Register it on `init`.

## Required Block Patterns

### 1. Gymcast Resource Guide

Pattern slug:

```text
gymcast/resource-guide
```

Title:

```text
Gymcast Resource Guide
```

Purpose:

A full article structure for evergreen SEO/resource content.

Default structure:

```text
Article intro
  Kicker
  Standfirst
  Guide metadata box

Automatic contents section

Main Article Content group (freely editable; add, remove, duplicate, or reorder H2 sections)
  H2: Why this matters
  H2: Option 1
  H2: Option 2
  H2: Option 3
  H2: When dedicated software makes sense
  H2: Which option is right for your gym?

The contents section is generated from H2 headings in the Main Article Content
group. It must update when headings are added, renamed, removed, or reordered,
and must link to unique heading anchors. Headings in the FAQ, related guides,
and final CTA are excluded.

FAQ section

Related guides

Final CTA
```

Suggested placeholder copy should be specific enough to guide writing, but easy to replace.

Use normal core blocks only:

- Group
- Heading
- Paragraph
- List
- Table
- Separator
- Buttons
- Button

Use custom class names for styling.

Suggested class names:

```text
gc-resource-article
gc-article-intro
gc-kicker
gc-standfirst
gc-guide-meta
gc-toc
gc-faq
gc-related-guides
gc-article-cta
gc-comparison-table
```

### 2. Related Guides

Pattern slug:

```text
gymcast/related-guides
```

Title:

```text
Related Guides
```

Purpose:

A reusable section for internal linking between resource articles.

The section uses the dynamic `gymcast/related-guides` block and displays up to
three published Guide posts in this order:

1. Manually selected guides.
2. Guides ranked by the number of tags shared with the current post.

The current article is always excluded. If neither manual choices nor shared-tag
matches exist, the section is not rendered. Resource Guide posts are automatically
assigned to both categories in the following hierarchy, which the plugin creates
when necessary:

```text
Resources
└── Guide
```

### Dynamic FAQ Section

Block slug:

```text
gymcast/faq-section
```

FAQ posts use the `faq` category. The post title is rendered as the question and
the post body as the answer. The block displays up to four FAQs in this order:

1. Manually selected FAQs, in the editor-defined order.
2. FAQ posts ranked by tags shared with the current resource article, when
   automatic matching is enabled.

The current post is excluded and the entire FAQ section is omitted when no
manual or shared-tag matches exist. The plugin creates the FAQ category when it
does not already exist.

### 3. Article CTA

Pattern slug:

```text
gymcast/article-cta
```

Title:

```text
Gymcast Article CTA
```

Purpose:

A reusable call-to-action section for the end of articles.

Default content:

```text
Want an Easier Way to Manage Gym TV Displays?
Gymcast helps gyms automatically display the right class on the right screen at the right time.
[Book a Demo]
```

Default button URL:

```text
/book-demo/
```

### 4. FAQ Section

Pattern slug:

```text
gymcast/faq-section
```

Title:

```text
FAQ Section
```

Purpose:

A simple FAQ layout for SEO/resource articles.

Use headings and paragraphs, not accordions.

Example questions:

```text
Can I display gym workouts on any Smart TV?
Do I need a laptop connected to the TV?
Is portrait or landscape better for gym workout displays?
What is the easiest way to manage multiple gym TVs?
```

### 5. Comparison Table

Pattern slug:

```text
gymcast/comparison-table
```

Title:

```text
Gymcast Comparison Table
```

Purpose:

A reusable editable comparison table for SEO/resource articles.

Default columns:

```text
Option
Best for
Pros
Watch out for
```

Default rows:

```text
Smart TV app or browser
Laptop or casting device
Dedicated gym display software
```

Use the core Table block and the class name:

```text
gc-comparison-table
```

## CSS Requirements

Create `assets/css/gymcast-marketing.css`.

The CSS should make resource articles clean and readable without depending too heavily on the theme.

Recommended visual approach:

- Maximum article width around `760px`.
- Generous vertical spacing.
- Clear heading hierarchy.
- Larger standfirst paragraph.
- Simple bordered/pale panels for metadata, related guides, FAQ and CTAs.
- Rounded corners on panels.
- Buttons should inherit theme styling where possible.
- Avoid highly opinionated colours unless necessary.

Suggested CSS behaviours:

```text
.gc-resource-article {
  max-width: 760px;
  margin-inline: auto;
}

.gc-standfirst {
  font-size: 1.2rem;
  line-height: 1.5;
}

.gc-guide-meta,
.gc-related-guides,
.gc-article-cta,
.gc-faq {
  padding: 1.5rem;
  border-radius: 12px;
  border: 1px solid rgba(0,0,0,0.12);
  margin-block: 2rem;
}
```

Keep CSS robust and minimal.

Comparison tables should be full-width, readable, and horizontally scrollable on small screens rather than forcing columns to become too narrow.

## Editor Styling

The same CSS should be loaded in the Gutenberg editor using `enqueue_block_editor_assets`, so patterns look roughly similar while editing.

It does not need to be pixel-perfect in the editor.

## Implementation Notes

Use `register_block_pattern()` and `register_block_pattern_category()`.

Register patterns on the `init` hook.

Guard pattern registration functions with `function_exists()` where appropriate, for compatibility.

Example:

```php
if ( function_exists( 'register_block_pattern' ) ) {
    register_block_pattern(...);
}
```

All strings should be translation-ready using `__()` where practical, although this is a private plugin.

## Schema Output

Create `includes/schema.php`.

The plugin should output a small JSON-LD script on singular posts and pages only when the content includes the `gc-resource-article` class from the Gymcast Resource Guide pattern.

Initial schema scope:

- Use `BlogPosting` for standard posts.
- Use `Article` for pages.
- Use the WordPress post title as the schema headline.
- Use the excerpt as the schema description, falling back to trimmed post content.
- Include canonical permalink, published date, modified date, author, publisher, and featured image when available.
- Do not output schema on ordinary marketing pages, archives, search results, the homepage, or password-protected content.
- Add a filter such as `gymcast_marketing_tools_enable_schema` so schema can be disabled later if an SEO plugin handles it.

Do not generate FAQ schema in the first pass. FAQ schema should be added later only after the FAQ pattern content and parsing approach are settled, so the plugin does not output incorrect question/answer data.

## Content Editing Behaviour

These should be normal unsynced patterns.

When inserted into a post, the pattern content should become editable post content.

Changing the pattern later should not automatically update existing articles.

## Acceptance Criteria

The plugin is complete when:

1. The plugin can be activated without PHP errors.
2. A new pattern category called `Gymcast Resources` appears in Gutenberg.
3. The following patterns are available:
   - Gymcast Resource Guide
   - Related Guides
   - Gymcast Article CTA
   - FAQ Section
   - Gymcast Comparison Table
4. Inserting `Gymcast Resource Guide` creates a full editable article scaffold.
5. The front end displays the article with readable spacing and styled panels.
6. The editor displays the same classes with reasonable visual styling.
7. The plugin does not require a specific theme to function.
8. No custom post types or custom blocks are created.
9. Singular posts and pages using `gc-resource-article` output basic Article/BlogPosting JSON-LD.

## Future Enhancements

Possible later additions:

- FAQ schema output for `.gc-faq` sections, after confirming a reliable way to parse edited FAQ content.
- Breadcrumb schema.
- Additional patterns for comparison tables.
- A customer story pattern.
- A resource landing page pattern.
- A changelog pattern.

Do not build these in the first version unless explicitly requested.

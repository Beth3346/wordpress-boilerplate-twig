<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.2
 */

$templates = array( 'archive.twig', 'index.twig' );

$context = Timber::get_context();
$posts = Timber::get_posts();

$context['title'] = 'Archive';
if ( is_day() ) {
    $context['page_title'] = 'Archive: '.get_the_date( 'D M Y' );
} else if ( is_month() ) {
    $context['page_title'] = 'Archive: '.get_the_date( 'M Y' );
} else if ( is_year() ) {
    $context['page_title'] = 'Archive: '.get_the_date( 'Y' );
} else if ( is_tag() ) {
    $context['page_title'] = single_tag_title( '', false );
} else if ( is_category() ) {
    $current_category = get_the_category();
    $current_category = $current_category[0]->name;
    $context['page_title'] = single_cat_title( '', false );
    $context['current_category'] = $current_category;
    array_unshift( $templates, 'archive-' . get_query_var( 'cat' ) . '.twig' );
} else if ( is_post_type_archive() ) {
    $context['page_title'] = post_type_archive_title( '', false );
    array_unshift( $templates, 'archive-' . get_post_type() . '.twig' );
}

$context['posts'] = $posts;
$context['pagination'] = Timber::get_pagination();

Timber::render( $templates, $context );

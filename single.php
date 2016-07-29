<?php
/**
 * The Template for displaying all single posts
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context = Timber::get_context();

$context['post'] = new TimberPost();

Timber::render(['single-' . $post->ID . '.twig', 'single-' . $post->post_type . '.twig', 'single.twig'], $context);

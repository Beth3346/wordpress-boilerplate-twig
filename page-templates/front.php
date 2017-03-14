<?php
// Template Name: Front

$post = new TimberPost();

$context = Timber::get_context();
$context['post'] = $post;
$context['articles'] = Timber::get_posts('post_type=post&numberposts=3');
$context['recommendations'] = Timber::get_posts('post_type=recommendation&numberposts=3');
$context['skills'] = Timber::get_posts('post_type=skill&numberposts=3');
$context['experiences'] = Timber::get_posts('post_type=experience&numberposts=3');

Timber::render('front-page.twig', $context );
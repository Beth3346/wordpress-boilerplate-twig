<?php

use Framework\CptBuilder;

$tutorial_builder = new CptBuilder;
$singular_name = 'tutorial';
$plural_name = 'tutorials';

/* Get the administrator role. */
$role = get_role('administrator');

/* If the administrator role exists, add required capabilities for the plugin. */
if (!empty($role)) {
    $role->add_cap('manage_' . $singular_name);
    $role->add_cap('create_' . $plural_name);
    $role->add_cap('edit_' . $plural_name);
}

/* Register custom post types on the 'init' hook. */
add_action('init', function() use ($tutorial_builder) {
        $cpt_singular_name = 'tutorial';
        $cpt_plural_name = 'tutorials';
        $supports = ['title', 'editor', 'thumbnail', 'comments'];
        $taxonomies = ['category', 'post_tag'];
        return $tutorial_builder->registerPostTypes($cpt_singular_name, $cpt_plural_name, $supports, $taxonomies);
    }, 12
);

add_action('init', function() use ($tutorial_builder)
{
    $tax_singular_name = 'lesson';
    $tax_plural_name = 'lessons';
    $cpt_singular = 'tutorial';
    $cpt_plural = 'tutorials';
    $hierarchical = true;
    $default_terms = [];
    return $tutorial_builder->registerTaxonomies($tax_singular_name, $tax_plural_name, $cpt_singular, $cpt_plural, $hierarchical, $default_terms);
}, 12 );

add_action('init', function() use ($tutorial_builder)
{
    $tax_singular_name = 'difficulty';
    $tax_plural_name = 'difficulties';
    $cpt_singular = 'tutorial';
    $cpt_plural = 'tutorials';
    $hierarchical = true;
    $default_terms = [];
    return $tutorial_builder->registerTaxonomies($tax_singular_name, $tax_plural_name, $cpt_singular, $cpt_plural, $hierarchical, $default_terms);
}, 12 );
?>
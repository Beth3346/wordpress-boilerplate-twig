<?php

use Framework\CptBuilder;

$skill_builder = new CptBuilder;
$singular_name = 'skill';
$plural_name = 'skills';

/* Get the administrator role. */
$role = get_role('administrator');

/* If the administrator role exists, add required capabilities for the plugin. */
if (!empty($role)) {
    $role->add_cap('manage_' . $singular_name);
    $role->add_cap('create_' . $plural_name);
    $role->add_cap('edit_' . $plural_name);
}

/* Register custom post types on the 'init' hook. */
add_action('init', function() use ($skill_builder) {
        $cpt_singular_name = 'skill';
        $cpt_plural_name = 'skills';
        $supports = ['title', 'editor', 'thumbnail'];
        $taxonomies = [];
        return $skill_builder->registerPostTypes($cpt_singular_name, $cpt_plural_name, $supports, $taxonomies);
    }, 12
);

add_action('init', function() use ($skill_builder)
{
    $tax_singular_name = 'type';
    $tax_plural_name = 'types';
    $cpt_singular = 'skill';
    $cpt_plural = 'skills';
    $hierarchical = true;
    $default_terms = [];
    return $skill_builder->registerTaxonomies($tax_singular_name, $tax_plural_name, $cpt_singular, $cpt_plural, $hierarchical, $default_terms);
}, 12 );

?>
<?php

use Framework\CptBuilder;

$project_builder = new CptBuilder;
$singular_name = 'project';
$plural_name = 'projects';

/* Get the administrator role. */
$role = get_role('administrator');

/* If the administrator role exists, add required capabilities for the plugin. */
if (!empty($role)) {
    $role->add_cap('manage_' . $singular_name);
    $role->add_cap('create_' . $plural_name);
    $role->add_cap('edit_' . $plural_name);
}

/* Register custom post types on the 'init' hook. */
add_action('init', function() use ($project_builder) {
        $cpt_singular_name = 'project';
        $cpt_plural_name = 'projects';
        $supports = ['title', 'editor', 'thumbnail', 'comments'];
        $taxonomies = [];
        return $project_builder->registerPostTypes($cpt_singular_name, $cpt_plural_name, $supports, $taxonomies);
    }, 12
);

add_action('init', function() use ($project_builder)
{
    $tax_singular_name = 'portfolio';
    $tax_plural_name = 'portfolios';
    $cpt_singular = 'project';
    $cpt_plural = 'projects';
    $hierarchical = true;
    $default_terms = [];
    return $project_builder->registerTaxonomies($tax_singular_name, $tax_plural_name, $cpt_singular, $cpt_plural, $hierarchical, $default_terms);
}, 12 );

add_action('init', function() use ($project_builder)
{
    $tax_singular_name = 'technology';
    $tax_plural_name = 'technologies';
    $cpt_singular = 'project';
    $cpt_plural = 'projects';
    $hierarchical = false;
    $default_terms = [];
    return $project_builder->registerTaxonomies($tax_singular_name, $tax_plural_name, $cpt_singular, $cpt_plural, $hierarchical, $default_terms);
}, 12 );

add_action('init', function() use ($project_builder)
{
    $tax_singular_name = 'tool';
    $tax_plural_name = 'tools';
    $cpt_singular = 'project';
    $cpt_plural = 'projects';
    $hierarchical = false;
    $default_terms = [];
    return $project_builder->registerTaxonomies($tax_singular_name, $tax_plural_name, $cpt_singular, $cpt_plural, $hierarchical, $default_terms);
}, 12 );

add_action('init', function() use ($project_builder)
{
    $tax_singular_name = 'project_type';
    $tax_plural_name = 'project_types';
    $cpt_singular = 'project';
    $cpt_plural = 'projects';
    $hierarchical = false;
    $default_terms = [];
    return $project_builder->registerTaxonomies($tax_singular_name, $tax_plural_name, $cpt_singular, $cpt_plural, $hierarchical, $default_terms);
}, 12 );

// list all meta keys
$fields = array(
    '_project_start_date',
    '_project_end_date',
    '_project_client',
    '_project_url',
    '_project_location',
);

/* Register meta on the 'init' hook. */
add_action('init', function() use ($fields, $project_builder) { $project_builder->registerMeta($fields); }, 12);
add_action('add_meta_boxes', 'add_cpt_project_boxes');

add_action('save_post', function() use ($fields, $project_builder)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    //security check - nonce
    if (isset($_POST['cpt_nonce']) && $_POST && !wp_verify_nonce($_POST['cpt_nonce'], __FILE__)) {
        return;
    }

    return $project_builder->saveMeta($fields);
}, 12);

if (! function_exists('add_cpt_project_boxes'))
{
    function add_cpt_project_boxes()
    {
        // add meta boxes here
        add_meta_box(
            'elr_project_information',
            'Projects',
            'project_cpt_info_cb',
            'project',
            'normal',
            'high'
        );

        // create meta box html
        function project_cpt_info_cb()
        {
            global $post;
            $start_date = get_post_meta( $post->ID, '_project_start_date', true );
            $end_date = get_post_meta( $post->ID, '_project_end_date', true );
            $client = get_post_meta( $post->ID, '_project_client', true );
            $url = get_post_meta( $post->ID, '_project_url', true );
            $location = get_post_meta( $post->ID, '_project_location', true );

            //implement security
            wp_nonce_field(__FILE__, 'cpt_nonce'); ?>

        <label for="_project_start_date">Start Date: </label>
        <input
            type="date"
            id="_project_start_date"
            name="_project_start_date"
            value="<?php echo esc_attr( $start_date ); ?>"
            class="widefat"
        />

        <label for="_project_end_date">End Date: </label>
        <input
            type="date"
            id="_project_end_date"
            name="_project_end_date"
            value="<?php echo esc_attr( $end_date ); ?>"
            class="widefat"
        />

        <label for="_project_client">Client: </label>
        <input
            type="text"
            id="_project_client"
            name="_project_client"
            placeholder="Wonderful Client"
            value="<?php echo esc_attr( $client ); ?>"
            class="widefat"
        />

        <label for="_project_url">Website: </label>
        <input
            type="url"
            id="_project_url"
            name="_project_url"
            placeholder="http://"
            value="<?php echo esc_attr( $url ); ?>"
            class="widefat"
        />

        <label for="_project_location">Client: </label>
        <input
            type="text"
            id="_project_location"
            name="_project_location"
            placeholder="Houston, Texas, United States"
            value="<?php echo esc_attr( $location ); ?>"
            class="widefat"
        />
    <?php }
    }
}
?>
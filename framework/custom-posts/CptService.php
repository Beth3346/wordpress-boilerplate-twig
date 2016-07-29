<?php

use Framework\CptBuilder;

$service_builder = new CptBuilder;
$singular_name = 'service';
$plural_name = 'services';

/* Get the administrator role. */
$role = get_role('administrator');

/* If the administrator role exists, add required capabilities for the plugin. */
if (!empty($role)) {
    $role->add_cap('manage_' . $singular_name);
    $role->add_cap('create_' . $plural_name);
    $role->add_cap('edit_' . $plural_name);
}

/* Register custom post types on the 'init' hook. */
add_action('init', function() use ($service_builder) {
        $cpt_singular_name = 'service';
        $cpt_plural_name = 'services';
        $supports = ['title', 'editor', 'thumbnail', 'comments'];
        $taxonomies = ['category', 'post_tag'];
        return $service_builder->registerPostTypes($cpt_singular_name, $cpt_plural_name, $supports, $taxonomies);
    }, 12
);

add_action('init', function() use ($service_builder)
{
    $tax_singular_name = 'type';
    $tax_plural_name = 'types';
    $cpt_singular = 'service';
    $cpt_plural = 'services';
    $hierarchical = true;
    $default_terms = [];
    return $service_builder->registerTaxonomies($tax_singular_name, $tax_plural_name, $cpt_singular, $cpt_plural, $hierarchical, $default_terms);
}, 12 );

// list all meta keys
$fields = array(
    '_service_field',
);

/* Register meta on the 'init' hook. */
add_action('init', function() use ($fields, $service_builder) { $service_builder->registerMeta($fields); }, 12);
add_action('add_meta_boxes', 'add_cpt_service_boxes');

add_action('save_post', function() use ($fields, $service_builder)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    //security check - nonce
    if (isset($_POST['cpt_nonce']) && $_POST && !wp_verify_nonce($_POST['cpt_nonce'], __FILE__)) {
        return;
    }

    return $service_builder->saveMeta($fields);
}, 12);

if (! function_exists('add_cpt_service_boxes'))
{
    function add_cpt_service_boxes()
    {
        // add meta boxes here
        add_meta_box(
            'service_info',
            'Services',
            'service_cpt_info_cb',
            'service',
            'normal',
            'high'
        );

        // create meta box html
        function service_cpt_info_cb()
        {
            global $post;
            $field = get_post_meta($post->ID, '_service_field', true);


            //implement security
            wp_nonce_field(__FILE__, 'cpt_nonce'); ?>

            <label for="_service_field">Field: </label>
            <input
                type="text"
                id="_service_field"
                name="_service_field"
                class="widefat"
                value="<?php echo esc_attr($field); ?>"
            />
    <?php }
    }
}
?>
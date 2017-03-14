<?php

use Framework\CptBuilder;

$recommendation_builder = new CptBuilder;
$singular_name = 'recommendation';
$plural_name = 'recommendations';

/* Get the administrator role. */
$role = get_role('administrator');

/* If the administrator role exists, add required capabilities for the plugin. */
if (!empty($role)) {
    $role->add_cap('manage_' . $singular_name);
    $role->add_cap('create_' . $plural_name);
    $role->add_cap('edit_' . $plural_name);
}

/* Register custom post types on the 'init' hook. */
add_action('init', function() use ($recommendation_builder) {
        $cpt_singular_name = 'recommendation';
        $cpt_plural_name = 'recommendations';
        $supports = ['title', 'editor', 'thumbnail'];
        $taxonomies = [];
        return $recommendation_builder->registerPostTypes($cpt_singular_name, $cpt_plural_name, $supports, $taxonomies);
    }, 12
);

// list all meta keys
$fields = array(
    '_recommendation_title',
    '_recommendation_author',
    '_recommendation_url'
);

/* Register meta on the 'init' hook. */
add_action('init', function() use ($fields, $recommendation_builder) { $recommendation_builder->registerMeta($fields); }, 12);
add_action('add_meta_boxes', 'add_cpt_recommendation_boxes');

add_action('save_post', function() use ($fields, $recommendation_builder)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    //security check - nonce
    if (isset($_POST['cpt_nonce']) && $_POST && !wp_verify_nonce($_POST['cpt_nonce'], __FILE__)) {
        return;
    }

    return $recommendation_builder->saveMeta($fields);
}, 12);

if (! function_exists('add_cpt_recommendation_boxes'))
{
    function add_cpt_recommendation_boxes()
    {
        // add meta boxes here
        add_meta_box(
            'elr_recommendation_information',
            'Recommendations',
            'recommendation_cpt_info_cb',
            'recommendation',
            'normal',
            'high'
        );

        // create meta box html
        function recommendation_cpt_info_cb()
        {
            global $post;
            $name = get_post_meta( $post->ID, '_recommendation_name', true );
            $company_name = get_post_meta( $post->ID, '_recommendation_company_name', true );
            $role = get_post_meta( $post->ID, '_recommendation_role', true );

            //implement security
            wp_nonce_field(__FILE__, 'cpt_nonce'); ?>

        <label for="_recommendation_name">Name: </label>
        <input
            type="text"
            id="_recommendation_name"
            name="_recommendation_name"
            placeholder="Title"
            value="<?php echo esc_attr( $name ); ?>"
            class="widefat"
        />

        <label for="_recommendation_company_name">Company Name: </label>
        <input
            type="text"
            id="_recommendation_company_name"
            name="_recommendation_company_name"
            placeholder="Company Name"
            value="<?php echo esc_attr( $company_name ); ?>"
            class="widefat"
        />

        <label for="_recommendation_role>Role: </label>
        <input
            type="text"
            id="_recommendation_role"
            name="_recommendation_role"
            placeholder="Web Developer"
            value="<?php echo esc_attr( $role ); ?>"
            class="widefat"
        />
    <?php }
    }
}
?>
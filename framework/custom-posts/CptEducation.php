<?php

use Framework\CptBuilder;

$education_builder = new CptBuilder;
$singular_name = 'education';
$plural_name = 'education';

/* Get the administrator role. */
$role = get_role('administrator');

/* If the administrator role exists, add required capabilities for the plugin. */
if (!empty($role)) {
    $role->add_cap('manage_' . $singular_name);
    $role->add_cap('create_' . $plural_name);
    $role->add_cap('edit_' . $plural_name);
}

/* Register custom post types on the 'init' hook. */
add_action('init', function() use ($education_builder) {
        $cpt_singular_name = 'education';
        $cpt_plural_name = 'education';
        $supports = ['title', 'editor', 'thumbnail'];
        $taxonomies = [];
        return $education_builder->registerPostTypes($cpt_singular_name, $cpt_plural_name, $supports, $taxonomies);
    }, 12
);

// list all meta keys
$fields = array(
    '_education_start_date',
    '_education_end_date',
    '_education_institution',
    '_education_url',
    '_education_location',
    '_education_degree',
    '_education_concentration'
);

/* Register meta on the 'init' hook. */
add_action('init', function() use ($fields, $education_builder) { $education_builder->registerMeta($fields); }, 12);
add_action('add_meta_boxes', 'add_cpt_education_boxes');

add_action('save_post', function() use ($fields, $education_builder)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    //security check - nonce
    if (isset($_POST['cpt_nonce']) && $_POST && !wp_verify_nonce($_POST['cpt_nonce'], __FILE__)) {
        return;
    }

    return $education_builder->saveMeta($fields);
}, 12);

if (! function_exists('add_cpt_education_boxes'))
{
    function add_cpt_education_boxes()
    {
        // add meta boxes here
        add_meta_box(
            'elr_education_information',
            'Education',
            'education_cpt_info_cb',
            'education',
            'normal',
            'high'
        );

        // create meta box html
        function education_cpt_info_cb()
        {
            global $post;
            $start_date = get_post_meta( $post->ID, '_education_start_date', true );
            $end_date = get_post_meta( $post->ID, '_education_end_date', true );
            $institution = get_post_meta( $post->ID, '_education_institution', true );
            $url = get_post_meta( $post->ID, '_education_url', true );
            $location = get_post_meta( $post->ID, '_education_location', true );
            $role = get_post_meta( $post->ID, '_education_role', true );

            //implement security
            wp_nonce_field(__FILE__, 'cpt_nonce'); ?>

        <label for="_education_start_date">Start Date: </label>
        <input
            type="text"
            id="_education_start_date"
            name="_education_start_date"
            value="<?php echo esc_attr( $start_date ); ?>"
            class="widefat"
        />

        <label for="_education_end_date">End Date: </label>
        <input
            type="text"
            id="_education_end_date"
            name="_education_end_date"
            value="<?php echo esc_attr( $end_date ); ?>"
            class="widefat"
        />

        <label for="_education_institution">Institution: </label>
        <input
            type="text"
            id="_education_institution"
            name="_education_institution"
            placeholder="Wonderful Institution"
            value="<?php echo esc_attr( $institution ); ?>"
            class="widefat"
        />

        <label for="_education_url">Website: </label>
        <input
            type="url"
            id="_education_url"
            name="_education_url"
            placeholder="http://"
            value="<?php echo esc_attr( $url ); ?>"
            class="widefat"
        />

        <label for="_education_location">Location: </label>
        <input
            type="text"
            id="_education_location"
            name="_education_location"
            placeholder="Houston, Texas, United States"
            value="<?php echo esc_attr( $location ); ?>"
            class="widefat"
        />

        <label for="_education_degree">Degree/Certification: </label>
        <input
            type="text"
            id="_education_degree"
            name="_education_degree"
            placeholder="B.B.A."
            value="<?php echo esc_attr( $degree ); ?>"
            class="widefat"
        />

        <label for="_education_concentration">Concentration: </label>
        <input
            type="text"
            id="_education_concentration"
            name="_education_concentration"
            placeholder="Information Systems"
            value="<?php echo esc_attr( $concentration ); ?>"
            class="widefat"
        />
    <?php }
    }
}
?>
<?php

use Framework\CptBuilder;

$experience_builder = new CptBuilder;
$singular_name = 'experience';
$plural_name = 'experience';

/* Get the administrator role. */
$role = get_role('administrator');

/* If the administrator role exists, add required capabilities for the plugin. */
if (!empty($role)) {
    $role->add_cap('manage_' . $singular_name);
    $role->add_cap('create_' . $plural_name);
    $role->add_cap('edit_' . $plural_name);
}

/* Register custom post types on the 'init' hook. */
add_action('init', function() use ($experience_builder) {
        $cpt_singular_name = 'experience';
        $cpt_plural_name = 'experience';
        $supports = ['title', 'editor', 'thumbnail'];
        $taxonomies = [];
        return $experience_builder->registerPostTypes($cpt_singular_name, $cpt_plural_name, $supports, $taxonomies);
    }, 12
);

if (! function_exists('add_cpt_experience_boxes'))
{
    function add_cpt_experience_boxes()
    {
        // add meta boxes here
        add_meta_box(
            'elr_experience_information',
            'Experience',
            'experience_cpt_info_cb',
            'experience',
            'normal',
            'high'
        );

        // create meta box html
        function experience_cpt_info_cb()
        {
            global $post;
            $start_date = get_post_meta( $post->ID, '_experience_start_date', true );
            $end_date = get_post_meta( $post->ID, '_experience_end_date', true );
            $business_name = get_post_meta( $post->ID, '_experience_business_name', true );
            $url = get_post_meta( $post->ID, '_experience_url', true );
            $location = get_post_meta( $post->ID, '_experience_location', true );
            $role = get_post_meta( $post->ID, '_experience_role', true );

            //implement security
            wp_nonce_field(__FILE__, 'cpt_nonce'); ?>

        <label for="_experience_start_date">Start Date: </label>
        <input
            type="text"
            id="_experience_start_date"
            name="_experience_start_date"
            value="<?php echo esc_attr( $start_date ); ?>"
            class="widefat"
        />

        <label for="_experience_end_date">End Date: </label>
        <input
            type="text"
            id="_experience_end_date"
            name="_experience_end_date"
            value="<?php echo esc_attr( $end_date ); ?>"
            class="widefat"
        />

        <label for="_experience_business_name">Business Name: </label>
        <input
            type="text"
            id="_experience_business_name"
            name="_experience_business_name"
            placeholder="Wonderful Business"
            value="<?php echo esc_attr( $business_name ); ?>"
            class="widefat"
        />

        <label for="_experience_url">Website: </label>
        <input
            type="url"
            id="_experience_url"
            name="_experience_url"
            placeholder="http://"
            value="<?php echo esc_attr( $url ); ?>"
            class="widefat"
        />

        <label for="_experience_location">Location: </label>
        <input
            type="text"
            id="_experience_location"
            name="_experience_location"
            placeholder="Houston, Texas, United States"
            value="<?php echo esc_attr( $location ); ?>"
            class="widefat"
        />

        <label for="_experience_cole">Role: </label>
        <input
            type="text"
            id="_experience_cole"
            name="_experience_cole"
            placeholder="Web Developer"
            value="<?php echo esc_attr( $cole ); ?>"
            class="widefat"
        />
    <?php }
    }
}

?>
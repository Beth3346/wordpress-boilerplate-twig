<?php

use Framework\CptBuilder;

$reading_builder = new CptBuilder;
$singular_name = 'reading';
$plural_name = 'reading';

/* Get the administrator role. */
$role = get_role('administrator');

/* If the administrator role exists, add required capabilities for the plugin. */
if (!empty($role)) {
    $role->add_cap('manage_' . $singular_name);
    $role->add_cap('create_' . $plural_name);
    $role->add_cap('edit_' . $plural_name);
}

/* Register custom post types on the 'init' hook. */
add_action('init', function() use ($reading_builder) {
        $cpt_singular_name = 'reading';
        $cpt_plural_name = 'reading';
        $supports = ['title', 'editor', 'thumbnail', 'comments'];
        $taxonomies = [];
        return $reading_builder->registerPostTypes($cpt_singular_name, $cpt_plural_name, $supports, $taxonomies);
    }, 12
);

add_action('init', function() use ($reading_builder)
{
    $tax_singular_name = 'reading_type';
    $tax_plural_name = 'reading_types';
    $cpt_singular = 'reading';
    $cpt_plural = 'reading';
    $hierarchical = false;
    $default_terms = [];
    return $reading_builder->registerTaxonomies($tax_singular_name, $tax_plural_name, $cpt_singular, $cpt_plural, $hierarchical, $default_terms);
}, 12 );

// list all meta keys
$fields = array(
    '_reading_title',
    '_reading_author',
    '_reading_url'
);

/* Register meta on the 'init' hook. */
add_action('init', function() use ($fields, $reading_builder) { $reading_builder->registerMeta($fields); }, 12);
add_action('add_meta_boxes', 'add_cpt_reading_boxes');

add_action('save_post', function() use ($fields, $reading_builder)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    //security check - nonce
    if (isset($_POST['cpt_nonce']) && $_POST && !wp_verify_nonce($_POST['cpt_nonce'], __FILE__)) {
        return;
    }

    return $reading_builder->saveMeta($fields);
}, 12);

if (! function_exists('add_cpt_reading_boxes'))
{
    function add_cpt_reading_boxes()
    {
        // add meta boxes here
        add_meta_box(
            'elr_reading_information',
            'Reading',
            'reading_cpt_info_cb',
            'reading',
            'normal',
            'high'
        );

        // create meta box html
        function reading_cpt_info_cb()
        {
            global $post;
            $title = get_post_meta( $post->ID, '_reading_title', true );
            $author = get_post_meta( $post->ID, '_reading_author', true );
            $url = get_post_meta( $post->ID, '_reading_url', true );

            //implement security
            wp_nonce_field(__FILE__, 'cpt_nonce'); ?>

        <label for="_reading_title">Title: </label>
        <input
            type="text"
            id="_reading_title"
            name="_reading_title"
            placeholder="Title"
            value="<?php echo esc_attr( $title ); ?>"
            class="widefat"
        />

        <label for="_reading_author">Author: </label>
        <input
            type="text"
            id="_reading_author"
            name="_reading_author"
            placeholder="Author"
            value="<?php echo esc_attr( $author ); ?>"
            class="widefat"
        />

        <label for="_reading_url">Website: </label>
        <input
            type="url"
            id="_reading_url"
            name="_reading_url"
            placeholder="http://"
            value="<?php echo esc_attr( $url ); ?>"
            class="widefat"
        />
    <?php }
    }
}
?>
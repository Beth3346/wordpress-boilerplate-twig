<?php

use Framework\CptBuilder;

$video_builder = new CptBuilder;
$singular_name = 'video';
$plural_name = 'videos';

/* Get the administrator role. */
$role = get_role('administrator');

/* If the administrator role exists, add required capabilities for the plugin. */
if (!empty($role)) {
    $role->add_cap('manage_' . $singular_name);
    $role->add_cap('create_' . $plural_name);
    $role->add_cap('edit_' . $plural_name);
}

/* Register custom post types on the 'init' hook. */
add_action('init', function() use ($video_builder) {
        $cpt_singular_name = 'video';
        $cpt_plural_name = 'videos';
        $supports = ['title', 'editor', 'thumbnail', 'comments'];
        $taxonomies = ['category', 'post_tags'];
        return $video_builder->registerPostTypes($cpt_singular_name, $cpt_plural_name, $supports, $taxonomies);
    }, 12
);

// list all meta keys
$fields = array(
    '_video_url'
);

/* Register meta on the 'init' hook. */
add_action('init', function() use ($fields, $video_builder) { $video_builder->registerMeta($fields); }, 12);
add_action('add_meta_boxes', 'add_cpt_video_boxes');

add_action('save_post', function() use ($fields, $video_builder)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    //security check - nonce
    if (isset($_POST['cpt_nonce']) && $_POST && !wp_verify_nonce($_POST['cpt_nonce'], __FILE__)) {
        return;
    }

    return $video_builder->saveMeta($fields);
}, 12);

if (! function_exists('add_cpt_video_boxes'))
{
    function add_cpt_video_boxes()
    {
        // add meta boxes here
        add_meta_box(
            'elr_video_information',
            'Videos',
            'video_cpt_info_cb',
            'video',
            'normal',
            'high'
        );

        // create meta box html
        function video_cpt_info_cb()
        {
            global $post;
            $url = get_post_meta( $post->ID, '_video_url', true );

            //implement security
            wp_nonce_field(__FILE__, 'cpt_nonce'); ?>

        <label for="_video_url">URL: </label>
        <input
            type="url"
            id="_video_url"
            name="_video_url"
            placeholder="http://"
            value="<?php echo esc_attr( $url ); ?>"
            class="widefat"
        />
    <?php }
    }
}
?>
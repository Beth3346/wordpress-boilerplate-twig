<?php

namespace Framework;

class CptBuilder {
    /**
     * Returns the default settings for the plugin.
     *
     * @since  0.1.0
     * @access public
     * @param string    $singular_name  singular form of cpt name
     * @param string    $plural_name    plural form of cpt name
     * @return array
     */

    public function getDefaultSettings($singular_name, $plural_name)
    {

        $settings = array(
            $singular_name . '_root'      => str_replace('_' , '-', $plural_name),
            $singular_name . '_base'      => str_replace('_' , '-', $plural_name),
            $singular_name . '_item_base' => '%' . str_replace('_' , '-', $singular_name) . '%'
       );

        return $settings;
    }

    /**
     * Adds default terms to taxonomy
     *
     * @since  0.1.0
     * @access public
     * @param  string   $parent taxonomy to receive the terms
     * @param  array    $terms  array of terms to add to $parent
     * @return null
     */

    public function taxonomyAddDefaultTerms($parent, $terms)
    {
        $parent_term = term_exists($parent, $parent);
        $parent_term_id = $parent_term['term_id'];

        foreach ($terms as $term)
        {
            if (!term_exists($term, $parent))
            {
                wp_insert_term(
                  $term,
                  $parent,
                  array(
                    'slug' => $term,
                    'parent'=> $parent_term_id
                 )
               );
            }
        }
    }

    /**
     * Registers post types needed by the plugin.
     *
     * @since  0.1.0
     * @access public
     * @param  string   $singular_name  singular form of post type name
     * @param  string   $plural_name    plural form of post type name
     * @param  array    $supports       array of features a post type supports
     * @param  array    $taxonomies     built in taxonomies supported by cpt (category or post_tag)
     * @param  boolean  $hierarchical   whether a cpt is hierarchical
     * @return void
     */

    public function registerPostTypes(
        $singular_name,
        $plural_name,
        $supports,
        $taxonomies,
        $hierarchical = false,
        $archive = true
    ) {
        $text_domain = 'elr-' . str_replace('_' , '-', $singular_name);

        /* Get the plugin settings. */
        $settings = get_option('plugin_elr_' . $plural_name, $this->getDefaultSettings($singular_name, $plural_name));

        if ($archive == true)
        {
            $has_archive = $settings[$singular_name . '_root'];
        } else if ($archive == false)
        {
            $has_archive = false;
        } else {
            $has_archive = $archive;
        }

        /* Set up the arguments for the post type. */
        $args = array(
            'description'         => '',
            'public'              => true,
            'publicly_queryable'  => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'exclude_from_search' => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 11,
            'can_export'          => true,
            'delete_with_user'    => false,
            'hierarchical'        => $hierarchical,
            'taxonomies'          => $taxonomies,
            'has_archive'         => $has_archive,
            'query_var'           => $singular_name,
            'capability_type'     => 'post',
            'map_meta_cap'        => true,

            /* Only 3 caps are needed: 'manage', 'create', and 'edit'. */
            'capabilities' => array(

                // meta caps (don't assign these to roles)
                'edit_posts'              => 'edit_' . $plural_name,
                'read_post'              => 'read_' . $singular_name,
                'delete_post'            => 'delete_' . $singular_name,

                // primitive/meta caps
                'create_posts'           => 'create_' . $plural_name,

                // primitive caps used outside of map_meta_cap()
                'edit_posts'             => 'edit_' . $plural_name,
                'read_private_posts'     => 'read',

                // primitive caps used inside of map_meta_cap()
                'read'                   => 'read',
                'edit_private_posts'     => 'edit_' . $plural_name,
                'edit_published_posts'   => 'edit_' . $plural_name
           ),

            /* What features the post type supports. */
            'supports' => $supports,

            /* Labels used when displaying the posts. */
            'labels' => array(
                'name'               => __(ucwords(str_replace('_' , ' ', $plural_name)),                  $text_domain),
                'singular_name'      => __(ucwords(str_replace('_' , ' ', $singular_name)),                $text_domain),
                'menu_name'          => __(ucwords(str_replace('_' , ' ', $plural_name)),                $text_domain),
                'name_admin_bar'     => __(ucwords(str_replace('_' , ' ', $singular_name)),                $text_domain),
                'add_new'            => __('Add New',                                                          $text_domain),
                'add_new_item'       => __('Add New ' . ucwords(str_replace('_' , ' ', $singular_name)),   $text_domain),
                'edit_item'          => __('Edit ' . ucwords(str_replace('_' , ' ', $singular_name)),      $text_domain),
                'new_item'           => __('New ' . ucwords(str_replace('_' , ' ', $singular_name)),       $text_domain),
                'view_item'          => __('View ' . ucwords(str_replace('_' , ' ', $singular_name)),      $text_domain),
                'search_items'       => __('Search ' . ucwords(str_replace('_' , ' ', $plural_name)),      $text_domain),
                'not_found'          => __('No ' . str_replace('_' , ' ', $plural_name) . ' found',          $text_domain),
                'not_found_in_trash' => __('No ' . str_replace('_' , ' ', $plural_name) . ' found in trash', $text_domain),
                'all_items'          => __(ucwords(str_replace('_' , ' ', $plural_name)),                  $text_domain),

                // Custom labels b/c WordPress doesn't have anything to handle this.
                'archive_title'      => __(ucwords(str_replace('_' , ' ', $plural_name)),                  $text_domain),
           )
       );

        /* Register the post type. */
        register_post_type($singular_name, $args);
    }

    /**
     * Registers custom metadata for the plugin.
     *
     * @since  0.1.0
     * @access public
     * @param  array  $fields Array of fields to register
     * @return void
     */

    public function registerMeta($fields)
    {
        foreach ($fields as $field)
        {
            register_meta('post', $field, '[$this, sanitize_meta]', '__return_true');
        }
    }

    /**
     * Saves custom metadata for the plugin.
     *
     * @since  0.1.0
     * @access public
     * @param  array  $fields Array of fields to save
     * @return void
     */

    public function saveMeta($fields)
    {
        global $post;

        foreach ($fields as $field)
        {
            if (isset($_POST[ $field ]))
            {
                update_post_meta($post->ID, $field, $_POST[ $field ]);
            }
        }
    }

    /**
     * Callback function for sanitizing meta when add_metadata() or update_metadata() is called by WordPress.
     * If a developer wants to set up a custom method for sanitizing the data, they should use the
     * "sanitize_{$meta_type}_meta_{$meta_key}" filter hook to do so.
     *
     * @since  0.1.0
     * @access public
     * @param  mixed  $meta_value The value of the data to sanitize.
     * @param  string $meta_key   The meta key name.
     * @param  string $meta_type  The type of metadata (post, comment, user, etc.)
     * @return mixed  $meta_value
     */

    public function sanitizeMeta($meta_value, $meta_key, $meta_type)
    {
        // if meta key has url then sanitize url
        // if meta key has email then sanitize email
        return strip_tags($meta_value);
    }

    /**
     * Register taxonomies for the plugin.
     *
     * @since  0.1.0
     * @access public
     * @param  string   $singular_name    singular form of taxonomy name
     * @param  string   $plural_name      plural form of taxonomy name
     * @param  string   $cpt_singular     singular form of cpt name
     * @param  string   $cpt_plural       plural form of cpt name
     * @param  boolean  $hierarchical     whether or not taxonomy is hierarchical
     * @param  array    $terms            default taxonomy terms
     * @return void.
     */

    public function registerTaxonomies(
        $singular_name, $plural_name, $cpt_singular, $cpt_plural, $hierarchical = true, $default_terms
    ) {
        $text_domain = 'elr-' . str_replace('_' , '-', $singular_name);

        /* Get the plugin settings. */
        $settings = get_option('plugin_elr_' . $cpt_plural, $this->getDefaultSettings($cpt_singular, $cpt_plural));

        /* Set up the arguments for the priority taxonomy. */
        $args = array(
            'public'            => true,
            'show_ui'           => true,
            'show_in_nav_menus' => true,
            'show_admin_column' => true,
            'hierarchical'      => $hierarchical,
            'query_var'         => $singular_name,

            /* Only 2 caps are needed: 'manage_announcement' and 'edit_announcement'. */
            'capabilities' => array(
                'manage_terms' => 'manage_' . $cpt_singular,
                'edit_terms'   => 'manage_' . $cpt_singular,
                'delete_terms' => 'manage_' . $cpt_singular,
                'assign_terms' => 'edit_' . $cpt_plural,
           ),

            /* Labels used when displaying taxonomy and terms. */
            'labels' => array(
                'name'                       => __(ucwords(str_replace('_' , ' ', $plural_name)),                      $text_domain),
                'singular_name'              => __(ucwords(str_replace('_' , ' ', $singular_name)),                    $text_domain),
                'menu_name'                  => __(ucwords(str_replace('_' , ' ', $plural_name)),                      $text_domain),
                'name_admin_bar'             => __(ucwords(str_replace('_' , ' ', $singular_name)),                    $text_domain),
                'search_items'               => __('Search ' . ucwords(str_replace('_' , ' ', $plural_name)),          $text_domain),
                'popular_items'              => __('Popular ' . ucwords(str_replace('_' , ' ', $plural_name)),         $text_domain),
                'all_items'                  => __('All ' . ucwords(str_replace('_' , ' ', $plural_name)),             $text_domain),
                'edit_item'                  => __('Edit ' . ucwords(str_replace('_' , ' ', $singular_name)),          $text_domain),
                'view_item'                  => __('View ' . ucwords(str_replace('_' , ' ', $singular_name)),          $text_domain),
                'update_item'                => __('Update ' . ucwords(str_replace('_' , ' ', $singular_name)),        $text_domain),
                'add_new_item'               => __('Add New ' . ucwords(str_replace('_' , ' ', $singular_name)),       $text_domain),
                'new_item_name'              => __('New ' . ucwords(str_replace('_' , ' ', $singular_name)) . ' Name', $text_domain),
                'add_or_remove_items'        => __('Add or remove ' . str_replace('_' , ' ', $plural_name),            $text_domain),
                'choose_from_most_used'      => __('Choose from the most used ' . str_replace('_' , ' ', $plural_name),  $text_domain),
                'separate_items_with_commas' => __('Separate ' . str_replace('_' , ' ', $plural_name) . 'with commas',   $text_domain),
           )
       );

        // Register the taxonomy
        register_taxonomy($singular_name, array($cpt_singular), $args);

        // add default terms
        $this->taxonomyAddDefaultTerms($singular_name, $default_terms);
    }
}
<?php

namespace Framework\Helpers;

class Admin
{
    /**
     * Adds a custom column to the post admin view
     *
     * @since  1.0.0
     * @access public
     * @param  column_name, title
     * @return defaults
     */

    public function adminColumn($column_name, $title)
    {
        $defaults[$column_name] = __($title);

        return $defaults;
    }

    /**
     * Displays post meta field in custom column on post admin view
     *
     * @since  1.0.0
     * @access public
     * @param  column_name, meta_field, id
     * @return void
     */

    public function metaCustomColumn($column_name, $meta_field, $id)
    {
        if ($column_name === $column_name) {
            $column_name = get_post_meta($id, $meta_field, true);
            echo $column_name;
        }
    }

    /**
     * Adds a thumbnail column to the post admin view
     *
     * @since  1.0.0
     * @access public
     * @param  defaults
     * @return defaults
     */

    public function thumbnailColumn($defaults)
    {
        $defaults['$this->postThumbs'] = __('Thumbs');

        return $defaults;
    }

    /**
     * Displays a thumbnail in the thumbnail column of the post admin view
     *
     * @since  1.0.0
     * @access public
     * @param  column_name, id
     * @return void
     */

    public function thumbnailCustomColumn($column_name, $id)
    {
        if ($column_name === '$this->postThumbs') {
            echo the_post_thumbnail(array(100, 100));
        }
    }

    /**
     * Add custom post types to right now dashboard
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    public function dashboardCpts()
    {

        $args = array(
            'public' => true ,
            '_builtin' => false
      );

        $output = 'object';
        $operator = 'and';
        $post_types = get_post_types($args , $output , $operator);

        foreach($post_types as $post_type) {
            $num_posts = wp_count_posts($post_type->name);
            $num = number_format_i18n($num_posts->publish);
            $text = _n($post_type->labels->singular_name, $post_type->labels->name , intval($num_posts->publish));

            if (current_user_can('edit_posts')) {
                $num = "<a href='edit.php?post_type=$post_type->name'>$num";
                $text = "$text</a>";
            }

            echo '<li class="post-count">' . $num . ' ' . $text . '</li>';
        }
    }

    /**
     * Adds taxonomies to the Right Now dashboard
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    public function dashboardTaxonomies()
    {
        $taxonomies = get_taxonomies($args , $output , $operator);

        foreach($taxonomies as $taxonomy) {
            $num_terms  = wp_count_terms($taxonomy->name);
            $num = number_format_i18n($num_terms);
            $text = _n($taxonomy->labels->singular_name, $taxonomy->labels->name , intval($num_terms));

            if (current_user_can('manage_categories')) {
                $num = "<a href='edit-tags.php?taxonomy=$taxonomy->name'>$num";
                $text = "$text</a>";
            }

            echo '<li class="post-count">' . $num . ' ' . $text . '</li>';
        }
    }
}
<?php

namespace Framework\Helpers;

class Utility
{
    /**
     * Test to see if the page is a date based archive page cpt archive
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return boolean
     */

    public function isCptArchive()
    {
        if (is_category() || is_author() || is_tag() || is_date() || is_front_page() || is_home()) {
            return false;
        }

        return true;
    }

    /**
     * Test to find out if post type is cpt
     *
     * @since  1.0.0
     * @access public
     * @param  string $post post to test optional
     * @return void
     */

    public function isCustomPostType($post = NULL)
    {
        $all_custom_post_types = get_post_types(array ('_builtin' => false));

        // there are no custom post types
        if (empty ($all_custom_post_types)) {
            return false;
        }

        $custom_types = array_keys($all_custom_post_types);
        $current_post_type = get_post_type($post);

        // could not detect current type
        if (! $current_post_type) {
            return false;
        }

        return in_array($current_post_type, $custom_types);
    }

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    public function isBlogPage()
    {
        if (is_front_page() && is_home()) {
            return true;
        } elseif (is_front_page()) {
            return false;
        } elseif (is_home()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    public function slugify($str)
    {
        return str_replace(' ', '-', strtolower($str));
    }
}
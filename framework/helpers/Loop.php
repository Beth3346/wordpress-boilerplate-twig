<?php

namespace Framework\Helpers;

class Loop
{
    /**
     * Updates the number of posts that display on each archive page
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    public function setNumberOfCpts($query, $num = -1, $post_types = [], $taxonomies = [])
    {
        if ($query->is_main_query()) {
            foreach ($post_types as $post_type) {
                if (is_post_type_archive($post_type, $num)) {
                    $query->set('posts_per_page', $num);
                }
            }

            foreach ($taxonomies as $tax) {
                if (is_tax($tax)) {
                    $query->set('posts_per_page', $num);
                }
            }

            return $query;
        }
    }
}
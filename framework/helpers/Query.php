<?php

namespace Framework\Helpers;

class Query
{

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    public function getPostCount($post_type = 'post')
    {
        $posts = wp_count_posts($post_type);
        return $posts->publish;
    }

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    public function postQuery($post_type = 'post', $num = 3, $sort = 'date')
    {
        $args = [
            'post_type' => $post_type,
            'posts_per_page' => $num,
            'post_status' => 'publish',
            'orderby' => $sort
        ];

        $query = new \WP_Query($args);

        return $query;
    }

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    public function getRelatedPosts($taxonomy = 'category', $post_type = 'current', $num_posts = 3)
    {
        $id = get_the_ID();

        // config
        if ($taxonomy === 'category') {
            $term_name = $taxonomy;
            $term_id = 'cat_ID';
        } else if ($taxonomy === 'tag') {
            $term_name = 'post_tag';
            $term_id = 'term_id';
        } else {
            $term_name = $taxonomy;
            $term_id = 'term_id';
        }

        if ($post_type == 'current') {
            $post_type = get_post_type();
        }

        $terms = get_the_terms($id, $term_name);
        $related = [];

        // TODO: need to check if term exists
        if (!empty($terms)) {
            foreach($terms as $term) {
                $related[] = $term->$term_id;
            }
        } else {
            return;
        }

        if ($taxonomy == 'category') {
            $query = new \WP_Query(
                [
                    'posts_per_page' => $num_posts,
                    'category__in' => $related,
                    'post__not_in' => [$id],
                    'post_type' => $post_type
                ]
            );
        } else if ($taxonomy == 'tag') {
            $query = new \WP_Query(
                [
                    'posts_per_page' => $num_posts,
                    'tag__in' => $related,
                    'post__not_in' => [$id],
                    'post_type' => $post_type
                ]
            );
        } else {
            $query = new \WP_Query(
                [
                    'posts_per_page' => $num_posts,
                    'post_type' => $post_type,
                    'post__not_in' => [$id],
                    'tax_query' => [
                        [
                            'taxonomy' => $taxonomy,
                            'terms'    => $related,
                            'field'    => 'term_id',
                        ],
                    ],
                ]
            );
        }

        return $query;
    }

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    public function getQueryPostCount($query)
    {
        return $query->post_count;
    }
}
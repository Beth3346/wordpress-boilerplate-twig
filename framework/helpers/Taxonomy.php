<?php

namespace Framework\Helpers;

class Taxonomy
{

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    public function getCurrentTax($query)
    {
        if (is_tax()) {
            $tax_term = $query->queried_object;
            return $tax_term->name;
        } else {
            return null;
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

    public function getRelatedTerms($taxonomy, $type, $terms, $term_tax)
    {
        $rel_terms = [];
        $query = new \WP_Query([
            'post_type' => $type,
            'posts_per_page' => -1,
            'tax_query' => [
                [
                    'taxonomy' => $term_tax,
                    'terms'    => $terms,
                    'field'    => 'slug',
                ],
            ],
        ]);

        $items = $query->get_posts();

        foreach($items as $item) {
            $term = wp_get_post_terms($item->ID, $taxonomy);
            array_push($rel_terms, $term[0]->name);
        }

        return array_unique($rel_terms);
    }

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    public function isParentTerm($term)
    {
        if ($term->parent == 0) {
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

    public function getParents($taxonomy)
    {
        $terms = get_terms($taxonomy, 'orderby=count&hide_empty=1&hierarchical=1');
        $parents = [];

        foreach ($terms as $term) {
            if ($this->isParentTerm($term)) {
                array_push($parents, $term);
            }
        }

        return $parents;
    }

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    public function termHasPosts($id, $taxonomy)
    {
        $args = [
            'status' => 'publish',
            'tax_query' => [
                [
                    'taxonomy' => $taxonomy,
                    'field' => 'term_id',
                    'terms' => $id
                ]
            ]
        ];

        $term_query =  new \WP_Query($args);
        $term_posts_count = $term_query->found_posts;

        if ($term_posts_count > 0) {
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

    public function getChildren($term, $taxonomy)
    {
        if($this->isParentTerm($term)) {
            $terms = [];
            $ids = get_term_children($term->term_id, $taxonomy);

            foreach ($ids as $id) {
                if ($this->termHasPosts($id, $taxonomy)) {
                    array_push($terms, get_term($id));
                }
            }

        } else {
            $terms = null;
        }

        return $terms;
    }
}
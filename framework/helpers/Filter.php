<?php

namespace Framework\Helpers;

class Filter
{

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    public function cptFilters($taxonomies, $post_archive, $tax_term)
    {
        foreach ($taxonomies as $tax) {
            $this->tax_nav_filter($post_archive, $tax, $tax_term);
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

    public function cptLimitFilters($post_archive)
    {
        echo '<nav class="num-results-nav">';
        echo '<ul class="elr-inline-list num-results-menu">';
        echo '<li><a class="active" href="/' . $post_archive . '/" data-num="5">5</a></li>';
        echo '<li><a href="/' . $post_archive . '/" data-num="10">10</a></li>';
        echo '<li><a href="/' . $post_archive . '/" data-num="-1">All</a></li>';
        echo '</ul>';
        echo '</nav>';
    }

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    public function postCount($query, $num_posts)
    {
        return 'Showing '. $query->post_count . ' of ' . $num_posts;
    }

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    public function filterTaxonomyScripts($file_name = 'main', $current_tax)
    {
        wp_localize_script($file_name, '$this->vars', array(
                '$this->nonce' => wp_create_nonce('$this->nonce'),
                '$this->ajax_url' => admin_url('admin-ajax.php'),
                '$this->current_term' => strtolower(single_term_title('', false)),
                '$this->current_tax' => $current_tax
           )
       );
    }

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    // Script for getting posts
    public function filterTaxonomy($taxonomy, $limiter)
    {
        // Verify nonce
        if (!isset($_POST['$this->nonce']) || !wp_verify_nonce($_POST['$this->nonce'], '$this->nonce')) {
            die('Permission denied');
        }

        $tax_args = array();
        $current_tax = null;

        if (array_key_exists('taxonomy', $_POST)) {
            $taxonomy = $_POST['taxonomy'];

            foreach ($taxonomy as $key => $value) {
                $arr = array('taxonomy' => $key, 'field' => 'slug', 'terms' => array($value));
                array_push($tax_args, $arr);
            }

            // check if taxonomy page
            if ($_POST['$this->current_term']) {
                $current_tax = $_POST['$this->current_term'];
                $arr = array('taxonomy' => $limiter, 'field' => 'slug', 'terms' => array($current_tax));
                array_push($tax_args, $arr);
            }
        }

        if (array_key_exists('num', $_POST)) {
            $num = $_POST['num'];
        } else {
            $num = 20;
        }

        if (array_key_exists('post_type', $_POST)) {
            $post_type = $_POST['post_type'];
        } else {
            $post_type = 'posts';
        }

        if (post_type_exists($post_type)) {
            $count_posts = wp_count_posts($post_type);
            $num_posts = $count_posts->publish;
        } else {
            $num_posts = 0;
        }

        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

        // WP Query
        $args = array(
            'post_type' => $post_type,
            'posts_per_page' => $num,
            'tax_query' => $tax_args,
            'paged' => $paged,
            'post_status' => 'publish'
       );

        // If taxonomy is not set, remove key from array and get all posts
        if (!$taxonomy)
        {
            unset($args['tax_query']);
        }

        $query = new \WP_Query($args);

        if ($query->have_posts()) :
            while ($query->have_posts()) : $query->the_post(); ?>
                <?php require(get_template_directory() . '/content/content-' . $post_type . '.php'); ?>
            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
        <?php else: ?>
            <h2>No products found</h2>
        <?php endif;

        die();
    }
}
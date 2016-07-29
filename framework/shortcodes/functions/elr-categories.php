<?php

add_shortcode( 'elr-categories', function( $atts, $content = null ) {
    extract( shortcode_atts( array(
        'style' => '',
        'num' => 'all',
        'by_count' => false,
        'hierarchical' => true,
        'count' => false,
    ), $atts ) );

    $cat_args = array(
        'orderby' => 'name',
        'hierarchical' => $hierarchical,
        'hide_empty' => true
    );

    if ( $by_count ) {
        $cat_args['orderby'] = 'count';
        $cat_args['order'] = 'DESC';
    }

    if ( $num != 'all' ) {
        $cat_args['number'] = $num;
    }

    $string = '<section class="elr-categories">';

    if ( $content != null ) {
        $string .= '<h1>';
        $string .= esc_html( $content );
        $string .= '</h1>';
    }
    $string .= '<ul>';

    $terms = get_terms( 'category', $cat_args );

    if ( !empty( $terms ) && !is_wp_error( $terms ) ){
        foreach ( $terms as $term ) {
            $term_link = get_term_link( $term );

            $args = array(
                'post_type' => 'any',
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'category',
                        'terms' => $term->slug,
                        'field' => 'slug',
                        'operator' => 'IN',
                    )
                )
            );

            $query = new WP_Query( $args );
            $post_count = $query->post_count;

            $string .= '<li>';
            $string .= '<a href="';
            $string .= $term_link;
            $string .= '">';
            $string .= $term->name;
            $string .= '</a>';

            if ( $count ) {
                $string .= ' ' . $post_count;
            }

            $string .= '</li>';
        }
    }

    $string .= '</ul>';
    $string .= '</section>';

    return $string;
});
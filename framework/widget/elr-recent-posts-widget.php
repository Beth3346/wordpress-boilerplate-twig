<?php
/*
 * @package   ELR_Recent_Posts_Widget
 * @author    Elizabeth Rogers <beth@elizabeth-rogers.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014 Elizabeth Rogers
 */

class ELR_Recent_Posts_Widget extends WP_Widget {
    function __construct() {
        $widget_ops = array('classname' => 'widget_recent_custom_posts', 'description' => __( "Your site&#8217;s most recent posts with thumbnails.") );
        parent::__construct('recent-custom-posts', __('ELR Recent Posts'), $widget_ops);
        $this->alt_option_name = 'widget_recent_custom_posts';

        add_action( 'save_post', array($this, 'flush_widget_cache') );
        add_action( 'deleted_post', array($this, 'flush_widget_cache') );
        add_action( 'switch_theme', array($this, 'flush_widget_cache') );
    }

    function widget($args, $instance) {
        $cache = array();
        if ( ! $this->is_preview() ) {
            $cache = wp_cache_get( 'widget_recent_custom_posts', 'widget' );
        }

        if ( ! is_array( $cache ) ) {
            $cache = array();
        }

        if ( ! isset( $args['widget_id'] ) ) {
            $args['widget_id'] = $this->id;
        }

        if ( isset( $cache[ $args['widget_id'] ] ) ) {
            echo $cache[ $args['widget_id'] ];
            return;
        }

        ob_start();
        extract($args);

        $title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Recent Posts' );

        /** This filter is documented in wp-includes/default-widgets.php */
        $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
        $number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;

        if ( ! $number ) {
            $number = 5;
        }

        $show_thumbnail = isset( $instance['show_thumbnail'] ) ? $instance['show_thumbnail'] : false;
        $show_date = isset( $instance['show_date'] ) ? $instance['show_date'] : false;
        $show_all = isset( $instance['show_all'] ) ? $instance['show_all'] : false;
        $cpt = ( ! empty( $instance['cpt'] ) ) ? $instance['cpt'] : 'post';

        /**
         * Filter the arguments for the Recent Posts widget.
         *
         * @since 3.4.0
         *
         * @see WP_Query::get_posts()
         *
         * @param array $args An array of arguments used to retrieve the recent posts.
         */

        $query_args = array(
            'post_type' => $cpt,
            'no_found_rows' => true,
            'post_status' => 'publish',
            'ignore_sticky_posts' => true
        );

        if ( $show_all ) {
            $query_args['posts_per_page'] = -1;
        } else {
            $query_args['posts_per_page'] = $number;
        }

        $r = new WP_Query( apply_filters( 'widget_posts_args', $query_args ) );

        if ($r->have_posts()) :
?>
        <?php echo $before_widget; ?>
        <?php if ( $title ) echo $before_title . $title . $after_title; ?>
        <ul class="elr-recent-posts-widget">
        <?php while ( $r->have_posts() ) : $r->the_post(); ?>
            <li>
            <?php if ( has_post_thumbnail() && $show_thumbnail ) : ?>
                <figure class="elr-widget-thumbnail">
                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a>
                </figure>
            <?php endif; ?>
                <p><a href="<?php the_permalink(); ?>"><?php get_the_title() ? the_title() : the_ID(); ?></a>
                <?php if ( $show_date ) : ?>
                    <span class="post-date"><?php echo get_the_date(); ?></span>
                <?php endif; ?>
                </p>
            </li>
        <?php endwhile; ?>
        </ul>
        <?php echo $after_widget; ?>
<?php
        // Reset the global $the_post as this query will have stomped on it
        wp_reset_postdata();

        endif;

        if ( ! $this->is_preview() ) {
            $cache[ $args['widget_id'] ] = ob_get_flush();
            wp_cache_set( 'widget_recent_custom_posts', $cache, 'widget' );
        } else {
            ob_end_flush();
        }
    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['number'] = (int) $new_instance['number'];
        $instance['show_date'] = isset( $new_instance['show_date'] ) ? (bool) $new_instance['show_date'] : false;
        $instance['show_thumbnail'] = isset( $new_instance['show_thumbnail'] ) ? (bool) $new_instance['show_thumbnail'] : false;
        $instance['show_all'] = isset( $new_instance['show_all'] ) ? (bool) $new_instance['show_all'] : false;
        $instance['cpt'] = strip_tags($new_instance['cpt']);
        $this->flush_widget_cache();

        $alloptions = wp_cache_get( 'alloptions', 'options' );
        if ( isset($alloptions['widget_recent_custom_posts']) )
            delete_option('widget_recent_custom_posts');

        return $instance;
    }

    function flush_widget_cache() {
        wp_cache_delete('widget_recent_custom_posts', 'widget');
    }

    function form( $instance ) {
        $title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
        $number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
        $show_date = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;
        $show_thumbnail = isset( $instance['show_thumbnail'] ) ? (bool) $instance['show_thumbnail'] : false;
        $show_all = isset( $instance['show_all'] ) ? (bool) $instance['show_all'] : false;
        $cpt = isset( $instance['cpt'] ) ? esc_attr( $instance['cpt'] ) : '';
        $post_types = get_post_types( array ( '_builtin' => FALSE ) );
?>
        <p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

        <p><label for="<?php echo $this->get_field_id( 'cpt' ); ?>"><?php _e( 'Post Type:' ); ?></label></p>
        <select class="widefat" name="<?php echo $this->get_field_name( 'cpt' ); ?>" id="<?php echo $this->get_field_id( 'cpt' ); ?>">
            <option value="">Select Post Type</option>
            <?php if ( $cpt === 'post' ) : ?>
                <option selected value="post">Post</option>
            <?php else : ?>
                <option value="post">Post</option>
            <?php endif; ?>
            <?php foreach ( $post_types as $post_type ) { ?>
            <?php
                $count_posts = wp_count_posts( $post_type );
                $num_posts = $count_posts->publish;
             ?>
                <?php if ( $post_type === $cpt ) : ?>
                    <option selected value="<?php echo esc_attr( $post_type ); ?>"><?php echo esc_html( ucwords( str_replace( '_' , ' ', $post_type ) ) ); ?></option>
                <?php elseif ( $num_posts > 0 ) : ?>
                    <option value="<?php echo esc_attr( $post_type ); ?>"><?php echo esc_html( ucwords( str_replace( '_' , ' ', $post_type ) ) ); ?></option>
                <?php endif; ?>
            <?php } ?>
        </select>

        <p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to show:' ); ?></label>
        <input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>

        <p><input class="checkbox" type="checkbox" <?php checked( $show_all ); ?> id="<?php echo $this->get_field_id( 'show_all' ); ?>" name="<?php echo $this->get_field_name( 'show_all' ); ?>" />
        <label for="<?php echo $this->get_field_id( 'show_all' ); ?>"><?php _e( 'Show all?' ); ?></label></p>
        <small>Overrides number of posts to show field</small>

        <p><input class="checkbox" type="checkbox" <?php checked( $show_date ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" />
        <label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php _e( 'Display post date?' ); ?></label></p>

        <p><input class="checkbox" type="checkbox" <?php checked( $show_thumbnail ); ?> id="<?php echo $this->get_field_id( 'show_thumbnail' ); ?>" name="<?php echo $this->get_field_name( 'show_thumbnail' ); ?>" />
        <label for="<?php echo $this->get_field_id( 'show_thumbnail' ); ?>"><?php _e( 'Display post thumbnail?' ); ?></label></p>
<?php
    }
}

if ( ! function_exists( 'elr_register_elr_recent_posts_widget' ) ) {
    function elr_register_elr_recent_posts_widget() {
        register_widget( 'ELR_Recent_Posts_Widget' );
    }
}

add_action( 'widgets_init', 'elr_register_elr_recent_posts_widget', 1 );
?>
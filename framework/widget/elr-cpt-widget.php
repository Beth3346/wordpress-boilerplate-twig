<?php
/*
 * @package   ELR_CPT_Widget
 * @author    Elizabeth Rogers <beth@elizabeth-rogers.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014 Elizabeth Rogers
 */

class ELR_CPT_Widget extends WP_Widget {
    function __construct() {
        $widget_ops = array('classname' => 'widget_cpts', 'description' => __( "Display a list of registered custom post types.") );
        parent::__construct('widget_cpts', __('ELR Custom Post Types'), $widget_ops);
        $this->alt_option_name = 'widget_cpts';

        add_action( 'save_post', array($this, 'flush_widget_cache') );
        add_action( 'deleted_post', array($this, 'flush_widget_cache') );
        add_action( 'switch_theme', array($this, 'flush_widget_cache') );
    }

    function widget($args, $instance) {
        $cache = array();
        if ( ! $this->is_preview() ) {
            $cache = wp_cache_get( 'widget_cpts', 'widget' );
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

        $title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Post Types' );

        /** This filter is documented in wp-includes/default-widgets.php */
        $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
        $post_types = get_post_types( array ( '_builtin' => FALSE ) );
?>
        <?php echo $before_widget; ?>
        <?php if ( $title ) echo $before_title . $title . $after_title; ?>
        <?php foreach ( $post_types as $post_type ) { ?>
            <?php
                $cpt_archive = get_post_type_archive_link( $post_type );
                $post_name = get_post_type_object( $post_type )->label;
                $published_posts = wp_count_posts( $post_type )->publish;
            ?>
            <ul>
                <?php if ( $published_posts > 0 ) : ?>
                    <?php if ( $cpt_archive ) : ?>
                    <li>
                        <a href="<?php echo esc_url( $cpt_archive ); ?>">
                            <?php echo ucwords( $post_name ); ?>
                        </a>
                    </li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
        <?php } ?>
        <?php echo $after_widget; ?>
<?php
        if ( ! $this->is_preview() ) {
            $cache[ $args['widget_id'] ] = ob_get_flush();
            wp_cache_set( 'widget_cpts', $cache, 'widget' );
        } else {
            ob_end_flush();
        }
    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $this->flush_widget_cache();

        $alloptions = wp_cache_get( 'alloptions', 'options' );

        if ( isset($alloptions['widget_cpts']) )
            delete_option('widget_cpts');

        return $instance;
    }

    function flush_widget_cache() {
        wp_cache_delete('widget_cpts', 'widget');
    }

    function form( $instance ) {
        $title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';?>
        <p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input
                class="widefat"
                id="<?php echo $this->get_field_id( 'title' ); ?>"
                name="<?php echo $this->get_field_name( 'title' ); ?>"
                type="text"
                value="<?php echo $title; ?>"
            />
        </p>
<?php
    }
}

if ( ! function_exists( 'elr_register_elr_cpt_widget' ) ) {
    function elr_register_elr_cpt_widget() {
        register_widget( 'ELR_CPT_Widget' );
    }
}

add_action( 'widgets_init', 'elr_register_elr_cpt_widget', 1 );
?>
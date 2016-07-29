<?php

add_shortcode( 'elr-phone', function( $atts, $content = null ) {
    $options = get_option( 'elr_theme_social_options' );

    if ( !empty( $options['main_phone'] ) ) {
        $main_phone = $options['main_phone'];
    } else {
        $main_phone = null;
    }

    if ( $content == null ) {
        $content = $main_phone;
    }

    return '<a href="tel:' . $content . '">' . $content . '</a>';
});
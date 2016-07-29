<?php

add_shortcode( 'elr-email', function( $atts, $content = null ) {
    if ( $content == null ) {
        $content = '';
    }

    return '<a href="mailto:' . antispambot($content) . '">' . antispambot($content) . '</a>';
});
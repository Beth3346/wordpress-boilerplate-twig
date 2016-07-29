<?php

add_shortcode( 'elr-youtube', function( $atts ) {
    extract( shortcode_atts( array(
        'src' => '',
        'width' => 560,
        'height' => 315
    ), $atts ) );

    $string = '<div class="page-video-holder"><div class="elr-video-wrapper">';
    $string .= '<iframe width="' . $width . '" height="' . $height . '" src="';
    $string .= $src;
    $string .= '" frameborder="0" allowfullscreen></iframe></div></div>';

    return $string;
});
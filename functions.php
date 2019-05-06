<?php

function astra_child_enqueue_styles(){
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('main-css', get_stylesheet_directory_uri() . '/style.css');
}

add_action( 'wp_enqueue_scripts', 'astra_child_enqueue_styles' );

function add_excerpt_read_more( $excerpt ){
    global $post;

    if ( has_excerpt( $post->ID)) {        
        $more = sprintf(
            '<a class="read-more" href="%1$s">%2$s &raquo;</a>',
            get_permalink( get_the_ID() ),
            __( 'Read More', 'astrachild')
        );
        return $excerpt . $more;
    }

    return $excerpt;

}
add_filter( 'the_excerpt', 'add_excerpt_read_more');
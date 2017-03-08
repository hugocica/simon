<?php

// Incluindo arquivos de estilo
add_action( 'wp_enqueue_scripts', 'simon_enqueue_styles' );
function simon_enqueue_styles() {
    $parent_style = 'namo-style';
    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'bootstrap-style', 
    	get_stylesheet_directory_uri() . '/css/bootstrap.min.css',
    	array( $parent_style ), 
    	wp_get_theme()->get('Version')
    );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );
    wp_enqueue_style( 'simon-style', 
    	get_stylesheet_directory_uri() . '/css/simon.css',
    	array( $parent_style ), 
    	wp_get_theme()->get('Version')
    );
}
// Incluindo arquivos scripts js
add_action( 'wp_enqueue_scripts', 'simon_enqueue_scripts' );
function simon_enqueue_scripts() {
    wp_enqueue_script( 'royalslider', 
        get_stylesheet_directory_uri() . '/js/jquery.royalslider.min.js', 
        array ( 'jquery' ), 
        wp_get_theme()->get('Version'), 
        true
    );
    
    wp_enqueue_script( 'grupoi9_ajax', 
        get_stylesheet_directory_uri() . '/js/grupoi9-ajax.js'
    );
	wp_localize_script( 'grupoi9_ajax', 
	    'Grupoi9Ajax', 
	    array( 
	        'ajaxurl' => admin_url( 'admin-ajax.php' ) 
	    ) 
    );
}

// Escondendo a barra de admin do front-end
show_admin_bar( false );

add_action( 'after_setup_theme', 'register_footer_menu' );
function register_footer_menu() {
  register_nav_menu( 'footer_menu', __( 'Footer Menu', 'namo-child' ) );
}

// include metabox files
include_once get_stylesheet_directory() . '/metaboxes/setup.php';
include_once get_stylesheet_directory() . '/metaboxes/simon-spec.php';

// include ajax functions
include_once get_stylesheet_directory() . '/inc/grupoi9-ajax.php';

?>
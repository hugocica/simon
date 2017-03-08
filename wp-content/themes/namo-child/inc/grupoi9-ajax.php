<?php

add_action( 'wp_ajax_load_propriedades', 'load_propriedades' ); 
add_action( 'wp_ajax_nopriv_load_propriedades', 'load_propriedades' ); 

function load_propriedades() {
    $cityID = (int) trim( $_POST['pid'] );
    
    $propriedades = get_post_meta( $cityID, '_propriedades_metabox', true );
    
    $output['content'] = '';
    
    if ( ! empty($propriedades['propriedades']) ) {
        $output['content'] .= '<div class="propriedade-wrapper propriedade-'. $cityID .'">';
        $output['content'] .= '<div class="propriedade-title"><h2 class="be-wrap">'. get_the_title( $cityID ) .'</h2></div>';
        
        $output['content'] .= '<div class="propriedade-inner-wrapper be-wrap">';
        foreach ( $propriedades['propriedades'] as $propriedade ) {
            if ( !empty($propriedade['nome']) ) {
                $output['content'] .= '<div class="propriedade col-md-3">';
                $output['content'] .= '<div class="entry-meta">
                                            <h3 class="entry-title">'. $propriedade['nome'] .'</h3>
                                        </div>
                                        <div class="entry-thumb">
                                            <img src="'. $propriedade['photo'] .'">
                                        </div>
                                        <div class="entry-content">
                                            <p class="entry-description">'. $propriedade['description'] .'</p>
                                            <p class="entry-location">
                                                <span class="img-wrapper">
                                                    <img src="'. get_stylesheet_directory_uri() .'/images/location-icon.png" alt="location icon">
                                                </span>
                                                <span class="entry-text">'. $propriedade['location'] .'</span>
                                            </p>
                                            <p class="entry-time">
                                                <span class="img-wrapper">
                                                    <img src="'. get_stylesheet_directory_uri() .'/images/clock-icon.png" alt="clock icon">
                                                </span>
                                                <span class="entry-text">'. $propriedade['time'] .'</span>
                                            </p>
                                            <p class="entry-site">
                                                <span class="img-wrapper">
                                                    <img src="'. get_stylesheet_directory_uri() .'/images/link-icon.png" alt="link icon">
                                                </span>
                                                <span class="entry-text">'. $propriedade['link'] .'</span>
                                            </p>
                                        </div>';
                $output['content'] .= '</div>';
            }
        }
        
        $output['content'] .= '</div></div>';
        
        $output['type'] = 'success';   
    } else {
        $output['type'] = 'empty';   
    }
    echo json_encode( $output );
    
    die();
}
<?php

// Carica il file CSS specifico per UpSeller se esiste
if ( file_exists( get_stylesheet_directory() . '/UpSeller/style-upseller.css' ) ) {
    function upseller_enqueue_styles() {
        wp_enqueue_style(
            'style-upseller',
            get_stylesheet_directory_uri() . '/UpSeller/style-upseller.css',
            array(),
            '1.8.0'
        );
    }
    add_action('wp_enqueue_scripts', 'upseller_enqueue_styles');
}

// Includi il file delle funzioni UpSeller se esiste
if ( file_exists( get_stylesheet_directory() . '/UpSeller/functions-upseller.php' ) ) {
    include_once( get_stylesheet_directory() . '/UpSeller/functions-upseller.php' );
}

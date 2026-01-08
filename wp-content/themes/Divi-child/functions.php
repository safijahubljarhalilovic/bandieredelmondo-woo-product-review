<?php
// Includi il file functions-tow.php del tema child
include_once( get_stylesheet_directory() . '/functions-immagine-categoria-prodotto.php' );
include_once( get_stylesheet_directory() . '/functions-video-youtube.php' );
include_once( get_stylesheet_directory() . '/functions-tow.php' );
include_once( get_stylesheet_directory() . '/functions-utilizzo.php' );
include_once( get_stylesheet_directory() . '/functions-login.php' );
include_once( get_stylesheet_directory() . '/functions-calendario-bandiere.php' );
include_once( get_stylesheet_directory() . '/functions-slick-bandiere.php' );
include_once( get_stylesheet_directory() . '/functions-menus.php' );
include_once( get_stylesheet_directory() . '/functions_checkout.php' );
include_once( get_stylesheet_directory() . '/functions-bandiere-eco.php' );
include_once( get_stylesheet_directory() . '/functions-bandiere-hotel.php' );
include_once( get_stylesheet_directory() . '/functions-varianti-scheda-bandiera.php' );
include_once( get_stylesheet_directory() . '/functions-breadcrumb.php' );
include_once( get_stylesheet_directory() . '/functions-prezzo-recente-scontato.php' );
include_once( get_stylesheet_directory() . '/functions-banner-sconti.php' );
include_once( get_stylesheet_directory() . '/functions-tags.php' );
include_once( get_stylesheet_directory() . '/UpSeller/index.php' );



// Includi il file JavaScript varianti-ratio-standard.js con jQuery come dipendenza
function include_custom_js() {
    wp_enqueue_script( 'varianti-ratio-standard', get_stylesheet_directory_uri() . '/js/varianti-ratio-standard.js', array('jquery'), '1.8.0', true );
 //   wp_enqueue_script( 'nuovo-file', get_stylesheet_directory_uri() . '/js/nuovo-file.js', array('jquery'), '1.0', true );
}
add_action( 'wp_enqueue_scripts', 'include_custom_js' );


/* carica js css e fonts di slick libraries TOW*/

function enqueue_slick_scripts() {
// Include jQuery e jQuery Migrate da script esterni, non servono probabile già attivi con il tema DIVI
// wp_enqueue_script('jquery', '//code.jquery.com/jquery-1.11.0.min.js', array(), '1.11.0', true);
// wp_enqueue_script('jquery-migrate', '//code.jquery.com/jquery-migrate-1.2.1.min.js', array(), '1.2.1', true);
    
    wp_enqueue_script('slick-js', get_stylesheet_directory_uri() . '/slick/js/slick.js', array('jquery'), '1.8.0', true);
    wp_enqueue_script('index-js', get_stylesheet_directory_uri() . '/slick/index.js', array('jquery'), '1.8.0', true);
    wp_enqueue_style('slick-theme-css', get_stylesheet_directory_uri() . '/slick/css/slick-theme.css', array(), '1.8.0');
    wp_enqueue_style('slick-css', get_stylesheet_directory_uri() . '/slick/css/slick.css', array(), '1.8.0');

    // creata da tow
    wp_enqueue_style('slick-testi-slider-acf', get_stylesheet_directory_uri() . '/slick/css/slick-testi-slider-acf.css', array(), '1.8.0');
    wp_enqueue_style('style-tow', get_stylesheet_directory_uri() . '/style-tow.css', array(), '1.8.0');

}
add_action('wp_enqueue_scripts', 'enqueue_slick_scripts');
/*Fine*/


function defer_parsing_of_js($url) {
    if (is_admin()) return $url;
    if (strpos($url, '.js') === false) return $url;
    return str_replace(' src=', ' defer src=', $url);
}
add_filter('script_loader_tag', 'defer_parsing_of_js', 10);

/* setta dimensioni immagini per evitare effetto shift */

// Aggiunge width/height a TUTTE le immagini
function fix_image_dimensions($content) {
    preg_match_all('/<img[^>]+>/i', $content, $images);
    
    foreach ($images[0] as $image) {
        // Se già ha dimensioni, salta
        if (strpos($image, 'width=') !== false && strpos($image, 'height=') !== false) {
            continue;
        }
        
        // Estrai src
        preg_match('/src="([^"]+)"/i', $image, $src);
        if (!empty($src[1])) {
            // Ottieni dimensioni reali
            $image_path = str_replace(site_url(), ABSPATH, $src[1]);
            if (file_exists($image_path)) {
                list($width, $height) = getimagesize($image_path);
                
                // Sostituisci il tag img
                $new_image = str_replace('<img', '<img width="' . $width . '" height="' . $height . '"', $image);
                $content = str_replace($image, $new_image, $content);
            }
        }
    }
    
    return $content;
}
add_filter('the_content', 'fix_image_dimensions'); 


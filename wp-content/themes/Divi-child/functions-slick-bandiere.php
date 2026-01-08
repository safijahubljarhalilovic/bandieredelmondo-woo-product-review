<?php

function visualizza_slider_shortcode() {
    ob_start(); // Inizializza l'output buffer

    // Query per recuperare i prodotti WooCommerce con campo 'prodotto-slider' impostato su True
    $args = array(
        'post_type' => 'product', // Tipo di post WooCommerce
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'prodotto_slider',
                'value' => '1',
                'compare' => '=',
            ),
            
        ),
    );

    /*
    var_dump($current_date);
    var_dump($args['meta_query'][0]['value']); // Assuming 'prodotto-slider' is in $args
    var_dump($args['meta_query'][1]['value']); // Assuming 'data-evento-check-slider' is in $args
    */
    

    //  creare una nuova istanza di WP_Query per interrogare il database e recuperare i contenuti del sito in base agli argomenti specificati in $args.
    $query = new WP_Query($args);

    if ($query->have_posts()) {
        // Inizio dello slider HTML
        echo '<div class="slick-bandiere-del-mondo">';

        while ($query->have_posts()) {
            $query->the_post();

            $page_id = get_the_ID();

            // Recupera i campi desiderati con ACF
            // $titolo_del_post_slider = get_the_title($post_id);
            
            $testo_1_slider = get_field('testo_1_slider');
            $seo_title = get_post_meta(get_the_ID(), '_yoast_wpseo_title', true);
            $testo_2_slider = get_field('testo_2_slider');
            $img_slider = get_field('img_slider');
            $permalink = get_permalink();

            
            // Output dell'elemento dello slider
            
           // <img id="immagine-slider-id" src="' . $immagine_slider . '" alt="Immagine evento">';
           // echo '<p id="titolo-del-post-slider">' . $titolo_del_post_slider . '</p>';
            
            echo '<div class="bandiere-del-mondo-slider-container">';
            echo '<div class="bandiere-del-mondo-slider" style="background-image: url(' . $img_slider . '); ">';
            echo '<div class="testi-slider">';
            echo '<p id="testo-1-slider">' . $testo_1_slider . '</p>';
            echo '<p id="titolo-seo-title">' . $seo_title . '</p>';  
            echo '<p id="testo-2-slider">' . $testo_2_slider . '</p>';
            echo '<br>';
            echo '<a id="bottone-slick" href="' . $permalink . '" class="bottone-slick">Acquista ora</a>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }

        // Fine dello slider HTML
        echo '</div>';
    } else {
        // Messaggio se non ci sono prodotti
        echo 'Non ci sono prodotti disponibili.';
    }

    wp_reset_postdata();

    $output = ob_get_clean(); // Ottieni l'output dal buffer
    return $output;
}

add_shortcode('visualizza_slider_shortcode', 'visualizza_slider_shortcode');
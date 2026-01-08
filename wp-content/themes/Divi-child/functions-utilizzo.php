<?php

function display_utilizzo_image($atts) {
    global $product;

    if (!$product) {
        return '';
    }

    $product_id = $product->get_id();
    $utilizzo = get_field('utilizzo', $product_id);

    if ($utilizzo) {
        // Cambia il percorso delle immagini (senza / all'inizio)
        $image_directory = 'wp-content/themes/Divi-child/images/utilizzo/';

        // Estensione del file
        $image_extension = 'png';

        // Genera il nome del file immagine
        $image_filename = $utilizzo . '.' . $image_extension;

        // Genera l'URL dell'immagine
        $image_url = site_url('/') . $image_directory . $image_filename;

        // Imposta la larghezza dell'immagine a 100px e aggiungi l'ID e il nome del file come didascalia
        $image_html = '<div>';
        $image_html .= '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($utilizzo) . '" width="100" />';
        $image_html .= '</div>';

        return $image_html;
    } else {
        return ''; // Se il campo 'Utilizzo' non è stato trovato, non visualizzare l'immagine
    }
}

add_shortcode('utilizzo_image', 'display_utilizzo_image');
<?php

// Shortcode per mostrare le prime 4 bandiere (upsell)
function upseller_first_four_shortcode($atts) {
    global $product;

    // Verifica se siamo su una pagina prodotto
    if (!is_a($product, 'WC_Product')) {
        return ''; // Non fare nulla se non è un prodotto
    }

    // Ottieni gli ID dei prodotti upsell
    $upsell_ids = $product->get_upsell_ids();

    // Conta i prodotti upsell
    $total_upsell = count($upsell_ids);

    // Nessun prodotto upsell
    if ($total_upsell === 0) {
        return '';
    }

    // Modifica la query per mostrare solo i primi 4 prodotti
    $args = array(
        'post_type' => 'product',
        'post__in' => $upsell_ids,
        'posts_per_page' => min(4, $total_upsell), // Mostra massimo 4 prodotti
        'orderby' => 'post__in', // Mantieni l'ordine configurato
    );

    $query = new WP_Query($args);

    // Output dei prodotti upsell
    if ($query->have_posts()) {
        ob_start(); // Avvia l'output buffering
        echo '<div class="upseller-first-four">';
        while ($query->have_posts()) {
            $query->the_post();
            wc_get_template_part('content', 'product'); // Usa il template WooCommerce
        }
        echo '</div>';
        wp_reset_postdata(); // Resetta la query
        return ob_get_clean(); // Ritorna l'output bufferizzato
    }

    // In caso di problemi imprevisti
    return '';
}
add_shortcode('upseller_first_four', 'upseller_first_four_shortcode');




// Shortcode per mostrare i prodotti upsell dal 5° all'8° con un valore ACF dinamico
function upseller_next_four_shortcode($atts) {
    global $product;

    // Verifica se siamo su una pagina prodotto
    if (!is_a($product, 'WC_Product')) {
        return ''; // Non fare nulla se non è un prodotto
    }

    // Ottieni il valore del campo ACF
    $h2Acf = get_field('titolo_h2_bandiera'); // Sostituisci con il nome del tuo campo ACF
    if (!$h2Acf) {
        $h2Acf = 'questo prodotto'; // Testo di fallback se il campo ACF è vuoto
    }

    // Ottieni gli ID dei prodotti upsell
    $upsell_ids = $product->get_upsell_ids();

    // Conta i prodotti upsell
    $total_upsell = count($upsell_ids);

    // Nessun prodotto upsell
    if ($total_upsell === 0) {
        return ''; // Nessun output se non ci sono prodotti upsell
    }

    // Se ci sono meno di 5 prodotti, non mostra nulla
    if ($total_upsell < 5) {
        return ''; // Nessun output se non ci sono abbastanza prodotti
    }

    // Modifica la query per mostrare i prodotti dal 5° all'8°
    $args = array(
        'post_type' => 'product',
        'post__in' => $upsell_ids,
        'posts_per_page' => 4, // Mostra massimo 4 prodotti
        'orderby' => 'post__in', // Mantieni l'ordine configurato
        'offset' => 4 // Salta i primi 4 prodotti
    );

    $query = new WP_Query($args);

    // Output dei prodotti upsell
    if ($query->have_posts()) {
        ob_start(); // Avvia l'output buffering
        echo '<h4>Prodotti suggeriti per la ' . esc_html($h2Acf) . ':</h4>'; // Mostra il valore ACF dinamico
        echo '<div class="upseller-first-four">'; // Usa la stessa classe CSS
        while ($query->have_posts()) {
            $query->the_post();
            wc_get_template_part('content', 'product'); // Usa il template WooCommerce
        }
        echo '</div>';
        wp_reset_postdata(); // Resetta la query
        return ob_get_clean(); // Ritorna il contenuto bufferizzato
    }

    // In caso di problemi imprevisti
    return ''; // Nessun output
}
add_shortcode('upseller_next_four', 'upseller_next_four_shortcode');

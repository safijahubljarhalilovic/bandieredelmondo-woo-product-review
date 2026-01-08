<?php

function trova_varianti_per_bandiere_economiche($atts) {
    $args = array(
        'post_type'      => 'product_variation',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'meta_query'     => array(
            'relation' => 'AND',
            array(
                'key'     => 'attribute_pa_materiale',
                'value'   => 'poliestere-leggero',
                'compare' => '=',
            ),
            array(
                'key'     => 'attribute_pa_formati',
                'value'   => '91x140-cm',
                'compare' => '=',
            ),
            array(
                'key'     => '_price',
                'value'   => '',
                'compare' => '!=',
                'type'    => 'NUMERIC',
            ),
        ),
    );

    

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        $output = '';

        while ($query->have_posts()) {
            $query->the_post();
            $variation_id = get_the_ID();
            $variation = new WC_Product_Variation($variation_id);


            $sku = $variation->get_sku();
          
            $sku_validi = array(
                'ALB', 'DZA', 'ARG', 'ARM', 'AUS', 'AUT', 'BGD', 'BEL', 'BIH', 'BRA', 'BGR',
                'CAN', 'CHL', 'CHN', 'CYP', 'COL', 'PRK', 'KOR', 'HRV', 'CUB', 'DNK', 'ARE',
                'ECU', 'EST', 'FIN', 'FRA', 'DEU', 'GHA', 'JPN', 'GRC', 'GIN', 'IND', 'IRL',
                'ISL', 'ISR', 'ITA', 'LVA', 'LBY', 'LTU', 'LUX', 'MLT', 'MEX', 'NGA', 'NOR',
                'NLD', 'POL', 'PRT', 'GBR', 'CZE', 'ROU', 'RUS', 'SMR', 'SEN', 'SRB', 'SVK',
                'ESP', 'USA', 'SWE', 'CHE', 'TUN', 'TUR', 'HUN', 'URY', 'VAT'
            );

            // Se lo SKU non è nella lista, salta questa variante
            if ( ! in_array( $sku, $sku_validi ) ) {
                continue;
            }

            $confezionamento = 'Tasca chiusa in alto';

            $image = wp_get_attachment_image_src($variation->get_image_id(), 'medium');
            $title = $variation->get_title();
            $price = $variation->get_price_html();

            $output .= '<div class="bandiere-tipi-vari">';
            $output .= '<img src="' . $image[0] . '" alt="' . $title . '" />';
            $output .= '<p class="p-bandiere-tipi-vari">' . $title . ' eco</p>';
            $output .= '<p class="p-prezzo-bandiere-tipi-vari">' . $price . '</p>';
            $output .= '<form method="post" action="' . get_site_url() . '/">';
            $output .= '<input type="hidden" name="add-to-cart" value="' . $variation_id . '">';
            $output .= '<input type="hidden" name="confezionamento" value="' . $confezionamento . '">';
            $output .= '<input type="number" id="quantity-' . $variation_id . '" name="quantity" value="1" min="1" max="50000" class="quantity-bandiere-tipi-vari">';
            $output .= '<button type="submit" class="button-bandiere-tipo-vari">';
            $output .= '<img src="https://bandieredelmondo.it/wp-content/uploads/2024/04/carrello-bandiere-del-mondo.png" alt="Icona del carrello" class="carrello-bandiere-tipi-vari">';
            $output .= '</button>';
            $output .= '</form>';
            $output .= '</div>';
        }

        wp_reset_postdata();

        return $output;
    }

    return 'Nessuna variante trovata con gli attributi specificati.';
}

add_shortcode('trova_varianti_per_bandiere_eco', 'trova_varianti_per_bandiere_economiche');

function stampa_tutti_gli_sku_flags($atts) {
    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'tax_query'      => array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'name', // <-- Uso 'name' perché la categoria si chiama "Flags"
                'terms'    => 'Flags',
                'operator' => 'IN', // Deve essere tra le categorie
            ),
        ),
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        $output = '';
        $count = 0;

        while ($query->have_posts()) {
            $query->the_post();
            $product_id = get_the_ID();
            $product = wc_get_product($product_id);

            $sku = $product->get_sku();
            if ( $sku ) {
                $output .= esc_html($sku) . '<br>';
                $count++;
            }
        }

        wp_reset_postdata();

        $final_output = '<strong>Le bandiere inserite sono nr: ' . $count . '</strong><br><br>';
        $final_output .= $output;

        return $final_output;
    }

    return 'Nessun prodotto trovato nella categoria Flags.';
}

add_shortcode('stampa_tutti_gli_sku_flags', 'stampa_tutti_gli_sku_flags');




function trova_prodotti_senza_sku_flags($atts) {
    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'publish', // Solo pubblicati
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'tax_query'      => array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'name',
                'terms'    => 'Flags',
                'operator' => 'IN',
            ),
        ),
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        $output = '';
        $count_senza_sku = 0;

        while ($query->have_posts()) {
            $query->the_post();
            $product_id = get_the_ID();
            $product = wc_get_product($product_id);

            $sku = $product->get_sku();
            if ( ! $sku ) { // Se SKU è vuoto
                $output .= 'Prodotto senza SKU: ' . get_the_title() . ' (ID: ' . $product_id . ')<br>';
                $count_senza_sku++;
            }
        }

        wp_reset_postdata();

        if ( $count_senza_sku > 0 ) {
            $final_output = '<strong>Prodotti senza SKU trovati: ' . $count_senza_sku . '</strong><br><br>';
            $final_output .= $output;
            return $final_output;
        } else {
            return '<strong>Tutti i prodotti hanno uno SKU.</strong>';
        }
    }

    return 'Nessun prodotto trovato nella categoria Flags.';
}

add_shortcode('trova_prodotti_senza_sku_flags', 'trova_prodotti_senza_sku_flags');




/*
function trova_varianti_bandiere_errate($atts) {
    $args = array(
        'post_type'      => 'product_variation',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'meta_query'     => array(
            'relation' => 'AND',
            array(
                'key'     => 'attribute_pa_materiale',
                'value'   => 'poliestere-leggero',
                'compare' => '=',
            ),
            array(
                'key'     => 'attribute_pa_formati',
                'value'   => '91x140-cm',
                'compare' => '=',
            ),
            array(
                'key'     => '_price',
                'value'   => '',
                'compare' => '!=',
                'type'    => 'NUMERIC',
            ),
        ),
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        $output = '';

        while ($query->have_posts()) {
            $query->the_post();
            $variation_id = get_the_ID();
            $variation = new WC_Product_Variation($variation_id);

            $sku = strtoupper($variation->get_sku());
            $sku_validi = array(
                'ALB', 'DZA', 'ARG', 'ARM', 'AUS', 'AUT', 'BGD', 'BEL', 'BIH', 'BRA', 'BGR',
                'CAN', 'CHL', 'CHN', 'CYP', 'COL', 'PRK', 'KOR', 'HRV', 'CUB', 'DNK', 'ARE',
                'ECU', 'EST', 'FIN', 'FRA', 'DEU', 'GHA', 'JPN', 'GRC', 'GIN', 'IND', 'IRL',
                'ISL', 'ISR', 'ITA', 'LVA', 'LBY', 'LTU', 'LUX', 'MLT', 'MEX', 'NGA', 'NOR',
                'NLD', 'POL', 'PRT', 'GBR', 'CZE', 'ROU', 'RUS', 'SMR', 'SEN', 'SRB', 'SVK',
                'ESP', 'USA', 'SWE', 'CHE', 'TUN', 'TUR', 'HUN', 'URY', 'VAT'
            );

            // Se lo SKU è valido, salta
            if ( in_array( $sku, $sku_validi ) ) {
                continue;
            }

            $parent_id = wp_get_post_parent_id( $variation_id );
            $permalink = get_permalink( $parent_id );

            // Qui mostri solo le bandiere errate con link
            $output .= '<div>';
            $output .= '<strong>SKU sbagliato:</strong> <a href="' . esc_url($permalink) . '" target="_blank">' . esc_html($sku) . '</a><br>';
            $output .= 'Titolo: ' . esc_html($variation->get_title());
            $output .= '</div><hr>';
        }

        wp_reset_postdata();

        return $output;
    }

    return 'Nessuna variante errata trovata.';
}

add_shortcode('trova_varianti_errate', 'trova_varianti_bandiere_errate');
*/






// Aggiungi i dati del campo di confezionamento al carrello quando un prodotto viene aggiunto
add_filter( 'woocommerce_add_cart_item_data', 'aggiungi_dati_campo_confezionamento_al_carrello', 10, 2 );
function aggiungi_dati_campo_confezionamento_al_carrello( $cart_item_data, $product_id ) {
    if ( isset( $_POST['confezionamento'] ) ) {
        $cart_item_data['confezionamento'] = sanitize_text_field( $_POST['confezionamento'] );
    }
    return $cart_item_data;
}

// Visualizza il campo "confezionamento" nel carrello
add_filter( 'woocommerce_get_item_data', 'visualizza_campo_confezionamento_nel_carrello', 10, 2 );

function visualizza_campo_confezionamento_nel_carrello( $cart_data, $cart_item ) {
    if ( isset( $cart_item['confezionamento'] ) ) {
        $cart_data[] = array(
            'name'    => 'Confezionamento',
            'value'   => $cart_item['confezionamento'],
            'display' => '',
        );
    }
    return $cart_data;
}


// Aggiungi i dati del campo confezionamento all'ordine durante il checkout
add_action('woocommerce_add_order_item_meta', 'aggiungi_dati_campo_confezionamento_ordine', 10, 3);
function aggiungi_dati_campo_confezionamento_ordine($item_id, $values, $cart_item_key) {
    if ( isset( $values['confezionamento'] ) ) {
        wc_add_order_item_meta($item_id, 'Confezionamento', $values['confezionamento']);
    }
}



// Visualizza il confezionamento nell'area amministratore degli ordini
add_action('woocommerce_after_order_itemmeta', 'visualizza_confezionamento_ordine_amministratore', 10, 3);
function visualizza_confezionamento_ordine_amministratore($item_id, $item, $product) {
    if ( $confezionamento = wc_get_order_item_meta($item_id, 'Confezionamento', true) ) {
        echo '<br><small><strong>' . __("Confezionamento") . ':</strong> ' . $confezionamento . '</small>';
    }
}

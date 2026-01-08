<?php

// Funzione per includere il file JavaScript nel tema child
// javascipt che cancella 100x150-cm-100-gr dalla pagina del prodotto

function enqueue_custom_script() {
    wp_enqueue_script('bandiere-hotel-script', get_stylesheet_directory_uri() . '/js/bandiere-hotel.js', array('jquery'), '1.0', true);
}
add_action('wp_enqueue_scripts', 'enqueue_custom_script');




/*
function trova_varianti_per_bandiera_hotel($atts) {
    $args = array(
        'post_type'      => 'product_variation',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'meta_query'     => array(
            'relation' => 'AND',
            array(
                'key'     => 'attribute_pa_materiale',
                'value'   => 'poliestere-nautico',
                'compare' => '=',
            ),
            array(
                'key'     => 'attribute_pa_formati',
                'value'   => '100x150-cm-100-gr',
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

            $confezionamento_hotel = 'Corda e cappio';

            $image = wp_get_attachment_image_src($variation->get_image_id(), 'medium');
            $title = $variation->get_title();
            $price = $variation->get_price_html();

            $output .= '<div class="bandiere-tipi-vari">';
            $output .= '<img src="' . $image[0] . '" alt="' . $title . '" />';
            // $output .= '<p class="p-bandiere-tipi-vari">' . $title . ' Hotel</p>';
            $output .= '<p class="p-bandiere-tipi-vari">' . $title . ' Hotel</p>';
            $output .= '<p class="p-prezzo-bandiere-tipi-vari">' . $price . '</p>';
            $output .= '<form method="post" action="' . get_site_url() . '/">';
            $output .= '<input type="hidden" name="add-to-cart" value="' . $variation_id . '">';
            $output .= '<input type="hidden" name="confezionamento-input-hotel" value="' . $confezionamento_hotel . '">';
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

add_shortcode('trova_varianti_per_bandiera_hotel', 'trova_varianti_per_bandiera_hotel');
*/


function trova_varianti_per_bandiera_hotel($atts) {
    $args = array(
        'post_type'      => 'product_variation',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'meta_query'     => array(
            'relation' => 'AND',
            array(
                'key'     => 'attribute_pa_materiale',
                'value'   => 'poliestere-nautico',
                'compare' => '=',
            ),
            array(
                'key'     => 'attribute_pa_formati',
                'value'   => '100x150-cm-100-gr',
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

            // Filtro SKU
            $sku = strtoupper($variation->get_sku());
            $sku_validi = array(
                'AUT', 'BEL', 'BGR', 'CYP', 'HRV', 'DNK', 'EST', 'FIN', 'FRA', 'DEU',
                'GBR', 'GRC', 'IRL', 'LVA', 'LTU', 'LUX', 'MLT', 'NLD', 'POL', 'PRT',
                'CZE', 'ROU', 'SVK', 'SVN', 'ESP', 'USA', 'SWE', 'HUN'
            );
            if ( !in_array($sku, $sku_validi) ) {
                continue;
            }

            $confezionamento_hotel = 'Corda e cappio';

            $image = wp_get_attachment_image_src($variation->get_image_id(), 'medium');
            $title = $variation->get_title();
            $price = $variation->get_price_html();

            $output .= '<div class="bandiere-tipi-vari">';
            $output .= '<img src="' . $image[0] . '" alt="' . $title . '" />';
            $output .= '<p class="p-bandiere-tipi-vari">' . $title . ' Hotel</p>';
            $output .= '<p class="p-prezzo-bandiere-tipi-vari">' . $price . '</p>';
            $output .= '<form method="post" action="' . get_site_url() . '/">';
            $output .= '<input type="hidden" name="add-to-cart" value="' . $variation_id . '">';
            $output .= '<input type="hidden" name="confezionamento-input-hotel" value="' . $confezionamento_hotel . '">';
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

add_shortcode('trova_varianti_per_bandiera_hotel', 'trova_varianti_per_bandiera_hotel');


// Aggiungi i dati del campo di confezionamento al carrello quando un prodotto viene aggiunto
add_filter( 'woocommerce_add_cart_item_data', 'aggiungi_dati_campo_confezionamento_al_carrello_hotel', 10, 2 );
function aggiungi_dati_campo_confezionamento_al_carrello_hotel( $cart_item_data, $product_id ) {
    if ( isset( $_POST['confezionamento-input-hotel'] ) ) {
        $cart_item_data['confezionamento-input-hotel'] = sanitize_text_field( $_POST['confezionamento-input-hotel'] );
    }
    return $cart_item_data;
}


// Visualizza il campo "confezionamento" nel carrello
add_filter( 'woocommerce_get_item_data', 'visualizza_campo_confezionamento_nel_carrello_hotel', 10, 2 );

function visualizza_campo_confezionamento_nel_carrello_hotel( $cart_data, $cart_item ) {
    if ( isset( $cart_item['confezionamento-input-hotel'] ) ) {
        $cart_data[] = array(
            'name'    => 'Confezionamento',
            'value'   => $cart_item['confezionamento-input-hotel'],
            'display' => '',
        );
    }
    return $cart_data;
}


// Aggiungi i dati del campo confezionamento all'ordine durante il checkout
add_action('woocommerce_add_order_item_meta', 'aggiungi_dati_campo_confezionamento_ordine_hotel', 10, 3);
function aggiungi_dati_campo_confezionamento_ordine_hotel($item_id, $values, $cart_item_key) {
    if ( isset( $values['confezionamento-input-hotel'] ) ) {
        wc_add_order_item_meta($item_id, 'Confezionamento', $values['confezionamento-input-hotel']);
    }
}


/*
// Visualizza il confezionamento nell'area amministratore degli ordini
add_action('woocommerce_after_order_itemmeta', 'visualizza_confezionamento_ordine_amministratore_hotel', 10, 3);
function visualizza_confezionamento_ordine_amministratore_hotel($item_id, $item, $product) {
    if ( $confezionamento_hotel = wc_get_order_item_meta($item_id, 'Confezionamento-input-hotel', true) ) {
        echo '<br><small><strong>' . __("Confezionamento") . ':</strong> ' . $confezionamento_hotel . '</small>';
    }
}
*/

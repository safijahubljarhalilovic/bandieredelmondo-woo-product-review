<?php

/*
 * Questo codice ha l'obiettivo di aggiungere informazioni aggiuntive sui banner sconto
 * agli articoli nel carrello di WooCommerce. Vengono utilizzati tre filtri/azioni principali:
 *
 * 1. woocommerce_add_cart_item_data: Intercetta l'aggiunta di un prodotto al carrello e 
 *    aggiunge un parametro personalizzato 'confezionamento_personalizzato_banner' ai dati dell'articolo nel carrello.
 *
 * 2. woocommerce_get_item_data: Aggiunge il confezionamento personalizzato del banner alla visualizzazione del carrello,
 *    in modo che l'utente possa vedere questa informazione nel riepilogo del carrello.
 *
 * 3. woocommerce_checkout_create_order_line_item: Trasferisce il confezionamento personalizzato del banner
 *    ai metadati dell'ordine durante la creazione dell'ordine, assicurando che l'informazione
 *    sia visibile nei dettagli dell'ordine.
 *
 * Queste modifiche consentono di passare e visualizzare informazioni aggiuntive, come i banner sconto,
 * quando un prodotto viene aggiunto al carrello.
 */

add_filter('woocommerce_add_cart_item_data', 'add_custom_banner_packaging_to_cart_item', 10, 3);

function add_custom_banner_packaging_to_cart_item($cart_item_data, $product_id, $variation_id) {
    if (isset($_GET['confezionamento-banner-sconto'])) {
        $cart_item_data['confezionamento_personalizzato_banner'] = sanitize_text_field($_GET['confezionamento-banner-sconto']);
    }
    return $cart_item_data;
}

add_filter('woocommerce_get_item_data', 'display_custom_banner_packaging_cart', 10, 2);

function display_custom_banner_packaging_cart($item_data, $cart_item) {
    if (isset($cart_item['confezionamento_personalizzato_banner'])) {
        $item_data[] = array(
            'name' => 'Confezionamento',
            'value' => $cart_item['confezionamento_personalizzato_banner']
        );
    }
    return $item_data;
}

add_action('woocommerce_checkout_create_order_line_item', 'add_custom_banner_packaging_order_line_item', 10, 4);

function add_custom_banner_packaging_order_line_item($item, $cart_item_key, $values, $order) {
    if (isset($values['confezionamento_personalizzato_banner'])) {
        $item->add_meta_data('Confezionamento Personalizzato Banner', $values['confezionamento_personalizzato_banner']);
    }
}




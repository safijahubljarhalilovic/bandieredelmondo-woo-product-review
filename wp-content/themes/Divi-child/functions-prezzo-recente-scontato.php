<?

function aggiungi_testo_prezzo_scontato_variation($variation_data, $product, $variation) {
    // Controlla se la variazione ha uno sconto attivo
    if ($variation->is_on_sale()) {
        // Ottieni il prezzo scontato
        $prezzoScontato = $variation->get_sale_price();
        // Formatta il prezzo scontato
        $prezzoScontatoFormattato = wc_price($prezzoScontato);
        // Aggiungi il testo "Prezzo più basso recente" con il valore dinamico del prezzo scontato
        $variation_data['price_html'] .= '<p><span style="font-weight: normal; font-size: 80%;">Prezzo più basso recente ' . $prezzoScontatoFormattato . '</span></p>';
    }
    return $variation_data;
}
add_filter('woocommerce_available_variation', 'aggiungi_testo_prezzo_scontato_variation', 10, 3);


// Aggiungo un hook per chiamare la funzione che stampa la variabile globale
// add_action('prezzo_scontato', 'stampa_prezzoScontato_globale');
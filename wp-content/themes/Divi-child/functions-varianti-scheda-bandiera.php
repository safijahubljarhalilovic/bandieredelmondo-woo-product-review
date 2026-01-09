<?php
/*BANDIERE DEL MONDO*/ 

// Questa function crea il SELECT confezionamento con i controlli del caso
add_action( 'woocommerce_before_single_variation', 'aggiungi_campo_confezionamento_pagina_prodotto' );

function aggiungi_campo_confezionamento_pagina_prodotto() {
    global $product;

    // Verifica se il prodotto ha il tag "flags"
     if ( has_term( 'flags', 'product_tag', $product->get_id() ) ) {

        $materiale = $product->get_attribute( 'pa_materiale' );
        echo '<p id="materiale_display">Materiale: ' . esc_html( $materiale ) . '</p>';

    ?>
    <script>
        jQuery(function($) {
            function updateMaterialDisplay() {
                var materiale = $('select#pa_materiale').val();
                var materialeDisplay = $('#materiale_display');
                var descrizioneSelectHtml = '';

                if (materiale === 'poliestere-leggero') {
                    materialeDisplay.text('Il valore selezionato è poliestere-leggero. Esegui azioni specifiche...');
                    descrizioneSelectHtml = `
                        <div class="campo-descrizione">
                            <label for="descrizione"><b>Confezionamento</b></label><br>
                            <select id="descrizione" name="descrizione">
                                <option value="tasca-chiusa-in-alto" selected>Tasca chiusa in alto</option>
                            </select>
                        </div>`;
                } else if (materiale === 'poliestere-nautico' || materiale === 'bunting-poliestere' || materiale === 'stamina-di-poliestere') {
                    // materialeDisplay.text('Il valore selezionato è ' + materiale + '. Esegui azioni specifiche...');
                    materialeDisplay.text('');
                    descrizioneSelectHtml = `
                        <div class="campo-descrizione">
                            <label for="descrizione"><b>Confezionamento</b></label><br>
                            <select id="descrizione" name="descrizione">
                                <option value="anelli-lato-corto">Anelli lato corto</option>
                                <option value="corda-e-cappio" selected>Corda e cappio</option>
                                <option value="corda-moschettone-metallo">Corda moschettone metallo</option>
                                <option value="corda-moschettone-plastica">Corda moschettone plastica</option>
                                <option value="lacci-lato-corto">Lacci lato corto</option>
                                <option value="tasca-chiusa-in-alto">Tasca chiusa in alto</option>
                            </select>
                        </div>`;
                } else {
                    materialeDisplay.text('Nessun valore selezionato');
                    // Rimuovi il contenuto esistente e imposta solo il testo desiderato
                    $('.campo-descrizione').remove();
                    return;
                }

                $('.campo-descrizione').remove();
                $('#materiale_display').before(descrizioneSelectHtml);
            }

            $('select#pa_materiale').change(function() {
                var nuovoMateriale = $(this).val();

                $.ajax({
                    url: '<?php echo esc_url( admin_url("admin-ajax.php") ); ?>',
                    type: 'POST',
                    data: {
                        'action': 'get_nuovo_materiale',
                        'nuovo_materiale': nuovoMateriale
                    },
                    success: function(response) {
                        updateMaterialDisplay();
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            });

            updateMaterialDisplay();
        });
    </script>
    <?php
}
}



// Funzione per gestire la richiesta AJAX e restituire il nuovo valore del materiale
add_action('wp_ajax_get_nuovo_materiale', 'get_nuovo_materiale_callback');
add_action('wp_ajax_nopriv_get_nuovo_materiale', 'get_nuovo_materiale_callback');

function get_nuovo_materiale_callback() {
    if (isset($_POST['nuovo_materiale'])) {
        $nuovo_materiale = $_POST['nuovo_materiale'];
        
        // Puoi qui implementare la logica per ottenere il nuovo materiale
        // Ad esempio, se il materiale dipende dalla variazione selezionata, potresti dover effettuare una query al database WooCommerce per ottenere il materiale corrispondente alla variazione selezionata.

        // Restituisci il nuovo materiale (per ora, restituiamo semplicemente ciò che è stato passato)
        echo $nuovo_materiale;

        // Assicurati di terminare lo script dopo aver restituito la risposta
        wp_die();
    }
}

/*
// Aggiungi i dati del campo di descrizione al carrello quando un prodotto viene aggiunto
add_filter( 'woocommerce_add_cart_item_data', 'aggiungi_dati_campo_descrizione_al_carrello', 10, 2 );
function aggiungi_dati_campo_descrizione_al_carrello( $cart_item_data, $product_id ) {
    if ( isset( $_POST['descrizione'] ) ) {
        $cart_item_data['descrizione'] = sanitize_text_field( $_POST['descrizione'] );
    }
    return $cart_item_data;
}
*/

add_filter( 'woocommerce_add_cart_item_data', 'aggiungi_dati_campo_descrizione_al_carrello', 10, 2 );
function aggiungi_dati_campo_descrizione_al_carrello( $cart_item_data, $product_id ) {
    if ( isset( $_POST['descrizione'] ) ) {
        $descrizione = sanitize_text_field( $_POST['descrizione'] );
        // Rimuovi i segni "-" e aggiungi uno spazio prima di passare la descrizione al carrello
        $descrizione_senza_trattini = str_replace( '-', ' ', $descrizione );
        $cart_item_data['descrizione'] = $descrizione_senza_trattini;
    }
    return $cart_item_data;
}


// Visualizza la descrizione nel carrello
add_filter( 'woocommerce_get_item_data', 'visualizza_descrizione_nel_carrello', 10, 2 );
function visualizza_descrizione_nel_carrello( $cart_data, $cart_item ) {
    if ( isset( $cart_item['descrizione'] ) ) {
        $cart_data[] = array(
            'name'    => 'Confezionamento',
            'value'   => $cart_item['descrizione'],
            'display' => '',
        );
    }
    return $cart_data;
}

// Aggiungi i dati del campo descrizione all'ordine durante il checkout
add_action('woocommerce_add_order_item_meta', 'aggiungi_dati_campo_descrizione_ordine', 10, 3);
function aggiungi_dati_campo_descrizione_ordine($item_id, $values, $cart_item_key) {
    if ( isset( $values['descrizione'] ) ) {
        wc_add_order_item_meta($item_id, 'Descrizione', $values['descrizione']);
    }
}

// Visualizza la descrizione nell'area amministratore degli ordini
add_action('woocommerce_after_order_itemmeta', 'visualizza_descrizione_ordine_amministratore', 10, 3);
function visualizza_descrizione_ordine_amministratore($item_id, $item, $product) {
    if ( $descrizione = wc_get_order_item_meta($item_id, 'Descrizione', true) ) {
        echo '<br><small><strong>' . __("Confezionamento") . ':</strong> ' . $descrizione . '</small>';
    }
}

// fine codice per il confezionamento





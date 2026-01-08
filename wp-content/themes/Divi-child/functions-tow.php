<?php

/*aumentiamo le varianti caricate automaticamente */
define( 'WC_MAX_LINKED_VARIATIONS', 350 );
/*fine */


// Aumenta il limite di varianti per prodotto
add_filter( 'woocommerce_admin_meta_boxes_variations_per_page', 'my_wc_admin_meta_boxes_variations_per_page' );

function my_wc_admin_meta_boxes_variations_per_page( $per_page ) {
    return 50;
}
/*fine*/


/**
// Questo codice aggiunge i pulsanti di aumento e diminuzione della quantità dei prodotti in WooCommerce e gestisce la logica di aggiornamento della quantità nel carrello.
*/

// 1. Visualizza pulsanti + e -
add_action( 'woocommerce_after_quantity_input_field', 'bbloomer_display_quantity_plus' );

function bbloomer_display_quantity_plus() {
   echo '<button type="button" class="plus">+</button>';
}

add_action( 'woocommerce_before_quantity_input_field', 'bbloomer_display_quantity_minus' );

function bbloomer_display_quantity_minus() {
   echo '<button type="button" class="minus">-</button>';
}

// 2. Trigger update quantity script

add_action( 'wp_footer', 'bbloomer_add_cart_quantity_plus_minus' );

function bbloomer_add_cart_quantity_plus_minus() {

   if ( ! is_product() && ! is_cart() ) return;

   wc_enqueue_js( "
      $(document).on( 'click', 'button.plus, button.minus', function() {

         var qty = $( this ).parent( '.quantity' ).find( '.qty' );
         var val = parseFloat(qty.val());
         var max = parseFloat(qty.attr( 'max' ));
         var min = parseFloat(qty.attr( 'min' ));
         var step = parseFloat(qty.attr( 'step' ));

         if ( $( this ).is( '.plus' ) ) {
            if ( max && ( max <= val ) ) {
               qty.val( max ).change();
            } else {
               qty.val( val + step ).change();
            }
         } else {
            if ( min && ( min >= val ) ) {
               qty.val( min ).change();
            } else if ( val > 1 ) {
               qty.val( val - step ).change();
            }
         }
      });
   " );
}
/* fine */



/**
 * Funzione per generare uno shortcode personalizzato per visualizzare il prezzo di un prodotto WooCommerce.
 * inserita nella scheda del prodotto
 */

function display_product_price_shortcode() {
    global $product;
    
    if (is_product() && $product) {
        $price_html = $product->get_price_html();
        return $price_html;
    }
}

add_shortcode('product_price', 'display_product_price_shortcode');
/*fine*/



/*sostituire da h2 a h3 su elenco prodotti */
function custom_modify_product_title() {
    // Rimuovi il titolo originale
    remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title');
    
    // Aggiungi il nuovo titolo
    // echo '<p class="woocommerce-loop-product__title">';
  echo '<p style="font-weight: bold; color: black; text-align: center;">';
the_title();
echo '</p>';

}

add_action('woocommerce_before_shop_loop_item_title', 'custom_modify_product_title');



/*serve a mostrare le varianti nel carrello */
// Aggiunge dinamicamente l'ID del prodotto e della variante alla fine della descrizione del prodotto nel carrello
function sposta_id_prodotto_e_variante_in_fondo_carrello( $product_name, $cart_item, $cart_item_key ) {
    if ( is_cart() ) {
        $product = $cart_item['data'];
        $product_id = $product->get_id();

        if ( $product->is_type( 'variation' ) ) {
            $variation_id = $cart_item['variation_id'];
            $variant_info = ' (ID: ' . $product_id . ', Var: ' . $variation_id . ')';
        } else {
            $variant_info = ' (ID: ' . $product_id . ')';
        }

        // Aggiungi l'informazione dinamica alla fine della descrizione del prodotto
       $product_name .= '<br>' . '<span style="font-size: x-small;">' . $variant_info . '</span>';
    }

    return $product_name;
}
add_filter( 'woocommerce_cart_item_name', 'sposta_id_prodotto_e_variante_in_fondo_carrello', 10, 3 );



// Modifica il limite massimo per il numero di varianti gestite tramite richieste AJAX.
// in modo tale che vengano nascoste le varianti non utilizzate

add_filter( 'woo_variation_swatches_global_ajax_variation_threshold_max', 'woo_variation_swatches_global_ajax_variation_threshold_max_edit', 10, 2 );
function woo_variation_swatches_global_ajax_variation_threshold_max_edit( $size, $product ){
	return 550;
}




/*
Plugin Name: Remove GTX Trans Div
Description: Rimuove il div con id "gtx-trans" dal contenuto del sito.
Version: 1.0
Author: Il Tuo Nome
*/

function remove_gtx_trans_div($content) {
    // Utilizza una regex per rimuovere il div con id "gtx-trans"
    $content = preg_replace('/<div id="gtx-trans" style="position: absolute;left: 442px;top: 8.31945px">\s*<div class="gtx-trans-icon"><\/div>\s*<\/div>/', '', $content);
    return $content;
}
add_filter('the_content', 'remove_gtx_trans_div');


// Shortcode per mostrare "Ciao, [nome]" o "Ciao, accedi" nei moduli Divi

function nome_utente_loggato_shortcode() {
    if ( is_user_logged_in() ) {
        $current_user = wp_get_current_user();
        return 'Ciao, ' . esc_html( $current_user->display_name );
    } else {
        return 'Ciao, accedi';
    }
}
add_shortcode('nome_utente_loggato', 'nome_utente_loggato_shortcode');
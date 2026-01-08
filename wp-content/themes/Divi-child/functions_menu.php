<?php

// Aggiungi questo codice nel file functions.php del tuo tema o in un plugin personalizzato

function elenco_woocomerce_menu_shortcode() {
    ob_start();

    // Sostituisci 'nome-menu' con il nome del tuo menu WooCommerce
    $menu_items = wp_get_nav_menu_items('BandiereDelMondo');

    if ($menu_items) {
        echo '<ul>';

        foreach ($menu_items as $menu_item) {
           echo '<li><a href="' . esc_url($menu_item->url) . '">' . esc_html($menu_item->title) . '</a></li>';
        
        }

        echo '</ul>';
    }

    return ob_get_clean();
}
add_shortcode('elenco_woocomerce_menu', 'elenco_woocomerce_menu_shortcode');

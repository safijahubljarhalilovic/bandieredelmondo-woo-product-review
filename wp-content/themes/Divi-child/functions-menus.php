<?php

/*
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
*/

/*
function elenco_woocomerce_menu_shortcode_mobile() {
    // Sostituisci '57' con l'ID del tuo menu
    $menu_id = '57';

    // Ottieni il menu
    $menu = wp_get_nav_menu_object($menu_id);

    // Inizializza la stringa di output
    $output = '';

    // Verifica se il menu esiste
    if ($menu) {
        // Ottieni le voci del menu
        $menu_items = wp_get_nav_menu_items($menu);

        // Verifica se ci sono voci nel menu
        if ($menu_items) {
            // Cicla attraverso le voci del menu
            foreach ($menu_items as $menu_item) {
                // Verifica se l'elemento del menu è di primo livello
                if ($menu_item->menu_item_parent == 0) {
                    // Aggiungi il titolo dell'elemento del menu in grassetto con il link alla stringa di output
                    $output .= '<strong><a href="' . esc_url($menu_item->url) . '">' . $menu_item->title . '</a></strong>&nbsp;&nbsp;&nbsp;&nbsp;'; // Aggiunge spazio tra le parole
                } else {
                    // Aggiungi elementi figli senza lo stile grassetto e con più spazio tra le parole
                    $output .= '<span style="margin-right: 20px;"><a href="' . esc_url($menu_item->url) . '">' . $menu_item->title . '</a></span>';
                }

                // Verifica se ci sono elementi figli
                $menu_item_children = wp_get_nav_menu_items($menu, array('post_parent' => $menu_item->ID));
                if ($menu_item_children) {
                    // Cicla attraverso gli elementi figli e aggiungili all'output senza lo stile grassetto e con più spazio tra le parole
                    foreach ($menu_item_children as $child_item) {
                        $output .= '<span style="margin-right: 20px;"><a href="' . esc_url($child_item->url) . '">' . $child_item->title . '</a></span>';
                    }
                }
            }
        } else {
            // Messaggio in caso di menu vuoto
            $output = 'Nessun elemento nel menu';
        }
    } else {
        // Messaggio in caso di menu non trovato
        $output = 'Menu non trovato';
    }

    // Aggiungi stile per centrare il testo
    $output = '<div style="text-align:center; line-height: 2.5;">' . $output . '</div>';

    return $output;
}

// Registra lo shortcode
add_shortcode('elenco_woocomerce_menu_mobile', 'elenco_woocomerce_menu_shortcode_mobile');
*/





function elenco_desktop_menu_shortcode() {
    // Sostituisci '57' con l'ID del tuo menu
    $menu_id = '57';

    // Ottieni il menu
    $menu = wp_get_nav_menu_object($menu_id);

    // Inizializza la stringa di output
    $output = '';

    // Verifica se il menu esiste
    if ($menu) {
        // Ottieni le voci del menu
        $menu_items = wp_get_nav_menu_items($menu);

        // Verifica se ci sono voci nel menu
        if ($menu_items) {
            // Cicla attraverso le voci del menu
            foreach ($menu_items as $menu_item) {
                // Verifica se l'elemento del menu è di primo livello
                if ($menu_item->menu_item_parent == 0) {
                    // Aggiungi il titolo dell'elemento del menu in grassetto con il link alla stringa di output
                    $output .= '<strong><a href="' . esc_url($menu_item->url) . '" class="white">' . $menu_item->title . '</a></strong>&nbsp;&nbsp;&nbsp;&nbsp;'; // Aggiunge spazio tra le parole
                } else {
                    // Aggiungi elementi figli senza lo stile grassetto e con più spazio tra le parole
                    $output .= '<span><a href="' . esc_url($menu_item->url) . '" class="white">' . $menu_item->title . '</a></span>';
                }

                // Verifica se ci sono elementi figli
                $menu_item_children = wp_get_nav_menu_items($menu, array('post_parent' => $menu_item->ID));
                if ($menu_item_children) {
                    // Cicla attraverso gli elementi figli e aggiungili all'output senza lo stile grassetto e con più spazio tra le parole
                    foreach ($menu_item_children as $child_item) {
                        $output .= '<span><a href="' . esc_url($child_item->url) . '" class="white">' . $child_item->title . '</a></span>';
                    }
                }
            }
        } else {
            // Messaggio in caso di menu vuoto
            $output = 'Nessun elemento nel menu';
        }
    } else {
        // Messaggio in caso di menu non trovato
        $output = 'Menu non trovato';
    }

    // Aggiungi stile per centrare il testo
    
    
    // $output = '<div style="text-align:center; line-height: 2.5;">' . $output . '</div>';
    $output = '<div class="elenco_desktop_menu" style="text-align:center; line-height: 2.5;">' . $output . '</div>';

    return $output;
}

// Registra lo shortcode
add_shortcode('elenco_desktop_menu', 'elenco_desktop_menu_shortcode');




function elenco_desktop_menu_accessori_shortcode() {
    // Sostituisci '275' con l'ID del tuo menu
    $menu_id = '275';

    // Ottieni il menu
    $menu = wp_get_nav_menu_object($menu_id);

    // Inizializza la stringa di output
    $output = '';

    // Verifica se il menu esiste
    if ($menu) {
        // Ottieni le voci del menu
        $menu_items = wp_get_nav_menu_items($menu);

        // Verifica se ci sono voci nel menu
        if ($menu_items) {
            // Cicla attraverso le voci del menu
            foreach ($menu_items as $menu_item) {
                // Verifica se l'elemento del menu è di primo livello
                if ($menu_item->menu_item_parent == 0) {
                    // Aggiungi il titolo dell'elemento del menu in grassetto con il link alla stringa di output
                    $output .= '<strong><a href="' . esc_url($menu_item->url) . '" class="white">' . $menu_item->title . '</a></strong>&nbsp;&nbsp;&nbsp;&nbsp;'; // Aggiunge spazio tra le parole
                } else {
                    // Aggiungi elementi figli senza lo stile grassetto e con più spazio tra le parole
                    $output .= '<span><a href="' . esc_url($menu_item->url) . '" class="white">' . $menu_item->title . '</a></span>';
                }

                // Verifica se ci sono elementi figli
                $menu_item_children = wp_get_nav_menu_items($menu, array('post_parent' => $menu_item->ID));
                if ($menu_item_children) {
                    // Cicla attraverso gli elementi figli e aggiungili all'output senza lo stile grassetto e con più spazio tra le parole
                    foreach ($menu_item_children as $child_item) {
                        $output .= '<span><a href="' . esc_url($child_item->url) . '" class="white">' . $child_item->title . '</a></span>';
                    }
                }
            }
        } else {
            // Messaggio in caso di menu vuoto
            $output = 'Nessun elemento nel menu';
        }
    } else {
        // Messaggio in caso di menu non trovato
        $output = 'Menu non trovato';
    }

    // Aggiungi stile per centrare il testo
    $output = '<div class="elenco_desktop_menu" style="text-align:center; line-height: 2.5;">' . $output . '</div>';

    return $output;
}

// Registra lo shortcode
add_shortcode('elenco_desktop_menu_accessori', 'elenco_desktop_menu_accessori_shortcode');
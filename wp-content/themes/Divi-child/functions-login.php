<?php

/* mostra l'utente loggato sul sito */
function mostra_nome_utente() {
    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        // return 'Benvenuto, ' . esc_html($current_user->user_login);
        return esc_html($current_user->user_login);
    } else {
        return 'Utente non loggato';
    }
}
add_shortcode('mostra_utente', 'mostra_nome_utente');
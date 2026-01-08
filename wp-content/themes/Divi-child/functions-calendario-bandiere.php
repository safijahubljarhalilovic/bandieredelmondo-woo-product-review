<?php

function calendario_bandiere_shortcode() {
    setlocale(LC_TIME, 'it_IT.utf8', 'it_IT');
    $json_file_path = ABSPATH . 'wp-content/themes/Divi-child/calendario-bandiere/esportazioneCalendarioBandiere.json';

    if (file_exists($json_file_path)) {
        $json_data = file_get_contents($json_file_path);
        $data = json_decode($json_data, true);

        if (is_array($data)) {
            $dataAttuale = date("Y/m/d");
            $eventiAttuali = [];

            foreach ($data as $item) {
                if ($item['Data di Fine'] >= $dataAttuale) {
                    $eventiAttuali[] = $item;
                }
            }

            usort($eventiAttuali, function($a, $b) {
                return strcmp($a['Data di Fine'], $b['Data di Fine']);
            });

            $eventiDaMostrare = array_slice($eventiAttuali, 0, 3);

            $output = '<div class="calendario-bandiere-container-cdv">';

            foreach ($eventiDaMostrare as $item) {
                $dataInizio = strftime("%A %d, %B %Y", strtotime($item['Data di Inizio']));
                $dataFine = strftime("%A %d, %B %Y", strtotime($item['Data di Fine']));
                $titolo = esc_html($item['Titolo']);
                $descrizione = esc_html($item['Descrizione']);

                $output .= '<div class="evento-bandiera-cdv">';
                $output .= '<div class="evento-info-cdv">';
                $output .= '<div class="date-info-cdv">';
                $output .= '<span class="inizio-cdv">' . $dataInizio . '</span><br>';
                // $output .= '<span class="fine-cdv">Fine: ' . $dataFine . '</span>';
                $output .= '</div>';
                $output .= '<div class="titolo-cdv">' . $titolo . '</div>';

                if (!empty($descrizione)) {
                    global $wpdb;
                    $sku = $descrizione;
                    $product_id = $wpdb->get_var($wpdb->prepare(
                        "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key='_sku' AND meta_value=%s",
                        $sku
                    ));

                    if ($product_id) {
                        $product = wc_get_product($product_id);
                        $image = wp_get_attachment_image_src($product->get_image_id(), 'woocommerce_thumbnail');
                        $product_image_url = $image ? $image[0] : '';
                        $product_page_url = get_permalink($product_id);

                        $output .= '<div class="product-image-cdv">';
                        $output .= '<img src="' . esc_url($product_image_url) . '" alt="' . esc_attr($product->get_name()) . '">';
                        $output .= '</div>';
                        $output .= '<a href="' . esc_url($product_page_url) . '" class="button button-secondary product-link-cdv">' . esc_html($product->get_name()) . '</a>';
                        $output .= '<p class="product-description-cdv">Lascia che il vento racconti la tua storia</p>';

                    } else {
                        $output .= '<div class="product-not-found-cdv">Prodotto non trovato.</div>';
                    }
                } else {
                    $output .= '<div class="no-product-cdv">Nessun prodotto associato a questo evento.</div>';
                }

                $output .= '</div>'; // Chiude evento-info-cdv
                $output .= '</div>'; // Chiude evento-bandiera-cdv
            }

            $output .= '</div>'; // Chiude calendario-bandiere-container-cdv

            return $output;
        } else {
            return 'Errore nella decodifica del file JSON.';
        }
    } else {
        return 'Il file JSON non esiste.';
    }
}
add_shortcode('calendario_bandiere', 'calendario_bandiere_shortcode');


function calendario_bandiere_mese_shortcode() {
    setlocale(LC_TIME, 'it_IT.utf8', 'it_IT');
    $json_file_path = ABSPATH . 'wp-content/themes/Divi-child/calendario-bandiere/esportazioneCalendarioBandiere.json';

    if (file_exists($json_file_path)) {
        $json_data = file_get_contents($json_file_path);
        $data = json_decode($json_data, true);

        if (is_array($data)) {
            $meseCorrente = date("m");
            $annoCorrente = date("Y");
            $eventidelMese = [];

            foreach ($data as $item) {
                $meseEvento = date("m", strtotime($item['Data di Inizio']));
                $annoEvento = date("Y", strtotime($item['Data di Inizio']));
                if ($meseEvento == $meseCorrente && $annoEvento == $annoCorrente) {
                    $eventidelMese[] = $item;
                }
            }

            usort($eventidelMese, function($a, $b) {
                return strcmp($a['Data di Inizio'], $b['Data di Inizio']);
            });

            $output = '<h2 class="mese-titolo-cdv">Bandiere del mese di ' . strftime("%B %Y") . '</h2>';
            $output .= '<div class="calendario-bandiere-container-cdv flex-container-cdv">';

            foreach ($eventidelMese as $item) {
                $dataInizio = strftime("%A %d, %B %Y", strtotime($item['Data di Inizio']));
                $titolo = esc_html($item['Titolo']);
                $descrizione = esc_html($item['Descrizione']);

                $output .= '<div class="evento-bandiera-cdv">';
                $output .= '<div class="evento-info-cdv">';
                $output .= '<div class="date-info-cdv">';
                $output .= '<span class="inizio-cdv">' . $dataInizio . '</span>';
                $output .= '</div>';
                $output .= '<div class="titolo-cdv">' . $titolo . '</div>';

                if (!empty($descrizione)) {
                    global $wpdb;
                    $sku = $descrizione;
                    $product_id = $wpdb->get_var($wpdb->prepare(
                        "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key='_sku' AND meta_value=%s",
                        $sku
                    ));

                    if ($product_id) {
                        $product = wc_get_product($product_id);
                        $image = wp_get_attachment_image_src($product->get_image_id(), 'woocommerce_thumbnail');
                        $product_image_url = $image ? $image[0] : '';
                        $product_page_url = get_permalink($product_id);

                        $output .= '<div class="product-image-cdv">';
                        $output .= '<img src="' . esc_url($product_image_url) . '" alt="' . esc_attr($product->get_name()) . '">';
                        $output .= '</div>';
                        $output .= '<a href="' . esc_url($product_page_url) . '" class="button button-secondary product-link-cdv">' . esc_html($product->get_name()) . '</a>';
                        $output .= '<p class="product-description-cdv">Lascia che il vento racconti la tua storia</p>';
                    } else {
                        $output .= '<div class="product-not-found-cdv">Prodotto non trovato.</div>';
                    }
                } else {
                    $output .= '<div class="no-product-cdv">Nessun prodotto associato a questo evento.</div>';
                }

                $output .= '</div>'; // Chiude evento-info-cdv
                $output .= '</div>'; // Chiude evento-bandiera-cdv
            }

            $output .= '</div>'; // Chiude calendario-bandiere-container-cdv

            return $output;
        } else {
            return 'Errore nella decodifica del file JSON.';
        }
    } else {
        return 'Il file JSON non esiste.';
    }
}
add_shortcode('calendario_mese_bandiere', 'calendario_bandiere_mese_shortcode');
<?php


add_filter( 'woocommerce_get_breadcrumb', 'customize_woocommerce_breadcrumbs', 20, 2 );

function customize_woocommerce_breadcrumbs( $crumbs, $breadcrumb ) {
    // Rimuovi il link "Home"
    if ( isset( $crumbs[0] ) && $crumbs[0][0] === 'Home' ) {
        array_shift( $crumbs );
    }
    // Verifica se il primo breadcrumb è "Flags" e rimuovilo se presente
    if ( isset( $crumbs[0] ) && $crumbs[0][0] === 'Flags' ) {
        array_shift( $crumbs );
    }
    // Verifica se il primo breadcrumb è "Kit" e rimuovilo se presente
    if ( isset( $crumbs[0] ) && $crumbs[0][0] === 'Kit' ) {
        array_shift( $crumbs );
    }
    // Verifica se il primo breadcrumb è "Ensigns" e rimuovilo se presente
    if ( isset( $crumbs[0] ) && $crumbs[0][0] === 'Ensigns' ) {
        array_shift( $crumbs );
    }
    return $crumbs;
}



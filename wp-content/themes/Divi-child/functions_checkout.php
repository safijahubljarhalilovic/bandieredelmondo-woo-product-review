<?php

// Funzione per aggiungere campi personalizzati ai form di checkout e al backend di WooCommerce
function custom_woocommerce_fields() {
    // Aggiungere il campo Partita IVA nei campi di fatturazione durante il checkout
    add_filter( 'woocommerce_billing_fields' , 'add_billing_field_piva' );
    function add_billing_field_piva( $fields ) {
        $fields['billing_piva'] = array(
            'label' => __('Partita Iva', 'woocommerce'),
            'placeholder' => _x('Partita iva', 'placeholder', 'woocommerce'),
            'required' => false,
            'class' => array('form-row-wide'),
            'show' => true
        );
        return $fields;
    }

    // Aggiungere il campo Partita IVA nel backend di amministrazione di WooCommerce
    add_filter( 'woocommerce_admin_billing_fields' , 'add_admin_field_piva' );
    function add_admin_field_piva( $fields ) {
        $fields['piva'] = array(
            'label' => __('Partita Iva', 'woocommerce'),
            'show'  => true
        );
        return $fields;
    }

    // Aggiungere il campo PEC nei campi di fatturazione durante il checkout
    add_filter( 'woocommerce_billing_fields' , 'add_billing_field_pec' );
    function add_billing_field_pec( $fields ) {
        $fields['field_pec'] = array(
            'type' => 'text',
            'label' => __('PEC', 'woocommerce'),
            'placeholder' => _x('PEC', 'placeholder', 'woocommerce'),
            'required' => false,
            'class' => array('custom-class'),
            'show' => true
        );
        return $fields;
    }

    // Aggiungere il campo PEC nel backend di amministrazione di WooCommerce
    add_filter( 'woocommerce_admin_billing_fields' , 'add_admin_field_field_pec' );
    function add_admin_field_field_pec( $fields ) {
        $fields['field_pec'] = array(
            'label' => __('PEC', 'woocommerce'),
            'show'  => true
        );
        return $fields;
    }
}

// Eseguire la funzione custom_woocommerce_fields
add_action('init', 'custom_woocommerce_fields');

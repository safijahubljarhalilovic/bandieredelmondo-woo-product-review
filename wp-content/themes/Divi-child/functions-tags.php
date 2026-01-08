<?php

add_filter('single_term_title', 'add_prefix_to_product_tag_title');

function add_prefix_to_product_tag_title($title) {
    if (is_tax('product_tag')) {
        $title = 'Bandiera con ' . $title;
    }
    return $title;
}

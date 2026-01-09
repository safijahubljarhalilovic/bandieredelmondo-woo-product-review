<?php
if (!defined('ABSPATH')) exit;

class BDM_Review_Schema {

  public static function init() {
    add_filter('woocommerce_structured_data_product', [__CLASS__, 'output_schema'], 10, 2);
  }

  public static function output_schema($markup, $product) {
    if (!$product || !is_a($product, 'WC_Product')) return $markup;

    $product_id = $product->get_id();

    // Fetch approved reviews from your CPT
    $q = new WP_Query([
      'post_type' => 'bdm_review',
      'post_status' => 'publish', // approved
      'posts_per_page' => 200,
      'orderby' => 'date',
      'order' => 'DESC',
      'meta_query' => [[
        'key' => '_bdm_product_id',
        'value' => $product_id,
        'compare' => '=',
        'type' => 'NUMERIC',
      ]],
    ]);

    if (!$q->have_posts()) {
      wp_reset_postdata();
      // No approved reviews -> don't add review/aggregateRating
      return $markup;
    }

    $reviews = [];
    $sum = 0;
    $count = 0;

    while ($q->have_posts()) {
      $q->the_post();
      $rid = get_the_ID();

      $name   = get_post_meta($rid, '_bdm_name', true) ?: 'Anonymous';
      $rating = (int) get_post_meta($rid, '_bdm_rating', true);
      $body   = get_post_meta($rid, '_bdm_comment', true) ?: '';
      $date   = get_the_date('c', $rid);

      if ($rating < 1 || $rating > 5) continue;

      $sum += $rating;
      $count++;

      $reviews[] = [
        '@type' => 'Review',
        'author' => [
          '@type' => 'Person',
          'name' => $name,
        ],
        'datePublished' => $date,
        'reviewBody' => wp_strip_all_tags($body),
        'reviewRating' => [
          '@type' => 'Rating',
          'ratingValue' => (string)$rating,
          'bestRating' => '5',
          'worstRating' => '1',
        ],
      ];
    }

    wp_reset_postdata();

    if ($count < 1) return $markup;

    $avg = round($sum / $count, 2);

    // Add/override aggregateRating + review in Woo markup
    $markup['aggregateRating'] = [
      '@type' => 'AggregateRating',
      'ratingValue' => (string)$avg,
      'reviewCount' => (string)$count,
      'bestRating' => '5',
      'worstRating' => '1',
    ];

    // Keep review array not too large
    $markup['review'] = array_slice($reviews, 0, 20);

    return $markup;
  }
}

<?php
if (!defined('ABSPATH')) exit;

class BDM_Review_Schema {

  public static function init() {
    add_action('wp_head', [__CLASS__, 'output_schema'], 99);
  }

  public static function output_schema() {
    if (!function_exists('is_product') || !is_product()) return;
    if (!get_option('bdm_review_output_schema', true)) return;

    $product_id = get_the_ID();
    if (!$product_id) return;

    // Fetch approved reviews
    $q = new WP_Query([
      'post_type' => BDM_Review_CPT::CPT,
      'post_status' => 'publish',
      'posts_per_page' => 200,
      'orderby' => 'date',
      'order' => 'DESC',
      'meta_query' => [[
        'key' => '_bdm_product_id',
        'value' => $product_id,
        'compare' => '=',
        'type' => 'NUMERIC'
      ]],
    ]);

    if (!$q->have_posts()) return;

    $reviews = [];
    $sum = 0;
    $count = 0;

    while ($q->have_posts()) {
      $q->the_post();
      $rid = get_the_ID();

      $name = BDM_Review_CPT::meta($rid, '_bdm_name', 'Anonymous');
      $rating = (int)BDM_Review_CPT::meta($rid, '_bdm_rating', 0);
      $body = BDM_Review_CPT::meta($rid, '_bdm_comment', '');
      $date = get_the_date('c', $rid);

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
        'reviewBody' => $body,
        'reviewRating' => [
          '@type' => 'Rating',
          'ratingValue' => (string)$rating,
          'bestRating' => '5',
          'worstRating' => '1',
        ],
      ];
    }
    wp_reset_postdata();

    if ($count < 1) return;

    $avg = round($sum / $count, 2);

    // Product fields
    $product_name = get_the_title($product_id);
    $url = get_permalink($product_id);
    $image = wp_get_attachment_url(get_post_thumbnail_id($product_id));

    $data = [
      '@context' => 'https://schema.org',
      '@type' => 'Product',
      '@id' => $url . '#product',
      'name' => $product_name,
      'url' => $url,
    ];

    if ($image) $data['image'] = [$image];

    $data['aggregateRating'] = [
      '@type' => 'AggregateRating',
      'ratingValue' => (string)$avg,
      'reviewCount' => (string)$count,
      'bestRating' => '5',
      'worstRating' => '1',
    ];

    // Keep review array reasonable size for markup.
    $data['review'] = array_slice($reviews, 0, 20);

    echo "\n<script type=\"application/ld+json\">" . wp_json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "</script>\n";
  }
}

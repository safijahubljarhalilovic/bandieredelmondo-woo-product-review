<?php
if (!defined('ABSPATH')) exit;

class BDM_Review_Ajax {

  public static function init() {
    add_action('wp_ajax_bdm_submit_review', [__CLASS__, 'submit_review']);
    add_action('wp_ajax_nopriv_bdm_submit_review', [__CLASS__, 'submit_review']);
  }

  private static function is_certified_purchase($email, $product_id) {
    if (!function_exists('wc_get_orders')) return 0;

    $email = sanitize_email($email);
    if (!$email || !$product_id) return 0;

    // Search recent-ish orders by billing email
    $orders = wc_get_orders([
      'limit' => 50,
      'billing_email' => $email,
      'status' => ['wc-processing', 'wc-completed', 'wc-on-hold'],
      'orderby' => 'date',
      'order' => 'DESC'
    ]);

    foreach ($orders as $order) {
      foreach ($order->get_items() as $item) {
        if ((int)$item->get_product_id() === (int)$product_id || (int)$item->get_variation_id() === (int)$product_id) {
          return 1;
        }
      }
    }
    return 0;
  }

  public static function submit_review() {
    $nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
    if (!wp_verify_nonce($nonce, 'bdm_review_nonce')) {
      wp_send_json_error(['message' => 'Invalid request.']);
    }

    $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
    $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $rating = isset($_POST['rating']) ? absint($_POST['rating']) : 0;
    $comment = isset($_POST['comment']) ? wp_kses_post($_POST['comment']) : '';

    if (!$product_id || get_post_type($product_id) !== 'product') {
      wp_send_json_error(['message' => 'Invalid product.']);
    }
    if (strlen($name) < 1 || !$email) {
      wp_send_json_error(['message' => 'Name and email are required.']);
    }
    if ($rating < 1 || $rating > 5) {
      wp_send_json_error(['message' => 'Please select a rating.']);
    }

    // Create review post in "draft" = pending email confirmation
    $rid = wp_insert_post([
      'post_type' => BDM_Review_CPT::CPT,
      'post_status' => 'draft',
      'post_title' => sprintf('Review for product #%d', $product_id),
      'post_content' => '',
    ], true);

    if (is_wp_error($rid)) {
      wp_send_json_error(['message' => 'Could not save review.']);
    }

    update_post_meta($rid, '_bdm_product_id', $product_id);
    update_post_meta($rid, '_bdm_name', $name);
    update_post_meta($rid, '_bdm_email', $email);
    update_post_meta($rid, '_bdm_rating', $rating);
    update_post_meta($rid, '_bdm_comment', wp_strip_all_tags($comment));
    update_post_meta($rid, '_bdm_confirmed', 0);

    $cert = self::is_certified_purchase($email, $product_id);
    update_post_meta($rid, '_bdm_certified', $cert);

    // Confirmation token
    $token_plain = wp_generate_password(32, false, false);
    $token_hash  = wp_hash_password($token_plain);
    update_post_meta($rid, '_bdm_token_hash', $token_hash);

    $confirm_url = add_query_arg([
      'bdm_review_confirm' => 1,
      'rid' => $rid,
      'token' => rawurlencode($token_plain),
    ], home_url('/'));

    $product = function_exists('wc_get_product') ? wc_get_product($product_id) : null;
    $product_name = $product ? $product->get_name() : get_the_title($product_id);

    $subject = sprintf('[%s] Confirm your review', wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES));
    $message = "Hi {$name},\n\n";
    $message .= "Thanks for your review for: {$product_name}\n\n";
    $message .= "Please confirm your review by clicking this link:\n{$confirm_url}\n\n";
    $message .= "If you did not submit this review, you can ignore this email.\n";

    wp_mail($email, $subject, $message);

    wp_send_json_success([
      'message' => __('Thanks! Please check your email and confirm your review via the link we sent you.', 'bandieredelmondo-review'),
    ]);
  }
}

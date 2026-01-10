<?php
if (!defined('ABSPATH')) exit;

class BDM_Review_Uploads {

  public static function init() {
    add_action('init', [__CLASS__, 'add_query_vars']);
    add_action('template_redirect', [__CLASS__, 'handle_upload_page']);
    add_action('admin_post_nopriv_bdm_review_upload', [__CLASS__, 'handle_upload_post']);
    add_action('admin_post_bdm_review_upload', [__CLASS__, 'handle_upload_post']);
  }

  private static function validate_order_number_for_review($order_number, $rid) {
    if (!function_exists('wc_get_order')) return false;
  
    $order_number = trim((string)$order_number);
    if ($order_number === '') return false;
  
    // Most stores use numeric order IDs as "order number"
    // We'll accept numeric only. (If you use custom order numbers plugin, tell me and I’ll adapt.)
    if (!ctype_digit($order_number)) return false;
  
    $order_id = (int)$order_number;
    if ($order_id <= 0) return false;
  
    $order = wc_get_order($order_id);
    if (!$order) return false;
  
    $review_email = (string) get_post_meta($rid, '_bdm_email', true);
    $review_email = sanitize_email($review_email);
  
    $order_email = sanitize_email($order->get_billing_email());
    if (!$review_email || !$order_email) return false;
  
    // Must match email of the review
    if (strtolower($review_email) !== strtolower($order_email)) return false;
  
    // Must contain the reviewed product
    $product_id = (int) get_post_meta($rid, '_bdm_product_id', true);
    if ($product_id <= 0) return false;
  
    foreach ($order->get_items() as $item) {
      $pid = (int)$item->get_product_id();
      $vid = (int)$item->get_variation_id();
      if ($pid === $product_id || $vid === $product_id) {
        return true;
      }
    }
  
    return false;
  }  

  public static function add_query_vars() {
    add_filter('query_vars', function($vars){
      $vars[] = 'bdm_review_upload';
      return $vars;
    });
  }

  public static function render_upload_form($rid, $token_plain) {
    $action = esc_url(admin_url('admin-post.php'));
    $nonce = wp_create_nonce('bdm_review_upload_' . $rid);

    $html  = '<form method="post" action="' . $action . '" enctype="multipart/form-data">';
    $html .= '<input type="hidden" name="action" value="bdm_review_upload">';
    $html .= '<input type="hidden" name="rid" value="' . esc_attr($rid) . '">';
    $html .= '<input type="hidden" name="token" value="' . esc_attr($token_plain) . '">';
    $html .= '<input type="hidden" name="nonce" value="' . esc_attr($nonce) . '">';

    $html .= '<p style="margin:0 0 10px;">';
    $html .= '<label><strong>' . esc_html__('Numero dell\'ordine', 'bandieredelmondo-review') . '</strong><br>';
    $html .= '<input type="text" name="order_number" maxlength="80" style="width:100%;max-width:420px;padding:10px;border:1px solid #ddd;border-radius:8px;" placeholder="' . esc_attr__('Se hai acquistato, inserisci il numero dell\'ordine (facoltativo)', 'bandieredelmondo-review') . '">';
    $html .= '</label>';
    $html .= '<br><small style="opacity:.8;">' . esc_html__('Se non hai un numero d\'ordine, puoi lasciare questo campo vuoto. L\'amministratore verificherà se la recensione è certificata.', 'bandieredelmondo-review') . '</small>';
    $html .= '</p>';

    $html .= '<p><label><strong>Foto del prodotto</strong><br><input type="file" name="product_photo" accept="image/*"></label></p>';
    $html .= '<p><label><strong>Foto del profilo</strong><br><input type="file" name="profile_photo" accept="image/*"></label></p>';

    $html .= '<button type="submit" style="padding:10px 14px;border:1px solid #222;background:#222;color:#fff;border-radius:8px;cursor:pointer;">Invia</button>';
    $html .= '</form>';

    return $html;
  }

  private static function verify_token($rid, $token_plain) {
    $hash = get_post_meta($rid, '_bdm_token_hash', true);
    if (!$hash || !$token_plain) return false;
    return wp_check_password($token_plain, $hash);
  }

  private static function handle_one_upload($file_key, $rid) {
    if (empty($_FILES[$file_key]) || empty($_FILES[$file_key]['name'])) return 0;

    $file = $_FILES[$file_key];

    // Basic file validation
    $allowed = ['image/jpeg','image/png','image/webp','image/gif'];
    if (!in_array($file['type'], $allowed, true)) return 0;

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';

    $overrides = ['test_form' => false];
    $uploaded = wp_handle_upload($file, $overrides);

    if (!empty($uploaded['error']) || empty($uploaded['file'])) return 0;

    $attachment = [
      'post_mime_type' => $uploaded['type'],
      'post_title' => sanitize_file_name(basename($uploaded['file'])),
      'post_content' => '',
      'post_status' => 'inherit'
    ];

    $attach_id = wp_insert_attachment($attachment, $uploaded['file']);
    if (is_wp_error($attach_id)) return 0;

    $attach_data = wp_generate_attachment_metadata($attach_id, $uploaded['file']);
    wp_update_attachment_metadata($attach_id, $attach_data);

    // Attach to review post
    wp_update_post([
      'ID' => $attach_id,
      'post_parent' => $rid
    ]);

    return (int)$attach_id;
  }

  public static function handle_upload_page() {
    $flag = get_query_var('bdm_review_upload');
    if (!$flag) return;

    // This page is optional; we mostly post via admin-post handler.
    wp_die('This endpoint is used for photo uploads.', 'BDM Upload', ['response' => 200]);
  }

  public static function handle_upload_post() {
    $rid = isset($_POST['rid']) ? absint($_POST['rid']) : 0;
    $token = isset($_POST['token']) ? sanitize_text_field(wp_unslash($_POST['token'])) : '';
    $nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';

    if (!$rid || get_post_type($rid) !== BDM_Review_CPT::CPT) {
      wp_die('Invalid review.');
    }
    if (!wp_verify_nonce($nonce, 'bdm_review_upload_' . $rid)) {
      wp_die('Invalid request.');
    }
    if (!self::verify_token($rid, $token)) {
      wp_die('Invalid token.');
    }

    $order_number = isset($_POST['order_number']) ? sanitize_text_field(wp_unslash($_POST['order_number'])) : '';
    $order_number = trim($order_number);

    if ($order_number !== '') {
      // NEW: validate and certify
      if (self::validate_order_number_for_review($order_number, $rid)) {
        update_post_meta($rid, '_bdm_certified', 1);
        update_post_meta($rid, '_bdm_order_number', $order_number);
      } else {
        // Keep spontaneous if invalid
        update_post_meta($rid, '_bdm_certified', 0);
        update_post_meta($rid, '_bdm_order_invalid', 1);
      }
    } else {
      // No order number provided => spontaneous (no label on frontend)
      delete_post_meta($rid, '_bdm_order_number');
      delete_post_meta($rid, '_bdm_order_invalid');
      update_post_meta($rid, '_bdm_certified', 0);
    }

    $prod_id = self::handle_one_upload('product_photo', $rid);
    $prof_id = self::handle_one_upload('profile_photo', $rid);

    if ($prod_id) update_post_meta($rid, '_bdm_product_photo_id', $prod_id);
    if ($prof_id) update_post_meta($rid, '_bdm_profile_photo_id', $prof_id);

    $is_cert = (int) get_post_meta($rid, '_bdm_certified', true);
    $is_invalid = (int) get_post_meta($rid, '_bdm_order_invalid', true);

    $html  = '<div style="max-width:720px;margin:40px auto;padding:20px;border:1px solid #eee;border-radius:12px;font-family:system-ui;">';
    $html .= '<h2>' . esc_html__('Salvato', 'bandieredelmondo-review') . '</h2>';

    if ($order_number !== '' && $is_cert) {
      $html .= '<p style="color:#0a7;font-weight:600;">' . esc_html__('Ordine verificato ✅ La tua recensione verrà contrassegnata come certificata dopo l\'approvazione dell\'amministratore.', 'bandieredelmondo-review') . '</p>';
    } elseif ($order_number !== '' && $is_invalid) {
      $html .= '<p style="color:#b32d2e;font-weight:600;">' . esc_html__('Numero d\'ordine non verificato. La tua recensione sarà considerata spontanea, a meno che l\'amministratore non la verifichi manualmente.', 'bandieredelmondo-review') . '</p>';
    } else {
      $html .= '<p>' . esc_html__('Nessun numero d\'ordine fornito. La tua recensione sarà considerata spontanea, a meno che l\'amministratore non la verifichi manualmente.', 'bandieredelmondo-review') . '</p>';
    }

    $html .= '<p>' . esc_html__('Le tue foto sono state salvate. La tua recensione apparirà dopo l\'approvazione dell\'amministratore.', 'bandieredelmondo-review') . '</p>';
    $html .= '</div>';

    wp_die($html, 'Caricamento completato', ['response' => 200]);
  }
}

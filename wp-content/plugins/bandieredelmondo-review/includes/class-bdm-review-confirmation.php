<?php
if (!defined('ABSPATH')) exit;

class BDM_Review_Confirmation {

  public static function init() {
    add_action('init', [__CLASS__, 'add_query_vars']);
    add_action('template_redirect', [__CLASS__, 'handle_confirm']);
  }

  public static function add_query_vars() {
    add_filter('query_vars', function($vars){
      $vars[] = 'bdm_review_confirm';
      $vars[] = 'rid';
      $vars[] = 'token';
      return $vars;
    });
  }

  private static function verify_token($rid, $token_plain) {
    $hash = get_post_meta($rid, '_bdm_token_hash', true);
    if (!$hash || !$token_plain) return false;
    return wp_check_password($token_plain, $hash);
  }

  public static function handle_confirm() {
    $flag = get_query_var('bdm_review_confirm');
    if (!$flag) return;

    $rid = absint(get_query_var('rid'));
    $token = isset($_GET['token']) ? sanitize_text_field(wp_unslash($_GET['token'])) : '';

    if (!$rid || get_post_type($rid) !== BDM_Review_CPT::CPT) {
      wp_die('Invalid review.');
    }

    if (!self::verify_token($rid, $token)) {
      wp_die('Invalid or expired confirmation link.');
    }

    // Mark confirmed, move to pending admin approval
    update_post_meta($rid, '_bdm_confirmed', 1);
    wp_update_post([
      'ID' => $rid,
      'post_status' => 'pending'
    ]);

    $name = BDM_Review_CPT::meta($rid, '_bdm_name', 'User');
    $product_id = absint(BDM_Review_CPT::meta($rid, '_bdm_product_id', 0));
    $product_title = $product_id ? get_the_title($product_id) : '';

    $upload_url = add_query_arg([
      'bdm_review_upload' => 1,
      'rid' => $rid,
      'token' => rawurlencode($token),
    ], home_url('/'));

    // Render simple confirmation + upload form
    $html  = '<div style="max-width:720px;margin:40px auto;padding:20px;border:1px solid #eee;border-radius:12px;font-family:system-ui;">';
    $html .= '<h2>Grazie, ' . esc_html($name) . '!</h2>';
    $html .= '<p>La tua recensione per <strong>' . esc_html($product_title) . '</strong> è stata confermata ed è in attesa di approvazione da parte dell’amministratore.</p>';
    $html .= '<hr style="margin:18px 0;">';
    $html .= '<h3>Facoltativo: aggiungi foto</h3>';
    $html .= '<p>Puoi aggiungere una foto del prodotto e una foto del tuo profilo. (Facoltativo)</p>';
    $html .= BDM_Review_Uploads::render_upload_form($rid, $token);
    $html .= '<p style="margin-top:16px;opacity:.8;font-size:13px;">Puoi chiudere questa pagina. La tua recensione apparirà dopo l’approvazione dell’amministratore.</p>';
    $html .= '</div>';

    wp_die($html, 'Review confirmed', ['response' => 200]);
  }
}

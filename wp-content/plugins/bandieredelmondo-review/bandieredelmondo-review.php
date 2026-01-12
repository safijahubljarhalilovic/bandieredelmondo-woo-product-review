<?php
/**
 * Plugin Name: Bandieredelmondo Review
 * Description: Guest product reviews with email confirmation, admin approval, optional photos, certified purchase flag, and JSON-LD (review + aggregateRating).
 * Version: 1.0.0
 * Author: Bandieredelmondo
 * Text Domain: bandieredelmondo-review
 */

if (!defined('ABSPATH')) exit;

define('BDM_REVIEW_VERSION', '1.2.1');
define('BDM_REVIEW_PATH', plugin_dir_path(__FILE__));
define('BDM_REVIEW_URL', plugin_dir_url(__FILE__));

require_once BDM_REVIEW_PATH . 'includes/class-bdm-review-cpt.php';
require_once BDM_REVIEW_PATH . 'includes/class-bdm-review-shortcodes.php';
require_once BDM_REVIEW_PATH . 'includes/class-bdm-review-ajax.php';
require_once BDM_REVIEW_PATH . 'includes/class-bdm-review-confirmation.php';
require_once BDM_REVIEW_PATH . 'includes/class-bdm-review-uploads.php';
require_once BDM_REVIEW_PATH . 'includes/class-bdm-review-admin.php';
require_once BDM_REVIEW_PATH . 'includes/class-bdm-review-schema.php';

class BandieredelMondo_Review_Plugin {

  public static function init() {
    add_action('plugins_loaded', [__CLASS__, 'load']);
    add_action('init', [__CLASS__, 'precision']);

    // Assets
    add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
  }

  public static function load() {
    BDM_Review_CPT::init();
    BDM_Review_Shortcodes::init();
    BDM_Review_Ajax::init();
    BDM_Review_Confirmation::init();
    BDM_Review_Uploads::init();
    BDM_Review_Admin::init();
    BDM_Review_Schema::init();
  }

  public static function precision() {
    ini_set('serialize_precision', -1);
    ini_set('precision', 14);
  }

  public static function enqueue_assets() {
    // Only enqueue if at least one shortcode is used on the page OR we are on a product page.
    // (Still lightweight; safe to load broadly.)
    wp_register_style('bdm-review', BDM_REVIEW_URL . 'assets/bdm-review.css', [], BDM_REVIEW_VERSION);
    wp_register_script('bdm-review', BDM_REVIEW_URL . 'assets/bdm-review.js', ['jquery'], BDM_REVIEW_VERSION, true);

    wp_enqueue_style('bdm-review');
    wp_enqueue_script('bdm-review');

    wp_localize_script('bdm-review', 'BDMReview', [
      'ajaxUrl' => admin_url('admin-ajax.php'),
      'nonce'   => wp_create_nonce('bdm_review_nonce'),
      'siteUrl' => home_url('/'),
      'i18n' => [
        'submitting' => __('Invio...', 'bandieredelmondo-review'),
        'error' => __('Qualcosa è andato storto. Per favore riprova.', 'bandieredelmondo-review'),
      ],
    ]);
  }
}

BandieredelMondo_Review_Plugin::init();

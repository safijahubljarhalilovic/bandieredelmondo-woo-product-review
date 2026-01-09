<?php
if (!defined('ABSPATH')) exit;

class BDM_Review_CPT {

  const CPT = 'bdm_review';

  public static function init() {
    add_action('init', [__CLASS__, 'register_cpt']);
    add_action('init', [__CLASS__, 'register_post_status']);
  }

  public static function register_cpt() {
    register_post_type(self::CPT, [
      'labels' => [
        'name' => 'BDM Reviews',
        'singular_name' => 'BDM Review',
      ],
      'public' => false,
      'show_ui' => true,
      'show_in_menu' => true,
      'menu_icon' => 'dashicons-star-filled',
      'supports' => ['title', 'editor'],
      'capability_type' => 'post',
      'map_meta_cap' => true,
      'capabilities' => [
        'create_posts' => false,
      ],
    ]);
  }

  public static function register_post_status() {
    register_post_status('bdm_rejected', [
      'label' => 'Rejected',
      'public' => false,
      'exclude_from_search' => true,
      'show_in_admin_all_list' => true,
      'show_in_admin_status_list' => true,
      'label_count' => _n_noop('Rejected <span class="count">(%s)</span>', 'Rejected <span class="count">(%s)</span>'),
    ]);
  }

  public static function meta($review_id, $key, $default = '') {
    $v = get_post_meta($review_id, $key, true);
    return ($v === '' || $v === null) ? $default : $v;
  }
}

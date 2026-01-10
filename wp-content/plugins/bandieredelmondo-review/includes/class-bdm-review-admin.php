<?php
if (!defined('ABSPATH')) exit;

class BDM_Review_Admin {

  public static function init() {
    add_filter('manage_' . BDM_Review_CPT::CPT . '_posts_columns', [__CLASS__, 'columns']);
    add_action('manage_' . BDM_Review_CPT::CPT . '_posts_custom_column', [__CLASS__, 'column_content'], 10, 2);

    add_filter('post_row_actions', [__CLASS__, 'row_actions'], 10, 2);
    add_action('admin_init', [__CLASS__, 'handle_actions']);

    add_action('admin_menu', [__CLASS__, 'stats_menu']);

    add_filter('post_row_actions', [__CLASS__, 'hide_row_actions'], 999, 2);
  }

  public static function columns($cols) {
    $new = [];
    $new['cb'] = $cols['cb'];
    $new['title'] = 'Review';
    $new['bdm_status'] = 'Status';
    $new['bdm_product'] = 'Product';
    $new['bdm_rating'] = 'Rating';
    $new['bdm_email'] = 'Email';
    $new['bdm_cert'] = 'Certified';
    $new['bdm_order'] = 'Order #';
    $new['date'] = $cols['date'];
    return $new;
  }

  public static function column_content($col, $post_id) {
    if ($col === 'bdm_order') {
      $ord = get_post_meta($post_id, '_bdm_order_number', true);
      echo $ord ? esc_html($ord) : '—';
    }
    if ($col === 'bdm_product') {
      $pid = absint(get_post_meta($post_id, '_bdm_product_id', true));
      echo $pid ? '<a href="' . esc_url(get_edit_post_link($pid)) . '">' . esc_html(get_the_title($pid)) . '</a>' : '—';
    }
    if ($col === 'bdm_rating') {
      $r = (int)get_post_meta($post_id, '_bdm_rating', true);
      echo esc_html(str_repeat('★', max(0, min(5, $r))));
    }
    if ($col === 'bdm_email') {
      echo esc_html(get_post_meta($post_id, '_bdm_email', true));
    }
    if ($col === 'bdm_cert') {
      $c = (int) get_post_meta($post_id, '_bdm_certified', true);
      // traffic-light indicator
      if ($c) {
        echo '<span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#0a7;" title="Certificato">Certificato</span>';
      } else {
        echo '<span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#f2b400;" title="Spontaneo">Spontaneo</span>';
      }
    }
    if ($col === 'bdm_status') {
      $status = get_post_status($post_id);
      $confirmed = (int) get_post_meta($post_id, '_bdm_confirmed', true);

      if ($status === 'draft' && !$confirmed) {
        echo '<span style="color:#777;">🕒 Pending email</span>';
      }
      elseif ($status === 'pending') {
        echo '<span style="color:#d98400;">⏳ Pending approval</span>';
      }
      elseif ($status === 'publish') {
        echo '<span style="color:#0a7;font-weight:600;">✅ Approved</span>';
      }
      elseif ($status === 'bdm_rejected') {
        echo '<span style="color:#b32d2e;">❌ Rejected</span>';
      }
      else {
        echo esc_html($status);
      }
    }
  }

  public static function row_actions($actions, $post) {
    if ($post->post_type !== BDM_Review_CPT::CPT) return $actions;

    $approve_url = wp_nonce_url(add_query_arg([
      'bdm_review_action' => 'approve',
      'rid' => $post->ID
    ], admin_url('edit.php?post_type=' . BDM_Review_CPT::CPT)), 'bdm_review_action_' . $post->ID);

    $reject_url = wp_nonce_url(add_query_arg([
      'bdm_review_action' => 'reject',
      'rid' => $post->ID
    ], admin_url('edit.php?post_type=' . BDM_Review_CPT::CPT)), 'bdm_review_action_' . $post->ID);

    $actions['bdm_approve'] = '<a href="' . esc_url($approve_url) . '">Approve</a>';
    $actions['bdm_reject']  = '<a href="' . esc_url($reject_url) . '">Reject</a>';

    return $actions;
  }

  public static function handle_actions() {
    if (!is_admin()) return;
    if (empty($_GET['bdm_review_action']) || empty($_GET['rid'])) return;

    $action = sanitize_text_field(wp_unslash($_GET['bdm_review_action']));
    $rid = absint($_GET['rid']);

    if (!current_user_can('edit_post', $rid)) return;
    if (!wp_verify_nonce($_GET['_wpnonce'] ?? '', 'bdm_review_action_' . $rid)) return;

    if (get_post_type($rid) !== BDM_Review_CPT::CPT) return;

    if ($action === 'approve') {
      wp_update_post(['ID' => $rid, 'post_status' => 'publish']);
    } elseif ($action === 'reject') {
      wp_update_post(['ID' => $rid, 'post_status' => 'bdm_rejected']);
    }

    wp_safe_redirect(admin_url('edit.php?post_type=' . BDM_Review_CPT::CPT));
    exit;
  }

  public static function stats_menu() {
    add_submenu_page(
      'edit.php?post_type=' . BDM_Review_CPT::CPT,
      'Stats',
      'Stats',
      'manage_options',
      'bdm-review-stats',
      [__CLASS__, 'render_stats_page']
    );
  }

  public static function render_stats_page() {
    if (!current_user_can('manage_options')) return;

    $total = wp_count_posts(BDM_Review_CPT::CPT);
    $draft = (int)($total->draft ?? 0);     // pending email
    $pending = (int)($total->pending ?? 0); // pending admin
    $pub = (int)($total->publish ?? 0);     // approved
    $rej = (int)($total->bdm_rejected ?? 0);

    ?>
    <div class="wrap">
      <h1>BDM Review Stats</h1>
      <table class="widefat striped" style="max-width:620px;">
        <tbody>
          <tr><th>Total</th><td><?php echo esc_html($draft + $pending + $pub + $rej); ?></td></tr>
          <tr><th>Pending email confirmation</th><td><?php echo esc_html($draft); ?></td></tr>
          <tr><th>Pending admin approval</th><td><?php echo esc_html($pending); ?></td></tr>
          <tr><th>Approved</th><td><?php echo esc_html($pub); ?></td></tr>
          <tr><th>Rejected</th><td><?php echo esc_html($rej); ?></td></tr>
        </tbody>
      </table>
    </div>
    <?php
  }

  public static function hide_row_actions($actions, $post) {
    if (empty($post) || $post->post_type !== BDM_Review_CPT::CPT) {
      return $actions;
    }

    // Remove built-in row actions
    unset($actions['edit']);                 // "Edit"
    unset($actions['inline hide-if-no-js']); // "Quick Edit"
    // unset($actions['trash']);                // "Trash"
    unset($actions['view']);                 // "View"
    unset($actions['preview']);              // "Preview"

    // Remove actions added by other plugins (common keys)
    unset($actions['duplicate']);            // "Duplicate"
    unset($actions['download']);             // "Download"
    unset($actions['copy']);                 // sometimes used

    // Safety net: remove anything that contains these words in the label (for plugins using random keys)
    foreach ($actions as $key => $html) {
      $needle = strtolower(wp_strip_all_tags($html));
      if (
        str_contains($needle, 'quick edit') ||
        str_contains($needle, 'duplicate') ||
        str_contains($needle, 'download')
      ) {
        unset($actions[$key]);
      }
    }

    return $actions;
  }
}

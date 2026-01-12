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

    add_action('add_meta_boxes', [__CLASS__, 'add_edit_metaboxes']);
    add_action('save_post_' . BDM_Review_CPT::CPT, [__CLASS__, 'save_edit_metaboxes']);
    add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_admin_assets']);

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
        echo '<span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#0a7;" title="Certificato"></span> Certificato';
      } else {
        echo '<span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#f2b400;" title="Spontaneo"></span> Spontaneo';
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
    // unset($actions['edit']);                 // "Edit"
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

  public static function render_metabox($post) {
    $cert = (int) get_post_meta($post->ID, '_bdm_certified', true);
    $ord  = (string) get_post_meta($post->ID, '_bdm_order_number', true);
  
    wp_nonce_field('bdm_review_cert_save', 'bdm_review_cert_nonce');
  
    echo '<p><label style="display:flex;gap:8px;align-items:center;">';
    echo '<input type="checkbox" name="bdm_certified" value="1" ' . checked(1, $cert, false) . '>';
    echo '<strong>Acquisto certificato</strong>';
    echo '</label></p>';
  
    echo '<p style="margin:0;"><label><strong>Numero d\'ordine</strong><br>';
    echo '<input type="text" name="bdm_order_number" value="' . esc_attr($ord) . '" maxlength="80" style="width:100%;">';
    echo '</label></p>';
  
    echo '<p style="opacity:.8;font-size:12px;margin-top:8px;">';
    echo 'Se la certificazione è selezionata, la recensione mostrerà una stella verde sulla pagina del prodotto.';
    echo '</p>';
  }

  public static function enqueue_admin_assets($hook) {
    // Only load on BDM review edit screens
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || $screen->post_type !== BDM_Review_CPT::CPT) return;

    // Needed for wp.media uploader
    wp_enqueue_media();

    wp_enqueue_script(
      'bdm-review-admin',
      BDM_REVIEW_URL . 'assets/bdm-review-admin.js',
      ['jquery'],
      BDM_REVIEW_VERSION,
      true
    );
  }

  public static function add_edit_metaboxes() {
    add_meta_box(
      'bdm_review_details_box',
      __('Review details', 'bandieredelmondo-review'),
      [__CLASS__, 'render_details_metabox'],
      BDM_Review_CPT::CPT,
      'normal',
      'high'
    );

    add_meta_box(
      'bdm_review_photos_box',
      __('Review photos', 'bandieredelmondo-review'),
      [__CLASS__, 'render_photos_metabox'],
      BDM_Review_CPT::CPT,
      'side',
      'high'
    );
  }

  public static function render_details_metabox($post) {
    $name   = (string) get_post_meta($post->ID, '_bdm_name', true);
    $email  = (string) get_post_meta($post->ID, '_bdm_email', true);
    $rating = (int) get_post_meta($post->ID, '_bdm_rating', true);
    $comment = (string) get_post_meta($post->ID, '_bdm_comment', true);

    $product_id = (int) get_post_meta($post->ID, '_bdm_product_id', true);
    $order_number = (string) get_post_meta($post->ID, '_bdm_order_number', true);
    $certified = (int) get_post_meta($post->ID, '_bdm_certified', true);

    wp_nonce_field('bdm_review_admin_edit_save', 'bdm_review_admin_edit_nonce');

    echo '<table class="form-table" role="presentation"><tbody>';

    echo '<tr><th><label>' . esc_html__('Name', 'bandieredelmondo-review') . '</label></th><td>';
    echo '<input type="text" name="bdm_name" value="' . esc_attr($name) . '" class="regular-text" maxlength="120">';
    echo '</td></tr>';

    echo '<tr><th><label>' . esc_html__('Email', 'bandieredelmondo-review') . '</label></th><td>';
    echo '<input type="email" name="bdm_email" value="' . esc_attr($email) . '" class="regular-text" maxlength="190">';
    echo '</td></tr>';

    echo '<tr><th><label>' . esc_html__('Rating (1–5)', 'bandieredelmondo-review') . '</label></th><td>';
    echo '<input type="number" name="bdm_rating" value="' . esc_attr($rating ?: 0) . '" min="1" max="5" step="1" style="width:80px;">';
    echo '</td></tr>';

    echo '<tr><th><label>' . esc_html__('Comment', 'bandieredelmondo-review') . '</label></th><td>';
    echo '<textarea name="bdm_comment" rows="6" style="width:100%;max-width:820px;">' . esc_textarea($comment) . '</textarea>';
    echo '</td></tr>';

    echo '<tr><th><label>' . esc_html__('Product ID', 'bandieredelmondo-review') . '</label></th><td>';
    if ($product_id) {
      echo '<strong>' . esc_html(get_the_title($product_id)) . '</strong>';
      echo ' <span style="opacity:.7;">(#' . esc_html($product_id) . ')</span>';
    
      $edit_link = get_edit_post_link($product_id);
      if ($edit_link) {
        echo '<div style="margin-top:6px;">';
        echo '<a href="' . esc_url($edit_link) . '" target="_blank" rel="noopener">' . esc_html__('Edit product', 'bandieredelmondo-review') . '</a>';
        echo '</div>';
      }
    } else {
      echo '<em>' . esc_html__('No product assigned', 'bandieredelmondo-review') . '</em>';
    }
    echo '</td></tr>';

    echo '<tr><th><label>' . esc_html__('Order number', 'bandieredelmondo-review') . '</label></th><td>';
    echo '<input type="text" name="bdm_order_number" value="' . esc_attr($order_number) . '" class="regular-text" maxlength="80">';
    echo '<p class="description">' . esc_html__('Optional. Admin can verify purchase using this order number.', 'bandieredelmondo-review') . '</p>';
    echo '</td></tr>';

    echo '<tr><th><label>' . esc_html__('Certified purchase', 'bandieredelmondo-review') . '</label></th><td>';
    echo '<label><input type="checkbox" name="bdm_certified" value="1" ' . checked(1, $certified, false) . '> ';
    echo esc_html__('Mark as certified (shows green star on frontend)', 'bandieredelmondo-review') . '</label>';
    echo '</td></tr>';

    echo '</tbody></table>';
  }

  public static function render_photos_metabox($post) {
    $profile_id = (int) get_post_meta($post->ID, '_bdm_profile_photo_id', true);
    $product_id = (int) get_post_meta($post->ID, '_bdm_product_photo_id', true);

    $profile_url = $profile_id ? wp_get_attachment_image_url($profile_id, 'thumbnail') : '';
    $product_url = $product_id ? wp_get_attachment_image_url($product_id, 'thumbnail') : '';

    echo '<div class="bdm-admin-photo-box" style="margin-bottom:14px;">';
    echo '<strong>' . esc_html__('Profile photo (avatar)', 'bandieredelmondo-review') . '</strong>';
    echo '<input type="hidden" id="bdm_profile_photo_id" name="bdm_profile_photo_id" value="' . esc_attr($profile_id) . '">';
    echo '<div style="margin:10px 0;">';
    echo '<img id="bdm_profile_photo_preview" src="' . esc_url($profile_url) . '" style="width:90px;height:90px;object-fit:cover;border:1px solid #ddd;border-radius:12px;' . ($profile_url ? '' : 'display:none;') . '">';
    echo '</div>';
    echo '<p style="margin:0;display:flex;gap:8px;flex-wrap:wrap;">';
    echo '<button type="button" class="button bdm-media-pick" data-target="profile">' . esc_html__('Select/Upload', 'bandieredelmondo-review') . '</button>';
    echo '<button type="button" class="button bdm-media-remove" data-target="profile">' . esc_html__('Remove', 'bandieredelmondo-review') . '</button>';
    echo '</p>';
    echo '</div>';

    echo '<div class="bdm-admin-photo-box">';
    echo '<strong>' . esc_html__('Product photo', 'bandieredelmondo-review') . '</strong>';
    echo '<input type="hidden" id="bdm_product_photo_id" name="bdm_product_photo_id" value="' . esc_attr($product_id) . '">';
    echo '<div style="margin:10px 0;">';
    echo '<img id="bdm_product_photo_preview" src="' . esc_url($product_url) . '" style="width:90px;height:90px;object-fit:cover;border:1px solid #ddd;border-radius:12px;' . ($product_url ? '' : 'display:none;') . '">';
    echo '</div>';
    echo '<p style="margin:0;display:flex;gap:8px;flex-wrap:wrap;">';
    echo '<button type="button" class="button bdm-media-pick" data-target="product">' . esc_html__('Select/Upload', 'bandieredelmondo-review') . '</button>';
    echo '<button type="button" class="button bdm-media-remove" data-target="product">' . esc_html__('Remove', 'bandieredelmondo-review') . '</button>';
    echo '</p>';
    echo '</div>';
  }

  public static function save_edit_metaboxes($post_id) {
    if (!isset($_POST['bdm_review_admin_edit_nonce']) || !wp_verify_nonce($_POST['bdm_review_admin_edit_nonce'], 'bdm_review_admin_edit_save')) {
      return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    // Basic fields
    $name = isset($_POST['bdm_name']) ? sanitize_text_field(wp_unslash($_POST['bdm_name'])) : '';
    $email = isset($_POST['bdm_email']) ? sanitize_email(wp_unslash($_POST['bdm_email'])) : '';
    $rating = isset($_POST['bdm_rating']) ? absint($_POST['bdm_rating']) : 0;
    $comment = isset($_POST['bdm_comment']) ? wp_strip_all_tags(wp_unslash($_POST['bdm_comment'])) : '';

    $product_id = isset($_POST['bdm_product_id']) ? absint($_POST['bdm_product_id']) : 0;
    $order_number = isset($_POST['bdm_order_number']) ? sanitize_text_field(wp_unslash($_POST['bdm_order_number'])) : '';
    $certified = isset($_POST['bdm_certified']) ? 1 : 0;

    if ($name !== '') update_post_meta($post_id, '_bdm_name', $name);
    if ($email !== '') update_post_meta($post_id, '_bdm_email', $email);
    if ($rating >= 1 && $rating <= 5) update_post_meta($post_id, '_bdm_rating', $rating);
    update_post_meta($post_id, '_bdm_comment', $comment);

    if ($product_id > 0) update_post_meta($post_id, '_bdm_product_id', $product_id);

    if ($order_number !== '') update_post_meta($post_id, '_bdm_order_number', $order_number);
    else delete_post_meta($post_id, '_bdm_order_number');

    update_post_meta($post_id, '_bdm_certified', $certified);

    // Photo attachment IDs
    $profile_photo_id = isset($_POST['bdm_profile_photo_id']) ? absint($_POST['bdm_profile_photo_id']) : 0;
    $product_photo_id = isset($_POST['bdm_product_photo_id']) ? absint($_POST['bdm_product_photo_id']) : 0;

    if ($profile_photo_id) update_post_meta($post_id, '_bdm_profile_photo_id', $profile_photo_id);
    else delete_post_meta($post_id, '_bdm_profile_photo_id');

    if ($product_photo_id) update_post_meta($post_id, '_bdm_product_photo_id', $product_photo_id);
    else delete_post_meta($post_id, '_bdm_product_photo_id');
  }

}

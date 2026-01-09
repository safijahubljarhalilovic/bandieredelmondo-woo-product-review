<?php
if (!defined('ABSPATH')) exit;

class BDM_Review_Shortcodes {

  public static function init() {
    add_shortcode('bdm_review_cta', [__CLASS__, 'shortcode_cta']);
    add_shortcode('bdm_product_reviews', [__CLASS__, 'shortcode_list']);
  }

  private static function get_product_id_from_context($atts) {
    if (!empty($atts['product_id'])) return absint($atts['product_id']);
    if (function_exists('is_product') && is_product()) {
      global $product;
      if ($product && is_a($product, 'WC_Product')) return $product->get_id();
      $id = get_the_ID();
      return absint($id);
    }
    return 0;
  }

  public static function shortcode_cta($atts) {
    $atts = shortcode_atts([
      'product_id' => '',
      'label' => __('Leave a review', 'bandieredelmondo-review'),
      'button_class' => '',
    ], $atts);

    $product_id = self::get_product_id_from_context($atts);
    if (!$product_id) return '';

    $overlay_id = 'bdmReviewModal_' . $product_id . '_' . wp_generate_password(6, false, false);

    ob_start(); ?>
      <button class="bdm-review-cta-btn <?php echo esc_attr($atts['button_class']); ?>"
              data-target="<?php echo esc_attr($overlay_id); ?>">
        <?php echo esc_html($atts['label']); ?>
      </button>

      <div id="<?php echo esc_attr($overlay_id); ?>" class="bdm-review-modal-overlay" role="dialog" aria-modal="true">
        <div class="bdm-review-modal">
          <div class="bdm-review-modal-header">
            <strong><?php echo esc_html__('Write your review', 'bandieredelmondo-review'); ?></strong>
            <button class="bdm-review-modal-close" aria-label="Close">×</button>
          </div>

          <form class="bdm-review-form">
            <input type="hidden" name="action" value="bdm_submit_review" />
            <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce('bdm_review_nonce')); ?>" />
            <input type="hidden" name="product_id" value="<?php echo esc_attr($product_id); ?>" />
            <input type="hidden" name="rating" value="0" />

            <div class="bdm-review-field">
              <label><?php echo esc_html__('Name', 'bandieredelmondo-review'); ?></label>
              <input type="text" name="name" required maxlength="120" />
            </div>

            <div class="bdm-review-field">
              <label><?php echo esc_html__('Email', 'bandieredelmondo-review'); ?></label>
              <input type="email" name="email" required maxlength="190" />
            </div>

            <div class="bdm-review-field">
              <label><?php echo esc_html__('Rating', 'bandieredelmondo-review'); ?></label>
              <div class="bdm-stars" aria-label="Rating">
                <span class="bdm-star" data-star="1">★</span>
                <span class="bdm-star" data-star="2">★</span>
                <span class="bdm-star" data-star="3">★</span>
                <span class="bdm-star" data-star="4">★</span>
                <span class="bdm-star" data-star="5">★</span>
              </div>
            </div>

            <div class="bdm-review-field">
              <label><?php echo esc_html__('Comment', 'bandieredelmondo-review'); ?></label>
              <textarea name="comment" required maxlength="5000"></textarea>
            </div>

            <button class="bdm-review-submit" data-label="<?php echo esc_attr__('Submit review', 'bandieredelmondo-review'); ?>">
              <?php echo esc_html__('Submit review', 'bandieredelmondo-review'); ?>
            </button>

            <div class="bdm-review-msg" aria-live="polite"></div>
          </form>

          <p style="margin:10px 0 0; font-size:12px; opacity:.8;">
            <?php echo esc_html__('After submitting, you will receive an email to confirm your review.', 'bandieredelmondo-review'); ?>
          </p>
        </div>
      </div>
    <?php
    return ob_get_clean();
  }

  public static function shortcode_list($atts) {
    $atts = shortcode_atts([
      'product_id' => '',
      'limit' => 20,
      'show_photos' => 1,
    ], $atts);

    $product_id = self::get_product_id_from_context($atts);
    if (!$product_id) return '';

    $limit = max(1, min(200, absint($atts['limit'])));

    $q = new WP_Query([
      'post_type' => BDM_Review_CPT::CPT,
      'post_status' => 'publish', // approved
      'posts_per_page' => $limit,
      'orderby' => 'date',
      'order' => 'DESC',
      'meta_query' => [
        [
          'key' => '_bdm_product_id',
          'value' => $product_id,
          'compare' => '=',
          'type' => 'NUMERIC'
        ]
      ],
    ]);

    if (!$q->have_posts()) {
      return '<div class="bdm-review-list"><em>' . esc_html__('No reviews yet.', 'bandieredelmondo-review') . '</em></div>';
    }

    ob_start();
    echo '<div class="bdm-review-list">';
    while ($q->have_posts()) {
      $q->the_post();
      $rid = get_the_ID();
      $name = BDM_Review_CPT::meta($rid, '_bdm_name', __('Anonymous', 'bandieredelmondo-review'));
      $rating = (int) BDM_Review_CPT::meta($rid, '_bdm_rating', 0);
      $comment = BDM_Review_CPT::meta($rid, '_bdm_comment', '');
      $cert = (int) BDM_Review_CPT::meta($rid, '_bdm_certified', 0);

      $profile_photo_id = absint(BDM_Review_CPT::meta($rid, '_bdm_profile_photo_id', 0));
      $product_photo_id = absint(BDM_Review_CPT::meta($rid, '_bdm_product_photo_id', 0));

      echo '<div class="bdm-review-item">';
        echo '<div class="bdm-review-meta">';
          echo '<div><strong>' . esc_html($name) . '</strong><br/><small>' . esc_html(get_the_date()) . '</small></div>';
          echo '<div class="bdm-review-badges">';
            echo '<span class="bdm-badge">' . esc_html(str_repeat('★', max(0, min(5, $rating)))) . '</span>';
            if ($cert) echo '<span class="bdm-badge cert">' . esc_html__('Certified purchase', 'bandieredelmondo-review') . '</span>';
          echo '</div>';
        echo '</div>';

        echo '<div>' . wp_kses_post(nl2br(esc_html($comment))) . '</div>';

        if (!empty($atts['show_photos'])) {
          $imgs = [];
          if ($profile_photo_id) $imgs[] = wp_get_attachment_image($profile_photo_id, 'thumbnail');
          if ($product_photo_id) $imgs[] = wp_get_attachment_image($product_photo_id, 'thumbnail');
          if ($imgs) {
            echo '<div class="bdm-review-photos">' . implode('', $imgs) . '</div>';
          }
        }
      echo '</div>';
    }
    wp_reset_postdata();
    echo '</div>';

    return ob_get_clean();
  }
}

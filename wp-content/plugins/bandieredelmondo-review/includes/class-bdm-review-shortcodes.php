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
      'label' => __('Lascia una recensione', 'bandieredelmondo-review'),
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
            <strong><?php echo esc_html__('Scrivi la tua recensione', 'bandieredelmondo-review'); ?></strong>
            <button class="bdm-review-modal-close" aria-label="Close">×</button>
          </div>

          <form class="bdm-review-form">
            <input type="hidden" name="action" value="bdm_submit_review" />
            <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce('bdm_review_nonce')); ?>" />
            <input type="hidden" name="product_id" value="<?php echo esc_attr($product_id); ?>" />
            <input type="hidden" name="rating" value="0" />

            <div class="bdm-review-field">
              <label><?php echo esc_html__('Nome', 'bandieredelmondo-review'); ?></label>
              <input type="text" name="name" required maxlength="120" />
            </div>

            <div class="bdm-review-field">
              <label><?php echo esc_html__('Email', 'bandieredelmondo-review'); ?></label>
              <input type="email" name="email" required maxlength="190" />
            </div>

            <div class="bdm-review-field">
              <label><?php echo esc_html__('Voto', 'bandieredelmondo-review'); ?></label>
              <div class="bdm-stars" aria-label="Voto">
                <span class="bdm-star" data-star="1">★</span>
                <span class="bdm-star" data-star="2">★</span>
                <span class="bdm-star" data-star="3">★</span>
                <span class="bdm-star" data-star="4">★</span>
                <span class="bdm-star" data-star="5">★</span>
              </div>
            </div>

            <div class="bdm-review-field">
              <label><?php echo esc_html__('Commento', 'bandieredelmondo-review'); ?></label>
              <textarea name="comment" required maxlength="5000"></textarea>
            </div>

            <button class="bdm-review-submit" data-label="<?php echo esc_attr__('Submit review', 'bandieredelmondo-review'); ?>">
              <?php echo esc_html__('Conferma la recensione', 'bandieredelmondo-review'); ?>
            </button>

            <div class="bdm-review-msg" aria-live="polite"></div>
          </form>

          <p style="margin:10px 0 0; font-size:12px; opacity:.8;">
            <?php echo esc_html__('Dopo la submission, riceverai una email per confermare la recensione.', 'bandieredelmondo-review'); ?>
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
      return '<div class="bdm-reviews"><div class="bdm-reviews-empty">' . esc_html__('No reviews yet.', 'bandieredelmondo-review') . '</div></div>';
    }

    // Helpers
    $render_stars = function(int $rating) {
      $rating = max(0, min(5, $rating));
      $out = '<div class="bdm-stars-inline" aria-label="' . esc_attr($rating) . ' out of 5">';
      for ($i = 1; $i <= 5; $i++) {
        $out .= '<span class="bdm-star-inline ' . ($i <= $rating ? 'is-on' : 'is-off') . '">★</span>';
      }
      $out .= '</div>';
      return $out;
    };

    ob_start();

    echo '<div class="bdm-reviews">';
    echo '<div class="bdm-reviews-header">';
    echo '<h3 class="bdm-reviews-title">' . esc_html__('Customer reviews', 'bandieredelmondo-review') . '</h3>';
    echo '</div>';

    echo '<div class="bdm-reviews-list">';

    while ($q->have_posts()) {
      $q->the_post();
      $rid = get_the_ID();
    
      $name    = BDM_Review_CPT::meta($rid, '_bdm_name', __('Anonymous', 'bandieredelmondo-review'));
      $rating  = (int) BDM_Review_CPT::meta($rid, '_bdm_rating', 0);
      $comment = BDM_Review_CPT::meta($rid, '_bdm_comment', '');
      $cert    = (int) BDM_Review_CPT::meta($rid, '_bdm_certified', 0);
    
      $profile_photo_id = absint(BDM_Review_CPT::meta($rid, '_bdm_profile_photo_id', 0));
      $product_photo_id = absint(BDM_Review_CPT::meta($rid, '_bdm_product_photo_id', 0));
    
      $date_iso   = get_the_date('c', $rid);
      $date_human = get_the_date(get_option('date_format'), $rid);
    
      $has_photos = ($profile_photo_id || $product_photo_id);
    
      // Stars (Upwork-like)
      $stars_html = '<span class="bdm-up-stars" aria-label="' . esc_attr($rating) . ' out of 5">';
      for ($i=1; $i<=5; $i++) {
        $stars_html .= '<span class="bdm-up-star ' . ($i <= $rating ? 'is-on' : 'is-off') . '">★</span>';
      }
      $stars_html .= '</span>';
    
      echo '<article class="bdm-up-card">';
    
        // Avatar (profile photo or initials)
        $initials = '';
        $parts = preg_split('/\s+/', trim($name));
        if (!empty($parts[0])) $initials .= mb_strtoupper(mb_substr($parts[0], 0, 1));
        if (count($parts) > 1 && !empty($parts[count($parts)-1])) {
          $initials .= mb_strtoupper(mb_substr($parts[count($parts)-1], 0, 1));
        }
        $initials = $initials ?: 'U';

        if ($profile_photo_id) {
          $avatar_html = wp_get_attachment_image(
            $profile_photo_id,
            'thumbnail',
            false,
            [
              'class' => 'bdm-up-avatar-img',
              'loading' => 'lazy',
              'alt' => esc_attr($name),
            ]
          );
        } else {
          $avatar_html = '<div class="bdm-up-avatar-fallback">' . esc_html($initials) . '</div>';
        }

        echo '<div class="bdm-up-card-header">';
          echo '<div class="bdm-up-avatar">' . $avatar_html . '</div>';
          echo '<div class="bdm-up-title-wrap">';
            echo '<div class="bdm-up-title">' . esc_html($name) . '</div>';
          echo '</div>';
        echo '</div>';

    
        // Meta line
        echo '<div class="bdm-up-meta">';
          echo '<time datetime="' . esc_attr($date_iso) . '">' . esc_html($date_human) . '</time>';
          echo '<span class="bdm-up-dot">•</span>';
          echo $cert
            ? '<span class="bdm-up-verified">✔ ' . esc_html__('Certified purchase', 'bandieredelmondo-review') . '</span>'
            : '<span class="bdm-up-muted">' . esc_html__('Not certified', 'bandieredelmondo-review') . '</span>';
        echo '</div>';
    
        // Body
        echo '<div class="bdm-up-body">';
          echo wp_kses_post(nl2br(esc_html($comment)));
        echo '</div>';
    
        // Photos row (clean thumbnails)
        if (!empty($atts['show_photos'])) {
          $photo_ids = [];
          if ($product_photo_id) $photo_ids[] = $product_photo_id;
    
          if ($photo_ids) {
            echo '<div class="bdm-up-photos">';
            foreach ($photo_ids as $aid) {
              echo '<a class="bdm-up-photo" href="' . esc_url(wp_get_attachment_url($aid)) . '" target="_blank" rel="noopener">';
              echo wp_get_attachment_image($aid, 'thumbnail', false, [
                'loading' => 'lazy',
                'alt' => esc_attr__('Review photo', 'bandieredelmondo-review')
              ]);
              echo '</a>';
            }
            echo '</div>';
          }
        }
    
        // Bottom stats row (like screenshot)
        echo '<div class="bdm-up-stats">';
          echo '<div class="bdm-up-stat">' . $stars_html . '</div>';
        echo '</div>';
    
      echo '</article>';
    }
    

    wp_reset_postdata();

    echo '</div>'; // list
    echo '</div>'; // wrapper

    return ob_get_clean();
  }

}

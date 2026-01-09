<?php
if (!defined('WP_UNINSTALL_PLUGIN')) exit;

// If you want full cleanup, uncomment this block.
// Warning: This deletes ALL plugin review data and attachments references.
//
// $reviews = get_posts([
//   'post_type' => 'bdm_review',
//   'post_status' => 'any',
//   'numberposts' => -1,
//   'fields' => 'ids',
// ]);
// foreach ($reviews as $rid) {
//   wp_delete_post($rid, true);
// }
//
// delete_option('bdm_review_output_schema');

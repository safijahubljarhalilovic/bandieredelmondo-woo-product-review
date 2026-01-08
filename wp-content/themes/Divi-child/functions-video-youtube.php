<?

function video_youtube_acf_shortcode() {
    ob_start();

    $url = get_field('video_youtube');
    if (!$url) return '';

    // Estrai l'ID rimuovendo "https://youtu.be/"
    $video_id = str_replace('https://youtu.be/', '', $url);

    echo '<div class="acf-youtube-container">';
    echo '<iframe src="https://www.youtube.com/embed/' . esc_attr($video_id) . '?rel=0&modestbranding=1&controls=1" width="560" height="315" frameborder="0" allowfullscreen></iframe>';
    echo '</div>';

    return ob_get_clean();
}

add_shortcode('video_youtube_acf', 'video_youtube_acf_shortcode');

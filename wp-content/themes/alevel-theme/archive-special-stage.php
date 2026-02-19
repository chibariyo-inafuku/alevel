<?php get_template_part('parts/header'); ?>


    <main class="main bg-gray">
        <section class="page-header page-header--elabelleTv">
            <img src="<?php echo get_template_directory_uri() ?>/assets/img/lazy-load/pageHeader2.webp" alt="出展社一覧" loadin g="eager" class="page-header__bgImage">
            <div class="page-header__content">
                <div class="container">
                    <div class="page-header__heading">
                        <h2 class="page-header__title--lg">Alevel TV</h2>
                        <h3 class="page-header__title"> エラベルTV </h2>
                    </div>
                </div>
            </div>
        </section>
        <?php
/**
 * MOVIE スライダー（alevel_tv を Swiper + モーダルで表示）
 */

$movie_q = new WP_Query([
  'post_type'      => 'special-stage',
  'posts_per_page' => 12,  // 必要に応じて
  'post_status'    => 'publish',
  'orderby'        => 'date',
  'order'          => 'DESC',
]);

if ( ! $movie_q->have_posts() ) return;

/**
 * YouTube URL → embed URL（watch / youtu.be / shorts / embed ざっくり対応）
 */
if ( ! function_exists('alevel_tv_to_youtube_embed_url') ) {
  function alevel_tv_to_youtube_embed_url($url) {
    $url = trim((string)$url);
    if ($url === '') return '';

    $parts = wp_parse_url($url);
    if (empty($parts['host'])) return '';

    $host  = $parts['host'];
    $path  = $parts['path'] ?? '';
    $query = [];
    if (!empty($parts['query'])) parse_str($parts['query'], $query);

    $video_id = '';

    // youtu.be/xxxx
    if (strpos($host, 'youtu.be') !== false) {
      $video_id = ltrim($path, '/');
    }
    // youtube.com/watch?v=xxxx
    if (!$video_id && strpos($host, 'youtube.com') !== false && isset($query['v'])) {
      $video_id = (string)$query['v'];
    }
    // youtube.com/shorts/xxxx
    if (!$video_id && strpos($host, 'youtube.com') !== false && preg_match('~^/shorts/([^/?]+)~', $path, $m)) {
      $video_id = $m[1];
    }
    // youtube.com/embed/xxxx
    if (!$video_id && strpos($host, 'youtube.com') !== false && preg_match('~^/embed/([^/?]+)~', $path, $m)) {
      $video_id = $m[1];
    }

    if (!$video_id) return '';

    // autoplay/mute など（必要に応じて調整）
    $params = [
      'enablejsapi'  => '1',  // お好み（停止制御したいなら）
      'autoplay'     => '1',
      'mute'         => '1',
      'playsinline'  => '1',
      'rel'          => '0',
    ];

    return 'https://www.youtube.com/embed/' . rawurlencode($video_id) . '?' . http_build_query($params);
  }
}
?>

<section class="movie-slides">
  <div class="swiper" id="movie-slides">
    <div class="swiper-wrapper">
      <?php while ( $movie_q->have_posts() ) : $movie_q->the_post(); ?>
        <?php
          $post_id  = get_the_ID();
          $modal_id = 'movie-' . $post_id;

          // ACF oEmbed（raw値）
          $movie_url_raw = get_field('alevel_tv_movie', $post_id, false); // :contentReference[oaicite:1]{index=1}
          $embed_url     = alevel_tv_to_youtube_embed_url($movie_url_raw);

          // サムネ（アイキャッチ）
          $thumb_url = get_the_post_thumbnail_url($post_id, 'large');
          if (!$thumb_url) {
            $thumb_url = get_template_directory_uri() . '/assets/img/lazy-load/movie-thumbnail1.webp';
          }

          // 説明文
          if (has_excerpt($post_id)) {
            $desc = get_the_excerpt($post_id);
          } else {
            $desc = wp_trim_words( wp_strip_all_tags(get_the_content(null, false, $post_id)), 40, '…' );
          }

          // 動画URLがない投稿はスライドを出さない（運用的に安全）
          if (!$embed_url) continue;
        ?>

        <div class="swiper-slide">
          <div class="movie-card movie-card__overlay js-modaltrigger" attr-modal="<?php echo esc_attr($modal_id); ?>">
            <div class="movie-card__thumbnail">
              <img
                src="<?php echo esc_url($thumb_url); ?>"
                alt="<?php echo esc_attr(get_the_title($post_id)); ?>"
                width="640" height="334"
                loading="lazy"
              >
              <button class="movie-card__play-btn" type="button" aria-label="動画を再生">
                <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/lazy-load/play.svg'); ?>" alt="">
              </button>
            </div>

            <div class="movie-card__body">
              <h3 class="movie-card__title"><?php echo esc_html(get_the_title($post_id)); ?></h3>
              <p class="movie-card__desc"><?php echo esc_html($desc); ?></p>
            </div>
          </div>
        </div>

      <?php endwhile; wp_reset_postdata(); ?>
    </div>

    <div class="swiper-pagination"></div>
  </div>

  <!-- ===== モーダル群（スライドに対応）===== -->
  <div class="modal-container">
    <?php
      $movie_q->rewind_posts();
      while ( $movie_q->have_posts() ) : $movie_q->the_post();
        $post_id  = get_the_ID();
        $modal_id = 'movie-' . $post_id;

        $movie_url_raw = get_field('alevel_tv_movie', $post_id, false);
        $embed_url     = alevel_tv_to_youtube_embed_url($movie_url_raw);
        if (!$embed_url) continue;
    ?>
      <div class="modal js-modal" attr-modal="<?php echo esc_attr($modal_id); ?>" aria-hidden="true">
        <div class="modal-dialog modal-dialog--video modal-dialog--centered" role="dialog" aria-modal="true">
          <div class="modal-content">
            <div class="modal-content__video">
              <iframe
                width="1200" height="690"
                class="yt-video"
                data-src="<?php echo esc_url($embed_url); ?>"
                title="<?php echo esc_attr(get_the_title($post_id)); ?>"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                referrerpolicy="strict-origin-when-cross-origin"
                allowfullscreen
              ></iframe>
            </div>
          </div>
        </div>
        <button class="modal__btn-close js-closebtn" type="button" aria-label="閉じる"></button>
      </div>
    <?php endwhile; wp_reset_postdata(); ?>
  </div>
</section>


    </main>

    <?php get_template_part('parts/footer'); ?>
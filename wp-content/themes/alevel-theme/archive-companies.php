<?php get_template_part('parts/header'); ?>

<?php
// ==============================
// GET（1回だけ）
// ==============================
$type    = isset($_GET['type']) ? sanitize_text_field($_GET['type']) : 'all'; // all | paid | free
$keyword = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

$pref_in = isset($_GET['pref']) ? (array) $_GET['pref'] : [];
$job_in  = isset($_GET['job'])  ? (array) $_GET['job']  : [];

$pref_in = array_values(array_filter(array_map('sanitize_text_field', $pref_in)));
$job_in  = array_values(array_filter(array_map('sanitize_text_field', $job_in)));

// tax_query
$tax_query = ['relation' => 'AND'];
if (!empty($pref_in)) {
  $tax_query[] = [
    'taxonomy' => 'company_pref',
    'field'    => 'slug',
    'terms'    => $pref_in,
    'operator' => 'IN',
  ];
}
if (!empty($job_in)) {
  $tax_query[] = [
    'taxonomy' => 'company_jobtype',
    'field'    => 'slug',
    'terms'    => $job_in,
    'operator' => 'IN',
  ];
}
$has_tax = (count($tax_query) > 1);

// チェックボックス候補（管理画面のタームから生成）
$pref_terms = get_terms(['taxonomy' => 'company_pref',    'hide_empty' => false]);
$job_terms  = get_terms(['taxonomy' => 'company_jobtype', 'hide_empty' => false]);

// フォーム送信先
// ※companies の has_archive が無い場合 get_post_type_archive_link が空になるため保険
$action_url = get_post_type_archive_link('companies');
if (!$action_url) {
  // 同ページにGET送信（アーカイブ無しでも動く）
  $action_url = get_permalink();
}
?>

<main class="main bg-gray exhibitors-list">

  <section class="page-header">
    <img
      src="<?php echo get_template_directory_uri() ?>/assets/img/lazy-load/pageHeader.webp"
      alt="出展社一覧"
      loading="eager"
      class="page-header__bgImage"
    >
    <div class="page-header__content">
      <div class="container">
        <div class="page-header__heading">
          <h2 class="page-header__title--lg">COMPANY LIST</h2>
          <h3 class="page-header__title">出展社一覧</h3>
        </div>
        <p class="page-header__lead">カテゴリ・キーワードから<br class="only-sp">出展企業を探すことができます。</p>
      </div>
    </div>
  </section>

  <section class="">
    <div class="container">
      <div class="search -n-mt -mb-0">
        <div class="search-main">
          <div class="search-main__header">
            <h2 class="primary-heading ">
              SEARCH <span class="primary-heading__jp">出展社検索</span>
            </h2>
          </div>

          <div class="search-main__body">
            <div class="search-main__searchform" style="width: 100%;">
              <form action="<?php echo esc_url($action_url); ?>" method="get" class="search-form">

                <!-- 優良/無料 絞り込み -->
                <div class="search-form__group" style="display: none;">
                  <p class="search-form__title">掲載区分</p>
                  <div class="search-form__checkbox-wrap">
                    <label class="search-form__checkbox">
                      <input type="radio" class="search-form__checkbox-input" name="type" value="all" <?php checked($type, 'all'); ?>>
                      <span class="search-form__checkbox-label">すべて</span>
                    </label>
                    <label class="search-form__checkbox">
                      <input type="radio" class="search-form__checkbox-input" name="type" value="paid" <?php checked($type, 'paid'); ?>>
                      <span class="search-form__checkbox-label">優良企業</span>
                    </label>
                    <label class="search-form__checkbox">
                      <input type="radio" class="search-form__checkbox-input" name="type" value="free" <?php checked($type, 'free'); ?>>
                      <span class="search-form__checkbox-label">無料企業</span>
                    </label>
                  </div>
                </div>

                <!-- キーワード -->
                <div class="search-form__group">
                  <p class="search-form__title">キーワードで探す</p>
                  <input type="text" class="search-form__field" name="s" value="<?php echo esc_attr($keyword); ?>" placeholder="キーワード">
                </div>

                <!-- 都道府県 -->
                <div class="search-form__group">
                  <p class="search-form__title">場所から探す</p>
                  <div class="search-form__checkbox-wrap">
                    <?php if (!empty($pref_terms) && !is_wp_error($pref_terms)) : ?>
                      <?php foreach ($pref_terms as $t) : ?>
                        <label class="search-form__checkbox">
                          <input
                            type="checkbox"
                            class="search-form__checkbox-input"
                            name="pref[]"
                            value="<?php echo esc_attr($t->slug); ?>"
                            <?php checked(in_array($t->slug, $pref_in, true)); ?>
                          >
                          <span class="search-form__checkbox-label"><?php echo esc_html($t->name); ?></span>
                        </label>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </div>
                </div>

                <!-- 業種 -->
                <div class="search-form__group">
                  <p class="search-form__title">職種から探す</p>
                  <div class="search-form__checkbox-wrap">
                    <?php if (!empty($job_terms) && !is_wp_error($job_terms)) : ?>
                      <?php foreach ($job_terms as $t) : ?>
                        <label class="search-form__checkbox">
                          <input
                            type="checkbox"
                            class="search-form__checkbox-input"
                            name="job[]"
                            value="<?php echo esc_attr($t->slug); ?>"
                            <?php checked(in_array($t->slug, $job_in, true)); ?>
                          >
                          <span class="search-form__checkbox-label"><?php echo esc_html($t->name); ?></span>
                        </label>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </div>
                </div>

                <button type="submit" class="btn btn-primary -sm">
                  検索する
                  <span class="btn__icon">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                      <path d="M13 6.5C13 7.93438 12.5344 9.25938 11.75 10.3344L15.7063 14.2937C16.0969 14.6844 16.0969 15.3188 15.7063 15.7094C15.3156 16.1 14.6812 16.1 14.2906 15.7094L10.3344 11.75C9.25938 12.5375 7.93438 13 6.5 13C2.90937 13 0 10.0906 0 6.5C0 2.90937 2.90937 0 6.5 0C10.0906 0 13 2.90937 13 6.5ZM6.5 11C7.09095 11 7.67611 10.8836 8.22208 10.6575C8.76804 10.4313 9.26412 10.0998 9.68198 9.68198C10.0998 9.26412 10.4313 8.76804 10.6575 8.22208C10.8836 7.67611 11 7.09095 11 6.5C11 5.90905 10.8836 5.32389 10.6575 4.77792C10.4313 4.23196 10.0998 3.73588 9.68198 3.31802C9.26412 2.90016 8.76804 2.56869 8.22208 2.34254C7.67611 2.1164 7.09095 2 6.5 2C5.90905 2 5.32389 2.1164 4.77792 2.34254C4.23196 2.56869 3.73588 2.90016 3.31802 3.31802C2.90016 3.73588 2.56869 4.23196 2.34254 4.77792C2.1164 5.32389 2 5.90905 2 6.5C2 7.09095 2.1164 7.67611 2.34254 8.22208C2.56869 8.76804 2.90016 9.26412 3.31802 9.68198C3.73588 10.0998 4.23196 10.4313 4.77792 10.6575C5.32389 10.8836 5.90905 11 6.5 11Z" fill="currentColor" />
                    </svg>
                  </span>
                </button>

              </form>
            </div>
          </div>

        </div>
      </div>
    </div>
  </section>

  <?php if ($type === 'all' || $type === 'paid') : ?>
    <?php
    $paid_args = [
  'post_type'        => 'companies',
  'post_status'      => 'publish',
  's'                => $keyword,
  'my_acf_search_on' => 1,
  'posts_per_page'   => 999,
  'orderby'          => 'rand',
];

    if ($has_tax) $paid_args['tax_query'] = $tax_query;

    $paid_q = new WP_Query($paid_args);
    ?>

    <section class="company-list">
      <div class="container container--sm">
        <div class="company-grid company-grid--col-3">

          <?php if ($paid_q->have_posts()) : ?>
            <?php while ($paid_q->have_posts()) : $paid_q->the_post(); ?>
              <?php
              $post_id = get_the_ID();

              $pref_terms_post = get_the_terms($post_id, 'company_pref');
              $pref_name = (!empty($pref_terms_post) && !is_wp_error($pref_terms_post)) ? $pref_terms_post[0]->name : '';

              $pref_color_map = ['mie'=>'-green','gifu'=>'-purple','aichi'=>'-magenta','shizuoka'=>'-blue'];
              $tag_class = '';
              if (!empty($pref_terms_post) && !is_wp_error($pref_terms_post)) {
                foreach ($pref_terms_post as $t) {
                  if (!empty($pref_color_map[$t->slug])) { $tag_class = $pref_color_map[$t->slug]; break; }
                }
              }

              $job_terms_post = get_the_terms($post_id, 'company_jobtype');
              $job_names = [];
              if (!empty($job_terms_post) && !is_wp_error($job_terms_post)) {
                foreach ($job_terms_post as $t) $job_names[] = $t->name;
              }
              $job_text = !empty($job_names) ? implode('、', $job_names) : '';

              $logo = get_the_post_thumbnail_url($post_id, 'medium');
              if (!$logo) $logo = get_template_directory_uri() . '/assets/img/lazy-load/comapny-logo.webp';
              ?>

              <div class="company-grid__col">
                <a href="<?php the_permalink(); ?>" class="company-card company-card--lg">
                  <?php if ($pref_name !== '') : ?>
                    <span class="company-card__tag tag <?php echo esc_attr($tag_class); ?>">
                      <span class="tag__dot"></span> <?php echo esc_html($pref_name); ?>
                    </span>
                  <?php endif; ?>

                  <h3 class="company-card__title"><?php the_title(); ?></h3>

                  <div class="company-card__logo">
                    <img src="<?php echo esc_url($logo); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy">
                  </div>

                  <?php if ($job_text !== '') : ?>
                    <div class="company-card__compType">
                      <span class="company-card__compType-label">業種</span><?php echo esc_html($job_text); ?>
                    </div>
                  <?php endif; ?>

                  <p class="company-card__desc"><?php echo esc_html(wp_strip_all_tags(get_the_excerpt())); ?></p>

                  <button class="btn btn-primary -sm" type="button">
                    企業ブースへ
                    <span class="btn__icon btn__icon--arrow"></span>
                  </button>
                </a>
              </div>

            <?php endwhile; ?>
          <?php else : ?>
            <p>該当する出展企業はありません。</p>
          <?php endif; ?>

        </div>
      </div>
    </section>

    <?php wp_reset_postdata(); ?>
  <?php endif; ?>


  <?php if ($type === 'all' || $type === 'free') : ?>
    <?php
    $free_args = [
  'post_type'        => 'f_companies',
  'post_status'      => 'publish',
  's'                => $keyword,
  'my_acf_search_on' => 1,
  'posts_per_page'   => 999,
  'orderby'          => 'rand',
];

    if ($has_tax) $free_args['tax_query'] = $tax_query;

    $free_q = new WP_Query($free_args);
    ?>

    <section class="other-companies section">
      <div class="container container--sm">
        <h2 class="other-companies__heading">その他にも、本誌には<br class="only-sp">多数企業を掲載しています。</h2>

        <div class="company-grid company-grid--col-4">
          <?php if ($free_q->have_posts()) : ?>
            <?php while ($free_q->have_posts()) : $free_q->the_post(); ?>
              <?php
              $post_id = get_the_ID();

              $pref_terms_post = get_the_terms($post_id, 'company_pref');
              $pref_name = (!empty($pref_terms_post) && !is_wp_error($pref_terms_post)) ? $pref_terms_post[0]->name : '';

              $pref_color_map = ['mie'=>'-green','gifu'=>'-purple','aichi'=>'-magenta','shizuoka'=>'-blue'];
              $tag_class = '';
              if (!empty($pref_terms_post) && !is_wp_error($pref_terms_post)) {
                foreach ($pref_terms_post as $t) {
                  if (!empty($pref_color_map[$t->slug])) { $tag_class = $pref_color_map[$t->slug]; break; }
                }
              }

              $job_terms_post = get_the_terms($post_id, 'company_jobtype');
              $job_names = [];
              if (!empty($job_terms_post) && !is_wp_error($job_terms_post)) {
                foreach ($job_terms_post as $t) $job_names[] = $t->name;
              }
              $job_text = !empty($job_names) ? implode('、', $job_names) : '';
              ?>

              <div class="company-grid__col">
                <div class="company-card company-card--other">

                  <?php if ($pref_name !== '') : ?>
                    <span class="company-card__tag tag <?php echo esc_attr($tag_class); ?>">
                      <span class="tag__dot"></span> <?php echo esc_html($pref_name); ?>
                    </span>
                  <?php endif; ?>

                  <h2 class="company-card__logo company-card__logo--name"><?php the_title(); ?></h2>

                  <?php if ($job_text !== '') : ?>
                    <div class="company-card__compType">
                      <span class="company-card__compType-label">業種</span><?php echo esc_html($job_text); ?>
                    </div>
                  <?php endif; ?>

                </div>
              </div>

            <?php endwhile; ?>
          <?php else : ?>
            <p>該当する掲載企業はありません。</p>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <?php wp_reset_postdata(); ?>
  <?php endif; ?>

</main>

<?php get_template_part('parts/footer'); ?>

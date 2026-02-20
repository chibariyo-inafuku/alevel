<?php get_template_part('parts/header'); ?>
<?php
$post_id = get_the_ID();

/* ==============================
   都道府県（company_pref）
============================== */
$pref_terms_post = get_the_terms($post_id, 'company_pref');
$pref_name = (!empty($pref_terms_post) && !is_wp_error($pref_terms_post)) ? $pref_terms_post[0]->name : '';

// アーカイブと同じカラー対応
$pref_color_map = [
  'mie'      => '-green',
  'gifu'     => '-purple',
  'aichi'    => '-magenta',
  'shizuoka' => '-blue'
];

$tag_class = '';
if (!empty($pref_terms_post) && !is_wp_error($pref_terms_post)) {
  foreach ($pref_terms_post as $t) {
    if (!empty($pref_color_map[$t->slug])) {
      $tag_class = $pref_color_map[$t->slug];
      break;
    }
  }
}

/* ==============================
   業種（company_jobtype）
============================== */
$job_terms_post = get_the_terms($post_id, 'company_jobtype');
$job_names = [];

if (!empty($job_terms_post) && !is_wp_error($job_terms_post)) {
  foreach ($job_terms_post as $t) {
    $job_names[] = $t->name;
  }
}

$job_text = !empty($job_names) ? implode('、', $job_names) : '';
?>
<?PHP
// 採用DATA：画像（モーダルとリンクで共通利用）
$company_rec_toggle = get_field('company_rec'); // company_rec-show/hidden
$rec_img = '';

if ( $company_rec_toggle === 'company_rec-show' ) {
  $company_rec_group = get_field('company_rec-group'); // group
  if ( ! empty($company_rec_group['company_rec-img']) ) {
    $rec_img = $company_rec_group['company_rec-img']; // url
  }
}

// 社長インタビュー画像（リンクとモーダル共通）
$company_president_toggle = get_field('company_president');
$pres_img = '';

if ( $company_president_toggle === 'company_president-show' ) {
  $company_president_group = get_field('company_president-group');
  if ( ! empty($company_president_group['company_president-img']) ) {
    $pres_img = $company_president_group['company_president-img']; // url
  }
}
?>
<?php
/**
 * companies 詳細：モックHTML + ACF差し込み
 * 今回差し込むACF：
 * - 企業情報 company_info / company_info-group
 * - 事業内容 company_biz / company_biz-lead / company_biz-group(repeater)
 * - 採用DATA company_rec / company_rec-group
 * - 社長インタビュー company_president / company_president-group
 * - カタログ company_catalog / company_catalog-group(repeater)
 * - コンタクト company_contact / company_contact-group
 * - 担当者 company_manager / company_manager-group(repeater)
 */

if ( ! function_exists('get_field') ) return;

// toggle
$company_info_toggle      = get_field('company_info');       // company_info-show/hidden
$company_biz_toggle       = get_field('company_biz');        // company_biz-show/hidden
$company_rec_toggle       = get_field('company_rec');        // company_rec-show/hidden
$company_pres_toggle      = get_field('company_president');  // company_president-show/hidden
$company_catalog_toggle   = get_field('company_catalog');    // company_catalog-show/hidden
$company_contact_toggle   = get_field('company_contact');    // company_contact-show/hidden
$company_manager_toggle   = get_field('company_manager');    // company_manager-show/hidden
// movie (oEmbed)
$company_movie_oembed = get_field('company_movie'); // ACF oEmbed（iframe HTML or URL）
$company_movie_src = '';

if ( ! empty($company_movie_oembed) ) {
  // oEmbedが iframe HTML を返すケース：srcを抜く
  if (is_string($company_movie_oembed) && strpos($company_movie_oembed, '<iframe') !== false) {
    if (preg_match('/src=["\']([^"\']+)["\']/', $company_movie_oembed, $m)) {
      $company_movie_src = $m[1];
    }
  } else {
    // まれにURLだけ返る設定の場合
    $company_movie_src = $company_movie_oembed;
  }

  // autoplay/mute等を付与（既存の data-src 運用を維持）
  if ( $company_movie_src ) {
    $join = (strpos($company_movie_src, '?') !== false) ? '&' : '?';
    $company_movie_src .= $join . 'autoplay=1&mute=1&playsinline=1&rel=0';
  }
}

// 動画があるか
$has_movie = ! empty($company_movie_src);
// groups
$company_info    = ($company_info_toggle === 'company_info-show') ? get_field('company_info-group') : null;
$company_contact = ($company_contact_toggle === 'company_contact-show') ? get_field('company_contact-group') : null;
$company_rec     = ($company_rec_toggle === 'company_rec-show') ? get_field('company_rec-group') : null;
$company_pres    = ($company_pres_toggle === 'company_president-show') ? get_field('company_president-group') : null;

// biz lead
$company_biz_lead = ($company_biz_toggle === 'company_biz-show') ? get_field('company_biz-lead') : '';

/* ==============================
   ▼ここだけ変更（要望どおり）
   - MENUは常に表示
   - MENU内の「カタログ」「コンタクト」だけtoggleで出し分け
============================== */
$show_catalog_menu = ($company_catalog_toggle === 'company_catalog-show');
$show_contact_menu = ($company_contact_toggle === 'company_contact-show');


// 4リンク（企業情報/事業内容/採用DATA/社長）も、セクションが無ければ隠す
$has_info = ($company_info_toggle === 'company_info-show') && ! empty($company_info);
$has_biz  = ($company_biz_toggle === 'company_biz-show') && ( ! empty($company_biz_lead) || have_rows('company_biz-group') );
$has_rec  = ($company_rec_toggle === 'company_rec-show') && ! empty($company_rec);
$has_pres = ($company_pres_toggle === 'company_president-show') && ! empty($company_pres);
?>

<main class="main">
  <div class="exhibitors-detail">
    <a href="<?php echo esc_url( get_post_type_archive_link('companies') ); ?>" class="exhibitors-detail__back">
      <span class="exhibitors-detail__back-icon"></span> 出展社一覧へ <br>戻る
    </a>

    <div class="exhibitors-detail__layout">
      <div class="exhibitors-detail__content">
        <div class="exhibitors-content">
          <div class="exhibitors-content__top only-pc db">

  <?php if ($pref_name !== '') : ?>
    <span class="tag <?php echo esc_attr($tag_class); ?>">
      <span class="tag__dot"></span>
      <?php echo esc_html($pref_name); ?>
    </span>
  <?php endif; ?>

  <?php if ($job_text !== '') : ?>
    <span class="type">
      <?php echo esc_html($job_text); ?>
    </span>
  <?php endif; ?>

</div>

<div class="exhibitors-content__logo only-pc db">
  <?php
  $link_url  = get_field('logo_url');
  $logo_img  = get_field('logo_img');
  $thumb_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
  $img_src   = $logo_img ?: $thumb_url;
  ?>

  <?php if ($link_url) : ?>
    <a href="<?php echo esc_url($link_url); ?>" target="_blank" rel="noopener">
  <?php endif; ?>

    <?php if ($img_src) : ?>
      <img src="<?php echo esc_url($img_src); ?>"
           alt="<?php echo esc_attr(get_the_title()); ?>"
           width="238"
           height="71"
           loading="lazy">
    <?php else : ?>
      <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/lazy-load/comapny-logo.webp"
           alt="<?php echo esc_attr(get_the_title()); ?>"
           width="238"
           height="71"
           loading="lazy">
    <?php endif; ?>

  <?php if ($link_url) : ?>
    </a>
  <?php endif; ?>
</div>

          <div class="exhibitors-content__links">
            <?php if ( $has_info ) : ?>
              <div class="exhibitors-content__link js-modaltrigger" attr-modal="company-profile">
                <div class="exhibitors-content__link-image">
  <?php if ( ! empty($company_info['company_info-img']) ) : ?>
    <img 
      src="<?php echo esc_url($company_info['company_info-img']); ?>" 
      alt="企業情報"
      width="120"
      height="85"
      loading="lazy"
    >
  <?php endif; ?>
</div>

                <div class="exhibitors-content__link-info">
                  <h3 class="exhibitors-content__link-title">企業情報</h3>
                  <span class="exhibitors-content__link-arrow"></span>
                </div>
              </div>
            <?php endif; ?>

            <?php if ( $has_biz ) : ?>
              <div class="exhibitors-content__link js-modaltrigger" attr-modal="business-content">
                <div class="exhibitors-content__link-image">
  <?php
  if ( get_field('company_biz') === 'company_biz-show' ) {
    $biz_rows = get_field('company_biz-group'); // repeaterを配列で取得
    $biz_img_1st = ( ! empty($biz_rows[0]['company_biz-img']) ) ? $biz_rows[0]['company_biz-img'] : '';

    if ( $biz_img_1st ) :
  ?>
      <img
        src="<?php echo esc_url($biz_img_1st); ?>"
        alt="事業内容"
        width="120"
        height="85"
        loading="lazy"
      >
  <?php
    endif;
  }
  ?>
</div>


                <div class="exhibitors-content__link-info">
                  <h3 class="exhibitors-content__link-title">事業内容</h3>
                  <span class="exhibitors-content__link-arrow"></span>
                </div>
              </div>
            <?php endif; ?>

            <?php if ( $has_rec ) : ?>
              <div class="exhibitors-content__link js-modaltrigger" attr-modal="recruitment-data">
                <div class="exhibitors-content__link-image">
  <?php if ( ! empty($rec_img) ) : ?>
    <img
      src="<?php echo esc_url($rec_img); ?>"
      alt="採用DATA"
      width="120"
      height="85"
      loading="lazy"
    >
  <?php endif; ?>
</div>

                <div class="exhibitors-content__link-info">
                  <h3 class="exhibitors-content__link-title">採用DATA</h3>
                  <span class="exhibitors-content__link-arrow"></span>
                </div>
              </div>
            <?php endif; ?>

            <?php if ( $has_pres ) : ?>
              <div class="exhibitors-content__link js-modaltrigger" attr-modal="president-msg">
                <div class="exhibitors-content__link-image">
  <?php if ( $pres_img ) : ?>
    <img
      src="<?php echo esc_url($pres_img); ?>"
      alt="社長メッセージ"
      width="120"
      height="85"
      loading="lazy"
    >
  <?php endif; ?>
</div>

                <div class="exhibitors-content__link-info">
                  <h3 class="exhibitors-content__link-title">社長メッセージ</h3>
                  <span class="exhibitors-content__link-arrow"></span>
                </div>
              </div>
            <?php endif; ?>
          </div>

          <div class="exhibitors-content__external-links only-sp">
            <!-- ▼ ここは今回のACF一覧に無いので仮のまま（URLが必要なら別フィールドで対応） -->
            <!--<a href="" class="exhibitors-content__external-link" target="_blank">出展社お問い合わせ</a>
            <a href="" class="exhibitors-content__external-link" target="_blank">出展社プライバシーポリシー</a>-->
          </div>
        </div>
      </div>

      <div class="exhibitors-detail__video">
        <div class="exhibitors-detail__layout-bg">
          <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/lazy-load/exhibitor-content-bg.webp" alt="">
        </div>

        <div class="exhibitors-detail__spHeader">
          <div class="exhibitors-detail__spHeader-top">

  <?php if ($pref_name !== '') : ?>
    <span class="tag <?php echo esc_attr($tag_class); ?>">
      <span class="tag__dot"></span>
      <?php echo esc_html($pref_name); ?>
    </span>
  <?php endif; ?>

  <?php if ($job_text !== '') : ?>
    <span class="type">
      <?php echo esc_html($job_text); ?>
    </span>
  <?php endif; ?>

</div>

          <div class="exhibitors-detail__spHeader-logo">
  <?php
  $link_url  = get_field('logo_url');
  $logo_img  = get_field('logo_img');
  $thumb_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
  $img_src   = $logo_img ?: $thumb_url;
  ?>

  <?php if ($link_url) : ?>
    <a href="<?php echo esc_url($link_url); ?>" target="_blank" rel="noopener">
  <?php endif; ?>

    <?php if ($img_src) : ?>
      <img src="<?php echo esc_url($img_src); ?>"
           alt="<?php echo esc_attr(get_the_title()); ?>"
           loading="lazy">
    <?php else : ?>
      <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/lazy-load/comapny-logo.webp"
           alt="<?php echo esc_attr(get_the_title()); ?>"
           loading="lazy">
    <?php endif; ?>

  <?php if ($link_url) : ?>
    </a>
  <?php endif; ?>
</div>
        </div>

        <?php
  // 念のため投稿IDを明示（singleテンプレでもズレない）
  $post_id = get_the_ID();

  // ACF 画像（url or array どちらでも吸収）
  $company_movie_thumb = get_field('company_movie_img', $post_id);

  // 余計な空白対策 + 配列対策
  if (is_array($company_movie_thumb)) {
    $company_movie_thumb = $company_movie_thumb['url'] ?? '';
  }
  $company_movie_thumb = trim((string)$company_movie_thumb);

  $thumb_src = $company_movie_thumb !== ''
    ? $company_movie_thumb
    : (get_template_directory_uri() . '/assets/img/lazy-load/exhibitor-video.jpg');
?>

<div class="exhibitors-video">
  <div
    class="exhibitors-video__cover <?php echo $has_movie ? 'js-modaltrigger' : ''; ?>"
    <?php if ( $has_movie ) : ?>attr-modal="exhibitor-video"<?php endif; ?>
  >
    <img
      src="<?php echo esc_url($thumb_src); ?>"
      alt=""
      width="388"
      height="216"
      loading="lazy"
    >

    <?php if ( $has_movie ) : ?>
      <button class="exhibitors-video__play-btn btn-play" type="button">
        <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/lazy-load/play.svg" alt="">
      </button>
    <?php endif; ?>

  </div>
</div>



				
				
      </div>

      <aside class="exhibitors-detail__aside">
  <div class="department">
    <?php
      // 念のため投稿IDを固定
      $post_id = get_the_ID();

      // 表示/非表示（radio: value）
      $company_manager = get_field('company_manager', $post_id);
    ?>

    <?php if ( $company_manager === 'company_manager-show' && have_rows('company_manager-group', $post_id) ) : ?>
      <?php while ( have_rows('company_manager-group', $post_id) ) : the_row();
        // 画像は return_format=url なので文字列URL
        $thumb = trim((string) get_sub_field('company_manager-img'));
        $dept  = trim((string) get_sub_field('company_manager-department'));
        $pos   = trim((string) get_sub_field('company_manager-post'));
        $name  = trim((string) get_sub_field('company_manager-name'));

        $fallback_thumb = get_template_directory_uri() . '/assets/img/lazy-load/dummy-image.webp';
        $thumb_src = $thumb !== '' ? $thumb : $fallback_thumb;
      ?>
        <div class="department__item">
          <div class="department__personImage">
            <img src="<?php echo esc_url($thumb_src); ?>" alt="">
          </div>

          <div class="department__info">
            <?php if ( $dept !== '' ) : ?>
              <h5 class="department__section"><?php echo esc_html($dept); ?></h5>
            <?php endif; ?>

            <?php if ( $pos !== '' ) : ?>
              <p class="department__position"><?php echo esc_html($pos); ?></p>
            <?php endif; ?>

            <?php if ( $name !== '' ) : ?>
              <p class="department__person">担当：<?php echo esc_html($name); ?></p>
            <?php endif; ?>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else : ?>
      <!-- company_manager が非表示 or 担当者行が0件の場合は何も出さない -->
      <!-- 固定モックを出したい場合は、ここに固定HTMLを置いてください -->
    <?php endif; ?>
  </div>
</aside>

    </div>

    <!-- ▼ここだけ変更：MENUは常に表示（中身だけtoggleで出し分け） -->
    <div class="exhibitors-detail__floatingMenu">
      <div class="exhibitors-detail__floatingMenu-btn">MENU <span class="arrow"></span></div>
      <ul class="exhibitors-detail__floatingMenu-list">
      <?php if ( $show_catalog_menu ) : ?>
          <li class="exhibitors-detail__floatingMenu-item " style="min-width: 65px">
						
            <a class="exhibitors-detail__floatingMenu-link js-modaltrigger" attr-modal="catalog">
              <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/lazy-load/icon_catalog_sp.webp" alt="" width="65" height="52">
              カタログ
            </a>
						 
          </li>
          <?php endif; ?>
       

        

        
          <li class="exhibitors-detail__floatingMenu-item" style="min-width: 65px">
						<?php if ( $show_contact_menu ) : ?>
            <a class="exhibitors-detail__floatingMenu-link js-modaltrigger" attr-modal="contact">
              <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/lazy-load/icon_enterprise_sp.webp" alt="" width="65" height="52">
              コンタクト
            </a>
						 <?php endif; ?>
          </li>
       
      </ul>
    </div>
    <!-- ▲ここだけ変更 -->

  </div>
</main>


<div class="modal-container">

  <?php if ( $has_info ) : ?>
    <div class="modal js-modal" attr-modal="company-profile">
      <div class="modal-dialog modal-dialog--centered">
        <div class="modal-content -p-60">
          <div class="modal-header">
            <h5 class="modal-title">企業情報 <span class="modal-title--eng">COMPANY PROFILE</span></h5>
          </div>
          <div class="modal-body">
            <div class="exhibitor-details exhibitor-details__companyProfile">

              <div class="exhibitor-details__companyProfile-featImage">
                <?php if ( ! empty($company_info['company_info-img']) ) : ?>
                  <img src="<?php echo esc_url($company_info['company_info-img']); ?>" alt="">
                <?php endif; ?>
              </div>

              <div class="exhibitor-details__companyProfile-desc">
                <?php if ( ! empty($company_info['company_info-title']) ) : ?>
                  <h2 class="exhibitor-details__title"><?php echo esc_html($company_info['company_info-title']); ?></h2>
                <?php endif; ?>

                <?php if ( ! empty($company_info['company_info-text']) ) : ?>
                  <p><?php echo nl2br(esc_html($company_info['company_info-text'])); ?></p>
                <?php endif; ?>
              </div>

              <?php if ( ! empty($company_info['company_info-data-img']) || ! empty($company_info['company_info-data-text']) ) : ?>
                <div class="exhibitor-details__data">
                  <div class="exhibitor-details__row">
                    <div class="exhibitor-details__colImg">
                      <div class="exhibitor-details__data-img">
                        <?php if ( ! empty($company_info['company_info-data-img']) ) : ?>
                          <img src="<?php echo esc_url($company_info['company_info-data-img']); ?>" alt="会社DATA" loading="lazy" width="507" height="274">
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="exhibitor-details__colContent">
                      <div class="exhibitor-details__data-content">
                        <h5 class="exhibitor-details__data-title">会社DATA</h5>
                        <div class="exhibitor-details__data-block">
                          <?php if ( ! empty($company_info['company_info-data-text']) ) : ?>
                            <p><?php echo nl2br(esc_html($company_info['company_info-data-text'])); ?></p>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endif; ?>

              <hr>

              <?php if ( ! empty($company_info['company_info-tsr-lead']) || ! empty($company_info['company_info-tsr-text']) ) : ?>
                <div class="tsr-comment">
                  <div class="tsr-comment__pc">
                    <!-- ▼ TSR画像は今回ACFに無いので仮のまま（必要ならフィールド追加で差し替え） -->
                    <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/lazy-load/tsr-comment-pc.webp" alt="" width="212" >
                    <?php if ( ! empty($company_info['company_info-tsr-lead']) ) : ?>
  <h4 class="tsr-comment__lead"><?php echo wp_kses_post( nl2br( $company_info['company_info-tsr-lead'], false ) ); ?></h4>
<?php endif; ?>
<?php if ( ! empty($company_info['company_info-tsr-text']) ) : ?>
  <p class="tsr-comment__text"><?php echo wp_kses_post( nl2br( $company_info['company_info-tsr-text'], false ) ); ?></p>
<?php endif; ?>
                  </div>

                  <div class="tsr-comment__sp">
                    <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/lazy-load/36_sy79jhpvdc-sp.webp" alt="" width="100" height="46">
                    <?php if ( ! empty($company_info['company_info-tsr-lead']) ) : ?>
  <h4 class="tsr-comment__lead"><?php echo wp_kses_post( nl2br( $company_info['company_info-tsr-lead'], false ) ); ?></h4>
<?php endif; ?>
<?php if ( ! empty($company_info['company_info-tsr-text']) ) : ?>
  <p class="tsr-comment__text"><?php echo wp_kses_post( nl2br( $company_info['company_info-tsr-text'], false ) ); ?></p>
<?php endif; ?>
                  </div>
                </div>
              <?php endif; ?>

            </div>
          </div>
        </div>
      </div>
      <div class="modal__btn-close js-closebtn"></div>
    </div>
  <?php endif; ?>


  <?php if ( $has_biz ) : ?>
    <div class="modal js-modal" attr-modal="business-content">
      <div class="modal-dialog modal-dialog--centered">
        <div class="modal-content -p-60">
          <div class="modal-header">
            <h5 class="modal-title">事業内容 <span class="modal-title--eng">BUSINESS CONTENT</span></h5>
          </div>
          <div class="modal-body">
            <div class="exhibitor-details exhibitor-details__businessContent">

              <?php if ( $company_biz_lead ) : ?>
                <h2 class="exhibitor-details__title -mb-0"><?php echo nl2br(esc_html($company_biz_lead)); ?></h2>
              <?php endif; ?>

              <?php if ( have_rows('company_biz-group') ) : ?>
                <?php while ( have_rows('company_biz-group') ) : the_row();
                  $img   = get_sub_field('company_biz-img');   // url
                  $title = get_sub_field('company_biz-title'); // text
                  $text  = get_sub_field('company_biz-text');  // textarea
                  ?>
                  <div class="exhibitor-details__row">
                    <div class="exhibitor-details__colImg">
                      <div class="exhibitor-details__data-img">
                        <?php if ( $img ) : ?>
                          <img src="<?php echo esc_url($img); ?>" alt="会社DATA" loading="lazy" width="507" height="274">
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="exhibitor-details__colContent">
                      <?php if ( $title ) : ?>
                        <h5 class="exhibitor-details__data-title -bold">
  <?php
    $t = (string) $title;

    // ① まず HTMLエンティティを戻す（&lt;br&gt; → <br>）
    $t = html_entity_decode($t, ENT_QUOTES, 'UTF-8');

    // ② <br> 系だけ許可して出力（それ以外のHTMLは除去）
    echo wp_kses($t, ['br' => []]);
  ?>
</h5>
                      <?php endif; ?>
                      <?php if ( $text ) : ?>
                        <p><?php echo nl2br(esc_html($text)); ?></p>
                      <?php endif; ?>
                    </div>
                  </div>
                <?php endwhile; ?>
              <?php endif; ?>

            </div>
          </div>
        </div>
      </div>
      <div class="modal__btn-close js-closebtn"></div>
    </div>
  <?php endif; ?>


  <?php if ( $has_rec ) : ?>
    <div class="modal js-modal" attr-modal="recruitment-data">
      <div class="modal-dialog modal-dialog--centered">
        <div class="modal-content -p-60">
          <div class="modal-header">
            <h5 class="modal-title">採用DATA <span class="modal-title--eng">Recruitment data</span></h5>
          </div>
          <div class="modal-body">
            <div class="exhibitor-details exhibitor-details__recruitmentData">

              <?php
              $tags = $company_rec['company_rec-select'] ?? [];
              $rec_img = $company_rec['company_rec-img'] ?? '';
              $rec_text = $company_rec['company_rec-text'] ?? '';
              ?>

              <?php if ( ! empty($tags) ) : ?>
                <div class="exhibitor-details__recruitmentData-catItems">
                  <?php foreach ( (array)$tags as $t ) : ?>
                    <span class="category-item active"><?php echo esc_html($t); ?></span>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>

              <div class="exhibitor-details__data">
                <div class="exhibitor-details__row">
                  <div class="exhibitor-details__colImg">
                    <div class="exhibitor-details__data-img">
                      <?php if ( $rec_img ) : ?>
                        <img src="<?php echo esc_url($rec_img); ?>" alt="会社DATA" loading="lazy" width="507" height="274">
                      <?php endif; ?>
                    </div>
                  </div>
                  <div class="exhibitor-details__colContent">
                    <div class="exhibitor-details__data-content">
                      <?php if ( $rec_text ) : ?>
                        <div class="exhibitor-details__data-block">
                          <p><?php echo nl2br(esc_html($rec_text)); ?></p>
                        </div>
                      <?php endif; ?>
                      <!-- ▼ 元モックの細かい「募集人数/初任給…」は今回ACFに無いので rec_text にまとめて入れる想定 -->
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
      <div class="modal__btn-close js-closebtn"></div>
    </div>
  <?php endif; ?>


  <?php if ( $has_pres ) : ?>
    <div class="modal js-modal" attr-modal="president-msg">
      <div class="modal-dialog modal-dialog--centered">
        <div class="modal-content -p-60">
          <div class="modal-header">
            <h5 class="modal-title">社長インタビュー <span class="modal-title--eng">interview</span></h5>
          </div>
          <div class="modal-body">
            <div class="exhibitor-details exhibitor-details__presidentMsg">
              <?php
              $pres_img  = $company_pres['company_president-img'] ?? ''; // ※フィールド名が company_rec-img
              $pres_post = $company_pres['company_president-post'] ?? '';
              $pres_name = $company_pres['company_president-name'] ?? '';
              $pres_txt  = $company_pres['company_president-txt'] ?? '';
              ?>
              <div class="exhibitor-details__presidentMsg-img">
                <?php if ( $pres_img ) : ?>
                  <img src="<?php echo esc_url($pres_img); ?>" alt="">
                <?php endif; ?>
              </div>
              <div class="exhibitor-details__presidentMsg-info">
                <h5 class="exhibitor-details__presidentMsg-title">
                  <?php echo esc_html($pres_post); ?>
                  <?php if ( $pres_name ) : ?>
                    <span><?php echo esc_html($pres_name); ?></span>
                  <?php endif; ?>
                </h5>
                <?php if ( $pres_txt ) : ?>
                  <p><?php echo nl2br(esc_html($pres_txt)); ?></p>
                <?php endif; ?>
              </div>
              <!-- ▼ 「インタビュー詳細はこちら」リンク先は今回ACFに無いので仮 -->
              <?php
$president_group = get_field('company_president-group');

if ( $president_group && !empty($president_group['company_president-syousai']) ) :
?>
  <a href="<?php echo esc_url($president_group['company_president-syousai']); ?>"
     class="btn-secondary"
     target="_blank" rel="noopener">
    インタビュー詳細はこちら
  </a>
<?php endif; ?>

            </div>
          </div>
        </div>
      </div>
      <div class="modal__btn-close js-closebtn"></div>
    </div>
  <?php endif; ?>


  <?php if ( $has_movie ) : ?>
  <div class="modal js-modal" attr-modal="exhibitor-video">
    <div class="modal-dialog modal-dialog--video modal-dialog--centered">
      <div class="modal-content">
        <div class="modal-body">
          <div class="modal-content__video">
            <iframe
              width="1200"
              height="690"
              class="yt-video"
              data-src="<?php echo esc_url($company_movie_src); ?>"
              title="YouTube video player"
              frameborder="0"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              referrerpolicy="strict-origin-when-cross-origin"
              allowfullscreen></iframe>
          </div>
        </div>
      </div>
    </div>
    <div class="modal__btn-close js-closebtn"></div>
  </div>
<?php endif; ?>



  <!-- ▼ここだけ変更：モーダル側もtoggleのみで出し分け（MENUと整合） -->
  <?php if ( $show_catalog_menu ) : ?>
    <div class="modal js-modal modal-cat" attr-modal="catalog">
      <div class="modal-dialog  modal-dialog--centered">
        <div class="modal-content -p-60">
          <div class="modal-header">
            <h5 class="modal-title">カタログ一覧<span class="modal-title--eng">catalog</span></h5>
          </div>
          <div class="modal-body">
            <div class="exhibitor-details exhibitor-details__catalog">
              <div class="swiper-button swiper-button-next">
                <button class="swiper-button__arrow-btn"></button>
              </div>
              <div class="swiper-button swiper-button-prev">
                <button class="swiper-button__arrow-btn"></button>
              </div>

              <div class="swiper catalog-swiper">
                <div class="swiper-wrapper">
                  <?php while ( have_rows('company_catalog-group') ) : the_row();
                    $c_img  = get_sub_field('company_catalog-img');          // url
                    $c_pdf  = get_sub_field('company_catalog-pdf');          // url
                    $c_desc = get_sub_field('company_catalog-description');  // text
                    ?>
                    <div class="swiper-slide">
                      <div class="catalog-item">
                        <div class="catalog-item__img">
                          <?php if ( $c_img ) : ?>
                            <img src="<?php echo esc_url($c_img); ?>" alt="" width="248" height="376">
                          <?php endif; ?>
                        </div>

                        <?php if ( $c_desc ) : ?>
                          <p class="catalog-item__desc"><?php echo esc_html($c_desc); ?></p>
                        <?php endif; ?>

                        <div class="catalog-item__btns">
                          <?php if ( $c_pdf ) : ?>
                            <a class="btn-secondary" href="<?php echo esc_url($c_pdf); ?>" target="_blank" rel="noopener">カタログを見る</a>
                            <a class="btn-secondary -outline" href="<?php echo esc_url($c_pdf); ?>" download>ダウンロード</a>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  <?php endwhile; ?>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal__btn-close js-closebtn"></div>
    </div>
  <?php endif; ?>


  <?php if ( $show_contact_menu ) : ?>
    <div class="modal js-modal" attr-modal="contact">
      <div class="modal-dialog  modal-dialog--centered">
        <div class="modal-content -p-60">
          <div class="modal-header">
            <h5 class="modal-title">コンタクト<span class="modal-title--eng">CONTACT</span></h5>
          </div>
          <div class="modal-body">
            <div class="exhibitor-details exhibitor-details__contact">
              <div class="exhibitor-details__contact-row">

                <div class="exhibitor-details__contact-compCard">
                  <div class="company-card company-card--sm">

  <?php if ($pref_name !== '') : ?>
    <span class="company-card__tag tag <?php echo esc_attr($tag_class); ?>">
      <span class="tag__dot"></span>
      <?php echo esc_html($pref_name); ?>
    </span>
  <?php endif; ?>

  <div class="company-card__logo">
  <?php if ( has_post_thumbnail() ) : ?>
    <?php
      the_post_thumbnail(
        'medium',
        [
          'loading' => 'lazy',
          'alt' => esc_attr(get_the_title())
        ]
      );
    ?>
  <?php else : ?>
    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/lazy-load/comapny-logo.webp"
         alt=""
         loading="lazy">
  <?php endif; ?>
</div>


  <h3 class="company-card__title"><?php echo esc_html(get_the_title()); ?></h3>

  <?php if ($job_text !== '') : ?>
    <div class="company-card__compType">
      <span class="company-card__compType-label">業種</span>
      <?php echo esc_html($job_text); ?>
    </div>
  <?php endif; ?>

</div>


                  <?php
$contact_group = get_field('company_contact-group');

if ( $contact_group && !empty($contact_group['company_contact-contact-url']) ) :
?>
  <a class="btn btn-primary -sm"
     href="<?php echo esc_url($contact_group['company_contact-contact-url']); ?>"
     target="_blank" rel="noopener">
    コンタクトはこちら
    <span class="btn__icon btn__icon--arrow"></span>
  </a>
<?php endif; ?>
                </div>

                <div class="exhibitor-details__contact-detail">
                  <?php
                  $furigana = $company_contact['company_contact-furigana'] ?? '';
                  $biz      = $company_contact['company_contact-business'] ?? '';
                  $products = $company_contact['company_contact-products'] ?? '';
                  $loc      = $company_contact['company_contact-location'] ?? '';
                  $tel      = $company_contact['company_contact-tel'] ?? '';
                  $url      = $company_contact['company_contact-url'] ?? '';
                  ?>
                  <h4 class="exhibitor-details__contact-title">
                    <?php echo esc_html(get_the_title()); ?>
                    <?php if ( $furigana ) : ?>
                      <span>（<?php echo esc_html($furigana); ?>）</span>
                    <?php endif; ?>
                  </h4>

                  <ul class="contact-detail">
                    <?php if ( $biz ) : ?>
                      <li class="contact-detail__item">
                        <p class="contact-detail__item-label">事業内容</p>
                        <p class="contact-detail__item-content"><?php echo esc_html($biz); ?></p>
                      </li>
                    <?php endif; ?>

                    <?php if ( $products ) : ?>
                      <li class="contact-detail__item">
                        <p class="contact-detail__item-label">主な取扱製品·<br class="only-sp">技術·サービス</p>
                        <p class="contact-detail__item-content"><?php echo esc_html($products); ?></p>
                      </li>
                    <?php endif; ?>

                    <?php if ( $loc ) : ?>
                      <li class="contact-detail__item">
                        <p class="contact-detail__item-label">所在地</p>
                        <p class="contact-detail__item-content"><?php echo esc_html($loc); ?></p>
                      </li>
                    <?php endif; ?>

                    <?php if ( $tel ) : ?>
                      <li class="contact-detail__item">
                        <p class="contact-detail__item-label">TEL</p>
                        <p class="contact-detail__item-content"><?php echo esc_html($tel); ?></p>
                      </li>
                    <?php endif; ?>

                    <?php if ( $url ) : ?>
                      <li class="contact-detail__item">
                        <p class="contact-detail__item-label">公式サイト</p>
                        <p class="contact-detail__item-content">
                          <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener">公式サイトはこちら</a>
                        </p>
                      </li>
                    <?php endif; ?>
                  </ul>

                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal__btn-close js-closebtn"></div>
    </div>
  <?php endif; ?>

</div>

<?php get_template_part('parts/footer'); ?>

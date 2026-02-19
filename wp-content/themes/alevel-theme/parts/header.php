<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8" />
    <meta name="format-detection" content="email=no,telephone=no,address=no" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />

    	<?php if (is_front_page()) : ?>
        <title>ALEVEL ONLINE 2027 | エラベルオンライン</title>
        <meta name="description" content="株式会社東京商工リサーチ（TSR）が発行した東海4県の優良企業ガイド【ALEVEL-エラベル-2026年】からオンライン合同企業展を開催。株式会社東京商工リサーチ独自の企業分析法により、公正・中立の立場で優良企業を選定。企業とつながる交流の場を設けました。">
	<?php elseif (is_post_type_archive('companies') || is_page('companies')) : ?>
        <title>出展社一覧 | ALEVEL ONLINE 2027 | エラベルオンライン</title>
        <meta name="description" content="株式会社東京商工リサーチ（TSR）が発行した東海4県の優良企業ガイド【ALEVEL-エラベル-2026年】からオンライン合同企業展を開催。株式会社東京商工リサーチ独自の企業分析法により、公正・中立の立場で優良企業を選定。企業とつながる交流の場を設けました。">
	<?php elseif (is_singular('companies')) : ?>
  <title><?php the_title(); ?> | ALEVEL ONLINE 2027 | エラベルオンライン</title>
  <meta name="description" content="株式会社東京商工リサーチ（TSR）が発行した東海4県の優良企業ガイド【ALEVEL-エラベル-2026年】からオンライン合同企業展を開催。株式会社東京商工リサーチ独自の企業分析法により、公正・中立の立場で優良企業を選定。企業とつながる交流の場を設けました。">
	<?php elseif (is_post_type_archive('special-stage') || is_page('special-stage')) : ?>
	    <title>>エラベルTV | ALEVEL ONLINE 2027 | エラベルオンライン</title>
	    <meta name="description" content="株式会社東京商工リサーチ（TSR）が発行した東海4県の優良企業ガイド【ALEVEL-エラベル-2026年】からオンライン合同企業展を開催。株式会社東京商工リサーチ独自の企業分析法により、公正・中立の立場で優良企業を選定。企業とつながる交流の場を設けました。">
	<?php endif; ?>

	
    <!-- OGPタグ/twitterカード -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="エラベルオンライン" />
    <meta property="og:locale" content="ja_JP" />
    <link rel="icon" type="image/jpg" href="<?php echo get_template_directory_uri() ?>/assets/img/common/1_xLPLtYhsn1.jpg">
		<meta property="og:image" content="xxxxxxxxxxxxxxxxxxxxxxx/assets/img/ogp.jpg" />

    <!-- ファビコン -->
    <link rel="icon" href="/favicon.ico" type="image/png" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <!-- フィードページのURLを指定 -->
    <link rel="alternate" type="application/rss+xml" title="Site Title" href="xxxxxxxxxxxxxxxxxxxxxxx/" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css" />
    <link rel="stylesheet" href="<?php echo get_template_directory_uri() ?>/assets/css/common.css">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri() ?>/assets/css/top.css">
		<link rel="stylesheet" href="<?php echo get_template_directory_uri() ?>/assets/css/exhibitors-list.css">
		<link rel="stylesheet" href="<?php echo get_template_directory_uri() ?>/assets/css/exhibitors-detail.css">
		<link rel="stylesheet" href="<?php echo get_template_directory_uri() ?>/assets/css/elabelle-tv.css">
		<?php wp_head(); ?>
	
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-KFK2KZC');</script>
<!-- End Google Tag Manager -->
</head>

<body id="<?php echo is_front_page() ? '' : 'body-sub'; ?>" <?php body_class('sp-stickyMenu'); ?>>
<!-- Google Tag Manager (noscript) -->
            <!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KFK2KZC"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
        <!-- End Google Tag Manager (noscript) -->

    <header class="header" id="header">
        <div class="header__wrapper">
            <a href="/" class="header__logo">
                <img src="<?php echo get_template_directory_uri() ?>/assets/img/fv/header-logo.svg" alt="Alevel Online 2027">
            </a>
            <button class="header__toggle" id="js-navToggle">
                <span class="header__toggle-bar"></span>
                <span class="header__toggle-bar"></span>
                <span class="header__toggle-bar"></span>
            </button>
            <div class="header__menu header__menu-pc">
                <ul class="header__nav">
                     <li class="header__nav-item">
                        <a href="/#about___area" class="header__nav-link js-about-link">エラベルオンラインとは</a>
                    </li>
                    <li class="header__nav-item">
                        <a href="<?php echo esc_url( get_post_type_archive_link('companies') ); ?>" class="header__nav-link">出展社一覧</a>
                    </li>
                    <li class="header__nav-item">
                        <a href="/special-stage/" class="header__nav-link">エラベルTV</a>
                    </li>
                </ul>
            </div>
            <div class="header__menu header__menu-sp" id="js-spNav">
                <ul class="header__nav">
                    <li class="header__nav-item">
                        <a href="/" class="header__nav-link">トップページ</a>
                    </li>
                    <li class="header__nav-item">
                        <a href="/#about___area" class="header__nav-link js-about-link">エラベルオンラインとは</a>
                    </li>
                    <li class="header__nav-item">
                        <a href="/special-stage/" class="header__nav-link">エラベルTV</a>
                    </li>
                    <li class="header__nav-item">
                        <a href="<?php echo esc_url( get_post_type_archive_link('companies') ); ?>" class="header__nav-link">出展社一覧</a>
                    </li>
                    <li class="header__nav-item">
                        <a href="https://alevel.stores.jp/" class="header__nav-link" target="_blank">エラベル本誌購入</a>
                    </li>
                    <!--<li class="header__nav-item">
                        <a href="" class="header__nav-link">利用規約</a>
                    </li>-->
                    <li class="header__nav-item">
                        <a href="mailto:info-alevel@dexpo.jp" class="header__nav-link" target="_blank">お問い合わせ</a>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    <script>
document.addEventListener("DOMContentLoaded", function () {

const aboutLinks = document.querySelectorAll(".js-about-link");

aboutLinks.forEach(link => {

  if (window.innerWidth <= 768) {
    // SPだけリロード付きに変更
    link.href = "/?reload=1#about___area";
  } else {
    // PCは通常アンカー
    link.href = "/#about___area";
  }

});

});
    </script>
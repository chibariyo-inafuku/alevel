<?php
// /contact スラッグ専用テンプレ
if (!defined('ABSPATH')) exit;
get_template_part('parts/header'); ?>

<main class="main contact">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

            <section class="">
                <div class="container">
                    <!-- Page Header -->
                    <div class="section__pageheader">
                        <p class="section__header-jp">お問い合わせ</p>
                        <h2 class="section__header-eng">CONTACT</h2>
                    </div>

                    <div class="breadcrumb">
                        <ul class="breadcrumb__list">
                            <li class="breadcrumb__items"><a href="<?php echo esc_url(home_url('/')); ?>">TOP</a></li>
                            <li class="breadcrumb__items">CONTACT</li>
                        </ul>
                    </div>

                    <!-- 本文（CF7 のショートコードを置く固定ページ内容） -->
                    <div class="single__wp">
                        <?php the_content(); ?>
                    </div>
                </div>
            </section>

    <?php endwhile;
    endif; ?>
</main>

<?php get_template_part('parts/footer'); ?>
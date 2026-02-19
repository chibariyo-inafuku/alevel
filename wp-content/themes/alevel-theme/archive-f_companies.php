<?php
get_template_part('parts/header'); ?>

<main class="--bg-gray">
    <section class="section news" id="news">
        <div class="container">
            <div class="news__header">
                <div class="section__pageheader">
                    <p class="section__header-jp">ニュース</p>
                    <h2 class="section__header-eng">NEWS</h2>
                </div>
            </div>
            <div class="breadcrumb">
                <ul class="breadcrumb__list">
                    <li class="breadcrumb__items"><a href="<?php echo home_url(); ?>">TOP</a></li>
                    <li class="breadcrumb__items">NEWS</li>
                </ul>
            </div>
            <div class="news__block newslists">
                <?php if (have_posts()): ?>
                    <?php while (have_posts()):
                        the_post(); ?>
                        <?php get_template_part('parts/news-item'); ?>
                    <?php endwhile; ?>

                    <div class="pagination">
                        <?php the_posts_pagination(); ?>
                    </div>
                <?php else: ?>
                    <p style="text-align: center;">No news found.</p>
                <?php endif; ?>
            </div>

            <!-- <div class="news__pagination-wrap">
                <div class="pagination__container">
                    <button id="back-btn" class="pagination__nav-button">BACK</button>
                    <ul class="pagination__numbers">
                        <li class="paginations__numbers-txt active">1</li>
                        <li class="paginations__numbers-txt ">2</li>
                        <li class="paginations__numbers-txt ">3</li>
                        <li class="paginations__numbers-txt ">4</li>
                        <li class="paginations__numbers-txt ">5</li>
                    </ul>
                    <button id="next-btn" class="pagination__nav-button">NEXT</button>
                </div>
            </div> -->

        </div>
    </section>
</main>

<?php get_template_part('parts/footer'); ?>
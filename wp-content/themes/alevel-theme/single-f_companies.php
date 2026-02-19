<?php get_template_part('parts/header'); ?>

<main class="--bg-gray">
    <section class="single">
        <div class="single__wrapper">
            <?php
            $tags = get_the_tags();
            if ($tags):
                echo '<ul class="post-tags">';
                foreach ($tags as $tag) {
                    echo '<li><a class="single__categories" href="' . esc_url(get_tag_link($tag->term_id)) . '">'
                        . esc_html($tag->name) . '</a></li>';
                }
                echo '</ul>';
            endif;
            ?>
            <h1 class="single__title"><?php the_title() ?></h1>
            <div class="breadcrumb">
                <ul class="breadcrumb__list">
                    <li class="breadcrumb__items"><a href="<?php echo home_url(); ?>">TOP</a></li>
                    <li class="breadcrumb__items"><a href="<?php echo home_url("/news"); ?>">NEWS</a></li>
                    <li class="breadcrumb__items"><?php the_title() ?></li>
                </ul>
            </div>
            <div class="single__image">
                <?php if (has_post_thumbnail()) {
                    the_post_thumbnail('medium', ['loading' => 'lazy', 'alt' => get_the_title()]);
                } else { ?>
                <?php } ?>
            </div>
            <div class="single__wp">
                <?php the_content(); ?>
            </div>
        </div>
    </section>

</main>

<?php get_template_part('parts/footer'); ?>
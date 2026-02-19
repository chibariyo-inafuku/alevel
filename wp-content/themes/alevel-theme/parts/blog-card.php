<a href="<?php echo esc_url(get_permalink()); ?>" class="blog__box">
    <picture>
        <?php if (has_post_thumbnail()): ?>
            <?php the_post_thumbnail('medium', ['loading' => 'lazy', 'alt' => get_the_title()]); ?>
        <?php else: ?>
            <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/lazy-load/blog01.webp"
                alt="<?php the_title_attribute(); ?>" loading="lazy" width="514" height="294">
        <?php endif; ?>
    </picture>

    <?php
    $tags = get_the_tags();
    if ($tags):
        echo '<ul class="post-tags">';
        foreach ($tags as $tag) {
            echo '<li><p class="blog__categ">'
                . "/ " . esc_html($tag->name) . '</p></li>';
        }
        echo '</ul>';
    endif;
    ?>

    <h2 class="blog__title"><?php the_title(); ?></h2>
    <p class="blog__date"><?php echo get_the_date('Y.m.d'); ?></p>

    <p class="blog__desc">
        <?php echo wp_trim_words(get_the_excerpt()); ?>
    </p>
</a>
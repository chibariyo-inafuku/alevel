<a href=" <?php echo esc_url(get_permalink()); ?>" class="news__list">
    <p class="news__list-txt"><?php echo the_title() ?></p>
    <div class="news__list-content">
        <?php
        $tags = get_the_tags();
        if ($tags):
            echo '<ul class="post-tags">';
            foreach ($tags as $tag) {
                echo '<li><p class="news__list-note" href="' . esc_url(get_tag_link($tag->term_id)) . '">'
                    . esc_html($tag->name) . '</p></li>';
            }
            echo '</ul>';
        endif;
        ?>
        <p class="news__list-date"><?php echo get_the_date('Y.m.d'); ?></p>
    </div>
</a>
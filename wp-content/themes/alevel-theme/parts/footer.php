<footer class="footer">
        <div class="container">
            <div class="footer__wrapper">
                <div class="footer__left">
                    <a href="#" class="footer__logo">
                        <img src="<?php echo get_template_directory_uri() ?>/assets/img/fv/header-logo.svg" alt="Alevel Online 2027" width="314" height="50">
                    </a>
                    <ul class="footer__nav">
                        <li class="footer__nav-item">
                            <a href="<?php echo esc_url( get_post_type_archive_link('companies') ); ?>" class="footer__nav-link">出展社一覧</a>
                        </li>
                        <li class="footer__nav-item">
                            <a href="/special-stage/" class="footer__nav-link">エラベルTV</a>
                        </li>
                        <li class="footer__nav-item">
                            <a href="mailto:info-alevel@dexpo.jp" class="footer__nav-link" target="_blank">お問い合わせはこちら</a>
                        </li>
                    </ul>
                </div>
                <p class="footer__org">主催 : エラベル事務局 Ⓒ ALEVELonline2027</p>
            </div>
        </div>
    </footer>


    

    <script src="https://cdn.jsdelivr.net/npm/gsap@3.14.1/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script>
    <script src="<?php echo get_template_directory_uri() ?>/assets/js/common.js"></script>
    <script src="<?php echo get_template_directory_uri() ?>/assets/js/animations.js"></script>

<?php wp_footer(); ?>
</body>

</html>
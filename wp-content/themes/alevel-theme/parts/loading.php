<div class="loading" id="loading">
    <div class="loading__wrp">
        <div class="loading__logo">
            <img src="<?php echo get_template_directory_uri() ?>/assets/img/load-logo.svg" class="loading__logo-img"
                alt="chibariyo" width="62" height="70" loading="eager">
            <img src="<?php echo get_template_directory_uri() ?>/assets/img/load-chibariyo.svg" alt="chibariyo"
                loading="eager" width="212" height="70" class="loading__logo-txt">
        </div>

        <div class="loading__progress-wrp">
            <div class="loading__progress"></div>
            <p class="loading__progress-stat">
                NOW Loading ... <span id="loading-percent">0%</span>
            </p>
        </div>
    </div>
</div>
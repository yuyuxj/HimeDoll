<?php
defined('ABSPATH') || exit;
?>
<section class="hero">
    <div class="hero__decor hero__decor--one"></div>
    <div class="hero__decor hero__decor--two"></div>
    <div class="container hero__grid">
        <div class="hero__content">
            <p class="eyebrow"><?php echo esc_html(himedoll_get_theme_mod('himedoll_hero_eyebrow', 'HimeDoll Japan')); ?></p>
            <h1><?php echo esc_html(himedoll_get_theme_mod('himedoll_hero_title', 'あなたの理想を、リアルに。')); ?></h1>
            <p class="hero__lead"><?php echo esc_html(himedoll_get_theme_mod('himedoll_hero_description', '日本のお客様のために設計された、安心・高品質な専門ストア。')); ?></p>
            <div class="hero__actions">
                <a class="button button--primary" href="<?php echo esc_url(himedoll_get_theme_mod('himedoll_hero_primary_url', '/shop/')); ?>">
                    <?php echo esc_html(himedoll_get_theme_mod('himedoll_hero_primary_label', '商品を見る')); ?>
                </a>
                <a class="button button--secondary" href="<?php echo esc_url(himedoll_get_theme_mod('himedoll_hero_secondary_url', '#shopping-flow')); ?>">
                    <?php echo esc_html(himedoll_get_theme_mod('himedoll_hero_secondary_label', '初めての方へ')); ?>
                </a>
            </div>
            <ul class="hero__proof">
                <li>匿名配送</li>
                <li>日本語対応</li>
                <li>安心保証</li>
            </ul>
        </div>
        <div class="hero__visual" aria-hidden="true">
            <div class="hero__card hero__card--main">
                <span class="hero__badge">HimeDoll</span>
                <div class="hero__silhouette"></div>
                <p>Premium Collection</p>
            </div>
            <div class="hero__card hero__card--small">Japan Support</div>
        </div>
    </div>
</section>

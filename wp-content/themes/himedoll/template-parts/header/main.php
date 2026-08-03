<?php
defined('ABSPATH') || exit;
?>
<div class="container header-main">
    <button class="icon-button menu-toggle" type="button" aria-expanded="false" aria-controls="mobile-menu" data-menu-toggle>
        <span class="hamburger"><i></i><i></i><i></i></span>
        <span class="screen-reader-text"><?php esc_html_e('メニュー', 'himedoll'); ?></span>
    </button>

    <div class="site-branding">
        <?php if (has_custom_logo()) : ?>
            <?php the_custom_logo(); ?>
        <?php else : ?>
            <a class="site-logo-text" href="<?php echo esc_url(home_url('/')); ?>">
                <?php echo esc_html(himedoll_site_name()); ?>
            </a>
        <?php endif; ?>
    </div>

    <nav class="desktop-nav" aria-label="<?php esc_attr_e('メインメニュー', 'himedoll'); ?>">
        <?php
        wp_nav_menu([
            'theme_location' => 'primary',
            'container' => false,
            'menu_class' => 'desktop-nav__list',
            'fallback_cb' => 'himedoll_menu_fallback',
        ]);
        ?>
    </nav>

    <div class="header-actions">
        <button class="header-action" type="button" data-search-open aria-label="<?php esc_attr_e('検索', 'himedoll'); ?>">
            <span>検索</span>
        </button>
        <?php if (function_exists('wc_get_page_permalink')) : ?>
            <a class="header-action" href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>">マイページ</a>
        <?php endif; ?>
        <?php if (function_exists('wc_get_cart_url')) : ?>
            <a class="header-action" href="<?php echo esc_url(wc_get_cart_url()); ?>">
                カート <span class="cart-count"><?php echo esc_html((string) himedoll_cart_count()); ?></span>
            </a>
        <?php endif; ?>
    </div>
</div>

<?php
defined('ABSPATH') || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main-content"><?php esc_html_e('本文へ移動', 'himedoll'); ?></a>

<header class="site-header" data-site-header>
    <div class="announcement">
        <div class="container announcement__inner">
            <span>日本全国送料無料・匿名配送対応</span>
            <span class="announcement__desktop">安心保証・日本語サポート</span>
        </div>
    </div>

    <div class="container header-main">
        <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="mobile-menu" data-menu-toggle>
            <span></span><span></span><span></span>
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
                'container'      => false,
                'menu_class'     => 'desktop-nav__list',
                'fallback_cb'    => false,
            ]);
            ?>
        </nav>

        <div class="header-actions">
            <a href="<?php echo esc_url(home_url('/?s=')); ?>" aria-label="<?php esc_attr_e('検索', 'himedoll'); ?>">検索</a>
            <?php if (function_exists('wc_get_cart_url')) : ?>
                <a href="<?php echo esc_url(wc_get_cart_url()); ?>">
                    カート <span class="cart-count"><?php echo esc_html((string) himedoll_cart_count()); ?></span>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <nav id="mobile-menu" class="mobile-nav" hidden data-mobile-menu>
        <div class="container">
            <?php
            wp_nav_menu([
                'theme_location' => 'primary',
                'container'      => false,
                'menu_class'     => 'mobile-nav__list',
                'fallback_cb'    => false,
            ]);
            ?>
        </div>
    </nav>
</header>

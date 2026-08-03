<?php
defined('ABSPATH') || exit;
?>
<nav id="mobile-menu" class="mobile-nav" hidden data-mobile-menu>
    <div class="container">
        <?php
        wp_nav_menu([
            'theme_location' => 'primary',
            'container' => false,
            'menu_class' => 'mobile-nav__list',
            'fallback_cb' => 'himedoll_menu_fallback',
        ]);
        ?>
        <div class="mobile-nav__utility">
            <a href="<?php echo esc_url(himedoll_shop_url()); ?>">商品一覧</a>
            <a href="<?php echo esc_url(home_url('/contact/')); ?>">お問い合わせ</a>
        </div>
    </div>
</nav>

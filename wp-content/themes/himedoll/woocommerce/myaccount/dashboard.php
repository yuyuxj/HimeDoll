<?php
defined('ABSPATH') || exit;

$current_user = wp_get_current_user();
?>
<section class="account-dashboard">
    <div class="account-dashboard__hero">
        <p class="eyebrow">My HimeDoll</p>
        <h1><?php echo esc_html($current_user->display_name); ?> 様</h1>
        <p>注文、配送先、アカウント情報をここから管理できます。</p>
    </div>

    <div class="account-dashboard__grid">
        <a class="account-card" href="<?php echo esc_url(wc_get_account_endpoint_url('orders')); ?>">
            <span>01</span><h2>注文履歴</h2><p>注文状況と過去の購入履歴</p>
        </a>
        <a class="account-card" href="<?php echo esc_url(wc_get_account_endpoint_url('edit-address')); ?>">
            <span>02</span><h2>配送先</h2><p>住所と請求先情報の管理</p>
        </a>
        <a class="account-card" href="<?php echo esc_url(wc_get_account_endpoint_url('edit-account')); ?>">
            <span>03</span><h2>アカウント</h2><p>氏名、メール、パスワード</p>
        </a>
        <a class="account-card" href="<?php echo esc_url(home_url('/wishlist/')); ?>">
            <span>04</span><h2>お気に入り</h2><p>保存した商品を見る</p>
        </a>
    </div>

    <?php get_template_part('template-parts/account/recent-products'); ?>
</section>

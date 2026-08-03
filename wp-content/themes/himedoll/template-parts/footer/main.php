<?php
defined('ABSPATH') || exit;
?>
<footer class="site-footer">
    <div class="container footer-grid">
        <section class="footer-intro">
            <h2 class="footer-brand">HimeDoll</h2>
            <p>あなたの理想を、リアルに。</p>
            <p class="footer-note">日本のお客様に安心して選んでいただける専門ストアを目指します。</p>
        </section>
        <section>
            <h3>商品を探す</h3>
            <ul>
                <li><a href="<?php echo esc_url(himedoll_shop_url()); ?>">すべての商品</a></li>
                <li><a href="<?php echo esc_url(home_url('/product-category/tpe/')); ?>">TPE</a></li>
                <li><a href="<?php echo esc_url(home_url('/product-category/silicone/')); ?>">シリコン</a></li>
                <li><a href="<?php echo esc_url(home_url('/product-category/ai/')); ?>">AIシリーズ</a></li>
            </ul>
        </section>
        <section>
            <h3>ショッピングガイド</h3>
            <ul>
                <li><a href="<?php echo esc_url(home_url('/shipping/')); ?>">配送について</a></li>
                <li><a href="<?php echo esc_url(home_url('/payment/')); ?>">お支払いについて</a></li>
                <li><a href="<?php echo esc_url(home_url('/warranty/')); ?>">保証・返品</a></li>
                <li><a href="<?php echo esc_url(home_url('/faq/')); ?>">よくあるご質問</a></li>
            </ul>
        </section>
        <section>
            <h3>会社情報</h3>
            <ul>
                <li><a href="<?php echo esc_url(home_url('/about/')); ?>">会社概要</a></li>
                <li><a href="<?php echo esc_url(home_url('/contact/')); ?>">お問い合わせ</a></li>
                <li><a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>">プライバシーポリシー</a></li>
                <li><a href="<?php echo esc_url(home_url('/legal/')); ?>">特定商取引法に基づく表記</a></li>
            </ul>
        </section>
    </div>
    <div class="container footer-bottom">
        <small>&copy; <?php echo esc_html(wp_date('Y')); ?> HimeDoll. All rights reserved.</small>
        <span>18歳未満の方のご利用はご遠慮ください。</span>
    </div>
</footer>

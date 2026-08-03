<?php
defined('ABSPATH') || exit;
?>
<footer class="site-footer">
    <div class="container footer-grid">
        <section>
            <h2 class="footer-brand">HimeDoll</h2>
            <p>理想の美しさを、もっと身近に。</p>
        </section>
        <section>
            <h3>ショッピングガイド</h3>
            <ul>
                <li><a href="#">配送について</a></li>
                <li><a href="#">お支払いについて</a></li>
                <li><a href="#">返品・保証</a></li>
            </ul>
        </section>
        <section>
            <h3>サポート</h3>
            <ul>
                <li><a href="#">よくあるご質問</a></li>
                <li><a href="#">お問い合わせ</a></li>
                <li><a href="#">会社概要</a></li>
            </ul>
        </section>
        <section>
            <h3>法的情報</h3>
            <ul>
                <li><a href="#">利用規約</a></li>
                <li><a href="#">プライバシーポリシー</a></li>
                <li><a href="#">特定商取引法に基づく表記</a></li>
            </ul>
        </section>
    </div>
    <div class="container footer-bottom">
        <small>&copy; <?php echo esc_html(wp_date('Y')); ?> HimeDoll. All rights reserved.</small>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>

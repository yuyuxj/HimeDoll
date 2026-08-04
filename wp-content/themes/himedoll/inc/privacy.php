<?php
defined('ABSPATH') || exit;

add_action('wp_footer', function (): void {
    ?>
    <div class="age-gate" hidden data-age-gate>
        <div class="age-gate__panel">
            <p class="eyebrow">Age confirmation</p>
            <h2>18歳以上ですか？</h2>
            <p>このサイトは成人向け商品を取り扱っています。</p>
            <div class="age-gate__actions">
                <button type="button" data-age-accept>18歳以上です</button>
                <a href="https://www.google.co.jp/">退出する</a>
            </div>
        </div>
    </div>

    <div class="cookie-bar" hidden data-cookie-bar>
        <p>当サイトでは利便性向上とアクセス解析のためCookieを使用します。</p>
        <div>
            <button type="button" data-cookie-accept>同意する</button>
            <a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>">詳細</a>
        </div>
    </div>
    <?php
});

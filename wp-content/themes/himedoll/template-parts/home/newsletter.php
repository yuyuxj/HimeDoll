<?php
defined('ABSPATH') || exit;
?>
<section class="newsletter">
    <div class="container newsletter__inner">
        <div>
            <p class="eyebrow">HimeDoll News</p>
            <h2>新商品・入荷情報をお届けします。</h2>
            <p>登録解除はいつでも可能です。</p>
        </div>

        <form class="newsletter-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="himedoll_newsletter_signup">
            <?php wp_nonce_field('himedoll_newsletter_signup', 'hd_newsletter_nonce'); ?>
            <label class="screen-reader-text" for="newsletter-email">メールアドレス</label>
            <input id="newsletter-email" type="email" name="email" required placeholder="メールアドレス">
            <button class="button button--primary" type="submit">登録する</button>
        </form>
    </div>
</section>

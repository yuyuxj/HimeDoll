<?php
defined('ABSPATH') || exit;
get_header();
?>
<main id="main-content">
    <section class="hero">
        <div class="container hero__content">
            <p class="eyebrow">HimeDoll Japan</p>
            <h1>あなたの理想を、リアルに。</h1>
            <p class="hero__lead">日本のお客様のために設計された、安心・高品質な専門ストア。</p>
            <div class="hero__actions">
                <a class="button button--primary" href="<?php echo esc_url(home_url('/shop/')); ?>">商品を見る</a>
                <a class="button button--secondary" href="#features">初めての方へ</a>
            </div>
        </div>
    </section>

    <section id="features" class="trust-strip">
        <div class="container trust-grid">
            <div><strong>全国送料無料</strong><span>対象地域へ安心配送</span></div>
            <div><strong>匿名配送</strong><span>プライバシーに配慮</span></div>
            <div><strong>品質保証</strong><span>購入後も日本語対応</span></div>
            <div><strong>安全決済</strong><span>安心できる決済環境</span></div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-heading">
                <p class="eyebrow">Popular</p>
                <h2>人気商品</h2>
            </div>
            <?php if (shortcode_exists('products')) : ?>
                <?php echo do_shortcode('[products limit="8" columns="4" orderby="popularity"]'); ?>
            <?php else : ?>
                <p>WooCommerce を有効化すると商品が表示されます。</p>
            <?php endif; ?>
        </div>
    </section>

    <section class="section section--muted">
        <div class="container">
            <div class="section-heading">
                <p class="eyebrow">New arrivals</p>
                <h2>新着商品</h2>
            </div>
            <?php if (shortcode_exists('products')) : ?>
                <?php echo do_shortcode('[products limit="8" columns="4" orderby="date"]'); ?>
            <?php endif; ?>
        </div>
    </section>

    <section class="section">
        <div class="container category-grid">
            <a class="category-card" href="#"><span>TPE</span></a>
            <a class="category-card" href="#"><span>シリコン</span></a>
            <a class="category-card" href="#"><span>AIシリーズ</span></a>
        </div>
    </section>
</main>
<?php get_footer(); ?>

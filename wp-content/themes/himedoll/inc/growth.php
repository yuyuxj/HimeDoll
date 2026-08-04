<?php
defined('ABSPATH') || exit;

add_action('wp_head', function (): void {
    $gtm = trim((string) get_option('hd_gtm_id'));
    $ga4 = trim((string) get_option('hd_ga4_id'));

    if ($gtm) {
        ?>
        <script>
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','<?php echo esc_js($gtm); ?>');
        </script>
        <?php
    } elseif ($ga4) {
        ?>
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr($ga4); ?>"></script>
        <script>
        window.dataLayer=window.dataLayer||[];
        function gtag(){dataLayer.push(arguments);}
        gtag('js',new Date());
        gtag('config','<?php echo esc_js($ga4); ?>');
        </script>
        <?php
    }
}, 1);

add_action('wp_body_open', function (): void {
    $gtm = trim((string) get_option('hd_gtm_id'));
    if (!$gtm) return;
    ?>
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo esc_attr($gtm); ?>"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <?php
});

add_shortcode('himedoll_home_banners', function (): string {
    $query = new WP_Query([
        'post_type' => 'hd_home_banner',
        'post_status' => 'publish',
        'posts_per_page' => 6,
        'orderby' => 'menu_order',
        'order' => 'ASC',
    ]);

    if (!$query->have_posts()) return '';

    ob_start();
    ?>
    <section class="hd-home-banners">
        <div class="container hd-home-banners__grid">
            <?php while ($query->have_posts()) : $query->the_post();
                $url = get_post_meta(get_the_ID(), 'hd_banner_url', true);
                ?>
                <a class="hd-home-banner" href="<?php echo esc_url($url ?: '#'); ?>">
                    <?php if (has_post_thumbnail()) the_post_thumbnail('large'); ?>
                    <span class="hd-home-banner__overlay">
                        <strong><?php the_title(); ?></strong>
                        <small><?php echo esc_html(get_the_excerpt()); ?></small>
                    </span>
                </a>
            <?php endwhile; ?>
        </div>
    </section>
    <?php
    wp_reset_postdata();
    return ob_get_clean();
});

add_action('woocommerce_single_product_summary', function (): void {
    global $product;
    if (!$product instanceof WC_Product) return;
    ?>
    <button class="hd-compare-button"
            type="button"
            data-compare-product
            data-product-id="<?php echo esc_attr((string) $product->get_id()); ?>"
            data-product-name="<?php echo esc_attr($product->get_name()); ?>">
        商品を比較に追加
    </button>
    <?php
}, 40);

add_action('wp_footer', function (): void {
    ?>
    <div class="hd-compare-drawer" hidden data-compare-drawer>
        <div>
            <strong>商品比較</strong>
            <span data-compare-count>0</span>
        </div>
        <div data-compare-items></div>
        <a href="<?php echo esc_url(home_url('/compare/')); ?>">比較ページへ</a>
    </div>
    <?php
});

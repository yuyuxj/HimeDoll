<?php
defined('ABSPATH') || exit;

add_action('wp_footer', function (): void {
    $line_url = trim((string) get_option('hd_line_url'));
    $email = sanitize_email((string) get_option('hd_support_email'));

    if (!$line_url && !$email) {
        return;
    }
    ?>
    <div class="hd-contact-float" aria-label="お問い合わせ">
        <?php if ($line_url) : ?>
            <a class="hd-contact-float__line" href="<?php echo esc_url($line_url); ?>" target="_blank" rel="noopener">
                LINEで相談
            </a>
        <?php endif; ?>

        <?php if ($email) : ?>
            <a class="hd-contact-float__mail" href="mailto:<?php echo esc_attr($email); ?>">
                メール
            </a>
        <?php endif; ?>
    </div>
    <?php
});

add_action('woocommerce_single_product_summary', function (): void {
    $email = sanitize_email((string) get_option('hd_support_email'));

    if (!$email) {
        return;
    }

    global $product;
    if (!$product instanceof WC_Product) {
        return;
    }

    $subject = rawurlencode('商品についての相談：' . $product->get_name());
    ?>
    <a class="product-inquiry-link"
       href="mailto:<?php echo esc_attr($email); ?>?subject=<?php echo esc_attr($subject); ?>">
        この商品について相談する
    </a>
    <?php
}, 39);

add_action('woocommerce_before_main_content', function (): void {
    $promo = trim((string) get_option('hd_promotion_text'));

    if (!$promo || is_front_page()) {
        return;
    }
    ?>
    <div class="hd-promotion-bar">
        <div class="container"><?php echo esc_html($promo); ?></div>
    </div>
    <?php
}, 2);

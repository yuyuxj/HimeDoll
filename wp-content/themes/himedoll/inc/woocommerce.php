<?php
defined('ABSPATH') || exit;

add_filter('loop_shop_columns', fn(): int => 3);
add_filter('loop_shop_per_page', fn(): int => 18);

remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);

add_action('woocommerce_before_shop_loop', function (): void {
    get_template_part('template-parts/archive/toolbar');
}, 20);

add_action('woocommerce_before_shop_loop_item_title', function (): void {
    global $product;
    if (!$product instanceof WC_Product) {
        return;
    }

    $brand = himedoll_product_brand($product->get_id());
    if ($brand) {
        echo '<span class="product-brand-badge">' . esc_html($brand->name) . '</span>';
    }
}, 8);

remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);

add_action('woocommerce_single_product_summary', function (): void {
    get_template_part('template-parts/product/summary-meta');
}, 6);

add_action('woocommerce_single_product_summary', function (): void {
    get_template_part('template-parts/product/review-summary');
}, 12);

add_action('woocommerce_single_product_summary', function (): void {
    get_template_part('template-parts/product/delivery-card');
}, 25);

add_action('woocommerce_single_product_summary', function (): void {
    get_template_part('template-parts/product/actions');
}, 32);

add_action('woocommerce_single_product_summary', function (): void {
    get_template_part('template-parts/product/trust');
}, 38);

add_action('woocommerce_after_single_product_summary', function (): void {
    get_template_part('template-parts/product/content-tabs');
}, 7);

add_action('woocommerce_after_single_product_summary', function (): void {
    get_template_part('template-parts/product/specifications');
}, 8);

add_action('woocommerce_after_single_product_summary', function (): void {
    comments_template();
}, 14);

add_action('woocommerce_after_single_product_summary', function (): void {
    get_template_part('template-parts/product/related-brand');
}, 18);

add_filter('comment_text', function (string $comment_text, ?WP_Comment $comment = null): string {
    if (!$comment || get_post_type($comment->comment_post_ID) !== 'product') {
        return $comment_text;
    }

    $verified = wc_customer_bought_product(
        $comment->comment_author_email,
        $comment->user_id,
        $comment->comment_post_ID
    );

    if ($verified) {
        $comment_text = '<span class="verified-purchase">✓ 購入済み</span>' . $comment_text;
    }

    return $comment_text;
}, 10, 2);

add_action('woocommerce_thankyou', function (int $order_id): void {
    if (!$order_id) {
        return;
    }
    ?>
    <section class="hd-thankyou">
        <p class="eyebrow">Thank you</p>
        <h2>ご注文ありがとうございます。</h2>
        <p>確認メールを送信しました。発送準備が整い次第、改めてご案内します。</p>
        <div class="hd-thankyou__links">
            <a href="<?php echo esc_url(wc_get_account_endpoint_url('orders')); ?>">注文履歴を見る</a>
            <a href="<?php echo esc_url(home_url('/faq/')); ?>">よくあるご質問</a>
        </div>
    </section>
    <?php
}, 5);

add_filter('woocommerce_email_footer_text', function (): string {
    return 'HimeDoll｜匿名配送・日本語サポート';
});

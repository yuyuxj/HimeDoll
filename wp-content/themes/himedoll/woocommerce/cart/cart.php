<?php
defined('ABSPATH') || exit;

do_action('woocommerce_before_cart');
?>
<section class="cart-page">
    <div class="cart-page__header">
        <p class="eyebrow">Shopping cart</p>
        <h1>ショッピングカート</h1>
        <p>商品内容をご確認のうえ、購入手続きへお進みください。</p>
    </div>

    <form class="woocommerce-cart-form cart-layout" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
        <section class="cart-items">
            <?php do_action('woocommerce_before_cart_table'); ?>

            <?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) :
                $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);

                if (!$_product || !$_product->exists() || $cart_item['quantity'] <= 0) {
                    continue;
                }

                $product_permalink = apply_filters(
                    'woocommerce_cart_item_permalink',
                    $_product->is_visible() ? $_product->get_permalink($cart_item) : '',
                    $cart_item,
                    $cart_item_key
                );
                ?>
                <article class="cart-item">
                    <div class="cart-item__image">
                        <?php
                        $thumbnail = apply_filters(
                            'woocommerce_cart_item_thumbnail',
                            $_product->get_image('woocommerce_thumbnail'),
                            $cart_item,
                            $cart_item_key
                        );

                        echo $product_permalink
                            ? '<a href="' . esc_url($product_permalink) . '">' . $thumbnail . '</a>'
                            : $thumbnail;
                        ?>
                    </div>

                    <div class="cart-item__content">
                        <div class="cart-item__top">
                            <div>
                                <h2>
                                    <?php
                                    echo $product_permalink
                                        ? '<a href="' . esc_url($product_permalink) . '">' . wp_kses_post($_product->get_name()) . '</a>'
                                        : wp_kses_post($_product->get_name());
                                    ?>
                                </h2>
                                <?php echo wc_get_formatted_cart_item_data($cart_item); ?>
                            </div>

                            <div class="cart-item__remove">
                                <?php
                                echo apply_filters(
                                    'woocommerce_cart_item_remove_link',
                                    sprintf(
                                        '<a href="%s" aria-label="%s">削除</a>',
                                        esc_url(wc_get_cart_remove_url($cart_item_key)),
                                        esc_attr__('Remove this item', 'woocommerce')
                                    ),
                                    $cart_item_key
                                );
                                ?>
                            </div>
                        </div>

                        <div class="cart-item__bottom">
                            <div class="cart-item__quantity">
                                <span>数量</span>
                                <?php
                                if ($_product->is_sold_individually()) {
                                    echo '1';
                                } else {
                                    echo woocommerce_quantity_input(
                                        [
                                            'input_name'   => "cart[{$cart_item_key}][qty]",
                                            'input_value'  => $cart_item['quantity'],
                                            'max_value'    => $_product->get_max_purchase_quantity(),
                                            'min_value'    => 0,
                                            'product_name' => $_product->get_name(),
                                        ],
                                        $_product,
                                        false
                                    );
                                }
                                ?>
                            </div>

                            <div class="cart-item__price">
                                <?php
                                echo wp_kses_post(
                                    WC()->cart->get_product_subtotal($_product, $cart_item['quantity'])
                                );
                                ?>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>

            <div class="cart-actions">
                <?php if (wc_coupons_enabled()) : ?>
                    <div class="coupon">
                        <label class="screen-reader-text" for="coupon_code">クーポンコード</label>
                        <input type="text" name="coupon_code" id="coupon_code" value="" placeholder="クーポンコード">
                        <button type="submit" class="button" name="apply_coupon" value="1">適用</button>
                    </div>
                <?php endif; ?>

                <button type="submit" class="button cart-update" name="update_cart" value="1">
                    カートを更新
                </button>

                <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
            </div>

            <?php do_action('woocommerce_after_cart_table'); ?>
        </section>

        <aside class="cart-summary">
            <?php do_action('woocommerce_before_cart_collaterals'); ?>
            <?php woocommerce_cart_totals(); ?>

            <div class="cart-summary__trust">
                <div><strong>匿名配送</strong><span>商品内容が分からない梱包</span></div>
                <div><strong>安全決済</strong><span>暗号化された決済環境</span></div>
                <div><strong>日本語対応</strong><span>購入前後の相談に対応</span></div>
            </div>
        </aside>
    </form>
</section>
<?php do_action('woocommerce_after_cart'); ?>

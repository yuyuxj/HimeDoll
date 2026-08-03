<?php
defined('ABSPATH') || exit;

if (!$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()) {
    echo esc_html(apply_filters(
        'woocommerce_checkout_must_be_logged_in_message',
        __('You must be logged in to checkout.', 'woocommerce')
    ));
    return;
}

do_action('woocommerce_before_checkout_form', $checkout);
?>
<section class="checkout-page">
    <div class="checkout-page__header">
        <p class="eyebrow">Secure checkout</p>
        <h1>ご購入手続き</h1>
        <p>お届け先とお支払い方法をご確認ください。</p>
    </div>

    <form name="checkout" method="post" class="checkout woocommerce-checkout checkout-layout"
          action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">

        <section class="checkout-fields">
            <?php if ($checkout->get_checkout_fields()) : ?>
                <?php do_action('woocommerce_checkout_before_customer_details'); ?>

                <div id="customer_details">
                    <div class="checkout-card">
                        <div class="checkout-card__heading">
                            <span>1</span>
                            <h2>お届け先情報</h2>
                        </div>
                        <?php do_action('woocommerce_checkout_billing'); ?>
                    </div>

                    <div class="checkout-card">
                        <div class="checkout-card__heading">
                            <span>2</span>
                            <h2>配送先・ご要望</h2>
                        </div>
                        <?php do_action('woocommerce_checkout_shipping'); ?>
                    </div>
                </div>

                <?php do_action('woocommerce_checkout_after_customer_details'); ?>
            <?php endif; ?>
        </section>

        <aside class="checkout-review">
            <div class="checkout-card checkout-card--sticky">
                <div class="checkout-card__heading">
                    <span>3</span>
                    <h2>ご注文内容</h2>
                </div>

                <?php do_action('woocommerce_checkout_before_order_review_heading'); ?>
                <?php do_action('woocommerce_checkout_before_order_review'); ?>

                <div id="order_review" class="woocommerce-checkout-review-order">
                    <?php do_action('woocommerce_checkout_order_review'); ?>
                </div>

                <?php do_action('woocommerce_checkout_after_order_review'); ?>
            </div>
        </aside>
    </form>
</section>
<?php do_action('woocommerce_after_checkout_form', $checkout); ?>

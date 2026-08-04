<?php
defined('ABSPATH') || exit;

add_shortcode('himedoll_campaigns', function (): string {
    $query = new WP_Query([
        'post_type' => 'hd_campaign',
        'post_status' => 'publish',
        'posts_per_page' => 6,
        'orderby' => 'menu_order',
        'order' => 'ASC',
    ]);

    if (!$query->have_posts()) {
        return '';
    }

    ob_start();
    ?>
    <section class="hd-campaigns">
        <div class="container">
            <div class="section-heading">
                <p class="eyebrow">Campaign</p>
                <h2>キャンペーン</h2>
            </div>

            <div class="hd-campaigns__grid">
                <?php while ($query->have_posts()) : $query->the_post();
                    $coupon = get_post_meta(get_the_ID(), 'hd_campaign_coupon', true);
                    $deadline = get_post_meta(get_the_ID(), 'hd_campaign_deadline', true);
                    ?>
                    <article class="hd-campaign-card">
                        <?php if (has_post_thumbnail()) the_post_thumbnail('large'); ?>
                        <div class="hd-campaign-card__body">
                            <h3><?php the_title(); ?></h3>
                            <div><?php the_excerpt(); ?></div>

                            <?php if ($coupon) : ?>
                                <button type="button"
                                        class="hd-coupon-copy"
                                        data-coupon="<?php echo esc_attr($coupon); ?>">
                                    クーポン：<?php echo esc_html($coupon); ?>
                                </button>
                            <?php endif; ?>

                            <?php if ($deadline) : ?>
                                <div class="hd-campaign-deadline"
                                     data-promo-deadline="<?php echo esc_attr($deadline); ?>">
                                    残り <strong data-promo-countdown>計算中</strong>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
        </div>
    </section>
    <?php
    wp_reset_postdata();

    return ob_get_clean();
});

add_filter('robots_txt', function (string $output, bool $public): string {
    if (!$public) {
        return "User-agent: *
Disallow: /
";
    }

    $output .= "
User-agent: *
";
    $output .= "Disallow: /wp-admin/
";
    $output .= "Allow: /wp-admin/admin-ajax.php
";
    $output .= "Disallow: /cart/
";
    $output .= "Disallow: /checkout/
";
    $output .= "Disallow: /my-account/
";
    $output .= "Sitemap: " . home_url('/wp-sitemap.xml') . "
";

    return $output;
}, 10, 2);

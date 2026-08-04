<?php
defined('ABSPATH') || exit;

add_action('wp_footer', function (): void {
    if (!is_search()) {
        return;
    }
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.querySelector('input[name="s"]');
        if (!input) return;

        const box = document.createElement('div');
        box.className = 'hd-search-suggestions';
        input.parentNode.style.position = 'relative';
        input.parentNode.appendChild(box);

        let timer = null;

        input.addEventListener('input', function () {
            clearTimeout(timer);
            const q = input.value.trim();

            if (q.length < 2) {
                box.innerHTML = '';
                box.hidden = true;
                return;
            }

            timer = setTimeout(async function () {
                try {
                    const response = await fetch(
                        '<?php echo esc_url(rest_url('himedoll/v1/search-suggestions')); ?>?q=' +
                        encodeURIComponent(q)
                    );
                    const data = await response.json();

                    box.innerHTML = (data.items || []).map(function (item) {
                        return '<a href="' + item.url + '">' + item.title + '</a>';
                    }).join('');
                    box.hidden = !data.items || data.items.length === 0;
                } catch (error) {
                    box.hidden = true;
                }
            }, 250);
        });
    });
    </script>
    <?php
});

add_action('woocommerce_after_single_product_summary', function (): void {
    global $product;
    if (!$product instanceof WC_Product) {
        return;
    }

    $guide_ids = array_filter(array_map(
        'absint',
        (array) get_post_meta($product->get_id(), 'hd_related_guides', true)
    ));

    if (!$guide_ids) {
        return;
    }

    $query = new WP_Query([
        'post_type' => 'hd_buying_guide',
        'post__in' => $guide_ids,
        'orderby' => 'post__in',
        'posts_per_page' => 4,
    ]);

    if (!$query->have_posts()) {
        return;
    }
    ?>
    <section class="hd-related-guides">
        <div class="section-heading">
            <p class="eyebrow">Guide</p>
            <h2>この商品に関連するガイド</h2>
        </div>

        <div class="hd-related-guides__grid">
            <?php while ($query->have_posts()) : $query->the_post(); ?>
                <article>
                    <a href="<?php the_permalink(); ?>">
                        <?php if (has_post_thumbnail()) the_post_thumbnail('medium_large'); ?>
                        <h3><?php the_title(); ?></h3>
                        <p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 28)); ?></p>
                    </a>
                </article>
            <?php endwhile; ?>
        </div>
    </section>
    <?php
    wp_reset_postdata();
}, 22);

<?php
defined('ABSPATH') || exit;

add_action('wp_head', function (): void {
    echo '<link rel="manifest" href="' . esc_url(home_url('/wp-json/himedoll/v1/manifest')) . '">' . "\n";
    echo '<meta name="theme-color" content="#111111">' . "\n";
    echo '<meta name="apple-mobile-web-app-capable" content="yes">' . "\n";
});

add_action('wp_footer', function (): void {
    ?>
    <script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('<?php echo esc_url(home_url('/?hd_service_worker=1')); ?>')
                .catch(function (error) {
                    console.warn('HimeDoll service worker registration failed', error);
                });
        });
    }
    </script>
    <?php
});

add_action('template_redirect', function (): void {
    if (!isset($_GET['hd_service_worker'])) {
        return;
    }

    header('Content-Type: application/javascript; charset=UTF-8');
    header('Service-Worker-Allowed: /');
    ?>
    const CACHE = 'himedoll-v5';
    const CORE = [
      '<?php echo esc_js(home_url('/')); ?>',
      '<?php echo esc_js(home_url('/shop/')); ?>'
    ];

    self.addEventListener('install', event => {
      event.waitUntil(caches.open(CACHE).then(cache => cache.addAll(CORE)));
      self.skipWaiting();
    });

    self.addEventListener('activate', event => {
      event.waitUntil(
        caches.keys().then(keys => Promise.all(
          keys.filter(key => key !== CACHE).map(key => caches.delete(key))
        ))
      );
      self.clients.claim();
    });

    self.addEventListener('fetch', event => {
      if (event.request.method !== 'GET') return;
      event.respondWith(
        fetch(event.request).catch(() => caches.match(event.request))
      );
    });
    <?php
    exit;
});

add_action('woocommerce_account_dashboard', function (): void {
    if (!is_user_logged_in()) {
        return;
    }

    $user_id = get_current_user_id();
    $points = (int) get_user_meta($user_id, 'hd_loyalty_points', true);
    $tier = (string) get_user_meta($user_id, 'hd_membership_tier', true);
    $referral = (string) get_user_meta($user_id, 'hd_referral_code', true);

    if (!$referral && class_exists('HimeDoll_Referral')) {
        $referral = HimeDoll_Referral::instance()->ensure_code($user_id);
    }
    ?>
    <section class="hd-enterprise-account">
        <div>
            <span>保有ポイント</span>
            <strong><?php echo esc_html(number_format_i18n($points)); ?></strong>
        </div>
        <div>
            <span>会員ランク</span>
            <strong><?php echo esc_html($tier ?: 'Bronze'); ?></strong>
        </div>
        <div>
            <span>紹介コード</span>
            <strong><?php echo esc_html($referral); ?></strong>
        </div>
    </section>
    <?php
});

<?php
defined('ABSPATH') || exit;

final class HimeDoll_Enterprise_Dashboard {
    private static ?self $instance = null;

    public static function instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        add_action('admin_menu', [$this, 'menu']);
    }

    public function menu(): void {
        add_submenu_page(
            'himedoll-settings',
            '企业总览',
            '企业总览',
            'manage_woocommerce',
            'himedoll-enterprise-dashboard',
            [$this, 'render']
        );
    }

    public function render(): void {
        if (!current_user_can('manage_woocommerce')) {
            return;
        }

        $users = get_users(['number' => 1000, 'fields' => 'ids']);
        $tiers = ['Bronze' => 0, 'Silver' => 0, 'Gold' => 0, 'VIP' => 0];
        $points = 0;
        $referrals = 0.0;

        foreach ($users as $user_id) {
            $tier = (string) get_user_meta($user_id, 'hd_membership_tier', true) ?: 'Bronze';
            $tiers[$tier] = ($tiers[$tier] ?? 0) + 1;
            $points += (int) get_user_meta($user_id, 'hd_loyalty_points', true);

            foreach ((array) get_user_meta($user_id, 'hd_referral_ledger', true) as $row) {
                $referrals += (float) ($row['amount'] ?? 0);
            }
        }
        ?>
        <div class="wrap">
            <h1>HimeDoll 企业总览</h1>

            <div style="display:grid;grid-template-columns:repeat(4,minmax(160px,1fr));gap:16px;max-width:1100px">
                <div style="background:#fff;border:1px solid #ddd;padding:20px"><strong style="font-size:28px"><?php echo esc_html((string) count($users)); ?></strong><p>会员数</p></div>
                <div style="background:#fff;border:1px solid #ddd;padding:20px"><strong style="font-size:28px"><?php echo esc_html(number_format_i18n($points)); ?></strong><p>未使用积分</p></div>
                <div style="background:#fff;border:1px solid #ddd;padding:20px"><strong style="font-size:28px"><?php echo wp_kses_post(wc_price($referrals)); ?></strong><p>推荐返佣累计</p></div>
                <div style="background:#fff;border:1px solid #ddd;padding:20px"><strong style="font-size:28px"><?php echo esc_html((string) ($tiers['VIP'] ?? 0)); ?></strong><p>VIP 会员</p></div>
            </div>

            <h2>会员等级</h2>
            <table class="widefat striped" style="max-width:700px">
                <thead><tr><th>等级</th><th>人数</th></tr></thead>
                <tbody>
                <?php foreach ($tiers as $tier => $count) : ?>
                    <tr><td><?php echo esc_html($tier); ?></td><td><?php echo esc_html((string) $count); ?></td></tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}

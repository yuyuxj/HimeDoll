<?php
defined('ABSPATH') || exit;

final class HimeDoll_Checkout_Privacy {
    private static ?self $instance = null;
    public static function instance(): self { return self::$instance ??= new self(); }

    private function __construct() {
        add_action('woocommerce_after_order_notes', [$this, 'fields']);
        add_action('woocommerce_checkout_process', [$this, 'validate']);
        add_action('woocommerce_checkout_create_order', [$this, 'save'], 10, 2);
        add_action('woocommerce_admin_order_data_after_shipping_address', [$this, 'admin_summary']);
        add_action('woocommerce_order_details_after_order_table', [$this, 'customer_summary']);
    }

    public function fields($checkout): void {
        if (!function_exists('woocommerce_form_field')) return;
        echo '<div id="himedoll-delivery-options"><h3>配送・プライバシー設定</h3>';
        woocommerce_form_field('hd_discreet_packaging', [
            'type' => 'checkbox', 'class' => ['form-row-wide'],
            'label' => '無地梱包・品名を伏せて配送を希望する',
        ], $checkout->get_value('hd_discreet_packaging') ?: '1');
        woocommerce_form_field('hd_delivery_date', [
            'type' => 'date', 'class' => ['form-row-first'], 'label' => '配送希望日（任意）',
            'custom_attributes' => ['min' => wp_date('Y-m-d', strtotime('+3 days'))],
        ], $checkout->get_value('hd_delivery_date'));
        woocommerce_form_field('hd_delivery_slot', [
            'type' => 'select', 'class' => ['form-row-last'], 'label' => '配送時間帯（任意）',
            'options' => ['' => '指定なし', 'morning' => '午前中', '14-16' => '14:00〜16:00', '16-18' => '16:00〜18:00', '18-20' => '18:00〜20:00', '19-21' => '19:00〜21:00'],
        ], $checkout->get_value('hd_delivery_slot'));
        woocommerce_form_field('hd_age_confirmed', [
            'type' => 'checkbox', 'class' => ['form-row-wide validate-required'], 'required' => true,
            'label' => '私は18歳以上で、利用規約とプライバシーポリシーに同意します',
        ], $checkout->get_value('hd_age_confirmed'));
        echo '<div class="clear"></div></div>';
    }

    public function validate(): void {
        if (empty($_POST['hd_age_confirmed'])) {
            wc_add_notice('ご注文には18歳以上であることの確認が必要です。', 'error');
        }
        if (!empty($_POST['hd_delivery_date'])) {
            $date = sanitize_text_field(wp_unslash($_POST['hd_delivery_date']));
            $timestamp = strtotime($date);
            if (!$timestamp || $timestamp < strtotime('today +3 days')) {
                wc_add_notice('配送希望日は3日後以降を指定してください。', 'error');
            }
        }
    }

    public function save($order, array $data): void {
        $order->update_meta_data('_hd_discreet_packaging', empty($_POST['hd_discreet_packaging']) ? 'no' : 'yes');
        $order->update_meta_data('_hd_age_confirmed', empty($_POST['hd_age_confirmed']) ? 'no' : 'yes');
        if (!empty($_POST['hd_delivery_date'])) $order->update_meta_data('_hd_delivery_date', sanitize_text_field(wp_unslash($_POST['hd_delivery_date'])));
        if (!empty($_POST['hd_delivery_slot'])) $order->update_meta_data('_hd_delivery_slot', sanitize_key(wp_unslash($_POST['hd_delivery_slot'])));
    }

    private function slot_label(string $slot): string {
        return ['morning'=>'午前中','14-16'=>'14:00〜16:00','16-18'=>'16:00〜18:00','18-20'=>'18:00〜20:00','19-21'=>'19:00〜21:00'][$slot] ?? '指定なし';
    }

    public function admin_summary($order): void {
        echo '<p><strong>無地梱包:</strong> ' . esc_html($order->get_meta('_hd_discreet_packaging') === 'yes' ? '希望する' : '希望しない') . '</p>';
        echo '<p><strong>配送希望:</strong> ' . esc_html(trim($order->get_meta('_hd_delivery_date') . ' ' . $this->slot_label((string)$order->get_meta('_hd_delivery_slot')))) . '</p>';
    }

    public function customer_summary($order): void {
        $date = (string)$order->get_meta('_hd_delivery_date');
        $slot = (string)$order->get_meta('_hd_delivery_slot');
        if (!$date && !$slot) return;
        echo '<section class="woocommerce-order-details"><h2>配送希望</h2><p>' . esc_html(trim($date . ' ' . $this->slot_label($slot))) . '</p></section>';
    }
}

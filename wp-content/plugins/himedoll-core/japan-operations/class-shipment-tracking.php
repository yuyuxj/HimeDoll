<?php
defined('ABSPATH') || exit;

final class HimeDoll_Shipment_Tracking {
    private static ?self $instance = null;
    public static function instance(): self { return self::$instance ??= new self(); }

    private function __construct() {
        add_action('add_meta_boxes_shop_order', [$this, 'meta_box']);
        add_action('woocommerce_process_shop_order_meta', [$this, 'save']);
        add_action('woocommerce_order_details_after_order_table', [$this, 'order_tracking']);
        add_shortcode('himedoll_tracking', [$this, 'shortcode']);
        add_action('woocommerce_email_after_order_table', [$this, 'email_tracking'], 20, 4);
    }

    public function carriers(): array {
        return [
            '' => '選択してください', 'yamato' => 'ヤマト運輸', 'sagawa' => '佐川急便',
            'japanpost' => '日本郵便', 'dhl' => 'DHL', 'fedex' => 'FedEx', 'ups' => 'UPS', 'ems' => 'EMS',
        ];
    }

    public function meta_box(): void {
        add_meta_box('himedoll-shipment', 'HimeDoll 配送追跡', [$this, 'render_box'], 'shop_order', 'side', 'high');
    }

    public function render_box($post): void {
        $order = wc_get_order($post->ID); if (!$order) return;
        wp_nonce_field('himedoll_save_shipment', 'himedoll_shipment_nonce');
        $carrier = (string)$order->get_meta('_hd_tracking_carrier');
        echo '<p><label>配送会社</label><select name="hd_tracking_carrier" style="width:100%">';
        foreach ($this->carriers() as $key=>$label) echo '<option value="'.esc_attr($key).'" '.selected($carrier,$key,false).'>'.esc_html($label).'</option>';
        echo '</select></p><p><label>追跡番号</label><input type="text" name="hd_tracking_number" value="'.esc_attr($order->get_meta('_hd_tracking_number')).'" style="width:100%"></p>';
        echo '<p><label>発送日</label><input type="date" name="hd_shipped_at" value="'.esc_attr($order->get_meta('_hd_shipped_at')).'" style="width:100%"></p>';
    }

    public function save(int $order_id): void {
        if (empty($_POST['himedoll_shipment_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['himedoll_shipment_nonce'])), 'himedoll_save_shipment')) return;
        if (!current_user_can('edit_shop_order', $order_id)) return;
        $order = wc_get_order($order_id); if (!$order) return;
        $old = (string)$order->get_meta('_hd_tracking_number');
        $carrier = isset($_POST['hd_tracking_carrier']) ? sanitize_key(wp_unslash($_POST['hd_tracking_carrier'])) : '';
        $number = isset($_POST['hd_tracking_number']) ? sanitize_text_field(wp_unslash($_POST['hd_tracking_number'])) : '';
        $date = isset($_POST['hd_shipped_at']) ? sanitize_text_field(wp_unslash($_POST['hd_shipped_at'])) : '';
        if (!array_key_exists($carrier, $this->carriers())) $carrier = '';
        $order->update_meta_data('_hd_tracking_carrier', $carrier);
        $order->update_meta_data('_hd_tracking_number', $number);
        $order->update_meta_data('_hd_shipped_at', $date);
        if ($number && $number !== $old) $order->add_order_note(sprintf('配送追跡を登録しました：%s %s', $this->carriers()[$carrier] ?? $carrier, $number), true);
        $order->save();
    }

    private function tracking_url(string $carrier, string $number): string {
        $n = rawurlencode($number);
        return match($carrier) {
            'yamato' => 'https://toi.kuronekoyamato.co.jp/cgi-bin/tneko?number00=1&number01='.$n,
            'sagawa' => 'https://k2k.sagawa-exp.co.jp/p/sagawa/web/okurijosearcheng.jsp?okurijoNo='.$n,
            'japanpost','ems' => 'https://trackings.post.japanpost.jp/services/srv/search/direct?reqCodeNo1='.$n,
            'dhl' => 'https://www.dhl.com/jp-ja/home/tracking.html?tracking-id='.$n,
            'fedex' => 'https://www.fedex.com/fedextrack/?trknbr='.$n,
            'ups' => 'https://www.ups.com/track?tracknum='.$n,
            default => '',
        };
    }

    private function html($order): string {
        $number = (string)$order->get_meta('_hd_tracking_number'); if (!$number) return '';
        $carrier = (string)$order->get_meta('_hd_tracking_carrier');
        $label = $this->carriers()[$carrier] ?? $carrier;
        $url = $this->tracking_url($carrier, $number);
        $number_html = $url ? '<a href="'.esc_url($url).'" target="_blank" rel="noopener">'.esc_html($number).'</a>' : esc_html($number);
        return '<div class="himedoll-tracking"><strong>配送会社：</strong>'.esc_html($label).'<br><strong>追跡番号：</strong>'.$number_html.'</div>';
    }

    public function order_tracking($order): void { $html=$this->html($order); if ($html) echo '<section class="woocommerce-order-details"><h2>配送追跡</h2>'.$html.'</section>'; }
    public function email_tracking($order, bool $sent_to_admin, bool $plain_text, $email): void { if (!$sent_to_admin && !$plain_text) echo wp_kses_post($this->html($order)); }

    public function shortcode(): string {
        if (!is_user_logged_in()) return '<p>追跡情報を確認するにはログインしてください。</p>';
        $orders = wc_get_orders(['customer_id'=>get_current_user_id(),'limit'=>10,'orderby'=>'date','order'=>'DESC']);
        ob_start(); echo '<div class="himedoll-tracking-list">';
        foreach ($orders as $order) { $html=$this->html($order); if ($html) echo '<article><h3>注文 #'.esc_html($order->get_order_number()).'</h3>'.$html.'</article>'; }
        echo '</div>'; return (string)ob_get_clean();
    }
}

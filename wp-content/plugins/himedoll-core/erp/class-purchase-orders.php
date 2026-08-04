<?php
defined('ABSPATH') || exit;

final class HimeDoll_Purchase_Orders {
    private static ?self $instance = null;
    public static function instance(): self { return self::$instance ??= new self(); }

    private function __construct() {
        add_action('init', [$this, 'register']);
        add_action('add_meta_boxes', [$this, 'meta_boxes']);
        add_action('save_post_hd_purchase_order', [$this, 'save']);
    }

    public function register(): void {
        register_post_type('hd_purchase_order', [
            'labels' => [
                'name' => '采购单',
                'singular_name' => '采购单',
                'add_new_item' => '添加采购单',
                'edit_item' => '编辑采购单',
            ],
            'public' => false,
            'show_ui' => true,
            'menu_icon' => 'dashicons-cart',
            'supports' => ['title','editor','custom-fields'],
        ]);
    }

    public function meta_boxes(): void {
        add_meta_box('hd_po_data', '采购信息', [$this, 'render'], 'hd_purchase_order', 'normal');
    }

    public function render(WP_Post $post): void {
        wp_nonce_field('hd_po_data', 'hd_po_nonce');
        $fields = [
            'hd_po_supplier' => '供应商',
            'hd_po_external_no' => '外部采购单号',
            'hd_po_product_name' => '商品名称',
            'hd_po_quantity' => '数量',
            'hd_po_unit_cost' => '单价',
            'hd_po_currency' => '币种',
            'hd_po_note' => '采购备注',
            'hd_po_tracking_no' => '采购物流单号',
            'hd_po_warehouse_note' => '仓库备注',
            'hd_po_status' => '状态',
            'hd_po_wc_order_id' => '匹配 WooCommerce 订单 ID',
        ];
        echo '<table class="form-table">';
        foreach ($fields as $key => $label) {
            $value = get_post_meta($post->ID, $key, true);
            echo '<tr><th><label for="' . esc_attr($key) . '">' . esc_html($label) . '</label></th>';
            echo '<td><input class="regular-text" id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '"></td></tr>';
        }
        echo '</table>';
    }

    public function save(int $post_id): void {
        if (!isset($_POST['hd_po_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['hd_po_nonce'])), 'hd_po_data')) return;
        if (!current_user_can('edit_post', $post_id)) return;

        $keys = [
            'hd_po_supplier','hd_po_external_no','hd_po_product_name','hd_po_quantity',
            'hd_po_unit_cost','hd_po_currency','hd_po_note','hd_po_tracking_no',
            'hd_po_warehouse_note','hd_po_status','hd_po_wc_order_id'
        ];

        foreach ($keys as $key) {
            if (isset($_POST[$key])) {
                update_post_meta($post_id, $key, sanitize_text_field(wp_unslash($_POST[$key])));
            }
        }

        HimeDoll_Order_Matcher::instance()->match_purchase_order($post_id);
    }
}

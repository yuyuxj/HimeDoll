<?php
defined('ABSPATH') || exit;

final class HimeDoll_Product_Fields {
    private static ?self $instance = null;

    public static function instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        add_action('woocommerce_product_options_general_product_data', [$this, 'render']);
        add_action('woocommerce_process_product_meta', [$this, 'save']);
    }

    public function render(): void {
        echo '<div class="options_group">';

        $fields = [
            'hd_height' => ['身長', '例：160cm'],
            'hd_weight' => ['重量', '例：35kg'],
            'hd_material' => ['素材', '例：シリコン'],
            'hd_cup' => ['カップ', '例：D'],
            'hd_skin' => ['肌色', '例：ナチュラル'],
            'hd_ai' => ['AI機能', '例：音声会話対応'],
            'hd_delivery' => ['納期目安', '例：7～14営業日'],
            'hd_warranty' => ['保証', '保証内容を入力'],
            'hd_package' => ['付属品・梱包内容', '付属品を入力'],
            'hd_care' => ['保管・お手入れ', '保管方法を入力'],
        ];

        foreach ($fields as $id => [$label, $placeholder]) {
            woocommerce_wp_text_input([
                'id' => $id,
                'label' => $label,
                'placeholder' => $placeholder,
                'desc_tip' => true,
            ]);
        }

        woocommerce_wp_text_input([
            'id' => 'hd_video_url',
            'label' => '商品動画URL',
            'placeholder' => 'https://',
            'type' => 'url',
            'desc_tip' => true,
        ]);

        echo '</div>';
    }

    public function save(int $post_id): void {
        $text_fields = [
            'hd_height', 'hd_weight', 'hd_material', 'hd_cup',
            'hd_skin', 'hd_ai', 'hd_delivery', 'hd_warranty',
            'hd_package', 'hd_care',
        ];

        foreach ($text_fields as $key) {
            if (isset($_POST[$key])) {
                update_post_meta(
                    $post_id,
                    $key,
                    sanitize_text_field(wp_unslash($_POST[$key]))
                );
            }
        }

        if (isset($_POST['hd_video_url'])) {
            update_post_meta(
                $post_id,
                'hd_video_url',
                esc_url_raw(wp_unslash($_POST['hd_video_url']))
            );
        }
    }
}

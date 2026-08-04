<?php
defined('ABSPATH') || exit;

final class HimeDoll_Product_Components {
    private static ?self $instance = null;
    public static function instance(): self { return self::$instance ??= new self(); }
    private function __construct() {
        add_action('init', [$this, 'register']);
        add_action('add_meta_boxes', [$this, 'meta_boxes']);
        add_action('save_post', [$this, 'save'], 10, 2);
    }
    public function register(): void {
        $types = [
            'hd_body' => ['ボディ', 'ボディ'],
            'hd_head' => ['ヘッド', 'ヘッド'],
            'hd_accessory' => ['アクセサリー', 'アクセサリー'],
        ];
        foreach ($types as $type => [$singular, $plural]) {
            register_post_type($type, [
                'labels' => ['name'=>$plural, 'singular_name'=>$singular, 'add_new_item'=>$singular.'を追加', 'edit_item'=>$singular.'を編集'],
                'public' => false, 'show_ui' => true, 'show_in_menu' => 'himedoll-product-intelligence',
                'supports' => ['title','editor','thumbnail'], 'show_in_rest' => true,
                'capability_type' => 'product', 'map_meta_cap' => true,
            ]);
        }
    }
    public function meta_boxes(): void {
        foreach (['hd_body','hd_head','hd_accessory'] as $type) {
            add_meta_box('hd_component_specs', 'コンポーネント仕様', [$this, 'render'], $type, 'normal', 'high');
        }
    }
    public function render(WP_Post $post): void {
        wp_nonce_field('hd_component_save', 'hd_component_nonce');
        $fields = [
            'component_code'=>'管理コード', 'manufacturer'=>'メーカー', 'material'=>'素材', 'height'=>'身長/サイズ',
            'weight'=>'重量', 'cup'=>'カップ', 'skin'=>'肌色', 'cost'=>'原価', 'lead_time'=>'標準納期',
        ];
        echo '<table class="form-table"><tbody>';
        foreach ($fields as $key=>$label) {
            $value = get_post_meta($post->ID, '_hd_'.$key, true);
            echo '<tr><th><label for="hd_'.$key.'">'.esc_html($label).'</label></th><td><input class="regular-text" id="hd_'.$key.'" name="hd_'.$key.'" value="'.esc_attr($value).'" /></td></tr>';
        }
        echo '</tbody></table>';
    }
    public function save(int $post_id, WP_Post $post): void {
        if (!in_array($post->post_type, ['hd_body','hd_head','hd_accessory'], true)) return;
        if (!isset($_POST['hd_component_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['hd_component_nonce'])), 'hd_component_save')) return;
        if (!current_user_can('edit_post', $post_id) || wp_is_post_revision($post_id)) return;
        foreach (['component_code','manufacturer','material','height','weight','cup','skin','cost','lead_time'] as $key) {
            $value = isset($_POST['hd_'.$key]) ? sanitize_text_field(wp_unslash($_POST['hd_'.$key])) : '';
            $value === '' ? delete_post_meta($post_id, '_hd_'.$key) : update_post_meta($post_id, '_hd_'.$key, $value);
        }
    }
}

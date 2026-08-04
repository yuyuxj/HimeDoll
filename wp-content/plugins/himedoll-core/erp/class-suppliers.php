<?php
defined('ABSPATH') || exit;

final class HimeDoll_Suppliers {
    private static ?self $instance = null;
    public static function instance(): self { return self::$instance ??= new self(); }

    private function __construct() {
        add_action('init', [$this, 'register']);
    }

    public function register(): void {
        register_post_type('hd_supplier', [
            'labels' => [
                'name' => '供应商',
                'singular_name' => '供应商',
                'add_new_item' => '添加供应商',
                'edit_item' => '编辑供应商',
            ],
            'public' => false,
            'show_ui' => true,
            'menu_icon' => 'dashicons-businessperson',
            'supports' => ['title','editor','custom-fields'],
        ]);
    }
}

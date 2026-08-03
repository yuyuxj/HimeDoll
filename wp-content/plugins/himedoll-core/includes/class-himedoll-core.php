<?php
defined('ABSPATH') || exit;
final class HimeDoll_Core {
    private static ?self $instance = null;
    public static function instance(): self { return self::$instance ??= new self(); }
    private function __construct() { add_action('init', [$this,'register_brand_taxonomy']); }
    public function register_brand_taxonomy(): void {
        if (!post_type_exists('product')) return;
        register_taxonomy('product_brand',['product'],[
            'labels'=>['name'=>'ブランド','singular_name'=>'ブランド','add_new_item'=>'ブランドを追加','edit_item'=>'ブランドを編集'],
            'public'=>true,'show_admin_column'=>true,'show_in_rest'=>true,'hierarchical'=>true,
            'rewrite'=>['slug'=>'brand'],
        ]);
    }
}

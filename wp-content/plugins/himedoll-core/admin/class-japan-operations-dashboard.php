<?php
defined('ABSPATH') || exit;
final class HimeDoll_Japan_Operations_Dashboard {
    private static ?self $instance = null;
    public static function instance(): self { return self::$instance ??= new self(); }
    private function __construct(){ add_action('admin_menu',[$this,'menu']); }
    public function menu(): void { add_submenu_page('himedoll-settings','日本運営','日本運営','manage_woocommerce','himedoll-japan-operations',[$this,'render']); }
    public function render(): void {
        if (!current_user_can('manage_woocommerce')) return;
        $shipping=wc_get_orders(['limit'=>1,'status'=>['processing','on-hold'],'return'=>'ids','paginate'=>true]);
        $tracked=wc_get_orders(['limit'=>1,'meta_key'=>'_hd_tracking_number','meta_compare'=>'EXISTS','return'=>'ids','paginate'=>true]);
        echo '<div class="wrap"><h1>HimeDoll 日本運営</h1><div style="display:grid;grid-template-columns:repeat(3,minmax(180px,1fr));gap:16px;max-width:900px">';
        echo '<div style="background:#fff;border:1px solid #ddd;padding:20px"><strong style="font-size:28px">'.esc_html((string)$shipping->total).'</strong><p>発送待ち注文</p></div>';
        echo '<div style="background:#fff;border:1px solid #ddd;padding:20px"><strong style="font-size:28px">'.esc_html((string)$tracked->total).'</strong><p>追跡登録済み</p></div>';
        echo '<div style="background:#fff;border:1px solid #ddd;padding:20px"><strong style="font-size:28px">18+</strong><p>年齢確認をCheckoutで必須化</p></div></div>';
        echo '<h2>運用フロー</h2><ol><li>注文画面で無地梱包と配送希望を確認</li><li>注文編集画面で配送会社・追跡番号・発送日を登録</li><li>注文メモと顧客画面へ追跡情報を自動表示</li></ol>';
        echo '<p>固定ページに <code>[himedoll_tracking]</code> を配置すると、ログイン顧客専用の追跡一覧を表示できます。</p></div>';
    }
}

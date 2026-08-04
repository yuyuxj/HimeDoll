<?php
defined('ABSPATH') || exit;

final class HimeDoll_Setup_Wizard {
    private static ?self $instance = null;

    public static function instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        add_action('admin_menu', [$this, 'menu']);
        add_action('admin_post_himedoll_create_pages', [$this, 'create_pages']);
    }

    public function menu(): void {
        add_submenu_page(
            'himedoll-settings',
            'HimeDoll 初期设置',
            '初期设置',
            'manage_options',
            'himedoll-setup',
            [$this, 'render']
        );
    }

    public function render(): void {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1>HimeDoll v1.0 初期设置</h1>
            <p>创建商城运行所需的基础页面。创建后请补充真实公司和经营者信息。</p>

            <?php if (isset($_GET['created'])) : ?>
                <div class="notice notice-success"><p>页面创建完成。</p></div>
            <?php endif; ?>

            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="himedoll_create_pages">
                <?php wp_nonce_field('himedoll_create_pages'); ?>
                <?php submit_button('创建必要页面'); ?>
            </form>

            <h2>上线前必须手动确认</h2>
            <ul>
                <li>特定商取引法页面中的经营者名称、地址、电话</li>
                <li>隐私政策和退换货条件</li>
                <li>支付渠道是否允许当前商品类别</li>
                <li>配送时间与保证内容</li>
            </ul>
        </div>
        <?php
    }

    public function create_pages(): void {
        if (!current_user_can('manage_options')) {
            wp_die('Permission denied.');
        }

        check_admin_referer('himedoll_create_pages');

        $pages = [
            'faq' => ['よくあるご質問', '<h2>よくあるご質問</h2><p>配送、支払い、保証に関する内容を入力してください。</p>'],
            'shipping' => ['配送について', '<h2>配送について</h2><p>匿名梱包、配送方法、納期を入力してください。</p>'],
            'payment' => ['お支払いについて', '<h2>お支払いについて</h2><p>利用可能な支払い方法を入力してください。</p>'],
            'warranty' => ['保証・返品', '<h2>保証・返品</h2><p>保証範囲、初期不良、返品条件を入力してください。</p>'],
            'about' => ['会社概要', '<h2>会社概要</h2><p>会社名、所在地、代表者、連絡先を入力してください。</p>'],
            'contact' => ['お問い合わせ', '<h2>お問い合わせ</h2><p>お問い合わせフォームを設置してください。</p>'],
            'privacy-policy' => ['プライバシーポリシー', '<h2>プライバシーポリシー</h2><p>個人情報の利用目的、管理、第三者提供、Cookieについて入力してください。</p>'],
            'terms' => ['利用規約', '<h2>利用規約</h2><p>サイト利用条件と禁止事項を入力してください。</p>'],
            'legal' => ['特定商取引法に基づく表記', '<h2>特定商取引法に基づく表記</h2><p><strong>必ず実際の販売事業者情報に置き換えてください。</strong></p>'],
            'wishlist' => ['お気に入り', '<p>お気に入り商品を表示します。</p>'],
        ];

        foreach ($pages as $slug => [$title, $content]) {
            $existing = get_page_by_path($slug);

            if ($existing) {
                continue;
            }

            $page_id = wp_insert_post([
                'post_title' => $title,
                'post_name' => $slug,
                'post_content' => $content,
                'post_status' => 'publish',
                'post_type' => 'page',
            ]);

            if ($slug === 'wishlist' && $page_id && !is_wp_error($page_id)) {
                update_post_meta($page_id, '_wp_page_template', 'page-wishlist.php');
            }
        }

        wp_safe_redirect(add_query_arg(
            ['page' => 'himedoll-setup', 'created' => '1'],
            admin_url('admin.php')
        ));
        exit;
    }
}

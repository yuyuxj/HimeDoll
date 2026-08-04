<?php
defined('ABSPATH') || exit;

final class HimeDoll_Product_Importer {
    private static ?self $instance = null;

    public static function instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        add_action('admin_menu', [$this, 'menu']);
        add_action('admin_post_himedoll_import_products', [$this, 'import']);
        add_action('admin_post_himedoll_download_sample_csv', [$this, 'download_sample']);
    }

    public function menu(): void {
        add_submenu_page(
            'himedoll-settings',
            '商品批量导入',
            '商品批量导入',
            'manage_woocommerce',
            'himedoll-product-import',
            [$this, 'render']
        );
    }

    public function render(): void {
        if (!current_user_can('manage_woocommerce')) {
            return;
        }

        $log = get_transient('himedoll_import_log_' . get_current_user_id());
        delete_transient('himedoll_import_log_' . get_current_user_id());
        ?>
        <div class="wrap">
            <h1>HimeDoll 商品批量导入</h1>
            <p>上传 UTF-8 CSV。SKU 相同的商品将被更新。</p>

            <?php if (is_array($log)) : ?>
                <div class="notice notice-<?php echo empty($log['errors']) ? 'success' : 'warning'; ?>">
                    <p>
                        创建：<?php echo esc_html((string) $log['created']); ?>，
                        更新：<?php echo esc_html((string) $log['updated']); ?>，
                        错误：<?php echo esc_html((string) count($log['errors'])); ?>
                    </p>
                    <?php if ($log['errors']) : ?>
                        <ul>
                            <?php foreach ($log['errors'] as $error) : ?>
                                <li><?php echo esc_html($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data"
                  action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="himedoll_import_products">
                <?php wp_nonce_field('himedoll_import_products'); ?>

                <table class="form-table">
                    <tr>
                        <th><label for="hd_csv">CSV 文件</label></th>
                        <td><input id="hd_csv" type="file" name="hd_csv" accept=".csv,text/csv" required></td>
                    </tr>
                    <tr>
                        <th>导入设置</th>
                        <td>
                            <label>
                                <input type="checkbox" name="download_images" value="1" checked>
                                下载远程图片
                            </label>
                            <br>
                            <label>
                                <input type="checkbox" name="generate_seo" value="1" checked>
                                空白时自动生成 SEO
                            </label>
                        </td>
                    </tr>
                </table>

                <?php submit_button('开始导入'); ?>
            </form>

            <p>
                <a class="button"
                   href="<?php echo esc_url(wp_nonce_url(
                       admin_url('admin-post.php?action=himedoll_download_sample_csv'),
                       'himedoll_download_sample_csv'
                   )); ?>">
                    下载示例 CSV
                </a>
            </p>
        </div>
        <?php
    }

    public function import(): void {
        if (!current_user_can('manage_woocommerce')) {
            wp_die('Permission denied.');
        }

        check_admin_referer('himedoll_import_products');

        if (
            empty($_FILES['hd_csv']['tmp_name']) ||
            !is_uploaded_file($_FILES['hd_csv']['tmp_name'])
        ) {
            wp_die('CSV file is missing.');
        }

        $handle = fopen($_FILES['hd_csv']['tmp_name'], 'rb');
        if (!$handle) {
            wp_die('Unable to read CSV.');
        }

        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            wp_die('CSV header is missing.');
        }

        $headers = array_map(
            static fn($value): string => sanitize_key((string) $value),
            $headers
        );

        $created = 0;
        $updated = 0;
        $errors = [];
        $row_number = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $row_number++;

            if (count($row) !== count($headers)) {
                $errors[] = "第 {$row_number} 行：字段数量不一致";
                continue;
            }

            $data = array_combine($headers, $row);

            try {
                $result = $this->upsert_product(
                    $data,
                    !empty($_POST['download_images']),
                    !empty($_POST['generate_seo'])
                );

                $result === 'created' ? $created++ : $updated++;
            } catch (Throwable $e) {
                $errors[] = "第 {$row_number} 行：" . $e->getMessage();
            }
        }

        fclose($handle);

        set_transient(
            'himedoll_import_log_' . get_current_user_id(),
            compact('created', 'updated', 'errors'),
            10 * MINUTE_IN_SECONDS
        );

        wp_safe_redirect(admin_url('admin.php?page=himedoll-product-import'));
        exit;
    }

    private function upsert_product(array $data, bool $download_images, bool $generate_seo): string {
        if (!class_exists('WC_Product_Simple')) {
            throw new RuntimeException('WooCommerce 未启用');
        }

        $sku = sanitize_text_field($data['sku'] ?? '');
        $name = sanitize_text_field($data['name'] ?? '');

        if (!$name) {
            throw new InvalidArgumentException('商品名称为空');
        }

        $existing_id = $sku ? wc_get_product_id_by_sku($sku) : 0;
        $product = $existing_id
            ? wc_get_product($existing_id)
            : new WC_Product_Simple();

        if (!$product) {
            throw new RuntimeException('无法加载商品');
        }

        $product->set_name($name);
        if ($sku) $product->set_sku($sku);
        $product->set_regular_price(wc_format_decimal($data['regular_price'] ?? ''));
        $product->set_sale_price(wc_format_decimal($data['sale_price'] ?? ''));
        $product->set_description(wp_kses_post($data['description'] ?? ''));
        $product->set_short_description(wp_kses_post($data['short_description'] ?? ''));

        if (($data['stock_quantity'] ?? '') !== '') {
            $product->set_manage_stock(true);
            $product->set_stock_quantity(max(0, absint($data['stock_quantity'])));
            $product->set_stock_status(absint($data['stock_quantity']) > 0 ? 'instock' : 'outofstock');
        }

        $status = sanitize_key($data['status'] ?? 'draft');
        $product->set_status(in_array($status, ['draft', 'publish', 'private'], true) ? $status : 'draft');

        $product_id = $product->save();

        $this->assign_brand($product_id, $data['brand'] ?? '');
        $this->assign_categories($product_id, $data['categories'] ?? '');
        $this->save_meta($product_id, $data);

        if ($download_images && !empty($data['image_url'])) {
            $this->attach_image($product_id, esc_url_raw($data['image_url']));
        }

        if ($generate_seo) {
            $this->generate_seo($product_id, $data);
        }

        return $existing_id ? 'updated' : 'created';
    }

    private function assign_brand(int $product_id, string $brand_name): void {
        $brand_name = sanitize_text_field($brand_name);
        if (!$brand_name) return;

        $term = term_exists($brand_name, 'product_brand');
        if (!$term) {
            $term = wp_insert_term($brand_name, 'product_brand');
        }

        if (!is_wp_error($term)) {
            wp_set_object_terms($product_id, [(int) $term['term_id']], 'product_brand');
        }
    }

    private function assign_categories(int $product_id, string $category_string): void {
        $names = array_filter(array_map('trim', explode('|', $category_string)));
        if (!$names) return;

        $term_ids = [];

        foreach ($names as $name) {
            $name = sanitize_text_field($name);
            $term = term_exists($name, 'product_cat');

            if (!$term) {
                $term = wp_insert_term($name, 'product_cat');
            }

            if (!is_wp_error($term)) {
                $term_ids[] = (int) $term['term_id'];
            }
        }

        if ($term_ids) {
            wp_set_object_terms($product_id, $term_ids, 'product_cat');
        }
    }

    private function save_meta(int $product_id, array $data): void {
        $map = [
            'height' => 'hd_height',
            'height_numeric' => 'hd_height_numeric',
            'weight' => 'hd_weight',
            'material' => 'hd_material',
            'cup' => 'hd_cup',
            'skin' => 'hd_skin',
            'ai' => 'hd_ai',
            'delivery' => 'hd_delivery',
            'warranty' => 'hd_warranty',
            'seo_title' => 'hd_seo_title',
            'seo_description' => 'hd_seo_description',
        ];

        foreach ($map as $csv_key => $meta_key) {
            if (isset($data[$csv_key]) && $data[$csv_key] !== '') {
                update_post_meta(
                    $product_id,
                    $meta_key,
                    sanitize_text_field($data[$csv_key])
                );
            }
        }
    }

    private function generate_seo(int $product_id, array $data): void {
        $name = sanitize_text_field($data['name'] ?? '');
        $brand = sanitize_text_field($data['brand'] ?? 'HimeDoll');
        $material = sanitize_text_field($data['material'] ?? '');
        $height = sanitize_text_field($data['height'] ?? '');

        if (!get_post_meta($product_id, 'hd_seo_title', true)) {
            $parts = array_filter([$name, $height, strtoupper($material), $brand]);
            update_post_meta($product_id, 'hd_seo_title', implode('｜', $parts));
        }

        if (!get_post_meta($product_id, 'hd_seo_description', true)) {
            $description = sprintf(
                '%sの商品情報。身長%s、素材%s。匿名配送、日本語サポート対応。',
                $name,
                $height ?: '各種',
                $material ?: '各種'
            );
            update_post_meta($product_id, 'hd_seo_description', $description);
        }
    }

    private function attach_image(int $product_id, string $url): void {
        if (!$url) return;

        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $attachment_id = media_sideload_image($url, $product_id, null, 'id');

        if (!is_wp_error($attachment_id)) {
            set_post_thumbnail($product_id, $attachment_id);
        }
    }

    public function download_sample(): void {
        if (!current_user_can('manage_woocommerce')) {
            wp_die('Permission denied.');
        }

        check_admin_referer('himedoll_download_sample_csv');

        nocache_headers();
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="himedoll-products-sample.csv"');

        $out = fopen('php://output', 'wb');
        fwrite($out, "\xEF\xBB\xBF");

        fputcsv($out, [
            'sku','name','regular_price','sale_price','description',
            'short_description','stock_quantity','brand','categories',
            'image_url','height','height_numeric','weight','material',
            'cup','skin','ai','delivery','warranty','seo_title',
            'seo_description','status'
        ]);

        fputcsv($out, [
            'HD-001','サンプル商品','198000','188000','商品説明',
            '短い説明','5','HimeDoll','TPE|新商品',
            '','160cm','160','35kg','tpe',
            'D','ナチュラル','音声対応','7-14営業日','1年保証',
            '','','draft'
        ]);

        fclose($out);
        exit;
    }
}

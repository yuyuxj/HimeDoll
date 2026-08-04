<?php
defined('ABSPATH') || exit;

final class HimeDoll_AI_Product_Generator {
    private static ?self $instance = null;
    private HimeDoll_AI_Client $client;

    public static function instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        $this->client = new HimeDoll_AI_Client();
    }

    public function run(string $task, int $product_id): array {
        $product = wc_get_product($product_id);

        if (!$product) {
            return ['success' => false, 'error' => '商品不存在'];
        }

        $context = $this->product_context($product);
        $prompt = $this->build_prompt($task, $context);

        if (!$prompt) {
            return ['success' => false, 'error' => '不支持的 AI 任务'];
        }

        $result = $this->client->chat([
            [
                'role' => 'system',
                'content' => 'あなたは日本向けECサイトの編集者です。誇張、虚偽、医療効果の断定を避け、自然で読みやすい日本語を使用してください。',
            ],
            [
                'role' => 'user',
                'content' => $prompt,
            ],
        ], [
            'temperature' => 0.35,
            'max_tokens' => 1800,
        ]);

        HimeDoll_AI_Logger::instance()->log([
            'status' => $result['success'] ? 'success' : 'failed',
            'task' => $task,
            'product_id' => $product_id,
            'model' => $result['model'] ?? '',
            'error' => $result['error'] ?? '',
            'prompt_tokens' => absint($result['usage']['prompt_tokens'] ?? 0),
            'completion_tokens' => absint($result['usage']['completion_tokens'] ?? 0),
        ]);

        if (!$result['success']) {
            return $result;
        }

        return $this->save_result($task, $product_id, $result['content']);
    }

    private function product_context(WC_Product $product): string {
        $id = $product->get_id();

        $fields = [
            '商品名' => $product->get_name(),
            'SKU' => $product->get_sku(),
            '価格' => $product->get_price(),
            '身長' => get_post_meta($id, 'hd_height', true),
            '重量' => get_post_meta($id, 'hd_weight', true),
            '素材' => get_post_meta($id, 'hd_material', true),
            'カップ' => get_post_meta($id, 'hd_cup', true),
            '肌色' => get_post_meta($id, 'hd_skin', true),
            'AI機能' => get_post_meta($id, 'hd_ai', true),
            '納期' => get_post_meta($id, 'hd_delivery', true),
            '保証' => get_post_meta($id, 'hd_warranty', true),
        ];

        $lines = [];
        foreach ($fields as $label => $value) {
            if ($value !== '') {
                $lines[] = $label . '：' . $value;
            }
        }

        return implode("
", $lines);
    }

    private function build_prompt(string $task, string $context): string {
        $templates = [
            'description' => get_option(
                'hd_ai_prompt_description',
                "以下の商品情報を基に、日本向けECの商品説明を作成してください。
"
                . "見出し、特徴、仕様、配送上の注意を含めてください。

{context}"
            ),
            'seo' => get_option(
                'hd_ai_prompt_seo',
                "以下の商品情報を基に、SEOタイトル1件とMeta Description1件をJSONで出力してください。"
                . "キーは title と description にしてください。

{context}"
            ),
            'faq' => get_option(
                'hd_ai_prompt_faq',
                "以下の商品について、購入前によくある質問と回答を5件、JSON配列で出力してください。"
                . "各要素のキーは question と answer にしてください。

{context}"
            ),
            'alt' => get_option(
                'hd_ai_prompt_alt',
                "以下の商品情報を基に、商品画像用の自然な日本語ALTテキストを1文で作成してください。

{context}"
            ),
        ];

        if (!isset($templates[$task])) {
            return '';
        }

        return str_replace('{context}', $context, (string) $templates[$task]);
    }

    private function save_result(string $task, int $product_id, string $content): array {
        if ($task === 'description') {
            wp_update_post([
                'ID' => $product_id,
                'post_content' => wp_kses_post($content),
            ]);
        } elseif ($task === 'seo') {
            $json = json_decode($content, true);

            if (!is_array($json)) {
                return ['success' => false, 'error' => 'SEO 返回不是有效 JSON'];
            }

            update_post_meta(
                $product_id,
                'hd_seo_title',
                sanitize_text_field($json['title'] ?? '')
            );
            update_post_meta(
                $product_id,
                'hd_seo_description',
                sanitize_text_field($json['description'] ?? '')
            );
        } elseif ($task === 'faq') {
            $json = json_decode($content, true);

            if (!is_array($json)) {
                return ['success' => false, 'error' => 'FAQ 返回不是有效 JSON'];
            }

            update_post_meta($product_id, 'hd_ai_faq', wp_json_encode($json, JSON_UNESCAPED_UNICODE));
        } elseif ($task === 'alt') {
            $thumbnail_id = get_post_thumbnail_id($product_id);

            if (!$thumbnail_id) {
                return ['success' => false, 'error' => '商品没有主图'];
            }

            update_post_meta(
                $thumbnail_id,
                '_wp_attachment_image_alt',
                sanitize_text_field($content)
            );
        }

        return ['success' => true, 'content' => $content];
    }
}

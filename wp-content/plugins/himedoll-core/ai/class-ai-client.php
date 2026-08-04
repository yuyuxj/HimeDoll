<?php
defined('ABSPATH') || exit;

final class HimeDoll_AI_Client {
    public function chat(array $messages, array $options = []): array {
        $base_url = defined('HIMEDOLL_AI_BASE_URL')
            ? HIMEDOLL_AI_BASE_URL
            : get_option('hd_ai_base_url', 'https://api.openai.com/v1');

        $api_key = defined('HIMEDOLL_AI_API_KEY')
            ? HIMEDOLL_AI_API_KEY
            : get_option('hd_ai_api_key', '');

        $model = $options['model'] ?? get_option('hd_ai_model', 'gpt-4.1-mini');
        $timeout = absint(get_option('hd_ai_timeout', 60));

        if (!$api_key) {
            return [
                'success' => false,
                'error' => 'AI API Key 未设置',
            ];
        }

        $endpoint = untrailingslashit($base_url) . '/chat/completions';

        $payload = [
            'model' => sanitize_text_field($model),
            'messages' => $messages,
            'temperature' => isset($options['temperature'])
                ? (float) $options['temperature']
                : 0.4,
        ];

        if (!empty($options['max_tokens'])) {
            $payload['max_tokens'] = absint($options['max_tokens']);
        }

        $response = wp_remote_post($endpoint, [
            'timeout' => max(15, $timeout),
            'headers' => [
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json',
            ],
            'body' => wp_json_encode($payload),
        ]);

        if (is_wp_error($response)) {
            return [
                'success' => false,
                'error' => $response->get_error_message(),
            ];
        }

        $status = wp_remote_retrieve_response_code($response);
        $body = json_decode(wp_remote_retrieve_body($response), true);

        if ($status < 200 || $status >= 300) {
            return [
                'success' => false,
                'error' => $body['error']['message'] ?? ('HTTP ' . $status),
                'status' => $status,
            ];
        }

        $content = $body['choices'][0]['message']['content'] ?? '';

        if (!$content) {
            return [
                'success' => false,
                'error' => 'AI 返回内容为空',
            ];
        }

        return [
            'success' => true,
            'content' => trim((string) $content),
            'usage' => $body['usage'] ?? [],
            'model' => $body['model'] ?? $model,
        ];
    }
}

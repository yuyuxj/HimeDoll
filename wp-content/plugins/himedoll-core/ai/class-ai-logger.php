<?php
defined('ABSPATH') || exit;

final class HimeDoll_AI_Logger {
    private static ?self $instance = null;

    public static function instance(): self {
        return self::$instance ??= new self();
    }

    public function log(array $entry): void {
        $logs = (array) get_option('hd_ai_logs', []);

        $logs[] = array_merge([
            'created_at' => current_time('mysql'),
            'status' => 'unknown',
            'task' => '',
            'product_id' => 0,
            'model' => '',
            'error' => '',
            'prompt_tokens' => 0,
            'completion_tokens' => 0,
        ], $entry);

        $logs = array_slice($logs, -500);
        update_option('hd_ai_logs', $logs, false);
    }
}

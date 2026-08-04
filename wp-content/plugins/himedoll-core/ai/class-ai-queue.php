<?php
defined('ABSPATH') || exit;

final class HimeDoll_AI_Queue {
    private static ?self $instance = null;

    public static function instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        add_action('himedoll_ai_process_queue', [$this, 'process']);
        add_action('init', [$this, 'ensure_schedule']);
    }

    public function ensure_schedule(): void {
        if (!wp_next_scheduled('himedoll_ai_process_queue')) {
            wp_schedule_event(time() + 60, 'hourly', 'himedoll_ai_process_queue');
        }
    }

    public function add(string $task, int $product_id): void {
        $queue = (array) get_option('hd_ai_queue', []);

        $queue[] = [
            'id' => wp_generate_uuid4(),
            'task' => sanitize_key($task),
            'product_id' => absint($product_id),
            'attempts' => 0,
            'status' => 'pending',
            'created_at' => current_time('mysql'),
        ];

        update_option('hd_ai_queue', array_slice($queue, -1000), false);
    }

    public function process(): void {
        $queue = (array) get_option('hd_ai_queue', []);
        if (!$queue) return;

        $processed = 0;

        foreach ($queue as &$job) {
            if ($processed >= 5) break;
            if (($job['status'] ?? '') !== 'pending') continue;

            $job['attempts'] = absint($job['attempts'] ?? 0) + 1;

            try {
                $result = HimeDoll_AI_Product_Generator::instance()->run(
                    sanitize_key($job['task']),
                    absint($job['product_id'])
                );

                if ($result['success']) {
                    $job['status'] = 'completed';
                    $job['completed_at'] = current_time('mysql');
                } else {
                    $job['last_error'] = sanitize_text_field($result['error'] ?? 'Unknown error');
                    $job['status'] = $job['attempts'] >= 3 ? 'failed' : 'pending';
                }
            } catch (Throwable $e) {
                $job['last_error'] = $e->getMessage();
                $job['status'] = $job['attempts'] >= 3 ? 'failed' : 'pending';
            }

            $processed++;
        }

        unset($job);
        update_option('hd_ai_queue', $queue, false);
    }

    public function get_queue(): array {
        return (array) get_option('hd_ai_queue', []);
    }
}

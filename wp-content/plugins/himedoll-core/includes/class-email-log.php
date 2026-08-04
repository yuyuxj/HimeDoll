<?php
defined('ABSPATH') || exit;

final class HimeDoll_Email_Log {
    private static ?self $instance = null;

    public static function instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        add_action('wp_mail_succeeded', [$this, 'succeeded']);
        add_action('wp_mail_failed', [$this, 'failed']);
    }

    public function succeeded(array $mail_data): void {
        $this->store('success', $mail_data['to'] ?? '', $mail_data['subject'] ?? '', '');
    }

    public function failed(WP_Error $error): void {
        $data = $error->get_error_data();
        $this->store(
            'failed',
            is_array($data) ? ($data['to'] ?? '') : '',
            is_array($data) ? ($data['subject'] ?? '') : '',
            $error->get_error_message()
        );
    }

    private function store(string $status, mixed $to, string $subject, string $error): void {
        $logs = (array) get_option('hd_email_logs', []);

        $logs[] = [
            'status' => $status,
            'to' => is_array($to) ? implode(',', $to) : (string) $to,
            'subject' => $subject,
            'error' => $error,
            'created_at' => current_time('mysql'),
        ];

        $logs = array_slice($logs, -200);
        update_option('hd_email_logs', $logs, false);
    }
}

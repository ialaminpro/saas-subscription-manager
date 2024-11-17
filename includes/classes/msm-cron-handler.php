<?php
class MSM_Cron_Handler {
    public static function init() {
        if (!wp_next_scheduled('msm_check_renewals')) {
            wp_schedule_event(time(), 'daily', 'msm_check_renewals');
        }
        add_action('msm_check_renewals', [__CLASS__, 'check_renewals']);
    }

    public static function check_renewals() {
        global $wpdb;
        $table_name = msm_get_table_name('saas_subscriptions');

        // Get subscriptions expiring within the next 7 days
        $expiring_subscriptions = $wpdb->get_results(
            "SELECT * FROM $table_name WHERE renewal_date BETWEEN CURDATE() AND CURDATE() + INTERVAL 7 DAY"
        );

        if (!empty($expiring_subscriptions)) {
            foreach ($expiring_subscriptions as $subscription) {
                // Send renewal reminder email
                self::send_renewal_reminder($subscription);

                // Optionally, log the notification sent
                self::log_renewal_reminder_sent($subscription);
            }
        }
    }

    /**
     * Send a renewal reminder email
     *
     * @param object $subscription
     */
    public static function send_renewal_reminder($subscription) {
        $user_email = $subscription->user_email;
        $subject = 'Renewal Reminder for Your SaaS Subscription';
        $message = sprintf(
            "Dear user,\n\nThis is a friendly reminder that your SaaS subscription for %s is set to expire on %s.\n\nPlease renew soon to avoid service interruption.\n\nThank you!\n\SaaS Subscription Manager Team",
            $subscription->service_name,
            date('F j, Y', strtotime($subscription->renewal_date))
        );

        // Use WordPress email function or a dedicated email sending library like PHPMailer
        wp_mail($user_email, $subject, $message);
    }

    /**
     * Log the renewal reminder sent
     *
     * @param object $subscription
     */
    public static function log_renewal_reminder_sent($subscription) {
        global $wpdb;
        $table_name = msm_get_table_name('saas_renewal_logs');

        $wpdb->insert(
            $table_name,
            [
                'subscription_id' => $subscription->id,
                'email_sent' => true,
                'sent_at' => current_time('mysql')
            ],
            [
                '%d',
                '%d',
                '%s'
            ]
        );
    }

    public static function deactivate() {
        wp_clear_scheduled_hook('msm_check_renewals');
    }
}
register_deactivation_hook(__FILE__, ['MSM_Cron_Handler', 'deactivate']);

<?php

class MSM_Subscription_Manager_Repository {

    const TABLE_NAME = 'msm_subscriptions';

    /**
     * Create the subscriptions table if it doesn't exist.
     */
    public static function create_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . self::TABLE_NAME;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            subscription_level VARCHAR(50) NOT NULL,
            start_date DATETIME NOT NULL,
            end_date DATETIME NOT NULL,
            status VARCHAR(20) DEFAULT 'active',
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    /**
     * Insert a new subscription into the database.
     */
    public function create($user_id, $level, $duration) {
        global $wpdb;
        $table_name = $wpdb->prefix . self::TABLE_NAME;

        $start_date = current_time('mysql');
        $end_date = date('Y-m-d H:i:s', strtotime("+$duration"));

        $wpdb->insert($table_name, [
            'user_id' => $user_id,
            'subscription_level' => $level,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'status' => 'active'
        ]);
    }

    /**
     * Mark subscriptions as expired if they are past their end date.
     */
    public function mark_expired_subscriptions() {
        global $wpdb;
        $table_name = $wpdb->prefix . self::TABLE_NAME;

        $expired = $wpdb->get_results("SELECT * FROM $table_name WHERE end_date < NOW() AND status = 'active'");
        foreach ($expired as $subscription) {
            $wpdb->update($table_name, ['status' => 'expired'], ['id' => $subscription->id]);
            // Optionally notify the user
        }
    }

    /**
     * Check if a user has an active subscription.
     */
    public function check_subscription($user_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . self::TABLE_NAME;

        return $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE user_id = %d AND end_date > %s AND status = 'active'",
            $user_id,
            current_time('mysql')
        )) > 0;
    }
}


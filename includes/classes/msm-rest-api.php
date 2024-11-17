<?php
class MSM_REST_API {
    public static function init() {
        add_action('rest_api_init', [__CLASS__, 'register_routes']);
    }

    public static function register_routes() {
        register_rest_route('msm/v1', '/subscriptions', [
            'methods' => 'GET',
            'callback' => [__CLASS__, 'get_subscriptions'],
            'permission_callback' => function () {
                return current_user_can('manage_subscriptions');
            },
        ]);
    }

    public static function get_subscriptions() {
        global $wpdb;
        $table = $wpdb->prefix . 'saas_subscriptions';
        return $wpdb->get_results("SELECT * FROM $table");
    }
}

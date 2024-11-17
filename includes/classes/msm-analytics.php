<?php
class MSM_Analytics {
    public static function init() {
        add_action('admin_menu', [__CLASS__, 'add_admin_menu']);
    }

    public static function add_admin_menu() {
        add_menu_page(
            __('SaaS Analytics', 'saas-subscription-manager'),
            __('Analytics', 'saas-subscription-manager'),
            'view_analytics',
            'msm-analytics',
            [__CLASS__, 'render_analytics_page']
        );
    }

    public static function render_analytics_page() {
        include MSM_PLUGIN_DIR . 'templates/analytics.php';
    }
}

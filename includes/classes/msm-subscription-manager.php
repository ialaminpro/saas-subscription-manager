<?php

class MSM_Subscription_Manager {

    // Subscription plans
    const MSM_SUBSCRIPTION_PLANS = [
        'basic' => [
            'price' => 10,
            'duration' => '1 month',
            'features' => ['Feature A', 'Feature B']
        ],
        'premium' => [
            'price' => 20,
            'duration' => '1 month',
            'features' => ['Feature A', 'Feature B', 'Feature C']
        ]
    ];

    /**
     * Initialize the class and hooks.
     */
    public static function init() {
        add_action('admin_menu', [__CLASS__, 'add_admin_menu']);
        add_action('init', ['MSM_Subscription_Manager', 'setup_cron']);
        add_action('msm_check_expired_subscriptions', ['MSM_Subscription_Manager', 'check_expired_subscriptions']);
        register_activation_hook(__FILE__, [__CLASS__, 'create_subscription_table']);
        register_activation_hook(__FILE__, ['MSM_Subscription_Manager', 'setup_cron']);
        register_deactivation_hook(__FILE__, 'msm_cleanup_cron');
    }

    /**
     * Add admin menu for managing subscriptions.
     */
    public static function add_admin_menu() {
        add_menu_page(
            __('SaaS Subscriptions', 'saas-subscription-manager'),
            __('Subscriptions Manager', 'saas-subscription-manager'),
            'manage_subscriptions',
            'msm-subscriptions',
            [__CLASS__, 'render_subscription_page'],
            'dashicons-list-view',
            20
        );
    }

    /**
     * Render the subscription page.
     */
    public static function render_subscription_page() {
        echo '<h1>' . __('Manage Subscriptions', 'saas-subscription-manager') . '</h1>';

        echo '<h2>' . __('Create Subscription', 'saas-subscription-manager') . '</h2>';
        echo self::render_subscription_form();

        if (isset($_POST['create_subscription'])) {
            $user_id = intval($_POST['user_id']);
            $level = sanitize_text_field($_POST['subscription_level']);
            $duration = intval($_POST['duration']) . ' month';
            self::create_subscription($user_id, $level, $duration);
        }
    }

    /**
     * Render the subscription form.
     */
    public static function render_subscription_form() {
        ob_start(); ?>
        <form method="post" action="">
            <label for="user_id"><?php _e('User', 'saas-subscription-manager'); ?></label>
            <select name="user_id" id="user_id">
                <?php foreach (get_users() as $user) : ?>
                    <option value="<?php echo $user->ID; ?>"><?php echo $user->user_login . ' (' . $user->user_email . ')'; ?></option>
                <?php endforeach; ?>
            </select><br><br>

            <label for="subscription_level"><?php _e('Subscription Level', 'saas-subscription-manager'); ?></label>
            <select name="subscription_level" id="subscription_level">
                <option value="basic"><?php _e('Basic', 'saas-subscription-manager'); ?> - $10/month</option>
                <option value="premium"><?php _e('Premium', 'saas-subscription-manager'); ?> - $20/month</option>
            </select><br><br>

            <label for="duration"><?php _e('Subscription Duration', 'saas-subscription-manager'); ?></label>
            <input type="number" name="duration" id="duration" value="1" min="1"><br><br>

            <button type="submit" name="create_subscription"><?php _e('Create Subscription', 'saas-subscription-manager'); ?></button>
        </form>
        <?php
        return ob_get_clean();
    }

    /**
     * Create a subscription and store it in the database.
     */
    public static function create_subscription($user_id, $level, $duration) {
        $subscription_repo = new MSM_Subscription_Repository();
        $subscription_repo->create($user_id, $level, $duration);
        wp_redirect(admin_url('admin.php?page=msm-subscriptions&message=subscription_created'));
        exit;
    }

    /**
     * Create the subscription table on activation.
     */
    public static function create_subscription_table() {
        MSM_Subscription_Repository::create_table();
    }

    /**
     * Set up cron jobs for checking expired subscriptions.
     */
    public static function setup_cron() {
        if (!wp_next_scheduled('msm_check_expired_subscriptions')) {
            wp_schedule_event(time(), 'daily', 'msm_check_expired_subscriptions');
        }
    }

    /**
     * Check expired subscriptions and update their status.
     */
    public static function check_expired_subscriptions() {
        $subscription_repo = new MSM_Subscription_Repository();
        $subscription_repo->mark_expired_subscriptions();
    }

    /**
     * Cleanup cron events on deactivation.
     */
    public static function cleanup_cron() {
        $timestamp = wp_next_scheduled('msm_check_expired_subscriptions');
        if ($timestamp) {
            wp_unschedule_event($timestamp, 'msm_check_expired_subscriptions');
        }
    }
}

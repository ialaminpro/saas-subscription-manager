<h1><?php _e('Manage Subscriptions', 'saas-subscription-manager'); ?></h1>
<table class="widefat fixed">
    <thead>
        <tr>
            <th><?php _e('User ID', 'saas-subscription-manager'); ?></th>
            <th><?php _e('Subscription Level', 'saas-subscription-manager'); ?></th>
            <th><?php _e('Start Date', 'saas-subscription-manager'); ?></th>
            <th><?php _e('End Date', 'saas-subscription-manager'); ?></th>
            <th><?php _e('Status', 'saas-subscription-manager'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        global $wpdb;
        $subscriptions = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}msm_subscriptions");
        foreach ($subscriptions as $subscription) {
            echo "<tr>
                <td>{$subscription->user_id}</td>
                <td>{$subscription->subscription_level}</td>
                <td>{$subscription->start_date}</td>
                <td>{$subscription->end_date}</td>
                <td>{$subscription->status}</td>
            </tr>";
        }
        ?>
    </tbody>
</table>

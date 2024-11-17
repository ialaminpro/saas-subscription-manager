<?php
class MSM_Role_Manager {
    /**
     * Initialize the role manager.
     */
    public static function init() {
        // Hook for adding roles and capabilities during plugin activation
        register_activation_hook(MSM_PLUGIN_DIR . 'saas-subscription-manager.php', [__CLASS__, 'add_capabilities']);
        
        // Hook for removing roles and capabilities during plugin deactivation
        register_deactivation_hook(MSM_PLUGIN_DIR . 'saas-subscription-manager.php', [__CLASS__, 'remove_capabilities']);
        
        // Debug capabilities during plugin load
        add_action('init', [__CLASS__, 'debug_capabilities']);
    }

    /**
     * Add custom capabilities to roles during activation.
     */
    public static function add_capabilities() {
        $roles = ['administrator', 'editor'];
        
        foreach ($roles as $role_name) {
            $role = get_role($role_name);

            if ($role) {
                $role->add_cap('manage_subscriptions'); // Capability to manage subscriptions
                $role->add_cap('view_analytics');      // Capability to view analytics
                $role->add_cap('import_export_data');  // Capability to handle import/export
            }
        }
    }

    /**
     * Remove custom capabilities from roles during deactivation.
     */
    public static function remove_capabilities() {
        $roles = ['administrator', 'editor'];

        foreach ($roles as $role_name) {
            $role = get_role($role_name);

            if ($role) {
                $role->remove_cap('manage_subscriptions');
                $role->remove_cap('view_analytics');
                $role->remove_cap('import_export_data');
            }
        }
    }

    /**
     * Debug capabilities for the current user.
     * Useful for troubleshooting access issues.
     */
    public static function debug_capabilities() {
        if (current_user_can('manage_subscriptions')) {
            error_log('User has the "manage_subscriptions" capability.');
        } else {
            error_log('User does NOT have the "manage_subscriptions" capability.');
        }

        if (current_user_can('view_analytics')) {
            error_log('User has the "view_analytics" capability.');
        } else {
            error_log('User does NOT have the "view_analytics" capability.');
        }
    }

    /**
     * Check if the current user has the required capability.
     * @param string $capability The capability to check.
     * @return bool True if the user has the capability, false otherwise.
     */
    public static function current_user_has_capability($capability) {
        return current_user_can($capability);
    }
}

// Initialize the Role Manager class
MSM_Role_Manager::init();

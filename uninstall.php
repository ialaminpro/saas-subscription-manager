<?php
// Code to clean up after uninstalling the plugin
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

// Delete custom database tables or options
delete_option('msm_settings');
global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}msm_subscriptions");

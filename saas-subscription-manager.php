<?php
/**
 * Plugin Name: SaaS Subscription Manager
 * Plugin URI: https://wp.fidelstack.com/plugins/saas-subscription-manager
 * Description: A tool to manage subscriptions, analytics, and API integrations for SaaS platforms.
 * Version: 1.0.0
 * Author: Al Amin
 * Author URI: https://al-amin.xyz
 * License: GPLv2 or later
 */

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

// Define constants
define('MSM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MSM_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include main files
require_once MSM_PLUGIN_DIR . 'includes/classes/msm-admin.php';
require_once MSM_PLUGIN_DIR . 'includes/classes/msm-role-manager.php';
require_once MSM_PLUGIN_DIR . 'includes/classes/msm-subscription-manager.php';
require_once MSM_PLUGIN_DIR . 'includes/classes/msm-analytics.php';
require_once MSM_PLUGIN_DIR . 'includes/classes/msm-rest-api.php';
require_once MSM_PLUGIN_DIR . 'includes/classes/msm-import-export.php';
require_once MSM_PLUGIN_DIR . 'includes/classes/msm-cron-handler.php';
require_once MSM_PLUGIN_DIR . 'includes/classes/msm-payment-handler.php';

require_once MSM_PLUGIN_DIR . 'includes/helpers.php';


// Initialize plugin classes
function msm_init_plugin() {
    MSM_Admin::init();
    MSM_Role_Manager::init();
    MSM_Subscription_Manager::init();
    MSM_Analytics::init();
    MSM_REST_API::init();
    MSM_Import_Export::init();
    MSM_Cron_Handler::init();
}
add_action('plugins_loaded', 'msm_init_plugin');


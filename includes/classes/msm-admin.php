<?php
class MSM_Admin {
    public static function init() {
        // Add a new submenu page under Tools
        add_action('admin_menu', [__CLASS__, 'add_export_page']);
    }

    /**
     * Add export subscriptions page under the Tools menu
     */
    public static function add_export_page() {
        add_submenu_page(
            'tools.php',  // Parent menu (Tools)
            'Export Subscriptions',  // Page title
            'Export Subscriptions',  // Menu title
            'manage_options',  // Capability required
            'msm_export_subscriptions',  // Menu slug
            [__CLASS__, 'export_page_callback']  // Callback function
        );
    }

    /**
     * Callback for the export page
     */
    public static function export_page_callback() {
        // Display a button to trigger the CSV export
        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('Export Subscriptions', 'saas-subscription-manager') . '</h1>';
        echo '<p>' . esc_html__('Click the button below to export your subscriptions as a CSV file.', 'saas-subscription-manager') . '</p>';
        echo '<form method="post">';
        echo '<input type="submit" name="msm_export_csv" class="button button-primary" value="Export Subscriptions" />';
        echo '</form>';

        // Check if the form is submitted and call the export function
        if (isset($_POST['msm_export_csv'])) {
            // Trigger the CSV export when the button is clicked
            MSM_CSV_Handler::export_csv();
        }

        echo '</div>';
    }
}

MSM_Admin::init();

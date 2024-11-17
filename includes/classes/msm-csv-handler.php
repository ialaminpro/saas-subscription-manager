<?php
class MSM_CSV_Handler {

    // Function to export subscriptions as a CSV
    public static function export_csv() {
        global $wpdb;
        $table_name = msm_get_table_name('saas_subscriptions');

        // Fetch all subscriptions from the database
        $subscriptions = $wpdb->get_results("SELECT * FROM $table_name");

        if (empty($subscriptions)) {
            // Handle case when no subscriptions are found
            wp_die(__('No subscriptions found to export.', 'saas-subscription-manager'));
        }

        // Set CSV headers to force download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="subscriptions.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Open output stream (directly to browser)
        $output = fopen('php://output', 'w');

        // Add the CSV header (column names)
        fputcsv($output, ['ID', 'User Email', 'Service Name', 'Cost', 'Renewal Date', 'Created At']);

        // Loop through subscriptions and write each row to the CSV
        foreach ($subscriptions as $subscription) {
            fputcsv($output, [
                $subscription->id,
                $subscription->user_email,
                $subscription->service_name,
                $subscription->cost,
                $subscription->renewal_date,
                $subscription->created_at
            ]);
        }

        // Close the output stream
        fclose($output);

        exit;
    }

    // Function to import subscriptions from a CSV
    public static function import_csv($file) {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            wp_die(__('Error in uploading the file.', 'saas-subscription-manager'));
        }

        // Open the uploaded CSV file
        $handle = fopen($file['tmp_name'], 'r');
        if ($handle === false) {
            wp_die(__('Unable to open the uploaded file.', 'saas-subscription-manager'));
        }

        // Skip the CSV header (first row)
        fgetcsv($handle);

        global $wpdb;
        $table_name = msm_get_table_name('saas_subscriptions');
        $imported_count = 0;

        // Loop through each row in the CSV and insert data into the database
        while (($data = fgetcsv($handle)) !== false) {
            // Prepare the data for insertion
            $wpdb->insert(
                $table_name,
                [
                    'user_email'   => sanitize_email($data[1]),
                    'service_name' => sanitize_text_field($data[2]),
                    'cost'         => floatval($data[3]),
                    'renewal_date' => sanitize_text_field($data[4]),
                    'created_at'   => current_time('mysql')
                ],
                [
                    '%s', '%s', '%f', '%s', '%s'
                ]
            );
            $imported_count++;
        }

        // Close the file handle
        fclose($handle);

        // Provide feedback on import success
        if ($imported_count > 0) {
            echo sprintf(__('Successfully imported %d subscriptions.', 'saas-subscription-manager'), $imported_count);
        } else {
            echo __('No subscriptions were imported.', 'saas-subscription-manager');
        }

        exit;
    }
}

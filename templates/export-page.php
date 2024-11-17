<?php
/**
 * Export page for subscriptions
 * This file allows admins to export subscription data to CSV format.
 */

// Load WordPress environment
require_once( dirname( __FILE__ ) . '/wp-load.php' );

// Check if the user has the correct permissions
if ( ! current_user_can( 'manage_subscriptions' ) ) {
    wp_die( 'You do not have permission to access this page.' );
}

// Process the export if the button is clicked
if ( isset( $_POST['export_subscriptions'] ) ) {
    // Define the CSV file name
    $file_name = 'subscriptions_export_' . date( 'Y-m-d_H-i-s' ) . '.csv';

    // Open a file pointer
    $csv_output = fopen( 'php://output', 'w' );

    // Set the headers for the CSV file
    header( 'Content-Type: text/csv' );
    header( 'Content-Disposition: attachment; filename=' . $file_name );

    // Add the column headers to the CSV
    $columns = array( 'ID', 'User ID', 'Subscription Level', 'Start Date', 'End Date', 'Status' );
    fputcsv( $csv_output, $columns );

    // Fetch subscription data from the database
    global $wpdb;
    $table_name = $wpdb->prefix . 'msm_subscriptions';
    
    $subscriptions = $wpdb->get_results( "SELECT * FROM $table_name" );

    // Add subscription data to the CSV
    foreach ( $subscriptions as $subscription ) {
        $row = array(
            $subscription->id,
            $subscription->user_id,
            $subscription->subscription_level,
            $subscription->start_date,
            $subscription->end_date,
            $subscription->status
        );
        fputcsv( $csv_output, $row );
    }

    // Close the file pointer
    fclose( $csv_output );
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Subscriptions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .export-form {
            background-color: #f7f7f7;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .export-form h2 {
            margin-top: 0;
        }
        .export-form button {
            padding: 10px 15px;
            font-size: 16px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="export-form">
    <h2>Export Subscription Data</h2>
    <form method="post" action="">
        <p>
            <button type="submit" name="export_subscriptions">Export Subscriptions to CSV</button>
        </p>
    </form>
</div>

</body>
</html>

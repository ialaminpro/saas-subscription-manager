<?php
/**
 * Analytics page template for the  SaaS Subscription Manager Plugin.
 * Displays total monthly costs, renewal patterns, and other analytics.
 */

// Fetch the total monthly cost from the database
$total_monthly_cost = msm_get_monthly_costs();

// Fetch renewal pattern data (this is just an example, adjust as needed)
$renewal_data = msm_get_renewal_pattern_data();

// Prepare data for chart
$months = [];
$renewals = [];

foreach ($renewal_data as $data) {
    $months[] = $data->month;
    $renewals[] = $data->count;
}

?>

<div class="wrap">
    <h1><?php echo esc_html__('SaaS Subscription Analytics', 'saas-subscription-manager'); ?></h1>

    <div class="msm-analytics-summary">
        <h2><?php echo esc_html__('Total Monthly Cost:', 'saas-subscription-manager'); ?></h2>
        <p><?php echo esc_html__('€' . number_format($total_monthly_cost, 2), 'saas-subscription-manager'); ?></p>
    </div>

    <div class="msm-analytics-chart">
        <h2><?php echo esc_html__('Renewal Pattern (Monthly)', 'saas-subscription-manager'); ?></h2>
        <canvas id="renewalChart" width="400" height="200"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('renewalChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [{
                    label: '<?php echo esc_js(__('Number of Renewals', 'saas-subscription-manager')); ?>',
                    data: <?php echo json_encode($renewals); ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.raw + ' ' + '<?php echo esc_js(__('renewals', 'saas-subscription-manager')); ?>';
                            }
                        }
                    }
                }
            }
        });
    </script>
</div>

<?php
// Function to get the monthly cost (you can adjust this query based on your DB structure)
function msm_get_monthly_costs() {
    global $wpdb;
    $table_name = msm_get_table_name('saas_subscriptions');

    // Query to get the sum of costs for the current month
    $total_cost = $wpdb->get_var(
        "SELECT SUM(cost) FROM $table_name WHERE renewal_date >= CURDATE() AND renewal_date <= LAST_DAY(CURDATE())"
    );

    return $total_cost ?: 0;
}

// Function to get renewal pattern data (adjust according to your table structure)
function msm_get_renewal_pattern_data() {
    global $wpdb;
    $table_name = msm_get_table_name('saas_subscriptions');

    // Query to get renewal counts per month
    return $wpdb->get_results(
        "SELECT MONTH(renewal_date) AS month, COUNT(*) AS count
         FROM $table_name
         WHERE renewal_date >= CURDATE() - INTERVAL 12 MONTH
         GROUP BY MONTH(renewal_date)
         ORDER BY MONTH(renewal_date)"
    );
}
?>

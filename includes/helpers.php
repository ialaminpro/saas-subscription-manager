<?php
function msm_get_table_name($suffix) {
    global $wpdb;
    return $wpdb->prefix . $suffix;
}

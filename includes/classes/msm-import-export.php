<?php
class MSM_Import_Export {
    public static function init() {
        add_action('admin_post_msm_export', [__CLASS__, 'export_csv']);
        add_action('admin_post_msm_import', [__CLASS__, 'import_csv']);
    }

    public static function export_csv() {
        // Code for exporting CSV
    }

    public static function import_csv() {
        // Code for importing CSV
    }
}

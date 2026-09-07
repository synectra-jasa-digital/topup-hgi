<?php

if (! function_exists('log_activity')) {
    function log_activity(string $action, string $description): void
    {
        $adminId = session()->get('admin_id');
        if (! $adminId) {
            return;
        }

        try {
            $logModel = new \App\Models\ActivityLogModel();
            $logModel->log((int) $adminId, $action, $description);
        } catch (\Throwable $e) {
            log_message('error', 'Failed to save activity log: ' . $e->getMessage());
        }
    }
}

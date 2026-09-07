<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ActivityLogModel;

class ActivityLogController extends BaseController
{
    protected ActivityLogModel $logs;

    public function __construct()
    {
        $this->logs = new ActivityLogModel();
    }

    public function index()
    {
        $adminId = $this->request->getGet('admin_id') ? (int) $this->request->getGet('admin_id') : null;

        return view('admin/activity_logs/index', [
            'logs'       => $this->logs->listWithAdmin($adminId),
            'pager'      => $this->logs->pager,
            'admin_id'   => $adminId,
        ]);
    }
}

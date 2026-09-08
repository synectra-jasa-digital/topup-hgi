<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ActivityLogModel;
use App\Models\OrderModel;
use App\Models\ReportModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $role   = session('admin_role');
        $orders = new OrderModel();
        $logs   = new ActivityLogModel();

        $data = [
            'role'         => $role,
            'pendingCount' => $orders->whereIn('status', ['dibayar', 'diproses'])->countAllResults(),
            'recentLogs'   => $logs->listWithAdmin(null, 5),
            'statusCounts' => $orders->countByStatus(),
        ];

        if ($role === 'owner') {
            $report = new ReportModel();
            $range  = $report->revenueRange(30);

            $data['today']         = $report->dailySummary();
            $data['yesterday']     = $report->dailySummary(1);
            $data['chartLabels30'] = array_map(fn ($d) => $d['date'], $range);
            $data['chartData30']   = array_map(fn ($d) => (float) $d['revenue'], $range);
            $data['topCategories'] = $report->topCategories(30);
        } else {
            $data['today'] = (new ReportModel())->dailySummary();
        }

        return view('admin/dashboard/index', $data);
    }
}

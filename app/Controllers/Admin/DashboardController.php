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
        ];

        if ($role === 'owner') {
            $report = new ReportModel();

            $last7 = [];
            for ($i = 0; $i < 7; $i++) {
                $last7[] = $report->dailySummary($i);
            }

            $data['today']        = $last7[0];
            $data['yesterday']    = $last7[1];
            $data['chartLabels']  = array_reverse(array_map(fn ($d) => $d['date'], $last7));
            $data['chartData']    = array_reverse(array_map(fn ($d) => (float) $d['revenue'], $last7));
            $data['topCategories'] = $report->topCategories(30);
        } else {
            $data['today'] = (new ReportModel())->dailySummary();
        }

        return view('admin/dashboard/index', $data);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\OnlinePayment;
use App\Models\PaymentVerification;
use App\Models\ServiceRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminReportsController extends Controller
{
    public function index(Request $request)
    {
        $to = $request->date('to') ?? Carbon::today();
        $from = $request->date('from') ?? Carbon::today()->subDays(30);
        if ($from->gt($to)) {
            $from = $to->copy()->subDays(30);
        }

        $fromStr = $from->format('Y-m-d');
        $toStr = $to->format('Y-m-d');

        // —— Online registration payments (online_payments)
        $onlineBase = OnlinePayment::query()
            ->whereDate('created_at', '>=', $fromStr)
            ->whereDate('created_at', '<=', $toStr);

        $onlineSucceeded = (clone $onlineBase)->where('status', 'succeeded');
        $onlineRevenue = (float) $onlineSucceeded->sum('amount');
        $onlineSucceededCount = $onlineSucceeded->count();

        $onlineByStatus = (clone $onlineBase)
            ->selectRaw('status, COUNT(*) as cnt, COALESCE(SUM(amount), 0) as total')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        // —— Manual / portal service payments (payment_verifications)
        $pvBase = PaymentVerification::query()
            ->whereDate('payment_date', '>=', $fromStr)
            ->whereDate('payment_date', '<=', $toStr);

        $manualVerified = (clone $pvBase)->where('status', 'verified');
        $manualRevenue = (float) $manualVerified->sum('amount');
        $manualVerifiedCount = $manualVerified->count();

        $manualDiscrepancyCount = (clone $pvBase)->where('status', 'discrepancy')->count();

        $pvByStatus = (clone $pvBase)
            ->selectRaw('status, COUNT(*) as cnt, COALESCE(SUM(amount), 0) as total')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        // —— Revenue by service (merged online + manual in range)
        $onlineByService = OnlinePayment::query()
            ->where('online_payments.status', 'succeeded')
            ->whereDate('online_payments.created_at', '>=', $fromStr)
            ->whereDate('online_payments.created_at', '<=', $toStr)
            ->join('pending_registrations as pr', 'pr.id', '=', 'online_payments.pending_registration_id')
            ->leftJoin('services as s', 's.id', '=', 'pr.service_id')
            ->selectRaw('COALESCE(s.id, 0) as sid, COALESCE(MAX(s.name), ?) as sname, SUM(online_payments.amount) as online_total, COUNT(*) as online_cnt', ['Unassigned'])
            ->groupBy(DB::raw('COALESCE(s.id, 0)'))
            ->get();

        $manualByService = PaymentVerification::query()
            ->where('payment_verifications.status', 'verified')
            ->whereDate('payment_verifications.payment_date', '>=', $fromStr)
            ->whereDate('payment_verifications.payment_date', '<=', $toStr)
            ->join('service_requests as sr', 'sr.id', '=', 'payment_verifications.service_request_id')
            ->join('services as s', 's.id', '=', 'sr.service_id')
            ->groupBy('s.id', 's.name')
            ->selectRaw('s.id as sid, s.name as sname, SUM(payment_verifications.amount) as manual_total, COUNT(*) as manual_cnt')
            ->get();

        $byService = $this->mergeServiceRevenue($onlineByService, $manualByService);

        // —— Service requests: pipeline (current backlog) and new in period
        $requestStatusCounts = ServiceRequest::query()
            ->selectRaw('status, COUNT(*) as cnt')
            ->groupBy('status')
            ->pluck('cnt', 'status');

        $newRequestsInPeriod = ServiceRequest::query()
            ->whereDate('created_at', '>=', $fromStr)
            ->whereDate('created_at', '<=', $toStr)
            ->count();

        $newRequestsByStatus = ServiceRequest::query()
            ->whereDate('created_at', '>=', $fromStr)
            ->whereDate('created_at', '<=', $toStr)
            ->selectRaw('status, COUNT(*) as cnt')
            ->groupBy('status')
            ->pluck('cnt', 'status');

        $totalRevenue = $onlineRevenue + $manualRevenue;

        return view('admin.pages.reports', compact(
            'fromStr',
            'toStr',
            'onlineRevenue',
            'onlineSucceededCount',
            'manualRevenue',
            'manualVerifiedCount',
            'manualDiscrepancyCount',
            'totalRevenue',
            'onlineByStatus',
            'pvByStatus',
            'byService',
            'requestStatusCounts',
            'newRequestsInPeriod',
            'newRequestsByStatus'
        ));
    }

    /**
     * @param  \Illuminate\Support\Collection<int, object>  $onlineByService
     * @param  \Illuminate\Support\Collection<int, object>  $manualByService
     * @return \Illuminate\Support\Collection<int, array{sid: int, name: string, online_total: float, manual_total: float, combined: float, online_cnt: int, manual_cnt: int}>
     */
    private function mergeServiceRevenue($onlineByService, $manualByService)
    {
        $map = [];

        foreach ($onlineByService as $row) {
            $sid = (int) $row->sid;
            $map[$sid] = [
                'sid' => $sid,
                'name' => (string) $row->sname,
                'online_total' => (float) $row->online_total,
                'manual_total' => 0.0,
                'combined' => (float) $row->online_total,
                'online_cnt' => (int) $row->online_cnt,
                'manual_cnt' => 0,
            ];
        }

        foreach ($manualByService as $row) {
            $sid = (int) $row->sid;
            $manualTotal = (float) $row->manual_total;
            $manualCnt = (int) $row->manual_cnt;
            if (! isset($map[$sid])) {
                $map[$sid] = [
                    'sid' => $sid,
                    'name' => (string) $row->sname,
                    'online_total' => 0.0,
                    'manual_total' => $manualTotal,
                    'combined' => $manualTotal,
                    'online_cnt' => 0,
                    'manual_cnt' => $manualCnt,
                ];
            } else {
                $map[$sid]['manual_total'] = $manualTotal;
                $map[$sid]['manual_cnt'] = $manualCnt;
                $map[$sid]['combined'] = $map[$sid]['online_total'] + $manualTotal;
            }
        }

        return collect($map)->sortByDesc('combined')->values();
    }
}

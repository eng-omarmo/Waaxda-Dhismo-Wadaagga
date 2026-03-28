<?php

namespace App\Http\Controllers;

use App\Models\OnlinePayment;
use App\Models\PaymentVerification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class AdminPaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = OnlinePayment::with('registration');

        // Search by customer name, email, or reference
        if ($q = $request->string('q')->toString()) {
            $query->where(function ($w) use ($q) {
                $w->where('reference', 'like', "%$q%")
                    ->orWhere('transaction_id', 'like', "%$q%")
                    ->orWhere('receipt_number', 'like', "%$q%")
                    ->orWhereHas('registration', function ($sub) use ($q) {
                        $sub->where('full_name', 'like', "%$q%")
                            ->orWhere('email', 'like', "%$q%");
                    });
            });
        }

        // Filter by status
        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        // Filter by payment method
        if ($method = $request->string('method')->toString()) {
            $query->where('payment_method', $method);
        }

        // Filter by date range
        if ($from = $request->string('from')->toString()) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->string('to')->toString()) {
            $query->whereDate('created_at', '<=', $to);
        }

        // Sorting
        $sort = $request->string('sort', 'created_at')->toString();
        $direction = $request->string('direction', 'desc')->toString();
        $query->orderBy($sort, $direction);

        // Summary Statistics (Total, Average, Method Breakdown)
        $statsQuery = clone $query;
        $totalCollected = $statsQuery->where('status', 'succeeded')->sum('amount');
        $avgPayment = $statsQuery->where('status', 'succeeded')->avg('amount') ?? 0;
        $methodBreakdown = OnlinePayment::select('payment_method', DB::raw('count(*) as count'))
            ->where('status', 'succeeded')
            ->groupBy('payment_method')
            ->get();

        // Export Logic
        if ($request->has('export')) {
            return $this->export($query->get(), $request->string('export')->toString());
        }

        $perPage = min(max((int) $request->query('per_page', 10), 1), 100);
        $payments = $query->paginate($perPage)->withQueryString();

        // Manual / portal service payments live in payment_verifications (not online_payments).
        $servicePaymentsQuery = PaymentVerification::with(['request.service', 'verifier']);
        if ($q = $request->string('q')->toString()) {
            $servicePaymentsQuery->whereHas('request', function ($sub) use ($q) {
                $sub->where('user_full_name', 'like', "%$q%")
                    ->orWhere('user_email', 'like', "%$q%")
                    ->orWhere('user_phone', 'like', "%$q%");
            });
        }
        if ($from = $request->string('from')->toString()) {
            $servicePaymentsQuery->whereDate('payment_date', '>=', $from);
        }
        if ($to = $request->string('to')->toString()) {
            $servicePaymentsQuery->whereDate('payment_date', '<=', $to);
        }
        $servicePerPage = min(max((int) $request->query('service_per_page', 10), 1), 100);
        $servicePayments = $servicePaymentsQuery
            ->orderByDesc('created_at')
            ->paginate($servicePerPage, ['*'], 'service_page')
            ->withQueryString();

        $servicePaymentsVerifiedTotal = PaymentVerification::where('status', 'verified')->sum('amount');

        $statuses = ['initiated', 'succeeded', 'failed'];
        $methods = OnlinePayment::distinct()->pluck('payment_method')->filter()->values();

        return view('admin.pages.payments', compact(
            'payments',
            'servicePayments',
            'servicePaymentsVerifiedTotal',
            'statuses',
            'methods',
            'totalCollected',
            'avgPayment',
            'methodBreakdown',
            'sort',
            'direction'
        ));
    }

    private function export($payments, $format)
    {
        if ($format === 'csv') {
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="payments_export_' . now()->format('YmdHis') . '.csv"',
            ];

            $callback = function () use ($payments) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['ID', 'Customer Name', 'Email', 'Amount', 'Currency', 'Method', 'Status', 'Transaction ID', 'Reference', 'Date']);

                foreach ($payments as $p) {
                    fputcsv($file, [
                        $p->id,
                        $p->registration?->full_name,
                        $p->registration?->email,
                        $p->amount,
                        $p->currency,
                        $p->payment_method,
                        $p->status,
                        $p->transaction_id,
                        $p->reference,
                        $p->created_at->format('Y-m-d H:i:s'),
                    ]);
                }
                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        }

        if ($format === 'pdf') {
            $html = '<h1>Payment Records Export</h1>';
            $html .= '<p>Generated on: ' . now()->format('Y-m-d H:i:s') . '</p>';
            $html .= '<table border="1" cellpadding="5" cellspacing="0" style="width:100%; border-collapse: collapse;">';
            $html .= '<thead><tr style="background: #eee;"><th>ID</th><th>Customer</th><th>Amount</th><th>Method</th><th>Status</th><th>Reference</th><th>Date</th></tr></thead>';
            $html .= '<tbody>';
            foreach ($payments as $p) {
                $html .= '<tr>';
                $html .= '<td>' . $p->id . '</td>';
                $html .= '<td>' . e($p->registration?->full_name) . '<br><small>' . e($p->registration?->email) . '</small></td>';
                $html .= '<td>' . $p->amount . ' ' . $p->currency . '</td>';
                $html .= '<td>' . e($p->payment_method) . '</td>';
                $html .= '<td>' . e($p->status) . '</td>';
                $html .= '<td>' . e($p->reference) . '</td>';
                $html .= '<td>' . $p->created_at->format('Y-m-d H:i') . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody></table>';

            $pdf = Pdf::loadHTML($html)->setPaper('a4', 'landscape');
            return $pdf->download('payments_export_' . now()->format('YmdHis') . '.pdf');
        }

        return back()->with('error', 'Format not supported');
    }
}

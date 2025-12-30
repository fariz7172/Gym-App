<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Payment;
use App\Models\Attendance;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $branchId = $user->isSuperAdmin() ? null : $user->branch_id;

        $stats = [
            'total_members' => $this->getMemberCount($branchId),
            'active_members' => $this->getMemberCount($branchId, 'active'),
            'expired_members' => $this->getMemberCount($branchId, 'expired'),
            'month_revenue' => $this->getMonthRevenue($branchId),
            'month_attendance' => $this->getMonthAttendance($branchId),
        ];

        $branches = $user->isSuperAdmin() ? Branch::active()->get() : collect();

        return view('reports.index', compact('stats', 'branches'));
    }

    public function members(Request $request)
    {
        $user = Auth::user();
        $query = Member::with(['branch', 'activeMembership.plan']);

        if (!$user->isSuperAdmin()) {
            $query->where('branch_id', $user->branch_id);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $members = $query->orderBy('created_at', 'desc')->paginate(50);
        $branches = $user->isSuperAdmin() ? Branch::active()->get() : collect();

        return view('reports.members', compact('members', 'branches'));
    }

    public function revenue(Request $request)
    {
        $user = Auth::user();
        $query = Payment::with(['member', 'branch'])->paid();

        if (!$user->isSuperAdmin()) {
            $query->where('branch_id', $user->branch_id);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : now()->startOfMonth();
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : now()->endOfMonth();

        $query->whereBetween('payment_date', [$startDate, $endDate]);

        $payments = $query->orderBy('payment_date', 'desc')->get();

        $summary = [
            'total' => $payments->sum('amount'),
            'count' => $payments->count(),
            'by_method' => $payments->groupBy('payment_method')->map(fn($items) => $items->sum('amount')),
            'daily' => $payments->groupBy(fn($p) => $p->payment_date->format('Y-m-d'))->map(fn($items) => $items->sum('amount')),
        ];

        $branches = $user->isSuperAdmin() ? Branch::active()->get() : collect();

        return view('reports.revenue', compact('payments', 'summary', 'branches', 'startDate', 'endDate'));
    }

    public function attendance(Request $request)
    {
        $user = Auth::user();
        $query = Attendance::with(['member', 'branch']);

        if (!$user->isSuperAdmin()) {
            $query->where('branch_id', $user->branch_id);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $date = $request->filled('date') ? Carbon::parse($request->date) : today();
        $query->whereDate('check_in', $date);

        $attendances = $query->orderBy('check_in', 'desc')->get();

        $summary = [
            'total' => $attendances->count(),
            'currently_in' => $attendances->whereNull('check_out')->count(),
            'avg_duration' => $attendances->whereNotNull('duration_minutes')->avg('duration_minutes'),
        ];

        $branches = $user->isSuperAdmin() ? Branch::active()->get() : collect();

        return view('reports.attendance', compact('attendances', 'summary', 'branches', 'date'));
    }

    public function export(Request $request, $type)
    {
        // Export logic would go here
        // For now, return back with message
        return back()->with('info', 'Fitur export akan segera tersedia.');
    }

    private function getMemberCount($branchId = null, $status = null)
    {
        $query = Member::query();
        if ($branchId) $query->where('branch_id', $branchId);
        if ($status) $query->where('status', $status);
        return $query->count();
    }

    private function getMonthRevenue($branchId = null)
    {
        $query = Payment::thisMonth()->paid();
        if ($branchId) $query->where('branch_id', $branchId);
        return $query->sum('amount');
    }

    private function getMonthAttendance($branchId = null)
    {
        $query = Attendance::thisMonth();
        if ($branchId) $query->where('branch_id', $branchId);
        return $query->count();
    }
}

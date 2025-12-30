<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Member;
use App\Models\Attendance;
use App\Models\Payment;
use App\Models\Membership;
use App\Models\ClassBooking;
use App\Models\PtSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $branchId = $user->isSuperAdmin() ? null : $user->branch_id;

        // Get statistics based on user role
        $stats = $this->getStatistics($branchId);

        // Recent activities
        $recentAttendances = $this->getRecentAttendances($branchId);
        $upcomingClasses = $this->getUpcomingClasses($branchId);
        $expiringMemberships = $this->getExpiringMemberships($branchId);

        // Revenue chart data
        $revenueData = $this->getRevenueChartData($branchId);

        // Branches for super admin
        $branches = $user->isSuperAdmin() ? Branch::active()->get() : collect();

        return view('dashboard.index', compact(
            'stats',
            'recentAttendances',
            'upcomingClasses',
            'expiringMemberships',
            'revenueData',
            'branches'
        ));
    }

    private function getStatistics($branchId = null)
    {
        $memberQuery = Member::query();
        $attendanceQuery = Attendance::query();
        $paymentQuery = Payment::query();

        if ($branchId) {
            $memberQuery->where('branch_id', $branchId);
            $attendanceQuery->where('branch_id', $branchId);
            $paymentQuery->where('branch_id', $branchId);
        }

        return [
            'total_members' => $memberQuery->count(),
            'active_members' => (clone $memberQuery)->where('status', 'active')->count(),
            'today_attendance' => (clone $attendanceQuery)->whereDate('check_in', today())->count(),
            'currently_in' => (clone $attendanceQuery)->whereDate('check_in', today())->whereNull('check_out')->count(),
            'month_revenue' => (clone $paymentQuery)->thisMonth()->paid()->sum('amount'),
            'today_revenue' => (clone $paymentQuery)->today()->paid()->sum('amount'),
            'new_members_this_month' => (clone $memberQuery)->whereMonth('created_at', now()->month)->count(),
            'expiring_soon' => Membership::expiringIn(7)->count(),
        ];
    }

    private function getRecentAttendances($branchId = null, $limit = 10)
    {
        $query = Attendance::with('member')
            ->whereDate('check_in', today())
            ->orderBy('check_in', 'desc');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->limit($limit)->get();
    }

    private function getUpcomingClasses($branchId = null, $limit = 5)
    {
        $today = strtolower(now()->format('l'));

        $query = \App\Models\ClassSchedule::with(['gymClass', 'trainer'])
            ->where('day_of_week', $today)
            ->where('is_active', true)
            ->orderBy('start_time');

        if ($branchId) {
            $query->whereHas('gymClass', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }

        return $query->limit($limit)->get();
    }

    private function getExpiringMemberships($branchId = null, $limit = 5)
    {
        $query = Membership::with(['member', 'plan'])
            ->expiringIn(7)
            ->orderBy('end_date');

        if ($branchId) {
            $query->whereHas('member', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }

        return $query->limit($limit)->get();
    }

    private function getRevenueChartData($branchId = null)
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $query = Payment::whereDate('payment_date', $date)->paid();

            if ($branchId) {
                $query->where('branch_id', $branchId);
            }

            $data[] = [
                'date' => $date->format('d M'),
                'amount' => $query->sum('amount'),
            ];
        }
        return $data;
    }
}

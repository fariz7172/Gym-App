<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Attendance::with('member')
            ->whereDate('check_in', today())
            ->orderBy('check_in', 'desc');

        if (!$user->isSuperAdmin()) {
            $query->where('branch_id', $user->branch_id);
        }

        $attendances = $query->get();
        $currentlyIn = $attendances->whereNull('check_out')->count();

        return view('attendance.index', compact('attendances', 'currentlyIn'));
    }

    public function scan()
    {
        return view('attendance.scan');
    }

    public function checkIn(Request $request)
    {
        $request->validate([
            'member_code' => 'required|string',
        ]);

        $member = Member::where('member_code', $request->member_code)->first();

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Member tidak ditemukan.',
            ], 404);
        }

        // Check if member has active membership
        if (!$member->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Membership tidak aktif atau sudah expired.',
                'member' => [
                    'name' => $member->name,
                    'status' => $member->status,
                ],
            ], 403);
        }

        // Check if already checked in today
        if ($member->isCheckedIn()) {
            $attendance = $member->getLatestAttendance();
            return response()->json([
                'success' => false,
                'message' => 'Member sudah check-in dan belum check-out.',
                'member' => [
                    'name' => $member->name,
                    'check_in_time' => $attendance->check_in->format('H:i'),
                ],
                'action' => 'checkout',
                'attendance_id' => $attendance->id,
            ]);
        }

        $user = Auth::user();
        $branchId = $user->isSuperAdmin() ? $member->branch_id : $user->branch_id;

        $attendance = Attendance::checkInMember($member, $branchId);

        return response()->json([
            'success' => true,
            'message' => 'Check-in berhasil!',
            'member' => [
                'name' => $member->name,
                'member_code' => $member->member_code,
                'membership_expiry' => $member->membership_expiry?->format('d M Y'),
            ],
            'attendance' => [
                'id' => $attendance->id,
                'check_in' => $attendance->check_in->format('H:i'),
            ],
        ]);
    }

    public function checkOut(Attendance $attendance)
    {
        if ($attendance->check_out) {
            return response()->json([
                'success' => false,
                'message' => 'Member sudah check-out sebelumnya.',
            ], 400);
        }

        $attendance->checkOut();

        return response()->json([
            'success' => true,
            'message' => 'Check-out berhasil!',
            'attendance' => [
                'check_in' => $attendance->check_in->format('H:i'),
                'check_out' => $attendance->check_out->format('H:i'),
                'duration' => $attendance->duration_text,
            ],
        ]);
    }

    public function history(Request $request)
    {
        $user = Auth::user();
        $query = Attendance::with(['member', 'branch']);

        if (!$user->isSuperAdmin()) {
            $query->where('branch_id', $user->branch_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('check_in', $request->date);
        }

        if ($request->filled('member_id')) {
            $query->where('member_id', $request->member_id);
        }

        $attendances = $query->orderBy('check_in', 'desc')->paginate(20);

        return view('attendance.history', compact('attendances'));
    }
}

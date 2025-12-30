<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\MembershipPlan;
use App\Models\Member;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MembershipController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Membership::with(['member.branch', 'plan']);

        if (!$user->isSuperAdmin()) {
            $query->whereHas('member', fn($q) => $q->where('branch_id', $user->branch_id));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $memberships = $query->orderBy('end_date', 'asc')->paginate(15);

        return view('memberships.index', compact('memberships'));
    }

    public function plans()
    {
        $plans = MembershipPlan::withCount('memberships')->get();
        return view('memberships.plans', compact('plans'));
    }

    public function create()
    {
        $user = Auth::user();

        $membersQuery = Member::where('status', '!=', 'active');
        if (!$user->isSuperAdmin()) {
            $membersQuery->where('branch_id', $user->branch_id);
        }

        $members = $membersQuery->get();
        $plans = MembershipPlan::active()->get();

        return view('memberships.create', compact('members', 'plans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'membership_plan_id' => 'required|exists:membership_plans,id',
            'payment_method' => 'required|in:cash,transfer,card,e-wallet',
        ]);

        $member = Member::findOrFail($request->member_id);
        $plan = MembershipPlan::findOrFail($request->membership_plan_id);

        DB::beginTransaction();
        try {
            // Create membership
            $membership = Membership::create([
                'member_id' => $member->id,
                'membership_plan_id' => $plan->id,
                'start_date' => now(),
                'end_date' => now()->addDays($plan->duration_days),
                'status' => 'active',
            ]);

            // Create payment
            Payment::create([
                'member_id' => $member->id,
                'membership_id' => $membership->id,
                'branch_id' => $member->branch_id,
                'amount' => $plan->price,
                'payment_method' => $request->payment_method,
                'status' => 'paid',
                'payment_date' => now(),
            ]);

            // Update member status
            $member->update(['status' => 'active']);

            DB::commit();

            return redirect()->route('memberships.index')
                ->with('success', 'Membership berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan membership.');
        }
    }

    public function show(Membership $membership)
    {
        $membership->load(['member', 'plan', 'payment']);
        return view('memberships.show', compact('membership'));
    }
}

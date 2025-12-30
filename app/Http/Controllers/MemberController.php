<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Branch;
use App\Models\MembershipPlan;
use App\Models\Membership;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Member::with(['branch', 'activeMembership.plan']);

        // Filter by branch for non-super admin
        if (!$user->isSuperAdmin()) {
            $query->where('branch_id', $user->branch_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('member_code', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by branch (super admin only)
        if ($user->isSuperAdmin() && $request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $members = $query->orderBy('created_at', 'desc')->paginate(15);
        $branches = $user->isSuperAdmin() ? Branch::active()->get() : collect();

        return view('members.index', compact('members', 'branches'));
    }

    public function create()
    {
        $user = Auth::user();
        $branches = $user->isSuperAdmin() ? Branch::active()->get() : collect([Auth::user()->branch]);
        $plans = MembershipPlan::active()->get();

        return view('members.create', compact('branches', 'plans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:members',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:255',
            'health_notes' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'branch_id' => 'required|exists:branches,id',
            'membership_plan_id' => 'required|exists:membership_plans,id',
            'payment_method' => 'required|in:cash,transfer,card,e-wallet',
        ]);

        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            $validated['branch_id'] = $user->branch_id;
        }

        DB::beginTransaction();
        try {
            // Create member
            if ($request->hasFile('photo')) {
                $validated['photo'] = $request->file('photo')->store('members', 'public');
            }

            $member = Member::create($validated);

            // Create membership
            $plan = MembershipPlan::find($request->membership_plan_id);
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
                'branch_id' => $validated['branch_id'],
                'amount' => $plan->price,
                'payment_method' => $request->payment_method,
                'status' => 'paid',
                'payment_date' => now(),
            ]);

            DB::commit();

            return redirect()->route('members.index')
                ->with('success', 'Member berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menambahkan member: ' . $e->getMessage());
        }
    }

    public function show(Member $member)
    {
        $this->authorizeAccess($member);

        $member->load([
            'branch',
            'memberships.plan',
            'attendances' => fn($q) => $q->latest()->limit(10),
            'payments' => fn($q) => $q->latest()->limit(10),
            'bodyMeasurements' => fn($q) => $q->latest()->limit(5),
            'goals' => fn($q) => $q->where('status', 'in_progress'),
        ]);

        return view('members.show', compact('member'));
    }

    public function edit(Member $member)
    {
        $this->authorizeAccess($member);

        $user = Auth::user();
        $branches = $user->isSuperAdmin() ? Branch::active()->get() : collect([Auth::user()->branch]);

        return view('members.edit', compact('member', 'branches'));
    }

    public function update(Request $request, Member $member)
    {
        $this->authorizeAccess($member);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:members,email,' . $member->id,
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:255',
            'health_notes' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($member->photo) {
                Storage::disk('public')->delete($member->photo);
            }
            $validated['photo'] = $request->file('photo')->store('members', 'public');
        }

        $member->update($validated);

        return redirect()->route('members.show', $member)
            ->with('success', 'Data member berhasil diperbarui.');
    }

    public function destroy(Member $member)
    {
        $this->authorizeAccess($member);

        if ($member->photo) {
            Storage::disk('public')->delete($member->photo);
        }

        $member->delete();

        return redirect()->route('members.index')
            ->with('success', 'Member berhasil dihapus.');
    }

    public function qrCode(Member $member)
    {
        $this->authorizeAccess($member);
        return view('members.qrcode', compact('member'));
    }

    public function freeze(Member $member)
    {
        $this->authorizeAccess($member);

        if ($member->activeMembership) {
            $member->activeMembership->update(['status' => 'frozen']);
        }
        $member->update(['status' => 'frozen']);

        return back()->with('success', 'Membership berhasil di-freeze.');
    }

    public function unfreeze(Member $member)
    {
        $this->authorizeAccess($member);

        $membership = $member->memberships()->where('status', 'frozen')->first();
        if ($membership) {
            $membership->update(['status' => 'active']);
        }
        $member->update(['status' => 'active']);

        return back()->with('success', 'Membership berhasil diaktifkan kembali.');
    }

    private function authorizeAccess(Member $member)
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin() && $member->branch_id !== $user->branch_id) {
            abort(403, 'Anda tidak memiliki akses ke member ini.');
        }
    }
}

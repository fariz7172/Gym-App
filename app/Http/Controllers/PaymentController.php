<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Payment::with(['member', 'membership.plan']);

        if (!$user->isSuperAdmin()) {
            $query->where('branch_id', $user->branch_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(20);

        // Statistics
        $stats = [
            'today' => (clone $query)->today()->paid()->sum('amount'),
            'this_month' => (clone $query)->thisMonth()->paid()->sum('amount'),
            'pending' => (clone $query)->pending()->count(),
        ];

        return view('payments.index', compact('payments', 'stats'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['member', 'membership.plan', 'branch']);
        return view('payments.show', compact('payment'));
    }

    public function invoice(Payment $payment)
    {
        $payment->load(['member', 'membership.plan', 'branch']);
        return view('payments.invoice', compact('payment'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,transfer,card,e-wallet',
            'notes' => 'nullable|string',
        ]);

        $member = Member::findOrFail($request->member_id);
        $user = Auth::user();

        Payment::create([
            'member_id' => $member->id,
            'branch_id' => $user->isSuperAdmin() ? $member->branch_id : $user->branch_id,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'status' => 'paid',
            'payment_date' => now(),
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('payments.index')
            ->with('success', 'Pembayaran berhasil dicatat.');
    }

    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,paid,failed,refunded',
            'notes' => 'nullable|string',
        ]);

        $payment->update($validated);

        return back()->with('success', 'Status pembayaran berhasil diperbarui.');
    }
}

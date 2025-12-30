<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use App\Models\PtSession;
use App\Models\Member;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TrainerController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = Trainer::with('branch');

        if (!$user->isSuperAdmin()) {
            $query->where('branch_id', $user->branch_id);
        }

        $trainers = $query->get();
        return view('trainers.index', compact('trainers'));
    }

    public function create()
    {
        $user = Auth::user();
        $branches = $user->isSuperAdmin() ? Branch::active()->get() : collect([Auth::user()->branch]);
        return view('trainers.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:trainers',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female',
            'specialization' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'hourly_rate' => 'required|numeric|min:0',
            'branch_id' => 'required|exists:branches,id',
            'photo' => 'nullable|image|max:2048',
        ]);

        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            $validated['branch_id'] = $user->branch_id;
        }

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('trainers', 'public');
        }

        Trainer::create($validated);

        return redirect()->route('trainers.index')
            ->with('success', 'Trainer berhasil ditambahkan.');
    }

    public function show(Trainer $trainer)
    {
        $trainer->load(['branch', 'ptSessions' => fn($q) => $q->latest()->limit(10)]);
        return view('trainers.show', compact('trainer'));
    }

    public function edit(Trainer $trainer)
    {
        $user = Auth::user();
        $branches = $user->isSuperAdmin() ? Branch::active()->get() : collect([Auth::user()->branch]);
        return view('trainers.edit', compact('trainer', 'branches'));
    }

    public function update(Request $request, Trainer $trainer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:trainers,email,' . $trainer->id,
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female',
            'specialization' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'hourly_rate' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($trainer->photo) {
                Storage::disk('public')->delete($trainer->photo);
            }
            $validated['photo'] = $request->file('photo')->store('trainers', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        $trainer->update($validated);

        return redirect()->route('trainers.index')
            ->with('success', 'Data trainer berhasil diperbarui.');
    }

    public function destroy(Trainer $trainer)
    {
        if ($trainer->photo) {
            Storage::disk('public')->delete($trainer->photo);
        }

        $trainer->delete();

        return redirect()->route('trainers.index')
            ->with('success', 'Trainer berhasil dihapus.');
    }

    public function sessions(Request $request)
    {
        $user = Auth::user();
        $query = PtSession::with(['member', 'trainer']);

        if (!$user->isSuperAdmin()) {
            $query->where('branch_id', $user->branch_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('session_date', $request->date);
        }

        $sessions = $query->orderBy('session_date')->orderBy('start_time')->paginate(20);

        return view('trainers.sessions', compact('sessions'));
    }

    public function bookSession(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'trainer_id' => 'required|exists:trainers,id',
            'session_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'notes' => 'nullable|string',
        ]);

        $member = Member::findOrFail($request->member_id);
        $trainer = Trainer::findOrFail($request->trainer_id);

        // Check trainer availability
        if (!$trainer->isAvailable($request->session_date, $request->start_time, $request->end_time)) {
            return back()->with('error', 'Trainer tidak tersedia pada waktu tersebut.');
        }

        PtSession::create([
            'member_id' => $member->id,
            'trainer_id' => $trainer->id,
            'branch_id' => $member->branch_id,
            'session_date' => $request->session_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'notes' => $request->notes,
            'status' => 'scheduled',
        ]);

        return redirect()->route('trainers.sessions')
            ->with('success', 'Sesi PT berhasil dibooking.');
    }
}

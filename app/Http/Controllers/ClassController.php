<?php

namespace App\Http\Controllers;

use App\Models\GymClass;
use App\Models\ClassSchedule;
use App\Models\ClassBooking;
use App\Models\Trainer;
use App\Models\Member;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ClassController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = GymClass::with(['branch', 'schedules.trainer']);

        if (!$user->isSuperAdmin()) {
            $query->where('branch_id', $user->branch_id);
        }

        $classes = $query->get();
        return view('classes.index', compact('classes'));
    }

    public function create()
    {
        $user = Auth::user();
        $branches = $user->isSuperAdmin() ? Branch::active()->get() : collect([Auth::user()->branch]);
        $trainers = Trainer::active()->get();
        return view('classes.create', compact('branches', 'trainers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:15',
            'max_capacity' => 'required|integer|min:1',
            'branch_id' => 'required|exists:branches,id',
            'photo' => 'nullable|image|max:2048',
        ]);

        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            $validated['branch_id'] = $user->branch_id;
        }

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('classes', 'public');
        }

        GymClass::create($validated);

        return redirect()->route('classes.index')
            ->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function show(GymClass $class)
    {
        $class->load(['branch', 'schedules.trainer', 'schedules.bookings']);
        return view('classes.show', compact('class'));
    }

    public function edit(GymClass $class)
    {
        $user = Auth::user();
        $branches = $user->isSuperAdmin() ? Branch::active()->get() : collect([Auth::user()->branch]);
        $trainers = Trainer::active()->get();
        return view('classes.edit', compact('class', 'branches', 'trainers'));
    }

    public function update(Request $request, GymClass $class)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:15',
            'max_capacity' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($class->photo) {
                Storage::disk('public')->delete($class->photo);
            }
            $validated['photo'] = $request->file('photo')->store('classes', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        $class->update($validated);

        return redirect()->route('classes.index')
            ->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(GymClass $class)
    {
        if ($class->photo) {
            Storage::disk('public')->delete($class->photo);
        }

        $class->delete();

        return redirect()->route('classes.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }

    public function schedules()
    {
        $user = Auth::user();
        $today = strtolower(now()->format('l'));

        $query = ClassSchedule::with(['gymClass', 'trainer'])
            ->where('is_active', true)
            ->orderByRaw("FIELD(day_of_week, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday')")
            ->orderBy('start_time');

        if (!$user->isSuperAdmin()) {
            $query->whereHas('gymClass', fn($q) => $q->where('branch_id', $user->branch_id));
        }

        $schedules = $query->get()->groupBy('day_of_week');

        return view('classes.schedules', compact('schedules', 'today'));
    }

    public function book(Request $request, GymClass $class)
    {
        $request->validate([
            'schedule_id' => 'required|exists:class_schedules,id',
            'member_id' => 'required|exists:members,id',
            'booking_date' => 'required|date|after_or_equal:today',
        ]);

        $schedule = ClassSchedule::findOrFail($request->schedule_id);
        $member = Member::findOrFail($request->member_id);

        // Check if already booked
        $exists = ClassBooking::where('class_schedule_id', $schedule->id)
            ->where('member_id', $member->id)
            ->where('booking_date', $request->booking_date)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Member sudah booking kelas ini.');
        }

        // Check capacity
        if ($schedule->isFull($request->booking_date)) {
            return back()->with('error', 'Kelas sudah penuh.');
        }

        ClassBooking::create([
            'class_schedule_id' => $schedule->id,
            'member_id' => $member->id,
            'booking_date' => $request->booking_date,
            'status' => 'booked',
        ]);

        return back()->with('success', 'Booking berhasil.');
    }

    public function bookings(Request $request)
    {
        $user = Auth::user();
        $query = ClassBooking::with(['schedule.gymClass', 'member']);

        if ($request->filled('date')) {
            $query->where('booking_date', $request->date);
        } else {
            $query->where('booking_date', '>=', today());
        }

        $bookings = $query->orderBy('booking_date')->paginate(20);

        return view('classes.bookings', compact('bookings'));
    }
}

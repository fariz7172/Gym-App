<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\BodyMeasurement;
use App\Models\WorkoutLog;
use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProgressController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Member::with(['bodyMeasurements' => fn($q) => $q->latest()->limit(1)]);

        if (!$user->isSuperAdmin()) {
            $query->where('branch_id', $user->branch_id);
        }

        if ($request->filled('member_id')) {
            $member = Member::with([
                'bodyMeasurements' => fn($q) => $q->latest()->limit(10),
                'workoutLogs' => fn($q) => $q->latest()->limit(10),
                'goals',
            ])->findOrFail($request->member_id);

            return view('progress.show', compact('member'));
        }

        $members = $query->paginate(20);
        return view('progress.index', compact('members'));
    }

    public function store(Request $request)
    {
        // Body Measurement
        if ($request->type === 'measurement') {
            $validated = $request->validate([
                'member_id' => 'required|exists:members,id',
                'measured_at' => 'required|date',
                'weight' => 'nullable|numeric',
                'height' => 'nullable|numeric',
                'body_fat_percentage' => 'nullable|numeric',
                'chest' => 'nullable|numeric',
                'waist' => 'nullable|numeric',
                'hips' => 'nullable|numeric',
                'arms' => 'nullable|numeric',
                'thighs' => 'nullable|numeric',
                'notes' => 'nullable|string',
            ]);

            BodyMeasurement::create($validated);
            return back()->with('success', 'Pengukuran berhasil disimpan.');
        }

        // Workout Log
        if ($request->type === 'workout') {
            $validated = $request->validate([
                'member_id' => 'required|exists:members,id',
                'workout_date' => 'required|date',
                'exercises' => 'nullable|array',
                'duration_minutes' => 'nullable|integer',
                'calories_burned' => 'nullable|integer',
                'notes' => 'nullable|string',
            ]);

            WorkoutLog::create($validated);
            return back()->with('success', 'Workout log berhasil disimpan.');
        }

        // Goal
        if ($request->type === 'goal') {
            $validated = $request->validate([
                'member_id' => 'required|exists:members,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'goal_type' => 'nullable|string',
                'target_value' => 'nullable|numeric',
                'current_value' => 'nullable|numeric',
                'unit' => 'nullable|string',
                'target_date' => 'nullable|date',
            ]);

            Goal::create($validated);
            return back()->with('success', 'Goal berhasil ditambahkan.');
        }

        return back()->with('error', 'Tipe tidak valid.');
    }

    public function update(Request $request, $id)
    {
        // Update goal progress
        $goal = Goal::findOrFail($id);

        $validated = $request->validate([
            'current_value' => 'required|numeric',
            'status' => 'nullable|in:in_progress,achieved,abandoned',
        ]);

        $goal->update($validated);

        return back()->with('success', 'Progress berhasil diperbarui.');
    }

    public function destroy(Request $request, $id)
    {
        $type = $request->type;

        if ($type === 'measurement') {
            BodyMeasurement::findOrFail($id)->delete();
        } elseif ($type === 'workout') {
            WorkoutLog::findOrFail($id)->delete();
        } elseif ($type === 'goal') {
            Goal::findOrFail($id)->delete();
        }

        return back()->with('success', 'Data berhasil dihapus.');
    }
}

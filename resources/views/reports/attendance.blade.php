@extends('layouts.app')

@section('title', 'Laporan Kehadiran')
@section('page-title', 'Laporan Kehadiran')

@section('content')
<div class="card" style="margin-bottom: 1.5rem;">
    <form method="GET" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Tanggal</label>
            <input type="date" name="date" class="form-input" value="{{ $date->format('Y-m-d') }}">
        </div>
        @if(auth()->user()->isSuperAdmin() && $branches->count() > 0)
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Cabang</label>
            <select name="branch_id" class="form-input">
                <option value="">Semua</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-filter"></i> Filter
        </button>
    </form>
</div>

<!-- Summary -->
<div class="grid grid-cols-3" style="margin-bottom: 1.5rem;">
    <div class="stat-card gradient-1">
        <div class="stat-icon primary"><i class="fas fa-door-open"></i></div>
        <div class="stat-value">{{ $summary['total'] }}</div>
        <div class="stat-label">Total Kunjungan</div>
    </div>
    <div class="stat-card gradient-2">
        <div class="stat-icon success"><i class="fas fa-running"></i></div>
        <div class="stat-value">{{ $summary['currently_in'] }}</div>
        <div class="stat-label">Sedang di Gym</div>
    </div>
    <div class="stat-card gradient-3">
        <div class="stat-icon accent"><i class="fas fa-clock"></i></div>
        <div class="stat-value">{{ $summary['avg_duration'] ? round($summary['avg_duration']) : 0 }} min</div>
        <div class="stat-label">Rata-rata Durasi</div>
    </div>
</div>

<!-- Attendance List -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Detail Kehadiran - {{ $date->format('d M Y') }}</h3>
    </div>
    
    @if($attendances->count() > 0)
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Member</th>
                    @if(auth()->user()->isSuperAdmin())
                    <th>Cabang</th>
                    @endif
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Durasi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendances as $att)
                <tr>
                    <td>
                        <div style="font-weight: 600;">{{ $att->member->name }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary);">{{ $att->member->member_code }}</div>
                    </td>
                    @if(auth()->user()->isSuperAdmin())
                    <td>{{ $att->branch->name ?? '-' }}</td>
                    @endif
                    <td>{{ $att->check_in->format('H:i') }}</td>
                    <td>{{ $att->check_out?->format('H:i') ?? '-' }}</td>
                    <td>{{ $att->duration_text }}</td>
                    <td>
                        @if($att->isCheckedIn())
                            <span class="badge badge-success">Di Gym</span>
                        @else
                            <span class="badge badge-secondary">Selesai</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty-state">
        <i class="fas fa-calendar-xmark empty-state-icon"></i>
        <div class="empty-state-title">Tidak ada data</div>
    </div>
    @endif
</div>
@endsection

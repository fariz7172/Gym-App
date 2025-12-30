@extends('layouts.app')

@section('title', 'Riwayat Kehadiran')
@section('page-title', 'Riwayat Kehadiran')

@section('content')
<div class="card" style="margin-bottom: 1.5rem;">
    <form method="GET" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Tanggal</label>
            <input type="date" name="date" class="form-input" value="{{ request('date', today()->format('Y-m-d')) }}">
        </div>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-filter"></i> Filter
        </button>
        <a href="{{ route('attendance.index') }}" class="btn btn-secondary">
            <i class="fas fa-qrcode"></i> Check-in
        </a>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-history" style="color: var(--secondary); margin-right: 0.5rem;"></i>
            Riwayat Kehadiran
        </h3>
        <span class="badge badge-primary">{{ $attendances->total() }} data</span>
    </div>
    
    @if($attendances->count() > 0)
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Member</th>
                    @if(auth()->user()->isSuperAdmin())
                    <th>Cabang</th>
                    @endif
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Durasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendances as $attendance)
                <tr>
                    <td>{{ $attendance->check_in->format('d M Y') }}</td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div class="avatar-sm">
                                {{ strtoupper(substr($attendance->member->name, 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-weight: 600;">{{ $attendance->member->name }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-secondary);">{{ $attendance->member->member_code }}</div>
                            </div>
                        </div>
                    </td>
                    @if(auth()->user()->isSuperAdmin())
                    <td>{{ $attendance->branch->name ?? '-' }}</td>
                    @endif
                    <td>{{ $attendance->check_in->format('H:i') }}</td>
                    <td>{{ $attendance->check_out?->format('H:i') ?? '-' }}</td>
                    <td>{{ $attendance->duration_text }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div style="padding: 1rem; display: flex; justify-content: center;">
        {{ $attendances->links() }}
    </div>
    @else
    <div class="empty-state">
        <i class="fas fa-calendar-xmark empty-state-icon"></i>
        <div class="empty-state-title">Tidak ada data</div>
        <p class="empty-state-text">Tidak ada riwayat kehadiran pada tanggal ini</p>
    </div>
    @endif
</div>
@endsection

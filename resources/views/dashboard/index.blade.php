@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="grid grid-cols-4" style="margin-bottom: 1.5rem;">
    <!-- Total Members -->
    <div class="stat-card gradient-1 animate-fade-in" style="animation-delay: 0.1s;">
        <div class="stat-icon primary">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-value">{{ number_format($stats['total_members']) }}</div>
        <div class="stat-label">Total Member</div>
        <div class="stat-change positive">
            <i class="fas fa-arrow-up"></i>
            {{ $stats['new_members_this_month'] }} bulan ini
        </div>
    </div>
    
    <!-- Active Members -->
    <div class="stat-card gradient-2 animate-fade-in" style="animation-delay: 0.2s;">
        <div class="stat-icon success">
            <i class="fas fa-user-check"></i>
        </div>
        <div class="stat-value">{{ number_format($stats['active_members']) }}</div>
        <div class="stat-label">Member Aktif</div>
        <div class="stat-change positive">
            <i class="fas fa-check"></i>
            {{ round(($stats['active_members'] / max($stats['total_members'], 1)) * 100) }}% aktif
        </div>
    </div>
    
    <!-- Today Attendance -->
    <div class="stat-card gradient-3 animate-fade-in" style="animation-delay: 0.3s;">
        <div class="stat-icon accent">
            <i class="fas fa-door-open"></i>
        </div>
        <div class="stat-value">{{ number_format($stats['today_attendance']) }}</div>
        <div class="stat-label">Kunjungan Hari Ini</div>
        <div class="stat-change positive">
            <i class="fas fa-running"></i>
            {{ $stats['currently_in'] }} sedang di gym
        </div>
    </div>
    
    <!-- Month Revenue -->
    <div class="stat-card gradient-4 animate-fade-in" style="animation-delay: 0.4s;">
        <div class="stat-icon warning">
            <i class="fas fa-coins"></i>
        </div>
        <div class="stat-value" style="font-size: 1.5rem;">Rp {{ number_format($stats['month_revenue'] / 1000000, 1) }}jt</div>
        <div class="stat-label">Pendapatan Bulan Ini</div>
        <div class="stat-change positive">
            <i class="fas fa-chart-line"></i>
            Rp {{ number_format($stats['today_revenue'] / 1000, 0) }}rb hari ini
        </div>
    </div>
</div>

<div class="grid grid-cols-3">
    <!-- Revenue Chart -->
    <div class="card animate-fade-in" style="grid-column: span 2; animation-delay: 0.5s;">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-chart-area" style="color: var(--primary); margin-right: 0.5rem;"></i>
                Pendapatan 7 Hari Terakhir
            </h3>
        </div>
        <div style="height: 300px;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
    
    <!-- Expiring Memberships -->
    <div class="card animate-fade-in" style="animation-delay: 0.6s;">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-exclamation-triangle" style="color: var(--warning); margin-right: 0.5rem;"></i>
                Membership Segera Habis
            </h3>
            <span class="badge badge-warning">{{ $stats['expiring_soon'] }}</span>
        </div>
        
        @if($expiringMemberships->count() > 0)
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                @foreach($expiringMemberships as $membership)
                <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: rgba(245, 158, 11, 0.05); border-radius: 10px;">
                    <div class="avatar-sm" style="background: linear-gradient(135deg, var(--warning), #D97706);">
                        {{ strtoupper(substr($membership->member->name, 0, 1)) }}
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-weight: 600; font-size: 0.875rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ $membership->member->name }}
                        </div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary);">
                            {{ $membership->plan->name }}
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-weight: 600; font-size: 0.875rem; color: var(--warning);">
                            {{ $membership->days_remaining }}d
                        </div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary);">tersisa</div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="empty-state" style="padding: 2rem;">
                <i class="fas fa-check-circle" style="font-size: 2rem; color: var(--success);"></i>
                <p class="text-muted" style="margin-top: 0.5rem;">Tidak ada membership yang segera habis</p>
            </div>
        @endif
    </div>
</div>

<div class="grid grid-cols-2" style="margin-top: 1.5rem;">
    <!-- Today's Attendance -->
    <div class="card animate-fade-in" style="animation-delay: 0.7s;">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-history" style="color: var(--secondary); margin-right: 0.5rem;"></i>
                Check-in Terbaru
            </h3>
            <a href="{{ route('attendance.index') }}" class="btn btn-sm btn-secondary">
                Lihat Semua
            </a>
        </div>
        
        @if($recentAttendances->count() > 0)
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Check-in</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentAttendances as $attendance)
                        <tr>
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
                            <td>{{ $attendance->check_in->format('H:i') }}</td>
                            <td>
                                @if($attendance->isCheckedIn())
                                    <span class="badge badge-success">
                                        <i class="fas fa-circle" style="font-size: 0.5rem; margin-right: 0.25rem;"></i>
                                        Di Gym
                                    </span>
                                @else
                                    <span class="badge badge-secondary">
                                        Keluar {{ $attendance->check_out->format('H:i') }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-door-closed empty-state-icon"></i>
                <div class="empty-state-title">Belum ada check-in</div>
                <p class="empty-state-text">Member yang check-in akan muncul di sini</p>
            </div>
        @endif
    </div>
    
    <!-- Today's Classes -->
    <div class="card animate-fade-in" style="animation-delay: 0.8s;">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-calendar-day" style="color: var(--primary); margin-right: 0.5rem;"></i>
                Jadwal Kelas Hari Ini
            </h3>
            <a href="{{ route('classes.index') }}" class="btn btn-sm btn-secondary">
                Lihat Semua
            </a>
        </div>
        
        @if($upcomingClasses->count() > 0)
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                @foreach($upcomingClasses as $schedule)
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: rgba(99, 102, 241, 0.05); border-radius: 12px; border-left: 4px solid var(--primary);">
                    <div style="text-align: center; min-width: 60px;">
                        <div style="font-size: 1.25rem; font-weight: 700; color: var(--primary);">{{ is_string($schedule->start_time) ? $schedule->start_time : $schedule->start_time->format('H:i') }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary);">{{ is_string($schedule->end_time) ? $schedule->end_time : $schedule->end_time->format('H:i') }}</div>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 600;">{{ $schedule->gymClass->name }}</div>
                        <div style="font-size: 0.875rem; color: var(--text-secondary);">
                            <i class="fas fa-user" style="margin-right: 0.25rem;"></i>
                            {{ $schedule->trainer->name ?? 'TBA' }}
                            @if($schedule->room)
                                <span style="margin-left: 0.75rem;">
                                    <i class="fas fa-door-open" style="margin-right: 0.25rem;"></i>
                                    {{ $schedule->room }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <span class="badge badge-primary">
                            {{ $schedule->gymClass->max_capacity }} slot
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-calendar-xmark empty-state-icon"></i>
                <div class="empty-state-title">Tidak ada kelas</div>
                <p class="empty-state-text">Tidak ada jadwal kelas untuk hari ini</p>
            </div>
        @endif
    </div>
</div>

@if(auth()->user()->isSuperAdmin() && $branches->count() > 0)
<div class="card animate-fade-in" style="margin-top: 1.5rem; animation-delay: 0.9s;">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-building" style="color: var(--primary); margin-right: 0.5rem;"></i>
            Statistik Per Cabang
        </h3>
    </div>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Cabang</th>
                    <th>Total Member</th>
                    <th>Member Aktif</th>
                    <th>Kunjungan Hari Ini</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($branches as $branch)
                <tr>
                    <td>
                        <div style="font-weight: 600;">{{ $branch->name }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary);">{{ $branch->code }}</div>
                    </td>
                    <td>{{ $branch->total_members }}</td>
                    <td>{{ $branch->active_members }}</td>
                    <td>{{ $branch->today_attendance }}</td>
                    <td>
                        @if($branch->is_active)
                            <span class="badge badge-success">Aktif</span>
                        @else
                            <span class="badge badge-secondary">Nonaktif</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const revenueData = @json($revenueData);
    
    const isDark = document.documentElement.classList.contains('dark');
    const textColor = isDark ? '#94A3B8' : '#64748B';
    const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: revenueData.map(d => d.date),
            datasets: [{
                label: 'Pendapatan',
                data: revenueData.map(d => d.amount),
                borderColor: '#6366F1',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#6366F1',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: isDark ? '#1E293B' : '#fff',
                    titleColor: isDark ? '#F1F5F9' : '#1E293B',
                    bodyColor: isDark ? '#94A3B8' : '#64748B',
                    borderColor: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)',
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return 'Rp ' + context.raw.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: textColor }
                },
                y: {
                    grid: { color: gridColor },
                    ticks: {
                        color: textColor,
                        callback: function(value) {
                            return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush

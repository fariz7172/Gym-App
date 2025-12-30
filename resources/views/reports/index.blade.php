@extends('layouts.app')

@section('title', 'Laporan')
@section('page-title', 'Laporan')

@section('content')
<!-- Quick Stats -->
<div class="grid grid-cols-4" style="margin-bottom: 1.5rem;">
    <div class="stat-card gradient-1">
        <div class="stat-icon primary">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-value">{{ number_format($stats['total_members']) }}</div>
        <div class="stat-label">Total Member</div>
    </div>
    
    <div class="stat-card gradient-2">
        <div class="stat-icon success">
            <i class="fas fa-user-check"></i>
        </div>
        <div class="stat-value">{{ number_format($stats['active_members']) }}</div>
        <div class="stat-label">Member Aktif</div>
    </div>
    
    <div class="stat-card gradient-3">
        <div class="stat-icon accent">
            <i class="fas fa-coins"></i>
        </div>
        <div class="stat-value">Rp {{ number_format($stats['month_revenue'] / 1000000, 1) }}jt</div>
        <div class="stat-label">Pendapatan Bulan Ini</div>
    </div>
    
    <div class="stat-card gradient-4">
        <div class="stat-icon warning">
            <i class="fas fa-door-open"></i>
        </div>
        <div class="stat-value">{{ number_format($stats['month_attendance']) }}</div>
        <div class="stat-label">Kunjungan Bulan Ini</div>
    </div>
</div>

<!-- Report Links -->
<div class="grid grid-cols-3">
    <a href="{{ route('reports.members') }}" class="card" style="text-decoration: none; color: inherit;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-users" style="font-size: 1.25rem; color: white;"></i>
            </div>
            <div>
                <h3 style="font-weight: 700;">Laporan Member</h3>
                <p class="text-muted" style="font-size: 0.875rem;">Data lengkap semua member</p>
            </div>
            <i class="fas fa-chevron-right" style="margin-left: auto; color: var(--text-secondary);"></i>
        </div>
    </a>
    
    <a href="{{ route('reports.revenue') }}" class="card" style="text-decoration: none; color: inherit;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, var(--success), #059669); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-chart-line" style="font-size: 1.25rem; color: white;"></i>
            </div>
            <div>
                <h3 style="font-weight: 700;">Laporan Pendapatan</h3>
                <p class="text-muted" style="font-size: 0.875rem;">Analisis revenue & pembayaran</p>
            </div>
            <i class="fas fa-chevron-right" style="margin-left: auto; color: var(--text-secondary);"></i>
        </div>
    </a>
    
    <a href="{{ route('reports.attendance') }}" class="card" style="text-decoration: none; color: inherit;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, var(--warning), #D97706); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-calendar-check" style="font-size: 1.25rem; color: white;"></i>
            </div>
            <div>
                <h3 style="font-weight: 700;">Laporan Kehadiran</h3>
                <p class="text-muted" style="font-size: 0.875rem;">Statistik check-in member</p>
            </div>
            <i class="fas fa-chevron-right" style="margin-left: auto; color: var(--text-secondary);"></i>
        </div>
    </a>
</div>

@if(auth()->user()->isSuperAdmin() && $branches->count() > 0)
<div class="card" style="margin-top: 1.5rem;">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-building" style="color: var(--primary); margin-right: 0.5rem;"></i>
            Perbandingan Cabang
        </h3>
    </div>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Cabang</th>
                    <th>Total Member</th>
                    <th>Member Aktif</th>
                    <th>Pendapatan Bulan Ini</th>
                    <th>Kunjungan Bulan Ini</th>
                </tr>
            </thead>
            <tbody>
                @foreach($branches as $branch)
                <tr>
                    <td style="font-weight: 600;">{{ $branch->name }}</td>
                    <td>{{ $branch->members()->count() }}</td>
                    <td>{{ $branch->members()->where('status', 'active')->count() }}</td>
                    <td>Rp {{ number_format($branch->payments()->thisMonth()->paid()->sum('amount')) }}</td>
                    <td>{{ $branch->attendances()->thisMonth()->count() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection

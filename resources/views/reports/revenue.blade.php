@extends('layouts.app')

@section('title', 'Laporan Pendapatan')
@section('page-title', 'Laporan Pendapatan')

@section('content')
<div class="card" style="margin-bottom: 1.5rem;">
    <form method="GET" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Dari Tanggal</label>
            <input type="date" name="start_date" class="form-input" value="{{ $startDate->format('Y-m-d') }}">
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Sampai Tanggal</label>
            <input type="date" name="end_date" class="form-input" value="{{ $endDate->format('Y-m-d') }}">
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

<!-- Summary Cards -->
<div class="grid grid-cols-3" style="margin-bottom: 1.5rem;">
    <div class="stat-card gradient-1">
        <div class="stat-icon primary"><i class="fas fa-coins"></i></div>
        <div class="stat-value">Rp {{ number_format($summary['total']) }}</div>
        <div class="stat-label">Total Pendapatan</div>
    </div>
    <div class="stat-card gradient-2">
        <div class="stat-icon success"><i class="fas fa-receipt"></i></div>
        <div class="stat-value">{{ $summary['count'] }}</div>
        <div class="stat-label">Total Transaksi</div>
    </div>
    <div class="stat-card gradient-3">
        <div class="stat-icon accent"><i class="fas fa-calculator"></i></div>
        <div class="stat-value">Rp {{ $summary['count'] > 0 ? number_format($summary['total'] / $summary['count']) : 0 }}</div>
        <div class="stat-label">Rata-rata Transaksi</div>
    </div>
</div>

<!-- By Payment Method -->
<div class="card" style="margin-bottom: 1.5rem;">
    <h3 class="card-title" style="margin-bottom: 1rem;">Berdasarkan Metode Pembayaran</h3>
    <div style="display: flex; flex-wrap: wrap; gap: 1rem;">
        @foreach($summary['by_method'] as $method => $amount)
        <div style="flex: 1; min-width: 150px; padding: 1rem; background: var(--bg-secondary); border-radius: 8px; text-align: center;">
            <div style="font-size: 1.25rem; font-weight: 700;">Rp {{ number_format($amount) }}</div>
            <div class="text-muted">{{ ucfirst($method) }}</div>
        </div>
        @endforeach
    </div>
</div>

<!-- Transaction List -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Detail Transaksi</h3>
    </div>
    
    @if($payments->count() > 0)
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Invoice</th>
                    <th>Member</th>
                    <th>Metode</th>
                    <th style="text-align: right;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                <tr>
                    <td>{{ $payment->payment_date->format('d M Y') }}</td>
                    <td><code>{{ $payment->invoice_number }}</code></td>
                    <td>{{ $payment->member->name }}</td>
                    <td>{{ ucfirst($payment->payment_method) }}</td>
                    <td style="text-align: right; font-weight: 600;">{{ $payment->formatted_amount }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty-state">
        <i class="fas fa-receipt empty-state-icon"></i>
        <div class="empty-state-title">Tidak ada data</div>
    </div>
    @endif
</div>
@endsection

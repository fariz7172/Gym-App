@extends('layouts.app')

@section('title', 'Pembayaran')
@section('page-title', 'Pembayaran')

@section('content')
<!-- Stats -->
<div class="grid grid-cols-3" style="margin-bottom: 1.5rem;">
    <div class="stat-card gradient-1">
        <div class="stat-icon primary">
            <i class="fas fa-calendar-day"></i>
        </div>
        <div class="stat-value">Rp {{ number_format($stats['today'] / 1000, 0) }}rb</div>
        <div class="stat-label">Hari Ini</div>
    </div>
    
    <div class="stat-card gradient-2">
        <div class="stat-icon success">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <div class="stat-value">Rp {{ number_format($stats['this_month'] / 1000000, 1) }}jt</div>
        <div class="stat-label">Bulan Ini</div>
    </div>
    
    <div class="stat-card gradient-3">
        <div class="stat-icon warning">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-value">{{ $stats['pending'] }}</div>
        <div class="stat-label">Menunggu Pembayaran</div>
    </div>
</div>

<!-- Filters -->
<div class="card" style="margin-bottom: 1.5rem;">
    <form method="GET" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Dari Tanggal</label>
            <input type="date" name="date_from" class="form-input" value="{{ request('date_from') }}">
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Sampai Tanggal</label>
            <input type="date" name="date_to" class="form-input" value="{{ request('date_to') }}">
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Status</label>
            <select name="status" class="form-input">
                <option value="">Semua</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Lunas</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-filter"></i> Filter
        </button>
    </form>
</div>

<!-- Payments Table -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Riwayat Pembayaran</h3>
    </div>
    
    @if($payments->count() > 0)
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Member</th>
                    <th>Tanggal</th>
                    <th>Jumlah</th>
                    <th>Metode</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                <tr>
                    <td>
                        <code style="background: rgba(99, 102, 241, 0.1); padding: 0.25rem 0.5rem; border-radius: 4px;">
                            {{ $payment->invoice_number }}
                        </code>
                    </td>
                    <td>
                        <div style="font-weight: 600;">{{ $payment->member->name }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary);">{{ $payment->membership?->plan?->name ?? '-' }}</div>
                    </td>
                    <td>{{ $payment->payment_date->format('d M Y') }}</td>
                    <td style="font-weight: 600;">{{ $payment->formatted_amount }}</td>
                    <td>{{ $payment->payment_method_label }}</td>
                    <td>
                        @if($payment->status == 'paid')
                            <span class="badge badge-success">Lunas</span>
                        @elseif($payment->status == 'pending')
                            <span class="badge badge-warning">Pending</span>
                        @else
                            <span class="badge badge-danger">{{ $payment->status }}</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('payments.invoice', $payment) }}" class="btn btn-sm btn-secondary" target="_blank">
                            <i class="fas fa-file-invoice"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div style="padding: 1rem; display: flex; justify-content: center;">
        {{ $payments->links() }}
    </div>
    @else
    <div class="empty-state">
        <i class="fas fa-receipt empty-state-icon"></i>
        <div class="empty-state-title">Belum ada pembayaran</div>
        <p class="empty-state-text">Pembayaran akan muncul saat member melakukan transaksi</p>
    </div>
    @endif
</div>
@endsection

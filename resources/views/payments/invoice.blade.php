@extends('layouts.app')

@section('title', 'Invoice')
@section('page-title', 'Invoice Pembayaran')

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
    <div class="card" id="invoice">
        <div style="text-align: center; margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 2px solid var(--border);">
            <div style="font-size: 2rem; font-weight: 800; background: linear-gradient(135deg, var(--primary), var(--secondary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">GymPro</div>
            <div class="text-muted">{{ $payment->branch->name ?? 'Gym Management System' }}</div>
            <div class="text-muted" style="font-size: 0.875rem;">{{ $payment->branch->address ?? '' }}</div>
        </div>
        
        <div style="display: flex; justify-content: space-between; margin-bottom: 2rem;">
            <div>
                <div class="text-muted" style="font-size: 0.75rem;">INVOICE</div>
                <div style="font-size: 1.25rem; font-weight: 700;">{{ $payment->invoice_number }}</div>
            </div>
            <div style="text-align: right;">
                <div class="text-muted" style="font-size: 0.75rem;">TANGGAL</div>
                <div style="font-weight: 600;">{{ $payment->payment_date->format('d M Y') }}</div>
            </div>
        </div>
        
        <div style="background: var(--bg-secondary); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
            <div class="text-muted" style="font-size: 0.75rem; margin-bottom: 0.25rem;">MEMBER</div>
            <div style="font-weight: 600;">{{ $payment->member->name }}</div>
            <div class="text-muted">{{ $payment->member->member_code }}</div>
        </div>
        
        <table style="width: 100%; margin-bottom: 1.5rem;">
            <thead>
                <tr style="border-bottom: 1px solid var(--border);">
                    <th style="text-align: left; padding: 0.75rem 0; font-weight: 600;">Deskripsi</th>
                    <th style="text-align: right; padding: 0.75rem 0; font-weight: 600;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 0.75rem 0;">
                        {{ $payment->membership?->plan?->name ?? 'Pembayaran' }}
                        @if($payment->membership)
                        <div class="text-muted" style="font-size: 0.875rem;">
                            {{ $payment->membership->start_date->format('d M Y') }} - {{ $payment->membership->end_date->format('d M Y') }}
                        </div>
                        @endif
                    </td>
                    <td style="text-align: right; padding: 0.75rem 0; font-weight: 600;">{{ $payment->formatted_amount }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr style="border-top: 2px solid var(--border);">
                    <td style="padding: 0.75rem 0; font-weight: 700;">TOTAL</td>
                    <td style="text-align: right; padding: 0.75rem 0; font-size: 1.25rem; font-weight: 700; color: var(--primary);">{{ $payment->formatted_amount }}</td>
                </tr>
            </tfoot>
        </table>
        
        <div style="display: flex; gap: 2rem; margin-bottom: 1.5rem;">
            <div>
                <div class="text-muted" style="font-size: 0.75rem;">METODE</div>
                <div style="font-weight: 600;">{{ $payment->payment_method_label }}</div>
            </div>
            <div>
                <div class="text-muted" style="font-size: 0.75rem;">STATUS</div>
                @if($payment->status == 'paid')
                    <span class="badge badge-success">LUNAS</span>
                @else
                    <span class="badge badge-warning">{{ strtoupper($payment->status) }}</span>
                @endif
            </div>
        </div>
        
        @if($payment->notes)
        <div style="background: var(--bg-secondary); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
            <div class="text-muted" style="font-size: 0.75rem; margin-bottom: 0.25rem;">CATATAN</div>
            <div>{{ $payment->notes }}</div>
        </div>
        @endif
        
        <div style="text-align: center; padding-top: 1rem; border-top: 1px solid var(--border);">
            <p class="text-muted" style="font-size: 0.875rem;">Terima kasih atas kepercayaan Anda!</p>
        </div>
    </div>
    
    <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
        <a href="{{ route('payments.index') }}" class="btn btn-secondary" style="flex: 1;">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <button onclick="window.print()" class="btn btn-primary" style="flex: 1;">
            <i class="fas fa-print"></i> Cetak
        </button>
    </div>
</div>

@push('styles')
<style>
@media print {
    .sidebar, .top-navbar, .btn, button { display: none !important; }
    .main-content { margin-left: 0 !important; }
    .card { box-shadow: none; border: 1px solid #ccc; }
}
</style>
@endpush
@endsection

@extends('layouts.app')

@section('title', 'QR Code - ' . $member->name)
@section('page-title', 'QR Code Member')

@section('content')
<div style="max-width: 400px; margin: 0 auto; text-align: center;">
    <div class="card">
        <div class="avatar-lg" style="width: 80px; height: 80px; margin: 0 auto 1rem; font-size: 1.5rem;">
            @if($member->photo)
                <img src="{{ Storage::url($member->photo) }}" alt="{{ $member->name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
            @else
                {{ strtoupper(substr($member->name, 0, 1)) }}
            @endif
        </div>
        
        <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem;">{{ $member->name }}</h2>
        <div class="text-muted" style="margin-bottom: 1.5rem;">{{ $member->branch->name ?? '' }}</div>
        
        <!-- QR Code Placeholder -->
        <div style="background: white; padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; display: inline-block;">
            <div id="qrcode" style="width: 200px; height: 200px; margin: 0 auto;"></div>
        </div>
        
        <div style="background: rgba(99, 102, 241, 0.1); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
            <div style="font-size: 1.5rem; font-weight: 800; letter-spacing: 0.1em; color: var(--primary);">
                {{ $member->member_code }}
            </div>
        </div>
        
        @if($member->activeMembership)
        <div class="text-muted" style="font-size: 0.875rem;">
            Berlaku hingga: <strong>{{ $member->membership_expiry->format('d M Y') }}</strong>
        </div>
        @endif
        
        <div style="display: flex; gap: 0.5rem; margin-top: 1.5rem;">
            <a href="{{ route('members.show', $member) }}" class="btn btn-secondary" style="flex: 1;">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <button onclick="window.print()" class="btn btn-primary" style="flex: 1;">
                <i class="fas fa-print"></i> Cetak
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var qr = qrcode(0, 'M');
    qr.addData('{{ $member->member_code }}');
    qr.make();
    document.getElementById('qrcode').innerHTML = qr.createSvgTag(5, 0);
});
</script>
@endpush

@push('styles')
<style>
@media print {
    .sidebar, .top-navbar, .btn { display: none !important; }
    .main-content { margin-left: 0 !important; }
    .card { box-shadow: none; border: 1px solid #ccc; }
}
</style>
@endpush
@endsection

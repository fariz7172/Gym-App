@extends('layouts.app')

@section('title', 'Paket Membership')
@section('page-title', 'Paket Membership')

@section('content')
<div class="card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <div>
        <h2 style="font-size: 1.5rem; font-weight: 700;">Paket Membership</h2>
        <p class="text-muted">Kelola paket membership yang tersedia</p>
    </div>
</div>

<div class="grid grid-cols-4">
    @foreach($plans as $plan)
    <div class="card" style="border: 2px solid {{ $plan->is_active ? 'var(--primary)' : 'var(--border)' }};">
        <div style="text-align: center; padding: 1rem 0;">
            <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.25rem;">{{ $plan->duration_text }}</div>
            <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem;">{{ $plan->name }}</h3>
            <div style="font-size: 2rem; font-weight: 800; color: var(--primary);">{{ $plan->formatted_price }}</div>
        </div>
        
        <div style="border-top: 1px solid var(--border); padding-top: 1rem; margin-top: 1rem;">
            @if($plan->features)
            <ul style="list-style: none; padding: 0; margin: 0;">
                @foreach($plan->features as $feature)
                <li style="padding: 0.25rem 0; font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-check" style="color: var(--success);"></i>
                    {{ $feature }}
                </li>
                @endforeach
            </ul>
            @endif
        </div>
        
        <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border); text-align: center;">
            <div style="font-size: 0.875rem; color: var(--text-secondary);">
                {{ $plan->memberships_count ?? 0 }} member aktif
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection

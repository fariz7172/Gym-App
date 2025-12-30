@extends('layouts.app')

@section('title', $member->name)
@section('page-title', 'Detail Member')

@section('content')
<div class="grid grid-cols-3">
    <!-- Profile Card -->
    <div class="card">
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <div class="avatar-lg" style="width: 100px; height: 100px; margin: 0 auto 1rem; font-size: 2rem;">
                @if($member->photo)
                    <img src="{{ Storage::url($member->photo) }}" alt="{{ $member->name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                @else
                    {{ strtoupper(substr($member->name, 0, 1)) }}
                @endif
            </div>
            <h2 style="font-size: 1.25rem; font-weight: 700;">{{ $member->name }}</h2>
            <code style="font-size: 0.875rem; background: rgba(99, 102, 241, 0.1); padding: 0.25rem 0.75rem; border-radius: 4px;">{{ $member->member_code }}</code>
            
            <div style="margin-top: 1rem;">
                @if($member->status === 'active')
                    <span class="badge badge-success" style="font-size: 0.875rem; padding: 0.5rem 1rem;">Aktif</span>
                @elseif($member->status === 'expired')
                    <span class="badge badge-danger" style="font-size: 0.875rem; padding: 0.5rem 1rem;">Expired</span>
                @else
                    <span class="badge badge-warning" style="font-size: 0.875rem; padding: 0.5rem 1rem;">Frozen</span>
                @endif
            </div>
        </div>
        
        <div style="border-top: 1px solid var(--border); padding-top: 1rem;">
            @if($member->phone)
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                <i class="fas fa-phone" style="width: 20px; color: var(--text-secondary);"></i>
                <span>{{ $member->phone }}</span>
            </div>
            @endif
            @if($member->email)
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                <i class="fas fa-envelope" style="width: 20px; color: var(--text-secondary);"></i>
                <span>{{ $member->email }}</span>
            </div>
            @endif
            @if($member->date_of_birth)
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                <i class="fas fa-birthday-cake" style="width: 20px; color: var(--text-secondary);"></i>
                <span>{{ $member->date_of_birth->format('d M Y') }} ({{ $member->age }} tahun)</span>
            </div>
            @endif
            @if($member->address)
            <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                <i class="fas fa-map-marker-alt" style="width: 20px; color: var(--text-secondary); margin-top: 0.25rem;"></i>
                <span>{{ $member->address }}</span>
            </div>
            @endif
        </div>
        
        <div style="display: flex; gap: 0.5rem; margin-top: 1.5rem;">
            <a href="{{ route('members.edit', $member) }}" class="btn btn-primary" style="flex: 1;">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('members.qrcode', $member) }}" class="btn btn-secondary">
                <i class="fas fa-qrcode"></i>
            </a>
        </div>
    </div>
    
    <!-- Main Content -->
    <div style="grid-column: span 2;">
        <!-- Membership Info -->
        <div class="card" style="margin-bottom: 1.5rem;">
            <h3 class="card-title" style="margin-bottom: 1rem;">
                <i class="fas fa-id-card" style="color: var(--primary); margin-right: 0.5rem;"></i>
                Membership Aktif
            </h3>
            
            @if($member->activeMembership)
            <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
                <div>
                    <div class="text-muted" style="font-size: 0.75rem;">PAKET</div>
                    <div style="font-weight: 600;">{{ $member->activeMembership->plan->name }}</div>
                </div>
                <div>
                    <div class="text-muted" style="font-size: 0.75rem;">BERLAKU</div>
                    <div style="font-weight: 600;">{{ $member->activeMembership->start_date->format('d M Y') }} - {{ $member->activeMembership->end_date->format('d M Y') }}</div>
                </div>
                <div>
                    <div class="text-muted" style="font-size: 0.75rem;">SISA</div>
                    <div style="font-weight: 600; color: {{ $member->activeMembership->days_remaining <= 7 ? 'var(--warning)' : 'var(--success)' }};">
                        {{ $member->activeMembership->days_remaining }} hari
                    </div>
                </div>
            </div>
            
            <div style="margin-top: 1rem;">
                <div style="background: var(--bg-secondary); border-radius: 8px; height: 8px; overflow: hidden;">
                    <div style="background: linear-gradient(90deg, var(--primary), var(--secondary)); height: 100%; width: {{ $member->activeMembership->progress_percentage }}%;"></div>
                </div>
            </div>
            @else
            <div class="text-muted">Tidak ada membership aktif</div>
            @endif
        </div>
        
        <!-- Recent Attendance -->
        <div class="card" style="margin-bottom: 1.5rem;">
            <h3 class="card-title" style="margin-bottom: 1rem;">
                <i class="fas fa-history" style="color: var(--secondary); margin-right: 0.5rem;"></i>
                Riwayat Kunjungan Terakhir
            </h3>
            
            @if($member->attendances->count() > 0)
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Durasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($member->attendances as $att)
                        <tr>
                            <td>{{ $att->check_in->format('d M Y') }}</td>
                            <td>{{ $att->check_in->format('H:i') }}</td>
                            <td>{{ $att->check_out?->format('H:i') ?? '-' }}</td>
                            <td>{{ $att->duration_text }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-muted">Belum ada riwayat kunjungan</div>
            @endif
        </div>
        
        <!-- Goals -->
        @if($member->goals->count() > 0)
        <div class="card">
            <h3 class="card-title" style="margin-bottom: 1rem;">
                <i class="fas fa-bullseye" style="color: var(--accent); margin-right: 0.5rem;"></i>
                Goals Aktif
            </h3>
            
            @foreach($member->goals as $goal)
            <div style="margin-bottom: 1rem; padding: 1rem; background: var(--bg-secondary); border-radius: 8px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <span style="font-weight: 600;">{{ $goal->title }}</span>
                    <span class="badge badge-primary">{{ $goal->progress_percentage }}%</span>
                </div>
                <div style="background: var(--border); border-radius: 4px; height: 6px; overflow: hidden;">
                    <div style="background: var(--primary); height: 100%; width: {{ $goal->progress_percentage }}%;"></div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection

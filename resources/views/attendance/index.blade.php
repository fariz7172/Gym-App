@extends('layouts.app')

@section('title', 'Check-in')
@section('page-title', 'Check-in Member')

@section('content')
<div class="grid grid-cols-3">
    <!-- Check-in Form -->
    <div class="card" style="grid-column: span 2;">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-qrcode" style="color: var(--primary); margin-right: 0.5rem;"></i>
                Scan / Input Kode Member
            </h3>
        </div>
        
        <div x-data="checkInSystem()" style="padding-top: 1rem;">
            <div style="text-align: center; margin-bottom: 2rem;">
                <input 
                    type="text" 
                    x-model="memberCode"
                    @keyup.enter="checkIn"
                    class="form-input" 
                    style="max-width: 400px; text-align: center; font-size: 1.5rem; font-weight: 600; letter-spacing: 0.1em;"
                    placeholder="SCAN ATAU KETIK KODE"
                    autofocus
                >
                <p class="text-muted" style="margin-top: 0.5rem;">
                    Scan QR code member atau ketik kode member manual, lalu tekan Enter
                </p>
            </div>
            
            <!-- Result Display -->
            <div x-show="result" x-transition class="animate-fade-in">
                <div x-show="result?.success" class="alert alert-success" style="font-size: 1.125rem;">
                    <i class="fas fa-check-circle" style="font-size: 1.5rem;"></i>
                    <div>
                        <strong x-text="result?.message"></strong>
                        <div x-text="result?.member?.name" style="font-size: 0.875rem;"></div>
                    </div>
                </div>
                
                <div x-show="!result?.success && result?.action !== 'checkout'" class="alert alert-danger" style="font-size: 1.125rem;">
                    <i class="fas fa-exclamation-circle" style="font-size: 1.5rem;"></i>
                    <div>
                        <strong x-text="result?.message"></strong>
                        <div x-text="result?.member?.name" style="font-size: 0.875rem;"></div>
                    </div>
                </div>
                
                <!-- Checkout Prompt -->
                <div x-show="result?.action === 'checkout'" class="alert alert-warning" style="font-size: 1.125rem;">
                    <i class="fas fa-door-open" style="font-size: 1.5rem;"></i>
                    <div style="flex: 1;">
                        <strong x-text="result?.member?.name"></strong> sudah check-in pukul <span x-text="result?.member?.check_in_time"></span>
                        <div style="margin-top: 0.5rem;">
                            <button @click="checkOut(result?.attendance_id)" class="btn btn-warning btn-sm">
                                <i class="fas fa-sign-out-alt"></i>
                                Check-out Sekarang
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div style="display: flex; justify-content: center; gap: 1rem; margin-top: 1.5rem;">
                <button @click="checkIn" class="btn btn-primary" style="padding: 1rem 2rem; font-size: 1.125rem;">
                    <i class="fas fa-sign-in-alt"></i>
                    Check-in
                </button>
                <a href="{{ route('attendance.history') }}" class="btn btn-secondary" style="padding: 1rem 2rem;">
                    <i class="fas fa-history"></i>
                    Riwayat
                </a>
            </div>
        </div>
    </div>
    
    <!-- Today's Stats -->
    <div>
        <div class="stat-card gradient-1" style="margin-bottom: 1rem;">
            <div class="stat-icon success">
                <i class="fas fa-door-open"></i>
            </div>
            <div class="stat-value">{{ $currentlyIn }}</div>
            <div class="stat-label">Sedang di Gym</div>
        </div>
        
        <div class="stat-card gradient-2">
            <div class="stat-icon primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-value">{{ $attendances->count() }}</div>
            <div class="stat-label">Kunjungan Hari Ini</div>
        </div>
    </div>
</div>

<!-- Today's Attendance List -->
<div class="card" style="margin-top: 1.5rem;">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-list" style="color: var(--secondary); margin-right: 0.5rem;"></i>
            Kunjungan Hari Ini
        </h3>
    </div>
    
    @if($attendances->count() > 0)
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Member</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Durasi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendances as $attendance)
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
                    <td>{{ $attendance->check_out?->format('H:i') ?? '-' }}</td>
                    <td>{{ $attendance->duration_text }}</td>
                    <td>
                        @if($attendance->isCheckedIn())
                            <span class="badge badge-success">
                                <i class="fas fa-circle" style="font-size: 0.5rem; margin-right: 0.25rem;"></i>
                                Di Gym
                            </span>
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
        <i class="fas fa-door-closed empty-state-icon"></i>
        <div class="empty-state-title">Belum ada kunjungan</div>
        <p class="empty-state-text">Member yang check-in hari ini akan muncul di sini</p>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
function checkInSystem() {
    return {
        memberCode: '',
        result: null,
        loading: false,
        
        async checkIn() {
            if (!this.memberCode.trim()) return;
            
            this.loading = true;
            this.result = null;
            
            try {
                const response = await fetch('{{ route("attendance.checkin") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ member_code: this.memberCode.trim() })
                });
                
                this.result = await response.json();
                
                if (this.result.success) {
                    this.memberCode = '';
                    // Refresh page after 2 seconds
                    setTimeout(() => window.location.reload(), 2000);
                }
            } catch (error) {
                this.result = { success: false, message: 'Terjadi kesalahan. Silakan coba lagi.' };
            }
            
            this.loading = false;
        },
        
        async checkOut(attendanceId) {
            try {
                const response = await fetch(`/attendance/check-out/${attendanceId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                this.result = await response.json();
                
                if (this.result.success) {
                    this.memberCode = '';
                    setTimeout(() => window.location.reload(), 1500);
                }
            } catch (error) {
                this.result = { success: false, message: 'Gagal check-out. Silakan coba lagi.' };
            }
        }
    }
}
</script>
@endpush

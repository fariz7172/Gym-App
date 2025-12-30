@extends('layouts.app')

@section('title', 'Daftar Member')
@section('page-title', 'Daftar Member')

@section('content')
<div class="card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <div>
        <h2 style="font-size: 1.5rem; font-weight: 700;">Member</h2>
        <p class="text-muted">Kelola data member gym Anda</p>
    </div>
    <a href="{{ route('members.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        Tambah Member
    </a>
</div>

<!-- Filters -->
<div class="card" style="margin-bottom: 1.5rem;">
    <form method="GET" action="{{ route('members.index') }}" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
        <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 200px;">
            <label class="form-label">Cari Member</label>
            <input type="text" name="search" class="form-input" placeholder="Nama, kode, email, atau telepon..." value="{{ request('search') }}">
        </div>
        
        <div class="form-group" style="margin-bottom: 0; min-width: 150px;">
            <label class="form-label">Status</label>
            <select name="status" class="form-input">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                <option value="frozen" {{ request('status') == 'frozen' ? 'selected' : '' }}>Frozen</option>
            </select>
        </div>
        
        @if(auth()->user()->isSuperAdmin() && $branches->count() > 0)
        <div class="form-group" style="margin-bottom: 0; min-width: 150px;">
            <label class="form-label">Cabang</label>
            <select name="branch_id" class="form-input">
                <option value="">Semua Cabang</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                        {{ $branch->name }}
                    </option>
                @endforeach
            </select>
        </div>
        @endif
        
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i>
            Filter
        </button>
        
        <a href="{{ route('members.index') }}" class="btn btn-secondary">
            <i class="fas fa-refresh"></i>
            Reset
        </a>
    </form>
</div>

<!-- Members Table -->
<div class="card">
    @if($members->count() > 0)
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Member</th>
                    <th>Kode</th>
                    @if(auth()->user()->isSuperAdmin())
                    <th>Cabang</th>
                    @endif
                    <th>Membership</th>
                    <th>Status</th>
                    <th>Expired</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($members as $member)
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div class="avatar">
                                @if($member->photo)
                                    <img src="{{ Storage::url($member->photo) }}" alt="{{ $member->name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                @else
                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                @endif
                            </div>
                            <div>
                                <div style="font-weight: 600;">{{ $member->name }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-secondary);">
                                    {{ $member->phone ?? $member->email ?? '-' }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <code style="background: rgba(99, 102, 241, 0.1); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.875rem;">
                            {{ $member->member_code }}
                        </code>
                    </td>
                    @if(auth()->user()->isSuperAdmin())
                    <td>{{ $member->branch->name ?? '-' }}</td>
                    @endif
                    <td>{{ $member->activeMembership?->plan?->name ?? '-' }}</td>
                    <td>
                        @if($member->status === 'active')
                            <span class="badge badge-success">Aktif</span>
                        @elseif($member->status === 'expired')
                            <span class="badge badge-danger">Expired</span>
                        @else
                            <span class="badge badge-warning">Frozen</span>
                        @endif
                    </td>
                    <td>
                        @if($member->membership_expiry)
                            <div style="font-weight: 500;">{{ $member->membership_expiry->format('d M Y') }}</div>
                            @if($member->days_until_expiry !== null)
                                @if($member->days_until_expiry <= 7 && $member->days_until_expiry >= 0)
                                    <div style="font-size: 0.75rem; color: var(--warning);">{{ $member->days_until_expiry }} hari lagi</div>
                                @elseif($member->days_until_expiry < 0)
                                    <div style="font-size: 0.75rem; color: var(--danger);">Sudah expired</div>
                                @endif
                            @endif
                        @else
                            -
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <div style="display: flex; gap: 0.25rem; justify-content: center;">
                            <a href="{{ route('members.show', $member) }}" class="btn btn-sm btn-secondary" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('members.edit', $member) }}" class="btn btn-sm btn-secondary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('members.qrcode', $member) }}" class="btn btn-sm btn-secondary" title="QR Code">
                                <i class="fas fa-qrcode"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div style="padding: 1rem; display: flex; justify-content: center;">
        {{ $members->links() }}
    </div>
    @else
    <div class="empty-state">
        <i class="fas fa-users empty-state-icon"></i>
        <div class="empty-state-title">Belum ada member</div>
        <p class="empty-state-text">Mulai tambahkan member baru untuk gym Anda</p>
        <a href="{{ route('members.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Tambah Member Pertama
        </a>
    </div>
    @endif
</div>
@endsection

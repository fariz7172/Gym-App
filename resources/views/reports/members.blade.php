@extends('layouts.app')

@section('title', 'Laporan Member')
@section('page-title', 'Laporan Member')

@section('content')
<div class="card" style="margin-bottom: 1.5rem;">
    <form method="GET" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
        @if(auth()->user()->isSuperAdmin() && $branches->count() > 0)
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Cabang</label>
            <select name="branch_id" class="form-input">
                <option value="">Semua Cabang</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Status</label>
            <select name="status" class="form-input">
                <option value="">Semua</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-filter"></i> Filter
        </button>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Data Member</h3>
        <span class="badge badge-primary">{{ $members->total() }} member</span>
    </div>
    
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Kode</th>
                    @if(auth()->user()->isSuperAdmin())
                    <th>Cabang</th>
                    @endif
                    <th>Telepon</th>
                    <th>Membership</th>
                    <th>Status</th>
                    <th>Bergabung</th>
                </tr>
            </thead>
            <tbody>
                @foreach($members as $member)
                <tr>
                    <td style="font-weight: 600;">{{ $member->name }}</td>
                    <td><code>{{ $member->member_code }}</code></td>
                    @if(auth()->user()->isSuperAdmin())
                    <td>{{ $member->branch->name ?? '-' }}</td>
                    @endif
                    <td>{{ $member->phone ?? '-' }}</td>
                    <td>{{ $member->activeMembership?->plan?->name ?? '-' }}</td>
                    <td>
                        @if($member->status == 'active')
                            <span class="badge badge-success">Aktif</span>
                        @else
                            <span class="badge badge-danger">{{ ucfirst($member->status) }}</span>
                        @endif
                    </td>
                    <td>{{ $member->created_at->format('d M Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div style="padding: 1rem; display: flex; justify-content: center;">
        {{ $members->links() }}
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Manajemen Cabang')
@section('page-title', 'Manajemen Cabang')

@section('content')
<div class="card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <div>
        <h2 style="font-size: 1.5rem; font-weight: 700;">Cabang Gym</h2>
        <p class="text-muted">Kelola semua cabang gym Anda</p>
    </div>
    <a href="{{ route('branches.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        Tambah Cabang
    </a>
</div>

<div class="grid grid-cols-3">
    @foreach($branches as $branch)
    <div class="card">
        <div style="display: flex; align-items: flex-start; gap: 1rem; margin-bottom: 1rem;">
            <div style="width: 64px; height: 64px; background: linear-gradient(135deg, var(--primary), var(--secondary)); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-building" style="font-size: 1.5rem; color: white;"></i>
            </div>
            <div style="flex: 1;">
                <h3 style="font-weight: 700; margin-bottom: 0.25rem;">{{ $branch->name }}</h3>
                <code style="font-size: 0.75rem; background: rgba(99, 102, 241, 0.1); padding: 0.125rem 0.5rem; border-radius: 4px;">{{ $branch->code }}</code>
            </div>
            @if($branch->is_active)
                <span class="badge badge-success">Aktif</span>
            @else
                <span class="badge badge-secondary">Nonaktif</span>
            @endif
        </div>
        
        <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 1rem;">
            @if($branch->address)
            <div style="margin-bottom: 0.5rem;">
                <i class="fas fa-map-marker-alt" style="width: 20px;"></i>
                {{ $branch->address }}
            </div>
            @endif
            @if($branch->phone)
            <div style="margin-bottom: 0.5rem;">
                <i class="fas fa-phone" style="width: 20px;"></i>
                {{ $branch->phone }}
            </div>
            @endif
            <div>
                <i class="fas fa-clock" style="width: 20px;"></i>
                {{ $branch->opening_time }} - {{ $branch->closing_time }}
            </div>
        </div>
        
        <div style="display: flex; gap: 1rem; padding-top: 1rem; border-top: 1px solid var(--border);">
            <div style="flex: 1; text-align: center;">
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--primary);">{{ $branch->members_count ?? 0 }}</div>
                <div style="font-size: 0.75rem; color: var(--text-secondary);">Member</div>
            </div>
        </div>
        
        <div style="display: flex; gap: 0.5rem; margin-top: 1rem;">
            <a href="{{ route('branches.edit', $branch) }}" class="btn btn-sm btn-secondary" style="flex: 1;">
                <i class="fas fa-edit"></i>
                Edit
            </a>
            <form action="{{ route('branches.destroy', $branch) }}" method="POST" onsubmit="return confirm('Yakin hapus cabang ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </div>
    </div>
    @endforeach
</div>

@if($branches->isEmpty())
<div class="card">
    <div class="empty-state">
        <i class="fas fa-building empty-state-icon"></i>
        <div class="empty-state-title">Belum ada cabang</div>
        <p class="empty-state-text">Mulai dengan menambahkan cabang pertama Anda</p>
        <a href="{{ route('branches.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Tambah Cabang Pertama
        </a>
    </div>
</div>
@endif
@endsection

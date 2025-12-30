@extends('layouts.app')

@section('title', 'Personal Trainer')
@section('page-title', 'Personal Trainer')

@section('content')
<div class="card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <div>
        <h2 style="font-size: 1.5rem; font-weight: 700;">Personal Trainer</h2>
        <p class="text-muted">Kelola trainer di gym Anda</p>
    </div>
    <a href="{{ route('trainers.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        Tambah Trainer
    </a>
</div>

<div class="grid grid-cols-3">
    @foreach($trainers as $trainer)
    <div class="card">
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
            <div class="avatar-lg" style="background: linear-gradient(135deg, {{ $trainer->gender == 'female' ? '#F472B6, #EC4899' : 'var(--primary), var(--secondary)' }});">
                @if($trainer->photo)
                    <img src="{{ Storage::url($trainer->photo) }}" alt="{{ $trainer->name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                @else
                    {{ strtoupper(substr($trainer->name, 0, 1)) }}
                @endif
            </div>
            <div style="flex: 1;">
                <h3 style="font-weight: 700;">{{ $trainer->name }}</h3>
                <div style="font-size: 0.875rem; color: var(--text-secondary);">{{ $trainer->specialization ?? 'General Fitness' }}</div>
            </div>
            @if($trainer->is_active)
                <span class="badge badge-success">Aktif</span>
            @else
                <span class="badge badge-secondary">Nonaktif</span>
            @endif
        </div>
        
        <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 1rem;">
            @if($trainer->phone)
            <div style="margin-bottom: 0.25rem;">
                <i class="fas fa-phone" style="width: 16px;"></i> {{ $trainer->phone }}
            </div>
            @endif
            <div>
                <i class="fas fa-money-bill" style="width: 16px;"></i> {{ $trainer->formatted_rate }}
            </div>
        </div>
        
        @if($trainer->bio)
        <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 1rem;">{{ Str::limit($trainer->bio, 100) }}</p>
        @endif
        
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('trainers.edit', $trainer) }}" class="btn btn-sm btn-secondary" style="flex: 1;">
                <i class="fas fa-edit"></i> Edit
            </a>
            <form action="{{ route('trainers.destroy', $trainer) }}" method="POST" onsubmit="return confirm('Yakin hapus trainer ini?');">
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

@if($trainers->isEmpty())
<div class="card">
    <div class="empty-state">
        <i class="fas fa-user-ninja empty-state-icon"></i>
        <div class="empty-state-title">Belum ada trainer</div>
        <p class="empty-state-text">Tambahkan personal trainer untuk gym Anda</p>
        <a href="{{ route('trainers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Tambah Trainer
        </a>
    </div>
</div>
@endif
@endsection

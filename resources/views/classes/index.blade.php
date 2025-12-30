@extends('layouts.app')

@section('title', 'Jadwal Kelas')
@section('page-title', 'Jadwal Kelas')

@section('content')
<div class="card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <div>
        <h2 style="font-size: 1.5rem; font-weight: 700;">Kelas & Jadwal</h2>
        <p class="text-muted">Kelola jenis kelas dan jadwal</p>
    </div>
    <a href="{{ route('classes.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        Tambah Kelas
    </a>
</div>

<div class="grid grid-cols-3">
    @foreach($classes as $class)
    <div class="card">
        <div style="display: flex; align-items: flex-start; gap: 1rem; margin-bottom: 1rem;">
            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, var(--primary), var(--secondary)); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-dumbbell" style="font-size: 1.25rem; color: white;"></i>
            </div>
            <div style="flex: 1;">
                <h3 style="font-weight: 700;">{{ $class->name }}</h3>
                <div style="font-size: 0.875rem; color: var(--text-secondary);">
                    {{ $class->duration_text }} • Max {{ $class->max_capacity }} peserta
                </div>
            </div>
            @if($class->is_active)
                <span class="badge badge-success">Aktif</span>
            @else
                <span class="badge badge-secondary">Nonaktif</span>
            @endif
        </div>
        
        @if($class->description)
        <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 1rem;">
            {{ Str::limit($class->description, 80) }}
        </p>
        @endif
        
        <div style="margin-bottom: 1rem;">
            <div style="font-size: 0.75rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.5rem;">JADWAL:</div>
            @if($class->schedules->count() > 0)
                @foreach($class->schedules->take(3) as $schedule)
                <div style="font-size: 0.875rem; padding: 0.25rem 0;">
                    <span class="badge badge-primary" style="min-width: 60px;">{{ $schedule->day_label }}</span>
                    {{ $schedule->time_range }}
                </div>
                @endforeach
                @if($class->schedules->count() > 3)
                <div style="font-size: 0.75rem; color: var(--text-secondary);">+{{ $class->schedules->count() - 3 }} jadwal lainnya</div>
                @endif
            @else
                <span class="text-muted" style="font-size: 0.875rem;">Belum ada jadwal</span>
            @endif
        </div>
        
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('classes.edit', $class) }}" class="btn btn-sm btn-secondary" style="flex: 1;">
                <i class="fas fa-edit"></i> Edit
            </a>
            <form action="{{ route('classes.destroy', $class) }}" method="POST" onsubmit="return confirm('Yakin hapus kelas ini?');">
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

@if($classes->isEmpty())
<div class="card">
    <div class="empty-state">
        <i class="fas fa-calendar-alt empty-state-icon"></i>
        <div class="empty-state-title">Belum ada kelas</div>
        <p class="empty-state-text">Tambahkan jenis kelas untuk gym Anda</p>
        <a href="{{ route('classes.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Kelas
        </a>
    </div>
</div>
@endif
@endsection

@extends('layouts.app')

@section('title', 'Tambah Kelas')
@section('page-title', 'Tambah Kelas Baru')

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
    <div class="card">
        <h3 class="card-title" style="margin-bottom: 1.5rem;">
            <i class="fas fa-calendar-plus" style="color: var(--primary); margin-right: 0.5rem;"></i>
            Formulir Kelas Baru
        </h3>
        
        <form action="{{ route('classes.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            @if(auth()->user()->isSuperAdmin())
            <div class="form-group">
                <label class="form-label">Cabang *</label>
                <select name="branch_id" class="form-input" required>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            @else
            <input type="hidden" name="branch_id" value="{{ auth()->user()->branch_id }}">
            @endif
            
            <div class="form-group">
                <label class="form-label">Nama Kelas *</label>
                <input type="text" name="name" class="form-input" required placeholder="Contoh: Yoga, Zumba, HIIT">
            </div>
            
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" class="form-input" rows="3" placeholder="Deskripsi singkat tentang kelas..."></textarea>
            </div>
            
            <div class="grid grid-cols-2">
                <div class="form-group">
                    <label class="form-label">Durasi (menit) *</label>
                    <input type="number" name="duration_minutes" class="form-input" value="60" min="15" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Kapasitas Max *</label>
                    <input type="number" name="max_capacity" class="form-input" value="20" min="1" required>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Foto</label>
                <input type="file" name="photo" class="form-input" accept="image/*">
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                <a href="{{ route('classes.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    <i class="fas fa-save"></i> Simpan Kelas
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

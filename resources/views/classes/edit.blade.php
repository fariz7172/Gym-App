@extends('layouts.app')

@section('title', 'Edit Kelas')
@section('page-title', 'Edit Kelas')

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
    <div class="card">
        <h3 class="card-title" style="margin-bottom: 1.5rem;">
            <i class="fas fa-edit" style="color: var(--primary); margin-right: 0.5rem;"></i>
            Edit: {{ $class->name }}
        </h3>
        
        <form action="{{ route('classes.update', $class) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label class="form-label">Nama Kelas *</label>
                <input type="text" name="name" class="form-input" value="{{ old('name', $class->name) }}" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" class="form-input" rows="3">{{ old('description', $class->description) }}</textarea>
            </div>
            
            <div class="grid grid-cols-2">
                <div class="form-group">
                    <label class="form-label">Durasi (menit) *</label>
                    <input type="number" name="duration_minutes" class="form-input" value="{{ old('duration_minutes', $class->duration_minutes) }}" min="15" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Kapasitas Max *</label>
                    <input type="number" name="max_capacity" class="form-input" value="{{ old('max_capacity', $class->max_capacity) }}" min="1" required>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Status</label>
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $class->is_active) ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: var(--primary);">
                    <span>Kelas Aktif</span>
                </label>
            </div>
            
            <div class="form-group">
                <label class="form-label">Foto</label>
                @if($class->photo)
                <div style="margin-bottom: 0.5rem;">
                    <img src="{{ Storage::url($class->photo) }}" alt="{{ $class->name }}" style="max-width: 150px; border-radius: 8px;">
                </div>
                @endif
                <input type="file" name="photo" class="form-input" accept="image/*">
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                <a href="{{ route('classes.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

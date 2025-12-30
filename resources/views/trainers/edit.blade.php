@extends('layouts.app')

@section('title', 'Edit Trainer')
@section('page-title', 'Edit Trainer')

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
    <div class="card">
        <h3 class="card-title" style="margin-bottom: 1.5rem;">
            <i class="fas fa-user-edit" style="color: var(--primary); margin-right: 0.5rem;"></i>
            Edit: {{ $trainer->name }}
        </h3>
        
        <form action="{{ route('trainers.update', $trainer) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label class="form-label">Nama Lengkap *</label>
                <input type="text" name="name" class="form-input" value="{{ old('name', $trainer->name) }}" required>
            </div>
            
            <div class="grid grid-cols-2">
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" value="{{ old('email', $trainer->email) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">No. Telepon</label>
                    <input type="text" name="phone" class="form-input" value="{{ old('phone', $trainer->phone) }}">
                </div>
            </div>
            
            <div class="grid grid-cols-2">
                <div class="form-group">
                    <label class="form-label">Jenis Kelamin</label>
                    <select name="gender" class="form-input">
                        <option value="">Pilih...</option>
                        <option value="male" {{ old('gender', $trainer->gender) == 'male' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="female" {{ old('gender', $trainer->gender) == 'female' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Tarif per Jam (Rp) *</label>
                    <input type="number" name="hourly_rate" class="form-input" value="{{ old('hourly_rate', $trainer->hourly_rate) }}" required>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Spesialisasi</label>
                <input type="text" name="specialization" class="form-input" value="{{ old('specialization', $trainer->specialization) }}">
            </div>
            
            <div class="form-group">
                <label class="form-label">Bio</label>
                <textarea name="bio" class="form-input" rows="3">{{ old('bio', $trainer->bio) }}</textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label">Status</label>
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $trainer->is_active) ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: var(--primary);">
                    <span>Trainer Aktif</span>
                </label>
            </div>
            
            <div class="form-group">
                <label class="form-label">Foto</label>
                @if($trainer->photo)
                <div style="margin-bottom: 0.5rem;">
                    <img src="{{ Storage::url($trainer->photo) }}" alt="{{ $trainer->name }}" style="max-width: 100px; border-radius: 8px;">
                </div>
                @endif
                <input type="file" name="photo" class="form-input" accept="image/*">
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                <a href="{{ route('trainers.index') }}" class="btn btn-secondary">
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

@extends('layouts.app')

@section('title', 'Edit Cabang')
@section('page-title', 'Edit Cabang')

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
    <div class="card">
        <h3 class="card-title" style="margin-bottom: 1.5rem;">
            <i class="fas fa-edit" style="color: var(--primary); margin-right: 0.5rem;"></i>
            Edit: {{ $branch->name }}
        </h3>
        
        @if($errors->any())
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <ul style="margin: 0; padding-left: 1rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        
        <form action="{{ route('branches.update', $branch) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label class="form-label">Nama Cabang *</label>
                <input type="text" name="name" class="form-input" value="{{ old('name', $branch->name) }}" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Kode Cabang *</label>
                <input type="text" name="code" class="form-input" value="{{ old('code', $branch->code) }}" required maxlength="10" style="text-transform: uppercase;">
            </div>
            
            <div class="form-group">
                <label class="form-label">Alamat</label>
                <textarea name="address" class="form-input" rows="2">{{ old('address', $branch->address) }}</textarea>
            </div>
            
            <div class="grid grid-cols-2">
                <div class="form-group">
                    <label class="form-label">No. Telepon</label>
                    <input type="text" name="phone" class="form-input" value="{{ old('phone', $branch->phone) }}">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" value="{{ old('email', $branch->email) }}">
                </div>
            </div>
            
            <div class="grid grid-cols-2">
                <div class="form-group">
                    <label class="form-label">Jam Buka *</label>
                    <input type="time" name="opening_time" class="form-input" value="{{ old('opening_time', $branch->opening_time) }}" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Jam Tutup *</label>
                    <input type="time" name="closing_time" class="form-input" value="{{ old('closing_time', $branch->closing_time) }}" required>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Status</label>
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $branch->is_active) ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: var(--primary);">
                    <span>Cabang Aktif</span>
                </label>
            </div>
            
            <div class="form-group">
                <label class="form-label">Foto Cabang</label>
                @if($branch->photo)
                <div style="margin-bottom: 0.5rem;">
                    <img src="{{ Storage::url($branch->photo) }}" alt="{{ $branch->name }}" style="max-width: 200px; border-radius: 8px;">
                </div>
                @endif
                <input type="file" name="photo" class="form-input" accept="image/*">
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                <a href="{{ route('branches.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Kembali
                </a>
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    <i class="fas fa-save"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

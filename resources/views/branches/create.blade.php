@extends('layouts.app')

@section('title', 'Tambah Cabang')
@section('page-title', 'Tambah Cabang Baru')

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
    <div class="card">
        <h3 class="card-title" style="margin-bottom: 1.5rem;">
            <i class="fas fa-building" style="color: var(--primary); margin-right: 0.5rem;"></i>
            Formulir Cabang Baru
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
        
        <form action="{{ route('branches.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="form-group">
                <label class="form-label">Nama Cabang *</label>
                <input type="text" name="name" class="form-input" value="{{ old('name') }}" required placeholder="Contoh: GymPro Sudirman">
            </div>
            
            <div class="form-group">
                <label class="form-label">Kode Cabang *</label>
                <input type="text" name="code" class="form-input" value="{{ old('code') }}" required placeholder="Contoh: GPS" maxlength="10" style="text-transform: uppercase;">
                <small class="text-muted">Kode unik 3-10 karakter untuk identifikasi cabang</small>
            </div>
            
            <div class="form-group">
                <label class="form-label">Alamat</label>
                <textarea name="address" class="form-input" rows="2" placeholder="Alamat lengkap cabang">{{ old('address') }}</textarea>
            </div>
            
            <div class="grid grid-cols-2">
                <div class="form-group">
                    <label class="form-label">No. Telepon</label>
                    <input type="text" name="phone" class="form-input" value="{{ old('phone') }}" placeholder="021-12345678">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" value="{{ old('email') }}" placeholder="cabang@mail.com">
                </div>
            </div>
            
            <div class="grid grid-cols-2">
                <div class="form-group">
                    <label class="form-label">Jam Buka *</label>
                    <input type="time" name="opening_time" class="form-input" value="{{ old('opening_time', '06:00') }}" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Jam Tutup *</label>
                    <input type="time" name="closing_time" class="form-input" value="{{ old('closing_time', '22:00') }}" required>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Foto Cabang</label>
                <input type="file" name="photo" class="form-input" accept="image/*">
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                <a href="{{ route('branches.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Kembali
                </a>
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    <i class="fas fa-save"></i>
                    Simpan Cabang
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

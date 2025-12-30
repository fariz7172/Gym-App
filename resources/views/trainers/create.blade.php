@extends('layouts.app')

@section('title', 'Tambah Trainer')
@section('page-title', 'Tambah Personal Trainer')

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
    <div class="card">
        <h3 class="card-title" style="margin-bottom: 1.5rem;">
            <i class="fas fa-user-ninja" style="color: var(--primary); margin-right: 0.5rem;"></i>
            Formulir Trainer Baru
        </h3>
        
        <form action="{{ route('trainers.store') }}" method="POST" enctype="multipart/form-data">
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
                <label class="form-label">Nama Lengkap *</label>
                <input type="text" name="name" class="form-input" required>
            </div>
            
            <div class="grid grid-cols-2">
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">No. Telepon</label>
                    <input type="text" name="phone" class="form-input">
                </div>
            </div>
            
            <div class="grid grid-cols-2">
                <div class="form-group">
                    <label class="form-label">Jenis Kelamin</label>
                    <select name="gender" class="form-input">
                        <option value="">Pilih...</option>
                        <option value="male">Laki-laki</option>
                        <option value="female">Perempuan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Tarif per Jam (Rp) *</label>
                    <input type="number" name="hourly_rate" class="form-input" value="150000" required>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Spesialisasi</label>
                <input type="text" name="specialization" class="form-input" placeholder="Contoh: Strength Training, Yoga, Boxing">
            </div>
            
            <div class="form-group">
                <label class="form-label">Bio</label>
                <textarea name="bio" class="form-input" rows="3" placeholder="Deskripsi singkat tentang trainer..."></textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label">Foto</label>
                <input type="file" name="photo" class="form-input" accept="image/*">
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                <a href="{{ route('trainers.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    <i class="fas fa-save"></i> Simpan Trainer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Edit Member')
@section('page-title', 'Edit Member')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div class="card">
        <h3 class="card-title" style="margin-bottom: 1.5rem;">
            <i class="fas fa-user-edit" style="color: var(--primary); margin-right: 0.5rem;"></i>
            Edit: {{ $member->name }}
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
        
        <form action="{{ route('members.update', $member) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-2">
                <div style="grid-column: span 2;">
                    <h4 style="font-weight: 600; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border);">
                        <i class="fas fa-user" style="margin-right: 0.5rem;"></i>
                        Data Pribadi
                    </h4>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Nama Lengkap *</label>
                    <input type="text" name="name" class="form-input" value="{{ old('name', $member->name) }}" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" value="{{ old('email', $member->email) }}">
                </div>
                
                <div class="form-group">
                    <label class="form-label">No. Telepon</label>
                    <input type="text" name="phone" class="form-input" value="{{ old('phone', $member->phone) }}">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Jenis Kelamin</label>
                    <select name="gender" class="form-input">
                        <option value="">Pilih...</option>
                        <option value="male" {{ old('gender', $member->gender) == 'male' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="female" {{ old('gender', $member->gender) == 'female' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Tanggal Lahir</label>
                    <input type="date" name="date_of_birth" class="form-input" value="{{ old('date_of_birth', $member->date_of_birth?->format('Y-m-d')) }}">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Kontak Darurat</label>
                    <input type="text" name="emergency_contact" class="form-input" value="{{ old('emergency_contact', $member->emergency_contact) }}">
                </div>
                
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label">Alamat</label>
                    <textarea name="address" class="form-input" rows="2">{{ old('address', $member->address) }}</textarea>
                </div>
                
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label">Catatan Kesehatan</label>
                    <textarea name="health_notes" class="form-input" rows="2">{{ old('health_notes', $member->health_notes) }}</textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Foto</label>
                    @if($member->photo)
                    <div style="margin-bottom: 0.5rem;">
                        <img src="{{ Storage::url($member->photo) }}" alt="{{ $member->name }}" style="max-width: 100px; border-radius: 8px;">
                    </div>
                    @endif
                    <input type="file" name="photo" class="form-input" accept="image/*">
                </div>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                <a href="{{ route('members.show', $member) }}" class="btn btn-secondary">
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

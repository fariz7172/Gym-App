@extends('layouts.app')

@section('title', 'Tambah Member')
@section('page-title', 'Tambah Member Baru')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div class="card">
        <h3 class="card-title" style="margin-bottom: 1.5rem;">
            <i class="fas fa-user-plus" style="color: var(--primary); margin-right: 0.5rem;"></i>
            Formulir Member Baru
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
        
        <form action="{{ route('members.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-2">
                <!-- Data Pribadi -->
                <div style="grid-column: span 2;">
                    <h4 style="font-weight: 600; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border);">
                        <i class="fas fa-user" style="margin-right: 0.5rem;"></i>
                        Data Pribadi
                    </h4>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Nama Lengkap *</label>
                    <input type="text" name="name" class="form-input" value="{{ old('name') }}" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" value="{{ old('email') }}" placeholder="email@example.com">
                </div>
                
                <div class="form-group">
                    <label class="form-label">No. Telepon</label>
                    <input type="text" name="phone" class="form-input" value="{{ old('phone') }}" placeholder="081234567890">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Jenis Kelamin</label>
                    <select name="gender" class="form-input">
                        <option value="">Pilih...</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Tanggal Lahir</label>
                    <input type="date" name="date_of_birth" class="form-input" value="{{ old('date_of_birth') }}">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Kontak Darurat</label>
                    <input type="text" name="emergency_contact" class="form-input" value="{{ old('emergency_contact') }}" placeholder="Nama - No. Telepon">
                </div>
                
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label">Alamat</label>
                    <textarea name="address" class="form-input" rows="2">{{ old('address') }}</textarea>
                </div>
                
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label">Catatan Kesehatan</label>
                    <textarea name="health_notes" class="form-input" rows="2" placeholder="Alergi, riwayat penyakit, cedera, dll.">{{ old('health_notes') }}</textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Foto</label>
                    <input type="file" name="photo" class="form-input" accept="image/*">
                </div>
            </div>
            
            <div class="grid grid-cols-2" style="margin-top: 1.5rem;">
                <!-- Membership -->
                <div style="grid-column: span 2;">
                    <h4 style="font-weight: 600; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border);">
                        <i class="fas fa-id-card" style="margin-right: 0.5rem;"></i>
                        Membership
                    </h4>
                </div>
                
                @if(auth()->user()->isSuperAdmin())
                <div class="form-group">
                    <label class="form-label">Cabang *</label>
                    <select name="branch_id" class="form-input" required>
                        <option value="">Pilih Cabang...</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @else
                <input type="hidden" name="branch_id" value="{{ auth()->user()->branch_id }}">
                @endif
                
                <div class="form-group">
                    <label class="form-label">Paket Membership *</label>
                    <select name="membership_plan_id" class="form-input" required>
                        <option value="">Pilih Paket...</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ old('membership_plan_id') == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }} - {{ $plan->formatted_price }} ({{ $plan->duration_text }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Metode Pembayaran *</label>
                    <select name="payment_method" class="form-input" required>
                        <option value="">Pilih Metode...</option>
                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Tunai</option>
                        <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                        <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Kartu Kredit/Debit</option>
                        <option value="e-wallet" {{ old('payment_method') == 'e-wallet' ? 'selected' : '' }}>E-Wallet</option>
                    </select>
                </div>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                <a href="{{ route('members.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Kembali
                </a>
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    <i class="fas fa-save"></i>
                    Simpan Member
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

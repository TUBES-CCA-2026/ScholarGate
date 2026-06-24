@extends('layouts.app')

@section('content')
<h1 class="page-title">Edit Profil Mahasiswa</h1>

<div class="form-card profile-edit-card">
    <form method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data" class="form-stack">
        @csrf
        @method('PUT')

        <div class="profile-photo-preview">
            @if($user->photo_path)
                <img src="{{ asset('storage/' . $user->photo_path) }}" alt="Foto Profil">
            @else
                <div class="large-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
            @endif
        </div>

        <div>
            <label for="photo">Foto Profil</label>
            <input type="file" name="photo" id="photo" accept="image/*">
            <small class="help-text">Format: JPG, JPEG, PNG, atau WEBP. Ukuran maksimal 2 MB.</small>
        </div>

        <div>
            <label for="name">Nama Lengkap</label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required>
        </div>

        <div>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required>
        </div>

        <div class="grid-2">
            <div>
                <label for="nim">NIM</label>
                <input type="text" name="nim" id="nim" value="{{ old('nim', $user->nim) }}">
            </div>

            <div>
                <label for="kelas">Kelas</label>
                <input type="text" name="kelas" id="kelas" value="{{ old('kelas', $user->kelas) }}">
            </div>
        </div>

        <div>
            <label for="program_studi">Program Studi</label>
            <input type="text" name="program_studi" id="program_studi" value="{{ old('program_studi', $user->program_studi) }}">
        </div>

        <div class="grid-2">
            <div>
                <label for="ipk">IPK</label>
                <input type="number" step="0.01" min="0" max="4" name="ipk" id="ipk" value="{{ old('ipk', $user->ipk) }}">
            </div>

            <div>
                <label for="phone">Nomor Telepon</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn primary">Simpan Perubahan</button>
            <a href="{{ route('student.profile') }}" class="btn neutral">Batal</a>
        </div>
    </form>
</div>
@endsection
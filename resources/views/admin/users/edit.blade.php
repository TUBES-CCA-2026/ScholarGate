{{--
    Halaman edit akun user oleh admin.
--}}
@extends('layouts.app')
@section('content')
    <h1 class="page-title">Edit Akun User</h1>

    <div class="form-card" style="max-width: 720px;">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')

            <div style="display: grid; gap: 16px;">
                <div>
                    <label for="name">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" pattern="[a-zA-Z\s.,'\-]+" title="Nama lengkap hanya boleh berisi huruf, spasi, titik, koma, tanda petik, dan hubung." required>
                </div>

                <div>
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label for="nim">NIM</label>
                        <input type="text" name="nim" id="nim" value="{{ old('nim', $user->nim) }}" pattern="[0-9]+" title="NIM hanya boleh berisi angka.">
                    </div>

                    <div>
                        <label for="role">Role</label>
                        <select name="role" id="role" required>
                            <option value="student" {{ old('role', $user->role) === 'student' ? 'selected' : '' }}>Mahasiswa</option>
                            <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label for="program_studi">Program Studi</label>
                        <select name="program_studi" id="program_studi">
                            <option value="">Pilih Program Studi</option>
                            <option value="Teknik Informatika" {{ old('program_studi', $user->program_studi) === 'Teknik Informatika' ? 'selected' : '' }}>Teknik Informatika</option>
                            <option value="Sistem Informasi" {{ old('program_studi', $user->program_studi) === 'Sistem Informasi' ? 'selected' : '' }}>Sistem Informasi</option>
                        </select>
                    </div>

                    <div>
                        <label for="kelas">Kelas</label>
                        <x-kelas-selector value="{{ $user->kelas }}" />
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label for="ipk">IPK</label>
                        <input type="number" step="0.01" min="0" max="4" name="ipk" id="ipk" value="{{ old('ipk', $user->ipk) }}">
                    </div>

                    <div>
                        <label for="phone">Nomor Telepon</label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" pattern="[0-9]{10,15}" title="Nomor telepon harus berupa 10 hingga 15 digit angka saja.">
                    </div>
                </div>

                <div style="display: flex; gap: 12px; margin-top: 8px;">
                    <button class="btn primary" type="submit">Simpan Perubahan</button>
                    <a href="{{ route('admin.users.index') }}" class="btn neutral">Batal</a>
                </div>
            </div>
        </form>
    </div>
@endsection

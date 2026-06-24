@extends('layouts.app')
@section('content')
<h1 class="page-title">Profil Mahasiswa</h1>
<div class="profile-layout">
    <div class="profile-card">
        @if(auth()->user()->photo_path)
            <img class="large-avatar-img" src="{{ asset('storage/' . auth()->user()->photo_path) }}" alt="Foto Profil">
        @else
            <div class="large-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
        @endif
            <h2>{{ auth()->user()->name }}</h2>

            <a href="{{ route('student.profile.edit') }}" class="btn primary small profile-edit-btn">Edit Profil</a>
        <div class="short-line"></div>
        <div class="profile-detail">
            <span>NIM</span><strong>{{ auth()->user()->nim ?? '-' }}</strong>
            <span>Program Studi</span><strong>{{ auth()->user()->program_studi ?? '-' }}</strong>
            <span>Kelas</span><strong>{{ auth()->user()->kelas ?? '-' }}</strong>
            <span>IPK</span><strong>{{ auth()->user()->ipk ?? '-' }}</strong>
            <span>Email</span><strong>{{ auth()->user()->email }}</strong>
        </div>
    </div>
    <div class="profile-side">
        <div class="outline-panel">
            <h2>Data Akademik</h2>
            <p>Data ini dipakai sebagai identitas utama saat mahasiswa mengajukan berkas ke program studi.</p>
            <div class="info-grid">
                <div><span>Status Akun</span><strong>Aktif</strong></div>
                <div><span>Peran</span><strong>Mahasiswa</strong></div>
                <div><span>Nomor Telepon</span><strong>{{ auth()->user()->phone ?? '-' }}</strong></div>
            </div>
        </div>
        <div class="outline-panel tall">
            <h2>Riwayat Pengajuan</h2>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Kode</th><th>Jenis</th><th>Status</th><th>Progres</th></tr></thead>
                    <tbody>
                    @forelse($applications as $application)
                        <tr>
                            <td>{{ $application->application_code }}</td>
                            <td>{{ $application->documentType->name }}</td>
                            <td><span class="status {{ $application->status }}">{{ $application->status_label }}</span></td>
                            <td>{{ $application->completionPercentage() }}%</td>
                        </tr>
                    @empty
                        <tr><td colspan="4">Belum ada pengajuan.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

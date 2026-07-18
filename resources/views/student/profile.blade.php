{{--
    Halaman profil mahasiswa yang menampilkan data akun dan akademik.
--}}
@extends('layouts.app')
@section('content')


<h1 class="page-title">Profil Mahasiswa</h1>
<div class="profile-layout">
    <div class="profile-card">
        <div class="profile-banner"></div>
        @if(auth()->user()->photo_path)
            <img class="large-avatar-img" src="{{ asset('storage/' . auth()->user()->photo_path) }}" alt="Foto Profil">
        @else
            <div class="large-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
        @endif
            <h2>{{ auth()->user()->name }}</h2>

            <a href="{{ route('student.profile.edit') }}" class="btn primary small profile-edit-btn" style="margin-top: 10px; width: auto; display: inline-flex;">Edit Profil</a>
        <div class="short-line"></div>
        <div class="profile-detail">
            <div class="profile-info-item">
                <div class="profile-info-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                </div>
                <div>
                    <span>NIM</span>
                    <strong>{{ auth()->user()->nim ?? '-' }}</strong>
                </div>
            </div>
            <div class="profile-info-item">
                <div class="profile-info-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c0 1.1 2.7 3 6 3s6-1.9 6-3v-5"/></svg>
                </div>
                <div>
                    <span>Program Studi</span>
                    <strong>{{ auth()->user()->program_studi ?? '-' }}</strong>
                </div>
            </div>
            <div class="profile-info-item">
                <div class="profile-info-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div>
                    <span>Kelas</span>
                    <strong>{{ auth()->user()->kelas ?? '-' }}</strong>
                </div>
            </div>
            <div class="profile-info-item">
                <div class="profile-info-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20V10"/><path d="M18 20V4"/><path d="M6 20v-4"/></svg>
                </div>
                <div>
                    <span>IPK</span>
                    <strong>{{ auth()->user()->ipk ?? '-' }}</strong>
                </div>
            </div>
            <div class="profile-info-item">
                <div class="profile-info-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                </div>
                <div>
                    <span>Email</span>
                    <strong>{{ auth()->user()->email }}</strong>
                </div>
            </div>
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
            <h2>Riwayat Pengajuan Terbaru</h2>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Kode</th><th>Jenis</th><th>Status</th><th>Progres</th></tr></thead>
                    <tbody>
                    @forelse($applications as $application)
                        <tr>
                            <td>{{ $application->application_code }}</td>
                            <td>{{ $application->documentType->name }}</td>
                            <td><span class="status {{ $application->status }}">{{ $application->status_label }}</span></td>
                            <td>
                                <div class="progress" data-pct="{{ $application->completionPercentage() }}"><span style="width: {{ $application->completionPercentage() }}%"></span></div>
                                {{ $application->completionPercentage() }}%
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="empty-state" style="border: none; padding: 24px 0;">
                                    <div class="empty-state-icon" style="width: 48px; height: 48px;">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="12" y1="8" x2="12" y2="12"></line>
                                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                        </svg>
                                    </div>
                                    <strong>Belum Ada Pengajuan</strong>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

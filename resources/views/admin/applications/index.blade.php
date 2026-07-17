{{--
    Halaman admin untuk menelusuri dan memfilter daftar pengajuan mahasiswa.
--}}
@extends('layouts.app')
@section('content')
    @php
        $statusOptions = [
            'submitted' => 'Diajukan',
            'in_review' => 'Diproses',
            'ready_pickup' => 'Siap Diambil',
            'rejected' => 'Ditolak',
        ];
    @endphp
    <div class="breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Beranda</a>
        <span class="breadcrumb-sep">&gt;</span>
        <span class="breadcrumb-current">Kelola Pengajuan</span>
    </div>

    <div class="page-head-row">
        <div>
            <p>Admin dapat mencari, memfilter, dan membuka detail pengajuan mahasiswa.</p>
        </div>
    </div>

    <div class="panel">
        <form method="GET" class="search-filter-bar">
            <div class="search-input-wrap">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" name="q" placeholder="Cari nama atau NIM..." value="{{ request('q') }}">
            </div>
            <select name="status" class="filter-select">
                <option value="">Semua Status</option>
                @foreach ($statusOptions as $value => $label)
                    <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>
                        {{ $label }}</option>
                @endforeach
            </select>
            <button class="btn primary" type="submit" style="border-radius: 12px; padding: 12px 24px;">Filter</button>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Mahasiswa</th>
                        <th>NIM</th>
                        <th>Jenis</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $application)
                        <tr>
                            <td>{{ $application->application_code }}</td>
                            <td>{{ $application->user->name }}</td>
                            <td>{{ $application->user->nim }}</td>
                            <td>{{ $application->documentType->name }}</td>
                             <td>{{ ($application->submitted_at ?? $application->created_at)->format('d M Y') }}</td>
                            <td><span class="status {{ $application->status }}">{{ $application->status_label }}</span>
                            </td>
                            <td><a class="text-link"
                                    href="{{ route('admin.applications.show', $application) }}">Periksa</a>
                                    
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="12" y1="8" x2="12" y2="12"></line>
                                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                        </svg>
                                    </div>
                                    <strong>Data Tidak Ditemukan</strong>
                                    <p>Tidak ada pengajuan berkas mahasiswa yang cocok dengan kriteria pencarian Anda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($applications->hasPages())
    <div class="pagination-wrapper">

        <div class="prev">
            @if ($applications->onFirstPage())
                <span>Halaman Sebelumnya</span>
            @else
                <a href="{{ $applications->previousPageUrl() }}">
                    Halaman Sebelumnya
                </a>
            @endif
        </div>

        <div class="next">
            @if ($applications->hasMorePages())
                <a href="{{ $applications->nextPageUrl() }}">
                    Halaman Selanjutnya
                </a>
            @else
                <span>Halaman Selanjutnya</span>
            @endif
        </div>

    </div>
@endif
    </div>
@endsection

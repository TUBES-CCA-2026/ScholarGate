{{--
    Halaman mahasiswa untuk melihat riwayat pengajuan pribadi.
--}}
@extends('layouts.app')
@section('content')

<div class="page-head-row">
    <div>
        <h1>Pengajuan</h1>
        <p>Daftar pengajuan berkas yang sudah Anda kirim ke prodi.</p>
    </div>
    <a href="{{ route('student.applications.create') }}" class="btn primary">+ Ajukan Berkas Baru</a>
</div>

<div class="panel">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Jenis Pengajuan</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Progres</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($applications as $application)
                <tr>
                    <td>{{ $application->application_code }}</td>
                    <td>{{ $application->documentType->name }}</td>
                    <td>{{ ($application->submitted_at ?? $application->created_at)->format('d M Y') }}</td>
                    <td><span class="status {{ $application->status }}">{{ $application->status_label }}</span></td>
                    <td>
                        <div class="progress" data-pct="{{ $application->completionPercentage() }}"><span style="width: {{ $application->completionPercentage() }}%"></span></div>
                        {{ $application->completionPercentage() }}%
                    </td>
                    <td><a class="text-link" href="{{ route('student.applications.show', $application) }}">Detail</a></td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state" style="border: none; padding: 36px 12px;">
                            <div class="empty-state-icon">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                    <polyline points="10 9 9 9 8 9"></polyline>
                                </svg>
                            </div>
                            <strong>Belum Ada Pengajuan</strong>
                            <p>Anda belum mengirimkan berkas pengajuan beasiswa apa pun.</p>
                            <a href="{{ route('student.applications.create') }}" class="btn primary small" style="margin-top: 8px;">Ajukan Sekarang</a>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
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
</div>
@endsection

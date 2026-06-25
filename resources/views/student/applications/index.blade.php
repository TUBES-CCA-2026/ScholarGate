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
                    <td>{{ $application->submitted_at?->format('d M Y') }}</td>
                    <td><span class="status {{ $application->status }}">{{ $application->status_label }}</span></td>
                    <td>
                        <div class="progress"><span style="width: {{ $application->completionPercentage() }}%"></span></div>
                        {{ $application->completionPercentage() }}%
                    </td>
                    <td><a class="text-link" href="{{ route('student.applications.show', $application) }}">Detail</a></td>
                </tr>
            @empty
                <tr><td colspan="6">Belum ada pengajuan. Klik tombol Ajukan Berkas Baru.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $applications->links() }}
</div>
@endsection

{{--
    Halaman admin untuk menelusuri dan memfilter daftar pengajuan mahasiswa.
--}}
@extends('layouts.app')
@section('content')
@php
    $statusOptions = [
        'submitted' => 'Dikirim',
        'in_review' => 'Sedang Direview',
        'revision' => 'Perlu Revisi',
        'approved' => 'Disetujui',
        'rejected' => 'Ditolak',
        'completed' => 'Selesai',
    ];
@endphp
<div class="page-head-row">
    <div>
        <h1>Kelola Beasiswa</h1>
        <p>Admin dapat mencari, memfilter, dan membuka detail pengajuan mahasiswa.</p>
    </div>
</div>

<div class="panel">
    <form method="GET" class="filter-row">
        <input type="text" name="q" placeholder="Cari nama atau NIM" value="{{ request('q') }}">
        <select name="status">
            <option value="">Semua Status</option>
            @foreach($statusOptions as $value => $label)
                <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button class="btn small" type="submit">Filter</button>
    </form>

    <div class="table-wrap">
        <table>
            <thead><tr><th>Kode</th><th>Mahasiswa</th><th>NIM</th><th>Jenis</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            @forelse($applications as $application)
                <tr>
                    <td>{{ $application->application_code }}</td>
                    <td>{{ $application->user->name }}</td>
                    <td>{{ $application->user->nim }}</td>
                    <td>{{ $application->documentType->name }}</td>
                    <td>{{ $application->submitted_at?->format('d M Y') }}</td>
                    <td><span class="status {{ $application->status }}">{{ $application->status_label }}</span></td>
                    <td><a class="text-link" href="{{ route('admin.applications.show', $application) }}">Periksa</a></td>
                </tr>
            @empty
                <tr><td colspan="7">Data tidak ditemukan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $applications->links() }}
</div>
@endsection

{{--
    Dashboard admin berisi ringkasan pengajuan, status dokumen, dan aktivitas terbaru.
--}}
@extends('layouts.app')
@section('content')
<section class="dashboard-hero dashboard-hero--admin">
    <div class="dashboard-hero__content">
        <span class="dashboard-eyebrow">Dashboard Admin Prodi</span>
        <h1>Kelola pengajuan, master beasiswa, dan pengumuman dari satu sistem.</h1>
        <p>Admin dapat memantau data mahasiswa, memeriksa dokumen, memberi catatan revisi, dan mengelola informasi beasiswa secara terpusat.</p>
        <div class="hero-actions dashboard-hero__actions">
            <a href="{{ route('admin.applications.index') }}" class="btn warning">Periksa Pengajuan</a>
            <a href="{{ route('admin.document-types.index') }}" class="btn ghost">Kelola Beasiswa</a>
        </div>
    </div>

    <div class="dashboard-hero__panel dashboard-hero__panel--admin" aria-label="Ringkasan dashboard admin">
        <div class="dashboard-mini-card"><span>Mahasiswa</span><strong>{{ $summary['students'] }}</strong></div>
        <div class="dashboard-mini-card"><span>Beasiswa</span><strong>{{ $summary['document_types'] }}</strong></div>
        <div class="dashboard-mini-card"><span>Baru Dikirim</span><strong>{{ $summary['submitted'] }}</strong></div>
        <div class="dashboard-mini-card"><span>Direview</span><strong>{{ $summary['in_review'] }}</strong></div>
    </div>
</section>

<div class="panel panel--dashboard mt-24">
    <div class="dashboard-panel-heading">
        <div>
            <span class="dashboard-eyebrow dashboard-eyebrow--muted">Monitoring</span>
            <h2>Pengajuan Terbaru</h2>
        </div>
        <a href="{{ route('admin.applications.index') }}" class="text-link">Lihat semua</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Kode</th><th>Mahasiswa</th><th>Jenis</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            @forelse($latestApplications as $application)
                <tr>
                    <td>{{ $application->application_code }}</td>
                    <td>{{ $application->user->name }}</td>
                    <td>{{ $application->documentType->name }}</td>
                    <td><span class="status {{ $application->status }}">{{ $application->status_label }}</span></td>
                    <td><a class="text-link" href="{{ route('admin.applications.show', $application) }}">Periksa</a></td>
                </tr>
            @empty
                <tr><td colspan="5">Belum ada pengajuan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

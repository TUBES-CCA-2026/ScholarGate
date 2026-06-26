{{--
    Halaman admin untuk memeriksa detail pengajuan serta memvalidasi dokumen.
--}}
@extends('layouts.app')
@section('content')
@php
    $statusOptions = [
    'in_review' => 'Sedang Direview',
    'revision' => 'Perlu Revisi',
    'ready_pickup' => 'Siap Diambil',
    'completed' => 'Selesai',
];
@endphp
<div class="page-head-row application-detail-header">
    <div>
        <h1>Periksa Pengajuan</h1>
        <p>{{ $studentApplication->application_code }}</p>
    </div>

    <div class="page-actions">

        <form method="POST" action="{{ route('admin.applications.destroy', $studentApplication) }}" onsubmit="return confirm('Pindahkan pengajuan ini ke recycle bin?')">
            @csrf
            @method('DELETE')

            <button class="btn danger" type="submit">
                Hapus Pengajuan
            </button>
            <a href="{{ route('admin.applications.index') }}" class="btn neutral">
                Kembali
            </a>
        </form>
    </div>
</div>

<div class="two-column">
    <div class="panel">
        <h2>Informasi Pengajuan</h2>
        <div class="profile-detail compact-detail">
            <span>Mahasiswa</span><strong>{{ $studentApplication->user->name }}</strong>
            <span>NIM</span><strong>{{ $studentApplication->user->nim }}</strong>
            <span>Program Studi</span><strong>{{ $studentApplication->user->program_studi }}</strong>
            <span>Jenis</span><strong>{{ $studentApplication->documentType->name }}</strong>
            <span>Status</span><strong><span class="status {{ $studentApplication->status }}">{{ $studentApplication->status_label }}</span></strong>
        </div>
        <div class="note-box mt-16"><strong>Alasan:</strong><br>{{ $studentApplication->purpose }}</div>
    </div>
    <div class="panel">
        <h2>Ubah Status</h2>
        <form method="POST" action="{{ route('admin.applications.update-status', $studentApplication) }}" class="form-stack">
            @csrf
            @method('PATCH')
            <label>Status</label>
            <select name="status" required>
                @foreach($statusOptions as $value => $label)
                    <option value="{{ $value }}" {{ $studentApplication->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <label>Catatan Admin</label>
            <textarea name="admin_note" rows="5" placeholder="Tulis catatan hasil pemeriksaan...">{{ old('admin_note', $studentApplication->admin_note) }}</textarea>
            <button class="btn primary" type="submit">Simpan Status</button>
        </form>
    </div>
</div>

<div class="panel mt-24">
    <h2>Dokumen Mahasiswa</h2>

    @php
        $documentStatusOptions = [
            'valid' => 'Siap',
            'missing' => 'Belum Siap',
        ];
    @endphp

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Syarat</th>
                    <th>Cek Manual</th>
                    <th></th>
                    <th>Status</th>
                    <th>Catatan & Aksi</th>
                </tr>
            </thead>
            <tbody>
            @foreach($studentApplication->documents as $document)
                @php
                    $fileUrl = $document->file_path ? asset('storage/'.$document->file_path) : null;
                    $extension = strtolower(pathinfo($document->original_name ?? $document->file_path ?? '', PATHINFO_EXTENSION));
                @endphp

                <tr>
                    <td>{{ $document->requirement->name }}</td>



                    <td>{{ $document->is_checked_manual ? 'Ya' : 'Tidak' }}</td>
                    <td></td>
                    <td>
                        <span class="status {{ $document->status }}">
                            {{ $document->status_label }}
                        </span>
                    </td>

                    <td>
                        <form method="POST" action="{{ route('admin.applications.documents.update', [$studentApplication, $document]) }}" class="doc-review-form">
                            @csrf
                            @method('PATCH')

                            <select name="status" required>
                                @foreach($documentStatusOptions as $value => $label)
                                    <option value="{{ $value }}" {{ $document->status === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>

                            <textarea name="note" rows="3" placeholder="Catatan khusus dokumen...">{{ old('note', $document->note) }}</textarea>

                            <button class="btn small primary" type="submit">
                                Simpan
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

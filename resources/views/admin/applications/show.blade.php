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
        <h1>Periksa Pengajuan</h1>
        <p>{{ $studentApplication->application_code }}</p>
    </div>
    <a href="{{ route('admin.applications.index') }}" class="btn neutral">Kembali</a>
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
            'submitted' => 'Dikirim',
            'valid' => 'Valid',
            'invalid' => 'Perlu Revisi',
            'missing' => 'Belum Ada',
        ];
    @endphp

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Syarat</th>
                    <th>File & Preview</th>
                    <th>Cek Manual</th>
                    <th>Kedaluwarsa</th>
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

                    <td>
                        @if($document->file_path)
                            <a class="text-link" href="{{ $fileUrl }}" target="_blank">
                                {{ $document->original_name }}
                            </a>

                            @if(in_array($extension, ['jpg', 'jpeg', 'png', 'webp']))
                                <div class="document-preview">
                                    <img src="{{ $fileUrl }}" alt="Preview {{ $document->original_name }}" class="document-preview-image">
                                </div>
                            @elseif($extension === 'pdf')
                                <div class="document-preview">
                                    <iframe src="{{ $fileUrl }}" class="document-preview-frame"></iframe>
                                </div>
                            @else
                                <small class="help-text">Preview tidak tersedia untuk format ini. Gunakan tombol file untuk membuka dokumen.</small>
                            @endif
                        @else
                            -
                        @endif
                    </td>

                    <td>{{ $document->is_checked_manual ? 'Ya' : 'Tidak' }}</td>
                    <td>{{ $document->expired_at?->format('d M Y') ?? '-' }}</td>
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

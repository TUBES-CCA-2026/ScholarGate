{{--
    Halaman admin untuk memeriksa detail pengajuan serta memvalidasi dokumen.
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

    $documentStatusOptions = [
        'missing' => 'Belum Diunggah',
        'ready' => 'Siap Diambil',
        'invalid' => 'Dibatalkan',
    ];
@endphp
    <div class="breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Beranda</a>
        <span class="breadcrumb-sep">&gt;</span>
        <a href="{{ route('admin.applications.index') }}">Kelola Pengajuan</a>
        <span class="breadcrumb-sep">&gt;</span>
        <span class="breadcrumb-current">Periksa Pengajuan</span>
    </div>

<div class="page-head-row application-detail-header">
    <div>
        <h1>Periksa Pengajuan</h1>
        <p>{{ $studentApplication->application_code }}</p>
    </div>

    <div class="page-actions">

        <form method="POST" action="{{ route('admin.applications.destroy', $studentApplication) }}" data-confirm="Apakah Anda yakin ingin memindahkan pengajuan ini ke arsip terhapus?">
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

@if(session('success'))
    <div style="background: #065f46; border: 1px solid #059669; color: #d1fae5; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 0.9rem;">
        ✓ {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div style="background: #7f1d1d; border: 1px solid #dc2626; color: #fecaca; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 0.9rem;">
        ⚠️ {{ session('error') }}
    </div>
@endif

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
            <button class="btn primary" type="submit">Simpan Status</button>
        </form>
    </div>
</div>

<div class="panel mt-24">
    <h2>Dokumen Mahasiswa</h2>

    <form method="POST" action="{{ route('admin.applications.documents.update-all', $studentApplication) }}" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Syarat</th>
                        <th>Status</th>
                        <th>Catatan & Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($studentApplication->documents as $document)
                    <tr>
                        <td>{{ $document->requirement->name }}</td>
                        <td>
                            <span class="status {{ $document->status }}">
                                {{ $document->status_label }}
                            </span>
                        </td>

                        <td>
                            <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                                <select name="documents[{{ $document->id }}][status]" required style="width: auto;">
                                    @foreach($documentStatusOptions as $value => $label)
                                        <option value="{{ $value }}" {{ $document->status === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>

                                <div class="file-upload-zone file-upload-zone--inline" data-upload-zone>
                                    <input type="file" name="documents[{{ $document->id }}][file]" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="file-upload-input" data-upload-input>
                                    <div class="file-upload-content" data-upload-content>
                                        <div class="file-upload-icon file-upload-icon--small">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                                <polyline points="17 8 12 3 7 8"/>
                                                <line x1="12" y1="3" x2="12" y2="15"/>
                                            </svg>
                                        </div>
                                        <p class="file-upload-text" style="font-size:12px">Upload file</p>
                                    </div>
                                    <div class="file-upload-preview file-upload-preview--inline" data-upload-preview style="display:none">
                                        <span class="file-upload-filename" data-upload-filename></span>
                                        <button type="button" class="file-upload-remove file-upload-remove--small" data-upload-remove aria-label="Hapus file">&times;</button>
                                    </div>
                                </div>

                                @if($document->file_path)
                                    <a
                                        class="btn small neutral"
                                        href="{{ route(
                                            'admin.applications.documents.view',
                                            [
                                                'studentApplication' => $studentApplication,
                                                'applicationDocument' => $document,
                                            ]
                                        ) }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        style="padding: 6px 10px;"
                                    >
                                        Lihat File
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 16px; display: flex; justify-content: flex-end;">
            <button class="btn primary" type="submit">Simpan Semua Dokumen</button>
        </div>
    </form>
</div>

<script>
(() => {
    document.querySelectorAll('[data-upload-zone]').forEach((zone) => {
        const input = zone.querySelector('[data-upload-input]');
        const content = zone.querySelector('[data-upload-content]');
        const previewWrap = zone.querySelector('[data-upload-preview]');
        const previewImg = zone.querySelector('[data-upload-preview-img]');
        const filenameEl = zone.querySelector('[data-upload-filename]');
        const removeBtn = zone.querySelector('[data-upload-remove]');
        if (!input) return;

        const showPreview = (file) => {
            if (!file) return;
            if (previewImg && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.addEventListener('load', () => {
                    previewImg.src = reader.result;
                    content.style.display = 'none';
                    previewWrap.style.display = '';
                });
                reader.readAsDataURL(file);
            } else if (filenameEl) {
                filenameEl.textContent = file.name;
                content.style.display = 'none';
                previewWrap.style.display = '';
            }
        };

        const resetUpload = () => {
            input.value = '';
            if (previewImg) previewImg.src = '';
            if (filenameEl) filenameEl.textContent = '';
            content.style.display = '';
            previewWrap.style.display = 'none';
        };

        input.addEventListener('change', () => {
            const file = input.files && input.files[0];
            if (file) showPreview(file);
        });

        if (removeBtn) {
            removeBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                resetUpload();
            });
        }

        ['dragenter', 'dragover'].forEach((evt) => {
            zone.addEventListener(evt, (e) => { e.preventDefault(); zone.classList.add('is-dragover'); });
        });
        ['dragleave', 'drop'].forEach((evt) => {
            zone.addEventListener(evt, (e) => { e.preventDefault(); zone.classList.remove('is-dragover'); });
        });
        zone.addEventListener('drop', (e) => {
            const file = e.dataTransfer?.files?.[0];
            if (file) {
                const dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;
                showPreview(file);
            }
        });
    });
})();
</script>
@endsection

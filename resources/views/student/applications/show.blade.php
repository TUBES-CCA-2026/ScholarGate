{{--
    Halaman mahasiswa untuk melihat detail status pengajuan dan mengunggah revisi.
--}}
@extends('layouts.app')
@section('content')
@php
    $adminPhone = '6281241456546';

    $nama = $studentApplication->user->name;
    $nim = $studentApplication->user->nim;
    $kelas = $studentApplication->user->kelas;
    $prodi = $studentApplication->user->program_studi;
    $ipk = $studentApplication->user->ipk;
    $alasan = $studentApplication->purpose;
    $jenisBerkas = $studentApplication->documentType->name;

    $message = rawurlencode(
        "Assalamualaikum Saya {$nama} dengan NIM {$nim} ingin meminta berkas {$jenisBerkas} untuk keperluan Beasiswa\n" .
        "Berikut Data Diri saya:\n\n" .
        "NIM: {$nim}\n" .
        "Nama: {$nama}\n" .
        "Kelas: {$kelas}\n" .
        "Program Studi: {$prodi}\n" .
        "IPK: {$ipk}\n" .
        "Alasan: {$alasan}"
    );
@endphp
<div class="page-head-row">
    <div>
        <h1>Detail Pengajuan</h1>
        <p>{{ $studentApplication->application_code }}</p>
    </div>
    <a href="https://wa.me/{{ $adminPhone }}?text={{ $message }}" target="_blank" class="btn primary">Kirim via WhatsApp</a>
</div>

<div class="two-column">
    <div class="panel">
        <h2>{{ $studentApplication->documentType->name }}</h2>
        <p>{{ $studentApplication->purpose }}</p>
        <div class="info-grid">
            <div><span>Status</span><strong><span class="status {{ $studentApplication->status }}">{{ $studentApplication->status_label }}</span></strong></div>
            <div><span>Tanggal Pengajuan</span><strong>{{ $studentApplication->submitted_at?->format('d M Y H:i') }}</strong></div>
            <div><span>Progres Berkas</span><strong>{{ $studentApplication->completionPercentage() }}%</strong></div>
        </div>
        @if($studentApplication->admin_note)
            <div class="note-box"><strong>Catatan Admin:</strong><br>{{ $studentApplication->admin_note }}</div>
        @endif
    </div>
    <div class="panel">
        <h2>Data Mahasiswa</h2>
        <div class="profile-detail compact-detail">
            <span>Nama</span><strong>{{ $studentApplication->user->name }}</strong>
            <span>NIM</span><strong>{{ $studentApplication->user->nim }}</strong>
            <span>Program Studi</span><strong>{{ $studentApplication->user->program_studi }}</strong>
            <span>Kelas</span><strong>{{ $studentApplication->user->kelas }}</strong>
            <span>IPK</span><strong>{{ $studentApplication->user->ipk }}</strong>
        </div>
    </div>
</div>

<div class="panel mt-24">
    <h2>Kelengkapan Berkas</h2>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Syarat</th>
                    <th>File</th>
                    <th>Cek Manual</th>
                    <th>Kedaluwarsa</th>
                    <th>Status</th>
                    <th>Revisi</th>
                </tr>
            </thead>
            <tbody>
            @foreach($studentApplication->documents as $document)
                @php
                    $canRevise = $studentApplication->status === 'revision'
                        && in_array($document->status, ['invalid', 'missing'], true);
                @endphp

                <tr>
                    <td>{{ $document->requirement->name }}</td>

                    <td>
                        @if($document->file_path)
                            <a class="text-link" href="{{ asset('storage/'.$document->file_path) }}" target="_blank">
                                {{ $document->original_name }}
                            </a>
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
                        @if($document->note)
                            <div class="document-note">
                                <strong>Catatan Admin:</strong><br>
                                {{ $document->note }}
                            </div>
                        @endif

                        @if($canRevise)
                            <form method="POST" action="{{ route('student.applications.documents.revise', [$studentApplication, $document]) }}" enctype="multipart/form-data" class="revision-upload-form">
                                @csrf
                                @method('PATCH')

                                <input type="file" name="revision_file" required>
                                <button class="btn small primary" type="submit">
                                    Upload Revisi
                                </button>
                            </form>
                        @else
                            <small class="help-text">Tidak ada revisi.</small>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

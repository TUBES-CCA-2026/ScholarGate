@extends('layouts.app')
@section('content')
<div class="page-head-row">
    <div>
        <h1>Informasi</h1>
        <p>Katalog pengajuan, syarat berkas, dan pengumuman dari prodi.</p>
    </div>
    <a href="{{ route('student.applications.create') }}" class="btn primary">Ajukan Berkas</a>
</div>

<div class="two-column">
    <div class="panel">
        <h2>Katalog Pengajuan</h2>
        @foreach($documentTypes as $type)
            <div class="list-card information-master-card">
                @if($type->image_path)
                    <img class="information-master-image" src="{{ asset('storage/' . $type->image_path) }}" alt="Foto {{ $type->name }}" >
                @endif
                <div class="information-master-main">
                    <span class="tag">{{ $type->category }}</span>
                    <h3>{{ $type->name }}</h3>
                    <p>{{ $type->description }}</p>
                    <small>Batas waktu: {{ $type->deadline?->format('d M Y') ?? '-' }}</small>
                </div>
                <a class="btn small information-master-button" href="{{ route('student.applications.create', ['type' => $type->id]) }}">Pilih</a>
                <div class="requirement-list">
                    <strong>Syarat:</strong>
                    <ul>
                        @foreach($type->requirements as $requirement)
                            <li>{{ $requirement->name }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endforeach
    </div>
    <div class="panel">
        <h2>Pengumuman</h2>
        @forelse($announcements as $announcement)
            <div class="announcement-item">
                <h3>{{ $announcement->title }}</h3>
                <p>{{ $announcement->body }}</p>
                <small>{{ $announcement->published_at?->format('d M Y H:i') }}</small>
            </div>
        @empty
            <p>Belum ada pengumuman.</p>
        @endforelse
    </div>
</div>
@endsection

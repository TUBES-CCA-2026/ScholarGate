{{--
    Halaman recycle bin admin.
    Data di halaman ini adalah pengajuan yang sudah dihapus sementara
    menggunakan soft delete dan masih dapat dipulihkan.
--}}
@extends('layouts.app')

@section('content')
    <div class="page-head-row">
        <div>
            <h1>Recycle Bin Pengajuan</h1>
            <p>Pengajuan yang dihapus dari daftar utama tersimpan sementara di sini sebelum dihapus permanen.</p>
        </div>
    </div>

    <div class="panel">
        <form method="GET" class="filter-row filter-row--single">
            <input type="text" name="q" placeholder="Cari nama atau NIM" value="{{ request('q') }}">
            <button class="btn small" type="submit">Cari</button>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Mahasiswa</th>
                        <th>NIM</th>
                        <th>Jenis</th>
                        <th>Status Terakhir</th>
                        <th>Dihapus Pada</th>
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
                            <td>
                                <span class="status {{ $application->status }}">
                                    {{ $application->status_label }}
                                </span>
                            </td>
                            <td>{{ $application->deleted_at?->format('d M Y H:i') }}</td>
                            <td>
                                <div class="table-actions">
                                    <form method="POST"
                                        action="{{ route('admin.applications.restore', $application->id) }}"
                                        onsubmit="return confirm('Pulihkan pengajuan ini ke daftar utama?')">
                                        @csrf
                                        @method('PATCH')

                                        <button class="text-link" type="submit">
                                            Pulihkan
                                        </button>
                                    </form>

                                    <form method="POST"
                                        action="{{ route('admin.applications.force-delete', $application->id) }}"
                                        onsubmit="return confirm('Hapus pengajuan ini secara permanen? Tindakan ini tidak dapat dibatalkan.')">
                                        @csrf
                                        @method('DELETE')

                                        <button class="text-link text-danger" type="submit">
                                            Hapus Permanen
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">Recycle bin masih kosong.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $applications->links() }}
    </div>
@endsection

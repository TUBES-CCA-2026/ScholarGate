{{--
    Halaman daftar akun user untuk admin.
--}}
@extends('layouts.app')
@section('content')
    <div class="page-head-row">
        <div>
            <h1>Kelola Akun</h1>
            <p>Kelola data akun seluruh pengguna yang terdaftar dalam sistem.</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.users.index') }}" class="filter-row" style="grid-template-columns: 1fr 160px auto;">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, NIM, atau email...">
        <select name="role">
            <option value="">Semua Role</option>
            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Mahasiswa</option>
        </select>
        <button class="btn primary" type="submit">Filter</button>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>NIM</th>
                    <th>Email</th>
                    <th>Program Studi</th>
                    <th>Kelas</th>
                    <th>Role</th>
                    <th>Terdaftar</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                @if($user->photo_path)
                                    <img class="avatar-img" src="{{ asset('storage/' . $user->photo_path) }}" alt="{{ $user->name }}" style="width:32px;height:32px;">
                                @else
                                    <div class="avatar" style="width:32px;height:32px;font-size:13px;">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                @endif
                                <span>{{ $user->name }}</span>
                            </div>
                        </td>
                        <td>{{ $user->nim ?? '-' }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->program_studi ?? '-' }}</td>
                        <td>{{ $user->kelas ?? '-' }}</td>
                        <td>
                            <span class="tag">{{ $user->role === 'admin' ? 'Admin' : 'Mahasiswa' }}</span>
                        </td>
                        <td>{{ $user->created_at->format('d M Y') }}</td>
                        <td style="text-align: right;">
                            <div class="table-actions">
                                <a href="{{ route('admin.users.edit', $user) }}" class="text-link">Edit</a>
                                @if(auth()->id() !== $user->id)
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" data-confirm="Apakah Anda yakin ingin menghapus akun {{ $user->name }}? Semua data pengajuan user ini juga akan dihapus!">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-link text-danger" type="submit">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">Tidak ada user yang ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div style="margin-top: 18px;">
            {{ $users->links() }}
        </div>
    @endif
@endsection

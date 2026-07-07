{{--
    Form mahasiswa untuk memperbarui profil dan foto.
--}}
@extends('layouts.app')

@section('content')
<h1 class="page-title">Edit Profil Mahasiswa</h1>

<div class="form-card profile-edit-card">
    <form method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data" class="form-stack">
        @csrf
        @method('PUT')

        <div class="profile-photo-preview">
            @if($user->photo_path)
                <img src="{{ asset('storage/' . $user->photo_path) }}" alt="Foto Profil">
            @else
                <div class="large-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
            @endif
        </div>

        <div>
            <label>Foto Profil</label>
            <div class="file-upload-zone" data-upload-zone>
                <input type="file" name="photo" id="photo" accept="image/*" class="file-upload-input" data-upload-input>
                <div class="file-upload-content" data-upload-content>
                    <div class="file-upload-icon">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="17 8 12 3 7 8"/>
                            <line x1="12" y1="3" x2="12" y2="15"/>
                        </svg>
                    </div>
                    <p class="file-upload-text">Klik atau seret foto ke sini</p>
                    <p class="file-upload-hint">JPG, PNG, WEBP &bull; Maks. 2 MB</p>
                </div>
                <div class="file-upload-preview" data-upload-preview style="display:none">
                    <img src="" alt="Preview" data-upload-preview-img>
                    <button type="button" class="file-upload-remove" data-upload-remove aria-label="Hapus file">&times;</button>
                </div>
            </div>
        </div>

        <div>
            <label for="name">Nama Lengkap</label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" pattern="[a-zA-Z\s.,'\\-]+" title="Nama lengkap hanya boleh berisi huruf, spasi, titik, koma, tanda petik, dan hubung." required>
        </div>

        <div>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required>
        </div>

        <div class="grid-2">
            <div>
                <label for="nim">NIM</label>
                <input type="text" name="nim" id="nim" value="{{ old('nim', $user->nim) }}" pattern="[0-9]+" title="NIM hanya boleh berisi angka.">
            </div>

            <div>
                <label for="kelas">Kelas</label>
                <x-kelas-selector value="{{ $user->kelas }}" required="true" />
            </div>
        </div>

        <div>
            <label for="program_studi">Program Studi</label>
            <select name="program_studi" id="program_studi" required>
                <option value="">Pilih Program Studi</option>
                <option value="Teknik Informatika" {{ old('program_studi', $user->program_studi) === 'Teknik Informatika' ? 'selected' : '' }}>Teknik Informatika</option>
                <option value="Sistem Informasi" {{ old('program_studi', $user->program_studi) === 'Sistem Informasi' ? 'selected' : '' }}>Sistem Informasi</option>
            </select>
        </div>

        <div class="grid-2">
            <div>
                <label for="ipk">IPK</label>
                <input type="number" step="0.01" min="0" max="4" name="ipk" id="ipk" value="{{ old('ipk', $user->ipk) }}">
            </div>

            <div>
                <label for="phone">Nomor Telepon</label>
                <input type="tel" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" pattern="[0-9]{10,15}" title="Nomor telepon harus berupa 10 hingga 15 digit angka saja.">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn primary">Simpan Perubahan</button>
            <a href="{{ route('student.profile') }}" class="btn neutral">Batal</a>
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
        const removeBtn = zone.querySelector('[data-upload-remove]');
        if (!input) return;

        const showPreview = (file) => {
            if (!file || !file.type.startsWith('image/')) return;
            const reader = new FileReader();
            reader.addEventListener('load', () => {
                previewImg.src = reader.result;
                content.style.display = 'none';
                previewWrap.style.display = '';
            });
            reader.readAsDataURL(file);
        };

        const resetUpload = () => {
            input.value = '';
            previewImg.src = '';
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
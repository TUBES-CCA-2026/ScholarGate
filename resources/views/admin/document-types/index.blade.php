@extends('layouts.app')
@section('content')
<div class="page-head-row">
    <div>
        <h1>Data Beasiswa</h1>
        <p>Kelola jenis pengajuan, kategori, penyelenggara, batas waktu, foto, dan syarat dokumen.</p>
    </div>
</div>

<div class="two-column master-layout">
    <div class="panel master-create-panel">
        <h2>Tambah Beasiswa</h2>
        <form method="POST" action="{{ route('admin.document-types.store') }}" enctype="multipart/form-data" class="form-stack">
            @csrf
            <label for="create-name">Nama Beasiswa</label>
            <input id="create-name" type="text" name="name" placeholder="Contoh: Surat Rekomendasi Beasiswa" required>

            <label for="create-category">Kategori</label>
            <input id="create-category" type="text" name="category" placeholder="Prestasi / Kepemimpinan / Riset" required>

            <label for="create-provider">Penyelenggara</label>
            <input id="create-provider" type="text" name="provider" placeholder="Nama penyedia beasiswa">

            <label for="create-description">Deskripsi</label>
            <textarea id="create-description" name="description" rows="4" required></textarea>

            <label for="create-image">Foto Master Berkas</label>
            <input id="create-image" type="file" name="image" accept="image/jpeg,image/png,image/webp">
            <small class="field-help">Format JPG, PNG, atau WEBP. Maksimal 4 MB.</small>

            <label for="create-deadline">Batas Waktu</label>
            <input id="create-deadline" type="date" name="deadline">

            <label for="create-link">Link Pendaftaran Eksternal</label>
            <input id="create-link" type="url" name="registration_link" placeholder="https://...">

            <label for="create-requirements">Syarat Berkas</label>
            <textarea id="create-requirements" name="requirements" rows="5" placeholder="Tulis satu syarat per baris"></textarea>

            <button type="submit" class="btn primary">Simpan Master</button>
        </form>
    </div>

    <div class="panel master-list-panel">
        <div class="master-list-heading">
            <div>
                <h2>Daftar Master</h2>
                <p>{{ $documentTypes->count() }} jenis pengajuan tersimpan.</p>
            </div>
        </div>

        @forelse($documentTypes as $type)
            <div class="list-card master-card">
                <div class="master-card-image">
                    @if($type->image_path)
                        <img src="{{ asset('storage/' . $type->image_path) }}" alt="Foto {{ $type->name }}">
                    @else
                        <div class="master-card-placeholder" aria-label="Belum ada foto">
                            <span>{{ strtoupper(substr($type->category, 0, 1)) }}</span>
                        </div>
                    @endif
                </div>

                <div class="master-card-content">
                    <div class="master-card-badges">
                        <span class="tag">{{ $type->category }}</span>
                        <span class="status {{ $type->is_active ? 'valid' : 'missing' }}">
                            {{ $type->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    <h3>{{ $type->name }}</h3>
                    <p>{{ $type->description }}</p>
                    <div class="master-card-meta">
                        <span><strong>Penyelenggara:</strong> {{ $type->provider ?: '-' }}</span>
                        <span><strong>Batas waktu:</strong> {{ $type->deadline?->format('d M Y') ?? '-' }}</span>
                    </div>

                    @if($type->registration_link)
                        <a class="text-link master-link" href="{{ $type->registration_link }}" target="_blank" rel="noopener noreferrer">Buka link pendaftaran</a>
                    @endif

                    <div class="master-requirements">
                        <strong>Syarat berkas</strong>
                        @if($type->requirements->isNotEmpty())
                            <ul class="mini-list">
                                @foreach($type->requirements as $requirement)
                                    <li>{{ $requirement->name }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="master-empty-requirements">Belum ada syarat berkas.</p>
                        @endif
                    </div>
                </div>

                <div class="master-card-actions">
                    <form method="POST" action="{{ route('admin.document-types.destroy', $type) }}" onsubmit="return confirm('Hapus master ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn danger small" type="submit">Hapus</button>
                    </form>
                    <button
                        class="btn primary small master-edit-button"
                        type="button"
                        data-master-edit="{{ $type->id }}"
                        aria-haspopup="dialog"
                    >
                        Edit
                    </button>
                </div>
            </div>

            @php
                $isEditingWithErrors = (string) old('editing_id') === (string) $type->id;
                $editRequirements = $isEditingWithErrors
                    ? collect(old('requirements', []))
                    : $type->requirements->map(fn ($requirement) => [
                        'id' => $requirement->id,
                        'name' => $requirement->name,
                    ]);
            @endphp

            <div class="master-modal" id="master-edit-{{ $type->id }}" aria-hidden="true">
                <button class="master-modal-backdrop" type="button" data-master-close aria-label="Tutup formulir edit"></button>
                <div class="master-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="master-edit-title-{{ $type->id }}">
                    <div class="master-modal-header">
                        <div>
                            <span class="modal-eyebrow">EDIT MASTER BERKAS</span>
                            <h2 id="master-edit-title-{{ $type->id }}">{{ $type->name }}</h2>
                        </div>
                        <button class="master-modal-close" type="button" data-master-close aria-label="Tutup">×</button>
                    </div>

                    <form method="POST" action="{{ route('admin.document-types.update', $type) }}" enctype="multipart/form-data" class="form-stack master-edit-form">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="editing_id" value="{{ $type->id }}">

                        <div class="grid-2">
                            <div>
                                <label for="edit-name-{{ $type->id }}">Nama Pengajuan</label>
                                <input id="edit-name-{{ $type->id }}" type="text" name="name" value="{{ $isEditingWithErrors ? old('name') : $type->name }}" required>
                            </div>
                            <div>
                                <label for="edit-category-{{ $type->id }}">Kategori</label>
                                <input id="edit-category-{{ $type->id }}" type="text" name="category" value="{{ $isEditingWithErrors ? old('category') : $type->category }}" required>
                            </div>
                        </div>

                        <label for="edit-provider-{{ $type->id }}">Penyelenggara</label>
                        <input id="edit-provider-{{ $type->id }}" type="text" name="provider" value="{{ $isEditingWithErrors ? old('provider') : $type->provider }}">

                        <label for="edit-description-{{ $type->id }}">Deskripsi</label>
                        <textarea id="edit-description-{{ $type->id }}" name="description" rows="5" required>{{ $isEditingWithErrors ? old('description') : $type->description }}</textarea>

                        <div class="master-photo-editor">
                            <div class="master-photo-preview" data-image-preview>
                                @if($type->image_path)
                                    <img src="{{ asset('storage/' . $type->image_path) }}" alt="Foto {{ $type->name }}">
                                @else
                                    <span>Belum ada foto</span>
                                @endif
                            </div>
                            <div class="master-photo-controls">
                                <label for="edit-image-{{ $type->id }}">Ganti Foto</label>
                                <input
                                    id="edit-image-{{ $type->id }}"
                                    type="file"
                                    name="image"
                                    accept="image/jpeg,image/png,image/webp"
                                    data-image-input
                                >
                                <small class="field-help">Foto baru akan menggantikan foto lama. Maksimal 4 MB.</small>
                                @if($type->image_path)
                                    <label class="checkbox-line">
                                        <input type="checkbox" name="remove_image" value="1">
                                        Hapus foto saat ini
                                    </label>
                                @endif
                            </div>
                        </div>

                        <div class="grid-2">
                            <div>
                                <label for="edit-deadline-{{ $type->id }}">Batas Waktu</label>
                                <input id="edit-deadline-{{ $type->id }}" type="date" name="deadline" value="{{ $isEditingWithErrors ? old('deadline') : $type->deadline?->format('Y-m-d') }}">
                            </div>
                            <div>
                                <label for="edit-link-{{ $type->id }}">Link Pendaftaran</label>
                                <input id="edit-link-{{ $type->id }}" type="url" name="registration_link" value="{{ $isEditingWithErrors ? old('registration_link') : $type->registration_link }}" placeholder="https://...">
                            </div>
                        </div>

                        <div class="requirement-editor-heading">
                            <div>
                                <label>Syarat Berkas</label>
                                <small class="field-help">Edit setiap syarat secara terpisah agar data pengajuan lama tetap aman.</small>
                            </div>
                            <button type="button" class="btn neutral small" data-add-requirement>Tambah Syarat</button>
                        </div>

                        <div class="requirement-editor-list" data-requirements-list>
                            @foreach($editRequirements as $requirementIndex => $requirement)
                                <div class="requirement-editor-row" data-requirement-row>
                                    <input
                                        type="hidden"
                                        name="requirements[{{ $requirementIndex }}][id]"
                                        value="{{ $requirement['id'] ?? '' }}"
                                    >
                                    <input
                                        type="text"
                                        name="requirements[{{ $requirementIndex }}][name]"
                                        value="{{ $requirement['name'] ?? '' }}"
                                        placeholder="Nama syarat berkas"
                                        maxlength="255"
                                    >
                                    <button type="button" class="btn danger small" data-remove-requirement>Hapus</button>
                                </div>
                            @endforeach
                        </div>
                        <small class="field-help">Syarat yang sudah digunakan dalam pengajuan mahasiswa dapat diganti namanya, tetapi tidak dapat dihapus.</small>

                        <label class="checkbox-line master-active-check">
                            <input type="checkbox" name="is_active" value="1" {{ ($isEditingWithErrors ? old('is_active') : $type->is_active) ? 'checked' : '' }}>
                            Tampilkan jenis pengajuan ini kepada mahasiswa
                        </label>

                        <div class="master-modal-actions">
                            <button type="button" class="btn neutral" data-master-close>Batal</button>
                            <button type="submit" class="btn primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <div class="empty-state">Belum ada master berkas.</div>
        @endforelse
    </div>
</div>

<script>
    (() => {
        const body = document.body;
        const modals = document.querySelectorAll('.master-modal');
        const openButtons = document.querySelectorAll('[data-master-edit]');
        const closeButtons = document.querySelectorAll('[data-master-close]');

        const closeModal = (modal) => {
            if (!modal) return;
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            body.classList.remove('master-modal-open');
        };

        const openModal = (modal) => {
            if (!modal) return;
            modals.forEach(closeModal);
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            body.classList.add('master-modal-open');
            const firstField = modal.querySelector('input:not([type="hidden"]), textarea, select');
            if (firstField) window.setTimeout(() => firstField.focus(), 50);
        };

        openButtons.forEach((button) => {
            button.addEventListener('click', () => {
                openModal(document.getElementById(`master-edit-${button.dataset.masterEdit}`));
            });
        });

        closeButtons.forEach((button) => {
            button.addEventListener('click', () => closeModal(button.closest('.master-modal')));
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeModal(document.querySelector('.master-modal.is-open'));
            }
        });

        document.querySelectorAll('[data-image-input]').forEach((input) => {
            input.addEventListener('change', () => {
                const file = input.files && input.files[0];
                const preview = input.closest('.master-photo-editor')?.querySelector('[data-image-preview]');
                if (!file || !preview) return;

                const reader = new FileReader();
                reader.addEventListener('load', () => {
                    preview.innerHTML = '';
                    const image = document.createElement('img');
                    image.src = reader.result;
                    image.alt = 'Pratinjau foto baru';
                    preview.appendChild(image);
                });
                reader.readAsDataURL(file);
            });
        });

        document.querySelectorAll('.master-edit-form').forEach((form) => {
            const list = form.querySelector('[data-requirements-list]');
            const addButton = form.querySelector('[data-add-requirement]');
            if (!list || !addButton) return;

            let nextIndex = list.querySelectorAll('[data-requirement-row]').length;

            addButton.addEventListener('click', () => {
                const row = document.createElement('div');
                row.className = 'requirement-editor-row';
                row.dataset.requirementRow = '';
                row.innerHTML = `
                    <input type="hidden" name="requirements[${nextIndex}][id]" value="">
                    <input type="text" name="requirements[${nextIndex}][name]" placeholder="Nama syarat berkas" maxlength="255">
                    <button type="button" class="btn danger small" data-remove-requirement>Hapus</button>
                `;
                list.appendChild(row);
                row.querySelector('input[type="text"]')?.focus();
                nextIndex += 1;
            });

            list.addEventListener('click', (event) => {
                const removeButton = event.target.closest('[data-remove-requirement]');
                if (!removeButton) return;
                removeButton.closest('[data-requirement-row]')?.remove();
            });
        });

        const editingId = @json(old('editing_id'));
        if (editingId) {
            openModal(document.getElementById(`master-edit-${editingId}`));
        }
    })();
</script>
@endsection

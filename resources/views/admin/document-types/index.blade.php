{{--
Halaman admin untuk mengelola master beasiswa, foto, status aktif, dan syarat dokumen.
--}}
@extends('layouts.app')
@section('content')
    <div class="breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Beranda</a>
        <span class="breadcrumb-sep">&gt;</span>
        <span class="breadcrumb-current">Data Beasiswa</span>
    </div>

    <div class="page-head-row">
        <div>
            <h1>Data Beasiswa</h1>
            <p>Kelola jenis pengajuan, kategori, penyelenggara, batas waktu, foto, dan syarat dokumen.</p>
        </div>
    </div>

    <div class="two-column master-layout">
        <div class="panel master-create-panel">
            <h2>Tambah Beasiswa</h2>
            <form method="POST" action="{{ route('admin.document-types.store') }}" enctype="multipart/form-data"
                class="form-stack">
                @csrf
                <label for="create-name">Nama Beasiswa</label>
                <input id="create-name" type="text" name="name" placeholder="Masukkan Nama Beasiswa" required>

                <label for="create-category">Kategori</label>
                <select id="create-category" name="category" required>
                    <option value="">Pilih Kategori</option>
                    <option value="S1" {{ old('category') === 'S1' ? 'selected' : '' }}>S1</option>
                    <option value="S2" {{ old('category') === 'S2' ? 'selected' : '' }}>S2</option>
                    <option value="S3" {{ old('category') === 'S3' ? 'selected' : '' }}>S3</option>
                    <option value="Prestasi" {{ old('category') === 'Prestasi' ? 'selected' : '' }}>Prestasi</option>
                    <option value="Kepemimpinan" {{ old('category') === 'Kepemimpinan' ? 'selected' : '' }}>Kepemimpinan
                    </option>
                    <option value="Riset" {{ old('category') === 'Riset' ? 'selected' : '' }}>Riset</option>
                    <option value="Sosial" {{ old('category') === 'Sosial' ? 'selected' : '' }}>Sosial</option>
                    <option value="Olahraga" {{ old('category') === 'Olahraga' ? 'selected' : '' }}>Olahraga</option>
                </select>

                <label for="create-provider">Penyelenggara</label>
                <input id="create-provider" type="text" name="provider" placeholder="Nama penyedia beasiswa">

                <label for="create-description">Deskripsi</label>
                <textarea id="create-description" name="description" rows="4" required></textarea>

                <label>Logo Beasiswa</label>
                <div class="file-upload-zone" data-upload-zone>
                    <input id="create-image" type="file" name="image" accept="image/jpeg,image/png,image/webp"
                        class="file-upload-input" data-upload-input>
                    <div class="file-upload-content" data-upload-content>
                        <div class="file-upload-icon">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <polyline points="17 8 12 3 7 8" />
                                <line x1="12" y1="3" x2="12" y2="15" />
                            </svg>
                        </div>
                        <p class="file-upload-text">Klik atau seret file ke sini</p>
                        <p class="file-upload-hint">JPG, PNG, WEBP &bull; Maks. 4 MB</p>
                    </div>
                    <div class="file-upload-preview" data-upload-preview style="display:none">
                        <img src="" alt="Preview" data-upload-preview-img>
                        <button type="button" class="file-upload-remove" data-upload-remove
                            aria-label="Hapus file">&times;</button>
                    </div>
                </div>

                <label for="create-deadline">Batas Waktu</label>
                <input id="create-deadline" type="date" name="deadline">

                <label for="create-link">Link Pendaftaran Eksternal</label>
                <input id="create-link" type="url" name="registration_link" placeholder="https://...">

                <label for="create-requirements">Syarat Berkas</label>
                <textarea id="create-requirements" name="requirements" rows="5"
                    placeholder="Tulis satu syarat per baris"></textarea>

                <button type="submit" class="btn primary">Simpan Berkas</button>
            </form>
        </div>

        <div class="panel master-list-panel">
            <div class="master-list-heading">
                <div>
                    <h2>Daftar Beasiswa</h2>
                    <p>{{ $documentTypes->count() }} jenis beasiswa tersimpan.</p>
                </div>
            </div>

            @forelse($documentTypes as $type)
                <div class="list-card master-card">
                    <div class="master-card-image">
                        @if($type->image_path)
                            <img src="{{ asset('storage/' . $type->image_path) }}" alt="Logo {{ $type->name }}">
                        @else
                            <div class="master-card-placeholder" aria-label="Belum ada logo">
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
                            <a class="text-link master-link" href="{{ $type->registration_link }}" target="_blank"
                                rel="noopener noreferrer">Buka link pendaftaran</a>
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
                        <form method="POST" action="{{ route('admin.document-types.destroy', $type) }}"
                            data-confirm="Apakah Anda yakin ingin menghapus beasiswa master ini?">
                            @csrf
                            @method('DELETE')
                            <button class="btn danger small" type="submit">Hapus</button>
                        </form>
                        <button class="btn primary small master-edit-button" type="button" data-master-edit="{{ $type->id }}"
                            aria-haspopup="dialog">
                            Edit
                        </button>
                    </div>
                </div>

                @php
                    $isEditingWithErrors = (string) old('editing_id') === (string) $type->id;
                    $editRequirements = $isEditingWithErrors
                        ? collect(old('requirements', []))
                        : $type->requirements->map(fn($requirement) => [
                            'id' => $requirement->id,
                            'name' => $requirement->name,
                        ]);
                @endphp

                <div class="master-modal" id="master-edit-{{ $type->id }}" aria-hidden="true">
                    <button class="master-modal-backdrop" type="button" data-master-close
                        aria-label="Tutup formulir edit"></button>
                    <div class="master-modal-dialog" role="dialog" aria-modal="true"
                        aria-labelledby="master-edit-title-{{ $type->id }}">
                        <div class="master-modal-header">
                            <div>
                                <span class="modal-eyebrow">EDIT BERKAS BEASISWA</span>
                                <h2 id="master-edit-title-{{ $type->id }}">{{ $type->name }}</h2>
                            </div>
                            <button class="master-modal-close" type="button" data-master-close aria-label="Tutup">×</button>
                        </div>

                        <form method="POST" action="{{ route('admin.document-types.update', $type) }}"
                            enctype="multipart/form-data" class="form-stack master-edit-form">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="editing_id" value="{{ $type->id }}">

                            <div class="grid-2">
                                <div>
                                    <label for="edit-name-{{ $type->id }}">Nama Pengajuan</label>
                                    <input id="edit-name-{{ $type->id }}" type="text" name="name"
                                        value="{{ $isEditingWithErrors ? old('name') : $type->name }}" required>
                                </div>
                                <div>
                                    <label for="edit-category-{{ $type->id }}">Kategori</label>
                                    @php $editCategoryValue = $isEditingWithErrors ? old('category') : $type->category; @endphp
                                    <select id="edit-category-{{ $type->id }}" name="category" required>
                                        <option value="">Pilih Kategori</option>
                                        <option value="S1" {{ $editCategoryValue === 'S1' ? 'selected' : '' }}>S1</option>
                                        <option value="S2" {{ $editCategoryValue === 'S2' ? 'selected' : '' }}>S2</option>
                                        <option value="S3" {{ $editCategoryValue === 'S3' ? 'selected' : '' }}>S3</option>
                                        <option value="Prestasi" {{ $editCategoryValue === 'Prestasi' ? 'selected' : '' }}>
                                            Prestasi</option>
                                        <option value="Kepemimpinan" {{ $editCategoryValue === 'Kepemimpinan' ? 'selected' : '' }}>Kepemimpinan</option>
                                        <option value="Riset" {{ $editCategoryValue === 'Riset' ? 'selected' : '' }}>Riset
                                        </option>
                                        <option value="Sosial" {{ $editCategoryValue === 'Sosial' ? 'selected' : '' }}>Sosial
                                        </option>
                                        <option value="Olahraga" {{ $editCategoryValue === 'Olahraga' ? 'selected' : '' }}>
                                            Olahraga</option>
                                    </select>
                                </div>
                            </div>

                            <label for="edit-provider-{{ $type->id }}">Penyelenggara</label>
                            <input id="edit-provider-{{ $type->id }}" type="text" name="provider"
                                value="{{ $isEditingWithErrors ? old('provider') : $type->provider }}">

                            <label for="edit-description-{{ $type->id }}">Deskripsi</label>
                            <textarea id="edit-description-{{ $type->id }}" name="description" rows="5"
                                required>{{ $isEditingWithErrors ? old('description') : $type->description }}</textarea>

                            <div class="master-photo-editor">
                                <div class="master-photo-preview" data-image-preview>
                                    @if($type->image_path)
                                        <img src="{{ asset('storage/' . $type->image_path) }}" alt="Foto {{ $type->name }}">
                                    @else
                                        <span>Belum ada foto</span>
                                    @endif
                                </div>
                                <div class="master-photo-controls">
                                    <label>Ganti Foto</label>
                                    <div class="file-upload-zone file-upload-zone--compact" data-upload-zone>
                                        <input id="edit-image-{{ $type->id }}" type="file" name="image"
                                            accept="image/jpeg,image/png,image/webp" class="file-upload-input" data-upload-input
                                            data-image-input>
                                        <div class="file-upload-content" data-upload-content>
                                            <div class="file-upload-icon file-upload-icon--small">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                                    <polyline points="17 8 12 3 7 8" />
                                                    <line x1="12" y1="3" x2="12" y2="15" />
                                                </svg>
                                            </div>
                                            <p class="file-upload-text">Klik atau seret file</p>
                                            <p class="file-upload-hint">JPG, PNG, WEBP &bull; Maks. 4 MB</p>
                                        </div>
                                        <div class="file-upload-preview" data-upload-preview style="display:none">
                                            <img src="" alt="Preview" data-upload-preview-img>
                                            <button type="button" class="file-upload-remove" data-upload-remove
                                                aria-label="Hapus file">&times;</button>
                                        </div>
                                    </div>
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
                                    <input id="edit-deadline-{{ $type->id }}" type="date" name="deadline"
                                        value="{{ $isEditingWithErrors ? old('deadline') : $type->deadline?->format('Y-m-d') }}">
                                </div>
                                <div>
                                    <label for="edit-link-{{ $type->id }}">Link Pendaftaran</label>
                                    <input id="edit-link-{{ $type->id }}" type="url" name="registration_link"
                                        value="{{ $isEditingWithErrors ? old('registration_link') : $type->registration_link }}"
                                        placeholder="https://...">
                                </div>
                            </div>

                            <div class="requirement-editor-heading">
                                <div>
                                    <label>Syarat Berkas</label>
                                    <small class="field-help">Edit setiap syarat secara terpisah agar data pengajuan lama tetap
                                        aman.</small>
                                </div>
                                <button type="button" class="btn neutral small" data-add-requirement>Tambah Syarat</button>
                            </div>

                            <div class="requirement-editor-list" data-requirements-list>
                                @foreach($editRequirements as $requirementIndex => $requirement)
                                    <div class="requirement-editor-row" data-requirement-row>
                                        <input type="hidden" name="requirements[{{ $requirementIndex }}][id]"
                                            value="{{ $requirement['id'] ?? '' }}">
                                        <input type="text" name="requirements[{{ $requirementIndex }}][name]"
                                            value="{{ $requirement['name'] ?? '' }}" placeholder="Nama syarat berkas"
                                            maxlength="255">
                                        <button type="button" class="btn danger small" data-remove-requirement>Hapus</button>
                                    </div>
                                @endforeach
                            </div>
                            <small class="field-help">Syarat yang sudah digunakan dalam pengajuan mahasiswa dapat diganti
                                namanya, tetapi tidak dapat dihapus.</small>

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
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                    </div>
                    <strong>Belum Ada Berkas Beasiswa</strong>
                    <p>Silakan buat data beasiswa baru menggunakan form di sebelah kiri.</p>
                </div>
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

            /* ── File Upload Zone ── */
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
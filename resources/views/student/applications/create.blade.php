{{--
    Form mahasiswa untuk memilih master beasiswa dan mengunggah dokumen persyaratan.
--}}
@extends('layouts.app')

@section('content')


<div class="page-head-row compact">
    <div>
        <h1>Ajukan Berkas Baru</h1>
        <p>Lengkapi data mahasiswa, pilih jenis pengajuan, lalu masukkan alasan pengajuan.</p>
    </div>
</div>

<div class="form-card">
    @if(session('error'))
        <div class="alert alert-danger" style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 0.9rem;">
            ⚠️ {{ session('error') }}
        </div>
    @endif
    <form method="POST" action="{{ route('student.applications.store') }}" class="form-stack">
        @csrf

        <div class="form-section-title">DATA MAHASISWA</div>
        <div class="grid-2">
            <div>
                <label>NIM</label>
                <input value="{{ auth()->user()->nim }}" disabled>
            </div>
            <div>
                <label>IPK</label>
                <input value="{{ auth()->user()->ipk }}" disabled>
            </div>
        </div>

        <label>Nama Lengkap</label>
        <input value="{{ auth()->user()->name }}" disabled>

        <label>Program Studi</label>
        <input value="{{ auth()->user()->program_studi }}" disabled>

        <label>Kelas</label>
        <input value="{{ auth()->user()->kelas }}" disabled>

        <hr>

        <div class="form-section-title">DETAIL PENGAJUAN</div>
        <label>Jenis Pengajuan</label>
        <select name="document_type_uid" id="documentTypeSelect" required>
            <option value="">Pilih jenis pengajuan</option>
            @foreach($documentTypes as $type)
                @php
                    $requirementsPayload = $type->requirements->map(fn ($requirement) => [
                        'id' => $requirement->id,
                        'name' => $requirement->name,
                        'description' => $requirement->description,
                        'needs_file' => (bool) $requirement->needs_file,
                    ])->values();
                    $isApplied = $appliedTypeIds->contains($type->id);
                @endphp

                <option
                    value="{{ $type->uid }}"
                    data-requirements='@json($requirementsPayload)'
                    {{ $isApplied ? 'disabled' : '' }}
                    {{ !$isApplied && (string) old('document_type_uid', request('type')) === (string) $type->uid ? 'selected' : '' }}
                >
                    {{ $type->name }}{{ $isApplied ? ' — ✓ Sudah Diajukan' : '' }}
                </option>
            @endforeach
        </select>

        @error('document_type_uid')
            <small class="text-danger">{{ $message }}</small>
        @enderror

        <label>Alasan Mengajukan Berkas</label>
        <textarea name="purpose" rows="5" placeholder="Tulis alasan Anda mengajukan berkas..." required>{{ old('purpose') }}</textarea>
        @error('purpose')
            <small class="text-danger">{{ $message }}</small>
        @enderror

        <div id="requirementsBox" class="requirements-upload"></div>

        <div class="form-actions">
            <button type="submit" class="btn primary">Ajukan ke Prodi</button>
            <button type="reset" class="btn neutral">Kosongkan Form</button>
        </div>
    </form>
</div>

<script>
    const select = document.getElementById('documentTypeSelect');
    const box = document.getElementById('requirementsBox');

    function createTextElement(tag, text, className = null) {
        const element = document.createElement(tag);
        element.textContent = text;

        if (className) {
            element.className = className;
        }

        return element;
    }

    function renderRequirements() {
        box.innerHTML = '';

        const selected = select.options[select.selectedIndex];
        if (!selected || !selected.dataset.requirements) {
            return;
        }

        const requirements = JSON.parse(selected.dataset.requirements);
        if (!requirements.length) {
            return;
        }

        box.appendChild(createTextElement('div', 'BERKAS YANG AKAN DIPROSES', 'form-section-title'));

        requirements.forEach((item) => {
            const wrapper = document.createElement('div');
            wrapper.className = 'upload-row';

            const detail = document.createElement('div');
            detail.style.textAlign = 'center';
            detail.style.width = '100%';
            detail.appendChild(createTextElement('strong', item.name));

            wrapper.appendChild(detail);
            box.appendChild(wrapper);
        });
    }

    select.addEventListener('change', renderRequirements);
    renderRequirements();
</script>
@endsection

{{--
    Form mahasiswa untuk memilih master beasiswa dan mengunggah dokumen persyaratan.
--}}
@extends('layouts.app')

@section('content')
<div class="page-head-row compact">
    <div>
        <h1>Ajukan Berkas Baru</h1>
        <p>Lengkapi data mahasiswa, pilih jenis pengajuan, lalu unggah berkas yang dibutuhkan.</p>
    </div>
</div>

<div class="form-card">
    <form method="POST" action="{{ route('student.applications.store') }}" enctype="multipart/form-data" class="form-stack">
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
        <select name="document_type_id" id="documentTypeSelect" required>
            <option value="">Pilih jenis pengajuan</option>
            @foreach($documentTypes as $type)
                @php
                    $requirementsPayload = $type->requirements->map(fn ($requirement) => [
                        'id' => $requirement->id,
                        'name' => $requirement->name,
                        'description' => $requirement->description,
                        'needs_file' => (bool) $requirement->needs_file,
                    ])->values();
                @endphp

                <option
                    value="{{ $type->id }}"
                    data-requirements='@json($requirementsPayload)'
                    {{ (string) old('document_type_id', request('type')) === (string) $type->id ? 'selected' : '' }}
                >
                    {{ $type->name }}
                </option>
            @endforeach
        </select>

        @error('document_type_id')
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

    function createManualCheck(requirementId) {
        const label = document.createElement('label');
        label.className = 'checkbox-line';

        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.name = 'manual_checks[]';
        checkbox.value = requirementId;

        label.appendChild(checkbox);
        label.append(' Centang jika berkas diproses manual tanpa unggah file');

        return label;
    }

    function createFileInput(requirementId, needsFile) {
        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.name = `requirement_files[${requirementId}]`;
        fileInput.accept = '.pdf,.jpg,.jpeg,.png,.doc,.docx';
        fileInput.disabled = !needsFile;

        return fileInput;
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

        box.appendChild(createTextElement('div', 'BERKAS YANG DIBUTUHKAN', 'form-section-title'));

        requirements.forEach((item) => {
            const wrapper = document.createElement('div');
            wrapper.className = 'upload-row';

            const detail = document.createElement('div');
            detail.appendChild(createTextElement('strong', item.name));
      

            wrapper.appendChild(detail);
            box.appendChild(wrapper);
        });
    }

    select.addEventListener('change', renderRequirements);
    renderRequirements();
</script>
@endsection

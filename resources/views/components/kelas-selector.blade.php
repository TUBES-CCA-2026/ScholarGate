@props(['value' => '', 'required' => false, 'isAuth' => false])

@php
    $oldKelas = old('kelas', $value);
    $oldGrup = $oldKelas ? substr($oldKelas, 0, 1) : '';
    $oldIndex = $oldKelas ? substr($oldKelas, 1) : '';
@endphp

@if($isAuth)
<div style="display: flex; gap: 8px; width: 100%; padding-left: 38px; padding-right: 14px;">
    <select id="kelas_group" style="flex: 1; border: none; outline: none; background: transparent; padding: 12px 0; color: inherit; font-size: inherit; font-family: inherit; cursor: pointer;" {{ $required ? 'required' : '' }}>
@else
<div style="display: flex; gap: 8px; width: 100%;">
    <select id="kelas_group" style="flex: 1;" {{ $required ? 'required' : '' }}>
@endif
        <option value="">Grup</option>
        <option value="A" {{ $oldGrup === 'A' ? 'selected' : '' }}>A</option>
        <option value="B" {{ $oldGrup === 'B' ? 'selected' : '' }}>B</option>
        <option value="C" {{ $oldGrup === 'C' ? 'selected' : '' }}>C</option>
    </select>

@if($isAuth)
    <select id="kelas_index" style="flex: 1; border: none; outline: none; background: transparent; padding: 12px 0; color: inherit; font-size: inherit; font-family: inherit; cursor: pointer;" {{ $required ? 'required' : '' }}>
@else
    <select id="kelas_index" style="flex: 1;" {{ $required ? 'required' : '' }}>
@endif
        <option value="">Index</option>
        @if(in_array($oldGrup, ['A', 'B', 'C']))
            @for($i = 1; $i <= 10; $i++)
                <option value="{{ $i }}" {{ $oldIndex == $i ? 'selected' : '' }}>{{ $i }}</option>
            @endfor
        @endif
    </select>
</div>
<input type="hidden" name="kelas" id="kelas" value="{{ $oldKelas }}" {{ $required ? 'required' : '' }}>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const groupSelect = document.getElementById('kelas_group');
        const indexSelect = document.getElementById('kelas_index');
        const hiddenInput = document.getElementById('kelas');

        function updateKelas() {
            const group = groupSelect.value;
            if (group && indexSelect.value) {
                hiddenInput.value = group + indexSelect.value;
            } else {
                hiddenInput.value = '';
            }
        }

        groupSelect.addEventListener('change', function() {
            indexSelect.innerHTML = '<option value="">Index</option>';
            if (this.value) {
                for (let i = 1; i <= 10; i++) {
                    indexSelect.innerHTML += `<option value="${i}">${i}</option>`;
                }
            }
            updateKelas();
        });

        indexSelect.addEventListener('change', updateKelas);
    });
</script>

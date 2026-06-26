@extends('layouts.app')

@section('content')

<h1 class="page-title">Analitik</h1>

<div class="stats-grid">
    <div class="stat-card"><span>Total Pengajuan</span><strong>{{ $summary['total'] }}</strong></div>
    <div class="stat-card"><span>Dikirim</span><strong>{{ $summary['submitted'] }}</strong></div>
    <div class="stat-card"><span>Sedang Direview</span><strong>{{ $summary['in_review'] }}</strong></div>
    <div class="stat-card"><span>Disetujui</span><strong>{{ $summary['approved'] }}</strong></div>
</div>

<div class="panel mt-24">
    <h2>Catatan Progres</h2>
    <p>Menu ini menampilkan ringkasan status dokumen mahasiswa. Persentase kelengkapan dihitung dari jumlah syarat yang sudah diunggah atau dicentang manual.</p>
</div>


<h2>Analitik Beasiswa</h2>

<div class="chart-box">
    <canvas id="chartBeasiswa"></canvas>
</div>


<style>
.chart-box {
    width: 600px;
    height: 300px;
}
</style>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

const namaBeasiswa = @json(
    $beasiswa->map(function($item){
        return $item->documentType->name;
    })
);

const jumlah = @json(
    $beasiswa->pluck('total')
);


new Chart(document.getElementById('chartBeasiswa'), {

    type: 'bar',

    data: {
        labels: namaBeasiswa,

        datasets: [{
            label: 'Jumlah Pendaftar',
            data: jumlah
        }]
    },

    options: {
        responsive: true,
        maintainAspectRatio: false
    }

});

</script>

@endsection

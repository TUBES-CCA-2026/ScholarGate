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

<div class="analytics-charts-grid">
    <div class="analytics-chart-card">
        <div class="analytics-chart-header">
            <h3>Analitik Beasiswa</h3>
            <span class="analytics-chart-subtitle">Jumlah pendaftar per beasiswa</span>
        </div>
        <div class="analytics-chart-body">
            <canvas id="chartBeasiswa"></canvas>
        </div>
    </div>

    <div class="analytics-chart-card">
        <div class="analytics-chart-header">
            <h3>Distribusi Status</h3>
            <span class="analytics-chart-subtitle">Persentase status pengajuan Anda</span>
        </div>
        <div class="analytics-chart-body analytics-chart-body--donut">
            <canvas id="chartStatus"></canvas>
        </div>
    </div>
</div>

<style>
.analytics-charts-grid {
    display: grid;
    grid-template-columns: 1.4fr 1fr;
    gap: 20px;
    margin-top: 24px;
}
.analytics-chart-card {
    background: #fff;
    border: 1px solid #e5e9f0;
    border-radius: 18px;
    padding: 24px;
    box-shadow: 0 4px 24px rgba(15, 23, 42, 0.04);
}
.analytics-chart-header {
    margin-bottom: 20px;
}
.analytics-chart-header h3 {
    margin: 0 0 4px;
    font-size: 17px;
    font-weight: 800;
    color: #0f172a;
}
.analytics-chart-subtitle {
    font-size: 13px;
    color: #94a3b8;
    font-weight: 500;
}
.analytics-chart-body {
    position: relative;
    height: 280px;
}
.analytics-chart-body--donut {
    height: 260px;
    display: flex;
    align-items: center;
    justify-content: center;
}
@media (max-width: 980px) {
    .analytics-charts-grid {
        grid-template-columns: 1fr;
    }
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

// Modern Bar Chart
new Chart(document.getElementById('chartBeasiswa'), {
    type: 'bar',
    data: {
        labels: namaBeasiswa,
        datasets: [{
            label: 'Jumlah Pendaftar',
            data: jumlah,
            backgroundColor: 'rgba(99, 102, 241, 0.15)',
            borderColor: 'rgba(99, 102, 241, 1)',
            borderWidth: 2,
            borderRadius: 10,
            borderSkipped: false,
            hoverBackgroundColor: 'rgba(99, 102, 241, 0.35)',
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#0f172a',
                titleFont: { family: 'Inter, sans-serif', weight: '700', size: 13 },
                bodyFont: { family: 'Inter, sans-serif', size: 12 },
                padding: 12,
                cornerRadius: 10,
                displayColors: false,
            }
        },
        scales: {
            x: {
                grid: { display: false },
                ticks: {
                    font: { family: 'Inter, sans-serif', size: 12, weight: '600' },
                    color: '#64748b',
                    maxRotation: 0,
                },
                border: { display: false },
            },
            y: {
                beginAtZero: true,
                ticks: {
                    font: { family: 'Inter, sans-serif', size: 12 },
                    color: '#94a3b8',
                    stepSize: 1,
                    padding: 8,
                },
                grid: {
                    color: 'rgba(226, 232, 240, 0.6)',
                    drawBorder: false,
                },
                border: { display: false, dash: [4, 4] },
            }
        }
    }
});

// Donut Chart for Status Distribution
const statusData = {
    submitted: {{ $summary['submitted'] }},
    in_review: {{ $summary['in_review'] }},
    approved: {{ $summary['approved'] }},
    other: Math.max(0, {{ $summary['total'] }} - {{ $summary['submitted'] }} - {{ $summary['in_review'] }} - {{ $summary['approved'] }}),
};

const hasData = Object.values(statusData).some(v => v > 0);

new Chart(document.getElementById('chartStatus'), {
    type: 'doughnut',
    data: {
        labels: ['Dikirim', 'Direview', 'Disetujui', 'Lainnya'],
        datasets: [{
            data: hasData
                ? [statusData.submitted, statusData.in_review, statusData.approved, statusData.other]
                : [1],
            backgroundColor: hasData
                ? ['#6366f1', '#f59e0b', '#10b981', '#e2e8f0']
                : ['#f1f5f9'],
            borderWidth: 0,
            hoverOffset: 6,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '72%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    usePointStyle: true,
                    pointStyle: 'circle',
                    padding: 16,
                    font: { family: 'Inter, sans-serif', size: 12, weight: '600' },
                    color: '#475569',
                }
            },
            tooltip: {
                backgroundColor: '#0f172a',
                titleFont: { family: 'Inter, sans-serif', weight: '700', size: 13 },
                bodyFont: { family: 'Inter, sans-serif', size: 12 },
                padding: 12,
                cornerRadius: 10,
                displayColors: true,
                enabled: hasData,
            }
        }
    }
});
</script>

@endsection

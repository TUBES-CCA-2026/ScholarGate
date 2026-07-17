@extends('layouts.app')

@section('content')

<h1 class="page-title">Analitik</h1>

<div class="stats-grid">
    <div class="stat-card"><div class="stat-card-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div><span>Total Pengajuan</span><strong>{{ $summary['total'] }}</strong></div>
    <div class="stat-card"><div class="stat-card-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2L11 13"/><path d="M22 2L15 22 11 13 2 9z"/></svg></div><span>Diajukan</span><strong>{{ $summary['submitted'] }}</strong></div>
    <div class="stat-card"><div class="stat-card-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div><span>Sedang Direview</span><strong>{{ $summary['in_review'] }}</strong></div>
    <div class="stat-card"><div class="stat-card-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div><span>Disetujui</span><strong>{{ $summary['approved'] }}</strong></div>
</div>

<div class="panel mt-24">
    <h2>Catatan Progres</h2>
    <p>Ringkasan jumlah pendaftar per beasiswa. Setiap bar memiliki warna unik sesuai jenis beasiswanya.</p>
</div>

{{-- Full-width chart card with scrollable bar area --}}
<div class="analytics-chart-card mt-24">
    <div class="analytics-chart-header">
        <div>
            <h3>Analitik Beasiswa</h3>
            <span class="analytics-chart-subtitle">Jumlah pendaftar per beasiswa</span>
        </div>
    </div>

    {{-- Scrollable wrapper — chart expands to fit all bars --}}
    <div class="analytics-chart-scroll-wrap">
        <div class="analytics-chart-inner" id="chartWrap">
            <canvas id="chartBeasiswa"></canvas>
        </div>
    </div>

    {{-- Legend below chart --}}
    <div id="chartLegend" class="analytics-legend-row"></div>
</div>

<style>
.mt-24 { margin-top: 24px; }

.analytics-chart-card {
    background: #fff;
    border: 1px solid #e5e9f0;
    border-radius: 18px;
    padding: 24px;
    box-shadow: 0 4px 24px rgba(15, 23, 42, 0.04);
}
.analytics-chart-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
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

/* Scrollable container */
.analytics-chart-scroll-wrap {
    overflow-x: auto;
    overflow-y: hidden;
    border-radius: 12px;
    /* subtle scrollbar */
}
.analytics-chart-scroll-wrap::-webkit-scrollbar {
    height: 5px;
}
.analytics-chart-scroll-wrap::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}
.analytics-chart-scroll-wrap::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

/* Inner div grows with number of bars — 80px per bar, min 100% */
.analytics-chart-inner {
    position: relative;
    height: 300px;
    min-width: 100%;
}

/* Legend row — wraps chips below the chart */
.analytics-legend-row {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 20px;
    padding-top: 16px;
    border-top: 1px solid #e5e9f0;
}
.analytics-legend-chip {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 6px 12px;
    border-radius: 999px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    font-size: 12px;
    font-weight: 700;
    color: #334155;
    white-space: nowrap;
    transition: background 0.15s;
}
.analytics-legend-chip:hover {
    background: #f1f5f9;
}
.analytics-legend-dot {
    width: 10px;
    height: 10px;
    border-radius: 3px;
    flex-shrink: 0;
}
.analytics-legend-count {
    color: #94a3b8;
    font-weight: 600;
}

/* Dark mode */
:root[data-theme="dark"] .analytics-chart-card {
    background: #1e293b;
    border-color: #334155;
    box-shadow: none;
}
:root[data-theme="dark"] .analytics-chart-header h3 {
    color: #f8fafc;
}
:root[data-theme="dark"] .analytics-chart-scroll-wrap::-webkit-scrollbar-track {
    background: #0f172a;
}
:root[data-theme="dark"] .analytics-chart-scroll-wrap::-webkit-scrollbar-thumb {
    background: #475569;
}
:root[data-theme="dark"] .analytics-legend-row {
    border-top-color: #334155;
}
:root[data-theme="dark"] .analytics-legend-chip {
    background: #0f172a;
    border-color: #334155;
    color: #e2e8f0;
}
:root[data-theme="dark"] .analytics-legend-chip:hover {
    background: #1e3a5f;
}
:root[data-theme="dark"] .analytics-legend-count {
    color: #64748b;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const isDarkMode = document.documentElement.getAttribute('data-theme') === 'dark';
const chartGridColor  = isDarkMode ? 'rgba(148,163,184,0.12)' : 'rgba(226,232,240,0.7)';
const chartTickColor  = isDarkMode ? '#94a3b8' : '#64748b';

const namaBeasiswa = @json(
    $beasiswa->map(fn($item) => $item->documentType->name)
);
const jumlah = @json($beasiswa->pluck('total'));

// 10-color vivid palette
const palette = [
    { bg:'rgba(99,102,241,0.80)',  border:'rgba(99,102,241,1)'   },
    { bg:'rgba(20,184,166,0.80)',  border:'rgba(20,184,166,1)'   },
    { bg:'rgba(245,158,11,0.80)',  border:'rgba(245,158,11,1)'   },
    { bg:'rgba(239,68,68,0.80)',   border:'rgba(239,68,68,1)'    },
    { bg:'rgba(16,185,129,0.80)',  border:'rgba(16,185,129,1)'   },
    { bg:'rgba(168,85,247,0.80)',  border:'rgba(168,85,247,1)'   },
    { bg:'rgba(236,72,153,0.80)',  border:'rgba(236,72,153,1)'   },
    { bg:'rgba(59,130,246,0.80)',  border:'rgba(59,130,246,1)'   },
    { bg:'rgba(251,146,60,0.80)',  border:'rgba(251,146,60,1)'   },
    { bg:'rgba(52,211,153,0.80)',  border:'rgba(52,211,153,1)'   },
];

const barBg     = namaBeasiswa.map((_,i) => palette[i % palette.length].bg);
const barBorder = namaBeasiswa.map((_,i) => palette[i % palette.length].border);

// --- Resize inner canvas width: 80px per bar, minimum fills container ---
const MIN_BAR_WIDTH = 80; // px per bar
const wrap    = document.getElementById('chartWrap');
const minW    = Math.max(wrap.parentElement.clientWidth - 48, namaBeasiswa.length * MIN_BAR_WIDTH);
wrap.style.width = minW + 'px';

// --- Build legend chips ---
const legendEl = document.getElementById('chartLegend');
if (namaBeasiswa.length === 0) {
    legendEl.innerHTML = '<p style="color:#94a3b8;font-size:13px;">Belum ada data beasiswa.</p>';
} else {
    namaBeasiswa.forEach((name, i) => {
        const color = palette[i % palette.length].border;
        const chip  = document.createElement('div');
        chip.className = 'analytics-legend-chip';
        chip.innerHTML  = `<div class="analytics-legend-dot" style="background:${color}"></div>
                           <span>${name}</span>
                           <span class="analytics-legend-count">${jumlah[i]}</span>`;
        legendEl.appendChild(chip);
    });
}

// --- Bar Chart ---
new Chart(document.getElementById('chartBeasiswa'), {
    type: 'bar',
    data: {
        labels: namaBeasiswa,
        datasets: [{
            label: 'Pendaftar',
            data: jumlah,
            backgroundColor: barBg,
            borderColor: barBorder,
            hoverBackgroundColor: barBorder,
            borderWidth: 0,
            borderRadius: 12,
            borderSkipped: false,
            barThickness: 44,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: isDarkMode ? '#1e293b' : '#0f172a',
                titleFont : { family:'Inter,sans-serif', weight:'700', size:13 },
                bodyFont  : { family:'Inter,sans-serif', size:12 },
                padding     : 14,
                cornerRadius: 12,
                displayColors: true,
                callbacks: {
                    label: (item) => `  ${item.raw} pendaftar`,
                }
            }
        },
        scales: {
            x: {
                grid : { display: false },
                ticks: {
                    font        : { family:'Inter,sans-serif', size:11, weight:'600' },
                    color       : chartTickColor,
                    maxRotation : 30,
                    minRotation : 0,
                },
                border: { display: false },
            },
            y: {
                beginAtZero: true,
                ticks: {
                    font    : { family:'Inter,sans-serif', size:12 },
                    color   : isDarkMode ? '#64748b' : '#94a3b8',
                    stepSize: 1,
                    padding : 8,
                },
                grid  : { color: chartGridColor },
                border: { display: false, dash: [4,4] },
            }
        }
    }
});
</script>

@endsection

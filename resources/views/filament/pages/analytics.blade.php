@php
    $categories = $projectsByCategory ?? [];
    $byYear = $projectsByYear ?? [];
    $totalProjects = $totalProjects ?? 0;
    $totalCategories = $totalCategories ?? 0;
@endphp
<div class="filament-page-heading">
    <h1 class="filament-page-title">Analytics</h1>
</div>
<div style="display:flex;gap:1.5rem;flex-wrap:wrap;margin-top:1rem">
    <div style="flex:1 1 220px;padding:1rem;border-radius:8px;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,.06)">
        <h3>Total Projects</h3>
        <div style="font-size:2rem;font-weight:700">{{ $totalProjects }}</div>
    </div>
    <div style="flex:1 1 220px;padding:1rem;border-radius:8px;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,.06)">
        <h3>Total Categories</h3>
        <div style="font-size:2rem;font-weight:700">{{ $totalCategories }}</div>
    </div>
    <div style="flex:1 1 220px;padding:1rem;border-radius:8px;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,.06)">
        <h3>Inquiries</h3>
        <div style="font-size:2rem;font-weight:700">{{ $inquiriesCount ?? 0 }}</div>
    </div>
    <div style="flex:1 1 220px;padding:1rem;border-radius:8px;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,.06)">
        <h3>Page views</h3>
        <div style="font-size:2rem;font-weight:700">{{ $pageViewsCount ?? 0 }}</div>
    </div>
    <div style="flex:1 1 220px;padding:1rem;border-radius:8px;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,.06)">
        <h3>Storage</h3>
        <div style="font-size:1rem">Files: {{ $storageFiles ?? 0 }}</div>
        <div style="font-size:1rem">Size: {{ number_format(($storageBytes ?? 0) / 1024 / 1024, 2) }} MB</div>
    </div>
</div>

<div style="display:flex;gap:1.5rem;flex-wrap:wrap;margin-top:1.5rem">
    <div style="flex:1 1 480px;padding:1rem;border-radius:8px;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,.06)">
        <div style="display:flex;justify-content:space-between;align-items:center"><h3>Projects by Category</h3>
            <div>
                <button onclick="downloadChartPNG('chart-categories','projects-by-category.png')">Export PNG</button>
                <button onclick="downloadCSV('categories')">Export CSV</button>
            </div>
        </div>
        <canvas id="chart-categories" style="max-width:100%"></canvas>
    </div>
    <div style="flex:1 1 320px;padding:1rem;border-radius:8px;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,.06)">
        <div style="display:flex;justify-content:space-between;align-items:center"><h3>Projects by Year</h3>
            <div>
                <button onclick="downloadChartPNG('chart-years','projects-by-year.png')">Export PNG</button>
                <button onclick="downloadCSV('years')">Export CSV</button>
            </div>
        </div>
        <canvas id="chart-years" style="max-width:100%"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (function(){
        const categories = @json(array_column($categories, 'name'));
        const catCounts = @json(array_column($categories, 'count'));
        const years = @json(array_column($byYear, 'year'));
        const yearCounts = @json(array_column($byYear, 'count'));

        const ctx1 = document.getElementById('chart-categories');
        let chartCategories;
        if (ctx1) chartCategories = new Chart(ctx1.getContext('2d'), {
            type: 'bar',
            data: { labels: categories, datasets: [{ label: 'Projects', data: catCounts, backgroundColor: 'rgba(54,162,235,0.7)' }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { tooltip: { enabled: true }, legend: { display: false } } }
        });

        const ctx2 = document.getElementById('chart-years');
        let chartYears;
        if (ctx2) chartYears = new Chart(ctx2.getContext('2d'), {
            type: 'line',
            data: { labels: years, datasets: [{ label: 'Projects', data: yearCounts, borderColor: 'rgba(75,192,192,1)', tension: 0.3, fill: true, backgroundColor: 'rgba(75,192,192,0.15)' }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { tooltip: { enabled: true } } }
        });

        window.downloadChartPNG = function(id, filename){
            const c = document.getElementById(id);
            if (!c) return;
            const url = c.toDataURL('image/png');
            const a = document.createElement('a'); a.href = url; a.download = filename; a.click();
        }

        window.downloadCSV = function(type){
            let csv = '';
            if (type === 'categories'){
                csv += 'category,count\n';
                for(let i=0;i<categories.length;i++) csv += `${categories[i]},${catCounts[i]}\n`;
            } else {
                csv += 'year,count\n';
                for(let i=0;i<years.length;i++) csv += `${years[i]},${yearCounts[i]}\n`;
            }
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a'); a.href = url; a.download = (type==='categories'?'projects-by-category.csv':'projects-by-year.csv'); a.click();
            URL.revokeObjectURL(url);
        }
    })();
</script>

<?= $this->extend('layouts/main_tailwind') ?>



<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 md:pt-28 pb-10">
  <!-- Header -->
  <header class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
    <div>
      <h1 class="text-3xl font-bold text-slate-900">Dashboard</h1>
      <p class="mt-2 text-sm text-slate-500">Overview — Local Incident Gathering and Tracking for Aquatic Safety</p>
    </div>

    <div class="flex flex-col sm:flex-row items-center gap-2 sm:gap-3 w-full sm:w-auto">
      <button class="w-full sm:w-auto inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-full shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-300"><?php echo view('components/icon', ['name' => 'plus', 'class' => 'w-4 h-4']); ?> <span>New Incident</span></button>
      <button class="w-full sm:w-auto inline-flex items-center gap-2 px-3 py-2 bg-white border border-slate-100 rounded-full text-slate-700 shadow-sm"><?php echo view('components/icon', ['name' => 'cloud-upload', 'class' => 'w-4 h-4 text-slate-600']); ?> Export</button>
    </div>
  </header>

  <!-- Stats -->
  <section class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-6 shadow-sm">
      <div class="flex items-start justify-between gap-4">
        <div>
          <div class="text-sm text-slate-500">Total Incidents</div>
          <div class="mt-2 text-2xl font-semibold text-slate-900">2,847</div>
          <div class="text-xs text-slate-400 mt-1">All Provinces (2020–2024)</div>
        </div>
        <div class="w-12 h-12 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center text-indigo-600"><?php echo view('components/icon', ['name' => 'chart', 'class' => 'w-5 h-5 text-indigo-600']); ?></div>
      </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-6 shadow-sm">
      <div class="flex items-start justify-between gap-4">
        <div>
          <div class="text-sm text-slate-500">Total Fatalities</div>
          <div class="mt-2 text-2xl font-semibold text-slate-900">1,256</div>
          <div class="text-xs text-slate-400 mt-1">Death Rate: 44.1%</div>
        </div>
        <div class="w-12 h-12 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center text-red-500"><?php echo view('components/icon', ['name' => 'alert', 'class' => 'w-5 h-5 text-red-500']); ?></div>
      </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-6 shadow-sm">
      <div class="flex items-start justify-between gap-4">
        <div>
          <div class="text-sm text-slate-500">Highest Risk Province</div>
          <div class="mt-2 text-2xl font-semibold text-slate-900">Pangasinan</div>
          <div class="text-xs text-slate-400 mt-1">612 incidents (21.5%)</div>
        </div>
        <div class="w-12 h-12 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center text-indigo-600"><?php echo view('components/icon', ['name' => 'map-pin', 'class' => 'w-5 h-5 text-indigo-600']); ?></div>
      </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-6 shadow-sm">
      <div class="flex items-start justify-between gap-4">
        <div>
          <div class="text-sm text-slate-500">Most Affected Age Group</div>
          <div class="mt-2 text-2xl font-semibold text-slate-900">0–14 Years</div>
          <div class="text-xs text-slate-400 mt-1">38.2% of incidents</div>
        </div>
        <div class="w-12 h-12 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center text-indigo-600"><?php echo view('components/icon', ['name' => 'users', 'class' => 'w-5 h-5 text-indigo-600']); ?></div>
      </div>
    </div>
  </section>

  <!-- Charts grid -->
  <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-4 sm:p-6 shadow-sm">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-semibold text-slate-900">Incidents per Province</h3>
        <div class="text-xs text-slate-400">Last 12 months</div>
      </div>
      <div class="h-56 sm:h-72">
        <canvas id="provinceChart"></canvas>
      </div>
    </div>

<div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-6 shadow-sm">
      <h3 class="text-sm font-semibold text-slate-900 mb-4">Remarks Status</h3>
      <div class="h-56 sm:h-72"><canvas id="remarksChart"></canvas></div>
    </div>

    <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-6 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-900 mb-4">Incidents by Sex</h3>
        <div class="h-44 sm:h-56"><canvas id="sexChart"></canvas></div>
      </div>

      <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-6 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-900 mb-4">Incidents by Age Group</h3>
        <div class="h-44 sm:h-56"><canvas id="ageChart"></canvas></div>
      </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-6 shadow-sm">
      <h3 class="text-sm font-semibold text-slate-900 mb-4">Incidents by Year</h3>
      <div class="h-44 sm:h-56"><canvas id="yearChart"></canvas></div>
    </div>

    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-4 sm:p-6 shadow-sm">
      <div class="flex items-center justify-between">
        <h3 class="text-sm font-semibold text-slate-900">Incidents by Residence</h3>
        <div>
          <select id="residenceFilter" onchange="updateResidenceChart()" class="rounded-md border border-slate-100 px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-300">
            <option value="province">Province</option>
            <option value="municipality">Municipality</option>
          </select>
        </div>
      </div>
      <div class="mt-4 h-48 sm:h-64"><canvas id="residenceChart"></canvas></div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-6 shadow-sm">
      <h3 class="text-sm font-semibold text-slate-900 mb-4">Contributing Factors</h3>
      <div class="h-48 sm:h-64"><canvas id="factorsChart"></canvas></div>
    </div>

    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-4 sm:p-6 shadow-sm">
      <h3 class="text-sm font-semibold text-slate-900 mb-4">Incidents by Location Category</h3>
      <div class="h-48 sm:h-64"><canvas id="locationChart"></canvas></div>
    </div>
  </section>
</div>
<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const palette = {
        bg: '#f8fafc',        // slate-50
        cardBorder: '#eef2f7',
        primary: '#4f46e5',   // indigo-600
        muted: '#64748b',     // slate-500
        accentRed: '#ef4444'
    };

    const chartColors = [palette.primary, palette.muted, '#cbd5e1', '#e6edf3', '#94a3b8', '#a78bfa'];
    const accentRed = palette.accentRed;
    const accentYellow = '#f59e0b';
    const accentGreen = '#10b981';

    new Chart(document.getElementById('provinceChart'), {
        type: 'bar',
        data: {
            labels: ['Ilocos Norte', 'Ilocos Sur', 'La Union', 'Pangasinan'],
            datasets: [{
                label: 'Number of Incidents',
                data: [456, 523, 378, 612],
                backgroundColor: chartColors.slice(0, 4),
                borderColor: chartColors.slice(0, 4),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: true } },
            scales: { y: { beginAtZero: true } }
        }
    });

    new Chart(document.getElementById('remarksChart'), {
        type: 'pie',
        data: {
            labels: ['Saved', 'Deceased', 'Missing'],
            datasets: [{
                data: [892, 1256, 118],
                backgroundColor: [accentGreen, accentRed, accentYellow],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });

    new Chart(document.getElementById('sexChart'), {
        type: 'pie',
        data: {
            labels: ['Male', 'Female'],
            datasets: [{
                data: [72.5, 27.5],
                backgroundColor: ['#09637E', accentRed],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });

    new Chart(document.getElementById('ageChart'), {
        type: 'bar',
        data: {
            labels: [
                'Preschool (0-4)',
                'Gradeschool (5-12)',
                'High School (13-17)',
                'College (18-22)',
                'Young Adult (23-35)',
                'Middle Age (36-59)',
                'Senior Citizen (60+)'
            ],
            datasets: [{
                label: 'Percentage (%)',
                data: [8.2, 15.7, 12.3, 14.1, 18.4, 19.2, 12.1], // Example data, adjust as needed
                backgroundColor: accentRed,
                borderColor: accentRed,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: { legend: { display: true } },
            scales: { x: { beginAtZero: true } }
        }
    });

    new Chart(document.getElementById('yearChart'), {
        type: 'line',
        data: {
            labels: ['2020', '2021', '2022', '2023', '2024'],
            datasets: [{
                label: 'Total Incidents',
                data: [512, 598, 625, 702, 410],
                borderColor: accentYellow,
                backgroundColor: 'rgba(214, 180, 67, 0.15)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: true } },
            scales: { y: { beginAtZero: true } }
        }
    });

    new Chart(document.getElementById('occasionChart'), {
        type: 'bar',
        data: {
            labels: ['Christmas', 'New Year', 'Holy Week', 'All Saints Day', 'Independence Day', 'Halloween', 'Summer Break', 'Other Holidays'],
            datasets: [{
                label: 'Number of Incidents',
                data: [523, 487, 412, 298, 234, 189, 645, 159],
                backgroundColor: chartColors,
                borderColor: chartColors,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: true } },
            scales: { y: { beginAtZero: true } }
        }
    });

    let residenceChartInstance;

    const residenceDataByProvince = {
        labels: ['Ilocos Norte', 'Ilocos Sur', 'La Union', 'Pangasinan'],
        data: [456, 523, 378, 612],
        backgroundColor: chartColors.slice(0, 4)
    };

    const residenceDataByMunicipality = {
        labels: ['San Fernando', 'Dagupan', 'Laoag', 'Vigan', 'Alaminos', 'Candon', 'Batac', 'San Juan'],
        data: [234, 198, 167, 189, 145, 123, 156, 134],
        backgroundColor: chartColors
    };

    function updateResidenceChart() {
        const filterValue = document.getElementById('residenceFilter').value;
        const data = filterValue === 'province' ? residenceDataByProvince : residenceDataByMunicipality;

        if (residenceChartInstance) {
            residenceChartInstance.destroy();
        }

        residenceChartInstance = new Chart(document.getElementById('residenceChart'), {
            type: 'doughnut',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.data,
                    backgroundColor: data.backgroundColor,
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    updateResidenceChart();

    new Chart(document.getElementById('factorsChart'), {
        type: 'bar',
        data: {
            labels: ['Unable to Swim', 'Lack of Supervision', 'Intoxication', 'Sudden Illness', 'Water Hazards', 'No Life Jacket'],
            datasets: [{
                label: 'Percentage (%)',
                data: [52.3, 31.2, 18.5, 14.7, 22.1, 28.4],
                backgroundColor: [accentRed, '#09637E', accentYellow, accentGreen, '#0E7EA0', '#0B4F63'],
                borderColor: [accentRed, '#09637E', accentYellow, accentGreen, '#0E7EA0', '#0B4F63'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: { legend: { display: true } },
            scales: { x: { beginAtZero: true } }
        }
    });

    // Incidents by Location Category
    new Chart(document.getElementById('locationChart'), {
        type: 'doughnut',
        data: {
            labels: ['Resort', 'Tourist Spot', 'Beach', 'River', 'Lake', 'Pier/Port', 'Other'],
            datasets: [{
                label: 'Number of Incidents',
                data: [124, 89, 312, 67, 42, 25, 48], // sample data — replace with real numbers from backend when available
                backgroundColor: chartColors.slice(0, 7),
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });
</script>
<?= $this->endSection() ?>

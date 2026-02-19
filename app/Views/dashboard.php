<?= $this->extend('layouts/main_tailwind') ?>



<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 md:pt-10 pb-10">

  <!-- Four summary stat cards -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="w-full bg-white rounded-2xl border border-slate-200 p-4 shadow-sm flex items-center gap-4">
      <div class="w-12 h-12 rounded-md bg-indigo-50 flex items-center justify-center"><?= svg_icon('chart', 'w-6 h-6 text-indigo-600') ?></div>
      <div>
        <div class="text-xs text-slate-400">Total incidents (year)</div>
        <div class="mt-1 text-2xl font-semibold text-slate-900" id="stat-total-incidents">—</div>
        <div class="text-xs text-slate-400">(All provinces)</div>
      </div>
    </div>

    <div class="w-full bg-white rounded-2xl border border-slate-200 p-4 shadow-sm flex items-center gap-4">
      <div class="w-12 h-12 rounded-md bg-rose-50 flex items-center justify-center"><?= svg_icon('alert', 'w-6 h-6 text-rose-600') ?></div>
      <div>
        <div class="text-xs text-slate-400">Total fatalities</div>
        <div class="mt-1 text-2xl font-semibold text-slate-900" id="stat-total-fatalities">—</div>
        <div class="text-xs text-slate-400">(death rate)</div>
      </div>
    </div>

    <div class="w-full bg-white rounded-2xl border border-slate-200 p-4 shadow-sm flex items-center gap-4">
      <div class="w-12 h-12 rounded-md bg-yellow-50 flex items-center justify-center"><?= svg_icon('map-pin', 'w-6 h-6 text-yellow-600') ?></div>
      <div>
        <div class="text-xs text-slate-400">Highest-risk province</div>
        <div class="mt-1 text-2xl font-semibold text-slate-900" id="stat-highest-province">Region 1</div>
        <div class="text-xs text-slate-400">(most incidents)</div>
      </div>
    </div>

    <div class="w-full bg-white rounded-2xl border border-slate-200 p-4 shadow-sm flex items-center gap-4">
      <div class="w-12 h-12 rounded-md bg-green-50 flex items-center justify-center"><?= svg_icon('users', 'w-6 h-6 text-green-600') ?></div>
      <div>
        <div class="text-xs text-slate-400">Most affected age group</div>
        <div class="mt-1 text-2xl font-semibold text-slate-900" id="stat-age-group">15–24</div>
        <div class="text-xs text-slate-400">(range)</div>
      </div>
    </div>
  </div>

  <!-- Main chart area -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Primary chart: Incidents per province -->
    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-semibold text-slate-900">Incidents per province</h3>
        <div class="text-xs text-slate-400">By count</div>
      </div>
      <div id="chart-incidents-province" class="h-72 bg-indigo-50 rounded-md flex items-center justify-center text-slate-400">Chart placeholder</div>
    </div>

    <!-- Side widgets / small charts -->
    <div class="space-y-6">
      <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-6 shadow-sm">
        <div class="flex items-center justify-between mb-3">
          <h3 class="text-sm font-semibold text-slate-900">Remarks status</h3>
          <div class="text-xs text-slate-400">deceased / rescued / missing</div>
        </div>
        <div id="chart-remarks-status" class="h-40 bg-indigo-50 rounded-md flex items-center justify-center text-slate-400">Chart placeholder</div>
      </div>

      <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-6 shadow-sm">
        <div class="flex items-center justify-between mb-3">
          <h3 class="text-sm font-semibold text-slate-900">Incidents by sex</h3>
          <div class="text-xs text-slate-400">Male / Female</div>
        </div>
        <div id="chart-by-sex" class="h-36 bg-indigo-50 rounded-md flex items-center justify-center text-slate-400">Chart placeholder</div>
      </div>

      <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-6 shadow-sm">
        <div class="flex items-center justify-between mb-3">
          <h3 class="text-sm font-semibold text-slate-900">Incidents by age group</h3>
          <div class="text-xs text-slate-400">Group ranges</div>
        </div>
        <div id="chart-by-age" class="h-36 bg-indigo-50 rounded-md flex items-center justify-center text-slate-400">Chart placeholder</div>
      </div>
    </div>
  </div>

  <!-- Additional charts: year, residence, holiday, factors by location -->
  <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
      <h4 class="text-sm font-semibold text-slate-900 mb-3">Incidents by year</h4>
      <div id="chart-by-year" class="h-36 bg-indigo-50 rounded-md flex items-center justify-center text-slate-400">Chart</div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
      <h4 class="text-sm font-semibold text-slate-900 mb-3">By residence (Region 1 municipalities)</h4>
      <div id="chart-by-residence" class="h-36 bg-indigo-50 rounded-md flex items-center justify-center text-slate-400">Chart</div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
      <h4 class="text-sm font-semibold text-slate-900 mb-3">Incidents by holiday</h4>
      <div id="chart-by-holiday" class="h-36 bg-indigo-50 rounded-md flex items-center justify-center text-slate-400">Chart</div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
      <h4 class="text-sm font-semibold text-slate-900 mb-3">Factors by location</h4>
      <div id="chart-factors-location" class="h-36 bg-indigo-50 rounded-md flex items-center justify-center text-slate-400">Chart</div>
    </div>
  </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<!-- ApexCharts (CDN) -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  // -- sample stats (replace with server/API values) --
  const stats = {
    totalIncidents: 3142,
    totalFatalities: 124,
    highestProvince: 'Region 1',
    ageGroup: '15–24'
  };

  document.getElementById('stat-total-incidents').innerText = stats.totalIncidents.toLocaleString();
  document.getElementById('stat-total-fatalities').innerText = stats.totalFatalities + ' (' + ((stats.totalFatalities / stats.totalIncidents) * 100).toFixed(1) + '%)';
  document.getElementById('stat-highest-province').innerText = stats.highestProvince;
  document.getElementById('stat-age-group').innerText = stats.ageGroup;

  // -- Incidents per province (bar) --
  var optIncidentsProvince = {
    chart: { type: 'bar', height: 320, toolbar: { show: false } },
    series: [{ name: 'Incidents', data: [920, 640, 480, 310, 220] }],
    colors: ['#6366F1'],
    xaxis: { categories: ['Region 1','Region 2','Region 3','Region 4','Region 5'] },
    dataLabels: { enabled: false },
    tooltip: { y: { formatter: function (val) { return val; } } }
  };
  new ApexCharts(document.querySelector('#chart-incidents-province'), optIncidentsProvince).render();

  // -- Remarks status (donut) --
  var optRemarks = {
    chart: { type: 'donut', height: 220 },
    series: [124, 2800, 18],
    labels: ['Deceased','Rescued','Missing'],
    colors: ['#FB7185','#60A5FA','#A78BFA'],
    legend: { position: 'bottom' }
  };
  new ApexCharts(document.querySelector('#chart-remarks-status'), optRemarks).render();

  // -- Incidents by sex (donut) --
  var optBySex = {
    chart: { type: 'donut', height: 160 },
    series: [2000, 1142],
    labels: ['Male','Female'],
    colors: ['#374151','#93C5FD'],
    dataLabels: { enabled: false },
    legend: { show: true }
  };
  new ApexCharts(document.querySelector('#chart-by-sex'), optBySex).render();

  // -- Incidents by age group (bar) --
  var optByAge = {
    chart: { type: 'bar', height: 160 },
    series: [{ name: 'Incidents', data: [50, 420, 920, 640, 210, 0] }],
    xaxis: { categories: ['0-4','5-14','15-24','25-44','45-64','65+'] },
    colors: ['#10B981'],
    dataLabels: { enabled: false }
  };
  new ApexCharts(document.querySelector('#chart-by-age'), optByAge).render();

  // -- Incidents by year (line) --
  var optByYear = {
    chart: { type: 'line', height: 140, toolbar: { show: false } },
    series: [{ name: 'Incidents', data: [512, 680, 734, 842, 912, 1100] }],
    stroke: { curve: 'smooth', width: 2 },
    colors: ['#7C3AED'],
    xaxis: { categories: ['2019','2020','2021','2022','2023','2024'] }
  };
  new ApexCharts(document.querySelector('#chart-by-year'), optByYear).render();

  // -- By residence (Region 1 municipalities) --
  var optByResidence = {
    chart: { type: 'bar', height: 140 },
    series: [{ name: 'Incidents', data: [120, 95, 80, 60, 45] }],
    xaxis: { categories: ['Municipality A','Municipality B','Municipality C','Municipality D','Municipality E'] },
    colors: ['#60A5FA'],
    dataLabels: { enabled: false }
  };
  new ApexCharts(document.querySelector('#chart-by-residence'), optByResidence).render();

  // -- Incidents by holiday (pie) --
  var optByHoliday = {
    chart: { type: 'pie', height: 140 },
    series: [2450, 692],
    labels: ['Non-holiday','Holiday'],
    colors: ['#C7D2FE','#FDE68A'],
    legend: { show: false }
  };
  new ApexCharts(document.querySelector('#chart-by-holiday'), optByHoliday).render();

  // -- Factors by location (stacked bar) --
  var optFactorsLocation = {
    chart: { type: 'bar', height: 140, stacked: true },
    series: [
      { name: 'Unable to Swim', data: [120, 40, 30, 10] },
      { name: 'Lack of Supervision', data: [80, 20, 40, 5] },
      { name: 'Intoxication', data: [30, 5, 10, 1] },
      { name: 'Other', data: [20, 10, 5, 2] }
    ],
    xaxis: { categories: ['Beach','Pool','River','Resort'] },
    colors: ['#FB7185','#F59E0B','#34D399','#93C5FD'],
    legend: { position: 'bottom' },
    plotOptions: { bar: { horizontal: false, columnWidth: '60%' } }
  };
  new ApexCharts(document.querySelector('#chart-factors-location'), optFactorsLocation).render();

});
</script>
<?= $this->endSection() ?>

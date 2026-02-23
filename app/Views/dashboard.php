<?= $this->extend('layouts/main_tailwind') ?>



<?= $this->section('content') ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 md:pt-10 pb-10">
  
  <!-- stat cards -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

    <div class="relative rounded-2xl p-4 shadow-sm overflow-hidden bg-gradient-to-br from-sky-50 to-sky-100">
      <div class="absolute top-4 right-4 h-9 w-9 rounded-lg bg-white/70 flex items-center justify-center text-sky-600"><?= svg_icon('files','w-5 h-5') ?></div>
      <div class="h-36 flex flex-col justify-center">
        <div class="text-xs font-medium text-slate-600">Total Incidents (all provinces)</div>
        <div class="mt-2 text-3xl font-bold text-slate-900">1,234</div>
        <div class="text-xs text-sky-600 mt-2">124 this month</div>
      </div>
    </div>

    <div class="relative rounded-2xl p-4 shadow-sm overflow-hidden bg-gradient-to-br from-red-50 to-red-100">
      <div class="absolute top-4 right-4 h-9 w-9 rounded-lg bg-white/70 flex items-center justify-center text-red-600"><?= svg_icon('alert','w-5 h-5') ?></div>
      <div class="h-36 flex flex-col justify-center">
        <div class="text-xs font-medium text-slate-600">Total fatalities</div>
        <div class="mt-2 text-3xl font-bold text-slate-900">27</div>
        <div class="text-xs text-red-600 mt-2">2.2% death rate</div>
      </div>
    </div>

    <div class="relative rounded-2xl p-4 shadow-sm overflow-hidden bg-gradient-to-br from-amber-50 to-amber-100">
      <div class="absolute top-4 right-4 h-9 w-9 rounded-lg bg-white/70 flex items-center justify-center text-amber-600"><?= svg_icon('home','w-5 h-5') ?></div>
      <div class="h-36 flex flex-col justify-center">
        <div class="text-xs font-medium text-slate-600">Highest Risk Province (Region 1)</div>
        <div class="mt-2 text-3xl font-bold text-slate-900">Province A</div>
        <div class="text-xs text-amber-600 mt-2">risk score: 18% (sample)</div>
      </div>
    </div>

    <div class="relative rounded-2xl p-4 shadow-sm overflow-hidden custom-orange-card">
      <div class="absolute top-4 right-4 h-9 w-9 rounded-lg bg-white/70 flex items-center justify-center icon"><?= svg_icon('users','w-5 h-5') ?></div>
      <div class="h-36 flex flex-col justify-center">
        <div class="text-xs font-medium text-slate-600">Most affected age group</div>
        <div class="mt-2 text-3xl font-bold text-slate-900">25–34</div>
        <div class="text-xs text-orange-600 mt-2">28% affected (sample)</div>
      </div>
    </div>

  </div>

  <!-- ROW 1  -->
  <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">

    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
      <h4 class="text-sm font-semibold text-slate-900 mb-3">Incidents by year</h4>
      <div id="chart-incidents-year" class="h-72"></div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
      <h4 class="text-sm font-semibold text-slate-900 mb-3">Incidents per province (Region 1)</h4>
      <div id="chart-incidents-province" class="h-72"></div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
      <h4 class="text-sm font-semibold text-slate-900 mb-3">Incidents by residence (top 10)</h4>
      <div id="chart-incidents-residence" class="h-72"></div>
    </div>

  </div>

  <!-- ROW 2  -->
  <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">

    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
      <h4 class="text-sm font-semibold text-slate-900 mb-3">Contributing factors</h4>
      <div id="chart-contributing-factors" class="h-56"></div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
      <h4 class="text-sm font-semibold text-slate-900 mb-3">Incidents by location category</h4>
      <div id="chart-incidents-location" class="h-56"></div>
    </div>

  </div>

  <!-- ROW 3  -->
  <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
      <h4 class="text-sm font-semibold text-slate-900 mb-3">Incidents by holiday</h4>
      <div id="chart-incidents-holiday" class="h-56"></div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
      <h4 class="text-sm font-semibold text-slate-900 mb-3">Incidents by sex</h4>
      <div id="chart-incidents-sex" class="h-56"></div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
      <h4 class="text-sm font-semibold text-slate-900 mb-3">Incidents by age group</h4>
      <div id="chart-incidents-age" class="h-56"></div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
      <h4 class="text-sm font-semibold text-slate-900 mb-3">Remarks status</h4>
      <div id="chart-remarks-status" class="h-56"></div>
    </div>

  </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
// ApexCharts — (replace nalang ng real data afterwards)
document.addEventListener('DOMContentLoaded', function () {
  // Incidents per province 
  var optionsProvince = {
    chart: { type: 'bar', height: 288, toolbar: { show: false } },
    series: [{ name: 'Incidents', data: [120, 95, 80, 210] }],
    colors: ['#002C76'],
    plotOptions: { bar: { borderRadius: 6, columnWidth: '48%' } },
    dataLabels: { enabled: false },
    xaxis: { categories: ['Ilocos Norte','Ilocos Sur','La Union','Pangasinan'] },
    tooltip: { y: { formatter: function (val) { return val + ' incidents'; } } },
    grid: { strokeDashArray: 4 }
  };
  new ApexCharts(document.querySelector('#chart-incidents-province'), optionsProvince).render();

  // Remarks status (Alive / Deceased / Missing)
  var optionsRemarks = {
    chart: { type: 'donut', height: '100%' },
    series: [520, 27, 5],
    labels: ['Alive','Deceased','Missing'],
    colors: ['#10b981','#C9282D','#FFDE15'],
    legend: { position: 'bottom' },
    dataLabels: { enabled: false }
  };
  new ApexCharts(document.querySelector('#chart-remarks-status'), optionsRemarks).render();

  // Incidents by sex (Male / Female)
  var optionsSex = {
    chart: { type: 'pie', height: '100%' },
    series: [520, 360],
    labels: ['Male','Female'],
    colors: ['#1581BF','#F875AA'],
    dataLabels: { enabled: false },
    legend: { position: 'bottom' }
  };
  new ApexCharts(document.querySelector('#chart-incidents-sex'), optionsSex).render();

  // Incidents by age group
  var optionsAge = {
    chart: { type: 'bar', height: '100%' },
    series: [{ name: 'Incidents', data: [8, 34, 120, 234, 165, 90, 30] }],
    xaxis: { categories: ['0-4','5-14','15-24','25-34','35-44','45-64','65+'] },
    colors: ['#2C4E80'],
    plotOptions: { bar: { borderRadius: 6, columnWidth: '48%' } },
    dataLabels: { enabled: false }
  };
  new ApexCharts(document.querySelector('#chart-incidents-age'), optionsAge).render();

  // Contributing factors 
  var optionsFactors = {
    chart: { type: 'bar', height: '100%' },
    series: [{ name: 'Count', data: [210, 150, 90, 60, 30] }],
    plotOptions: { bar: { horizontal: true, barHeight: '48%', borderRadius: 6 } },
    xaxis: { categories: ['Unable to swim','Lack of supervision','Alcohol','Hazardous conditions','Overcrowding'] },
    colors: ['#002C76'],
    dataLabels: { enabled: false }
  };
  new ApexCharts(document.querySelector('#chart-contributing-factors'), optionsFactors).render();

  // Incidents by year 
  var optionsYear = {
    chart: { type: 'line', height: 288, toolbar: { show: false } },
    series: [{ name: 'Incidents', data: [320, 280, 360, 400, 450, 420] }],
    stroke: { curve: 'smooth', width: 3 },
    markers: { size: 4 },
    colors: ['#002C76'],
    xaxis: { categories: ['2019','2020','2021','2022','2023','2024'] },
    grid: { strokeDashArray: 4 }
  };
  new ApexCharts(document.querySelector('#chart-incidents-year'), optionsYear).render();

  // Incidents by holiday 
  var optionsHoliday = {
    chart: { type: 'bar', height: '100%', toolbar: { show: false } },
    series: [{ name: 'Incidents', data: [150, 110, 95, 160, 80, 70] }],
    plotOptions: { bar: { borderRadius: 6, columnWidth: '48%' } },
    xaxis: { categories: ['Holy Week','Christmas','Summer Vacation','New Year','All Saints Day','Others'] },
    colors: ['#C9282D'],
    dataLabels: { enabled: false }
  };
  new ApexCharts(document.querySelector('#chart-incidents-holiday'), optionsHoliday).render();

  // Incidents by residence (palitan nalang ulit pag meron na - filter top 10 only)
  var optionsResidence = {
    chart: { type: 'bar', height: 288 },
    series: [{ name: 'Incidents', data: [85,80,72,65,60,55,50,45,40,35] }],
    plotOptions: { bar: { horizontal: true, barHeight: '44%', borderRadius: 6 } },
    xaxis: { categories: ['Dagupan','San Carlos','Alaminos','Urdaneta','Laoag','Vigan','Candon','San Fernando (LU)','Agoo','Bacnotan'] },
    colors: ['#06b6d4'],
    dataLabels: { enabled: false }
  };
  new ApexCharts(document.querySelector('#chart-incidents-residence'), optionsResidence).render();

  // Incidents by location category 
  var optionsLocation = {
    chart: { type: 'donut', height: '100%' },
    series: [210,30,120,50,15,40],
    labels: ['Beach','Lake','River','Pool','Swamp','Other'],
    colors: ['#06b6d4','#60a5fa','#3b82f6','#60a5fa','#f97316','#9ca3af'],
    legend: { position: 'bottom' },
    dataLabels: { enabled: false }
  };
  new ApexCharts(document.querySelector('#chart-incidents-location'), optionsLocation).render();

});
</script>
<?= $this->endSection() ?>

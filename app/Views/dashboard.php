<?= $this->extend('layouts/main_tailwind') ?>



<?= $this->section('content') ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 md:pt-10 pb-10">
  <?php $firstName = session()->get('first_name') ?: session()->get('username'); ?>
  <div class="mb-6">
    <div class="bg-white rounded-2xl shadow p-4 sm:p-6 flex flex-wrap items-center justify-between gap-4">
      <h2 class="text-xl font-semibold">Hello, <?= esc($firstName) ?>!</h2>
      <div class="flex flex-wrap items-center gap-3">
        <label for="remarks-filter" class="text-sm font-medium text-slate-600 whitespace-nowrap">Filter by Remarks:</label>
        <select id="remarks-filter" class="text-sm border border-slate-300 rounded-lg px-3 py-1.5 bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 min-w-[170px] cursor-pointer">
          <option value="">All Statuses</option>
        </select>
        <span id="filter-badge" class="hidden text-xs font-semibold bg-blue-100 text-blue-700 px-2.5 py-1 rounded-full"></span>
      </div>
    </div>
  </div>
  <!-- stat cards -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

    <div class="relative rounded-2xl p-4 shadow-sm overflow-hidden bg-gradient-to-br from-sky-50 to-sky-100">
      <div class="absolute top-4 right-4 h-9 w-9 rounded-lg bg-white/70 flex items-center justify-center text-sky-600"><?= svg_icon('files','w-5 h-5') ?></div>
      <div class="h-36 flex flex-col justify-center">
        <div class="text-xs font-medium text-slate-600">Total Incidents (all provinces)</div>
        <div class="mt-2 text-3xl font-bold text-slate-900" id="stat-total-incidents">—</div>
        <div class="text-xs text-sky-600 mt-2" id="stat-this-month">— this month</div>
      </div>
    </div>

    <div class="relative rounded-2xl p-4 shadow-sm overflow-hidden bg-gradient-to-br from-red-50 to-red-100">
      <div class="absolute top-4 right-4 h-9 w-9 rounded-lg bg-white/70 flex items-center justify-center text-red-600"><?= svg_icon('alert','w-5 h-5') ?></div>
      <div class="h-36 flex flex-col justify-center">
        <div class="text-xs font-medium text-slate-600">Total fatalities</div>
        <div class="mt-2 text-3xl font-bold text-slate-900" id="stat-total-fatalities">—</div>
        <div class="text-xs text-red-600 mt-2" id="stat-death-rate">—% death rate</div>
      </div>
    </div>

    <div class="relative rounded-2xl p-4 shadow-sm overflow-hidden bg-gradient-to-br from-amber-50 to-amber-100">
      <div class="absolute top-4 right-4 h-9 w-9 rounded-lg bg-white/70 flex items-center justify-center text-amber-600"><?= svg_icon('home','w-5 h-5') ?></div>
      <div class="h-36 flex flex-col justify-center">
        <div class="text-xs font-medium text-slate-600">Highest Risk Province (Region 1)</div>
        <div class="mt-2 text-3xl font-bold text-slate-900" id="stat-highest-province">—</div>
        <div class="text-xs text-amber-600 mt-2" id="stat-province-info">—% of total incidents</div>
      </div>
    </div>

    <div class="relative rounded-2xl p-4 shadow-sm overflow-hidden" style="background: linear-gradient(135deg, #fed7aa 0%, #fca5a5 100%);">
      <div class="absolute top-4 right-4 h-9 w-9 rounded-lg bg-white/70 flex items-center justify-center icon"><?= svg_icon('users','w-5 h-5') ?></div>
      <div class="h-36 flex flex-col justify-center">
        <div class="text-xs font-medium text-slate-600">Most affected age group</div>
        <div class="mt-2 text-3xl font-bold text-slate-900" id="stat-most-age-group">—</div>
        <div class="text-xs text-orange-600 mt-2" id="stat-most-age-pct">—% affected</div>
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
// Dashboard — all charts sourced from /incident-report/chart-data (approved only).
// The remarks dropdown filters every chart AND all stat cards in real time.
document.addEventListener('DOMContentLoaded', function () {

  var allCharts      = [];
  var dropdownReady  = false;   // populate options only on first successful load

  // ── Resize / scroll: always reference the live allCharts array ──────────
  ['resize', 'scroll'].forEach(function (evt) {
    window.addEventListener(evt, function () {
      allCharts.forEach(function (c) { if (c) c.resize(); });
    });
  });

  // ── Destroy all existing chart instances and clear their containers ─────
  function destroyAllCharts() {
    allCharts.forEach(function (c) { if (c) { try { c.destroy(); } catch (e) {} } });
    allCharts = [];
    [
      'chart-incidents-year', 'chart-incidents-province', 'chart-incidents-residence',
      'chart-contributing-factors', 'chart-incidents-location', 'chart-incidents-holiday',
      'chart-incidents-sex', 'chart-incidents-age', 'chart-remarks-status'
    ].forEach(function (id) {
      var el = document.getElementById(id);
      if (el) el.innerHTML = '';
    });
  }

  // ── Helper: extract parallel label / value arrays ───────────────────────
  function pluck(arr, keyField, valField) {
    return {
      keys: arr.map(function (r) { return r[keyField]; }),
      vals: arr.map(function (r) { return +r[valField]; })
    };
  }

  // ── Populate the dropdown once from the unfiltered by_remarks dataset ───
  function populateDropdown(byRemarks) {
    if (dropdownReady) return;
    var sel = document.getElementById('remarks-filter');
    byRemarks.forEach(function (r) {
      var opt = document.createElement('option');
      opt.value       = r.remarks;
      opt.textContent = r.remarks + ' (' + Number(r.cnt).toLocaleString() + ')';
      sel.appendChild(opt);
    });
    dropdownReady = true;
  }

  // ── Update the active-filter badge ──────────────────────────────────────
  function updateBadge(filter, total) {
    var badge = document.getElementById('filter-badge');
    if (!badge) return;
    if (filter) {
      badge.textContent = 'Showing: ' + filter + ' — ' + Number(total).toLocaleString() + ' incident(s)';
      badge.classList.remove('hidden');
    } else {
      badge.textContent = '';
      badge.classList.add('hidden');
    }
  }

  // ── Render everything from one API response ──────────────────────────────
  function renderDashboard(d) {
    var s      = d.stats;
    var active = d.active_remarks_filter || '';

    // stat cards
    document.getElementById('stat-total-incidents').textContent  = Number(s.total_incidents).toLocaleString();
    document.getElementById('stat-this-month').textContent       = Number(s.this_month).toLocaleString() + ' this month';
    document.getElementById('stat-total-fatalities').textContent = Number(s.total_fatalities).toLocaleString();
    document.getElementById('stat-death-rate').textContent       = s.death_rate + '% death rate';
    document.getElementById('stat-highest-province').textContent = s.highest_risk_province;
    document.getElementById('stat-province-info').textContent    = s.highest_risk_province_pct + '% of total incidents';
    document.getElementById('stat-most-age-group').textContent   = s.most_affected_age_group || '—';
    document.getElementById('stat-most-age-pct').textContent     = s.most_affected_age_pct + '% affected';

    // dropdown (once) + badge
    populateDropdown(d.by_remarks);
    updateBadge(active, s.total_incidents);

    destroyAllCharts();

    // ── Incidents by year (line) ──────────────────────────────────────────
    var yr = pluck(d.by_year, 'yr', 'cnt');
    var chartYear = new ApexCharts(document.querySelector('#chart-incidents-year'), {
      chart:   { type: 'line', height: 288, width: '100%', toolbar: { show: false } },
      series:  [{ name: 'Incidents', data: yr.vals }],
      stroke:  { curve: 'smooth', width: 3 },
      markers: { size: 4 },
      colors:  ['#002C76'],
      xaxis:   { categories: yr.keys },
      grid:    { strokeDashArray: 4 }
    });
    chartYear.render();
    allCharts.push(chartYear);

    // ── Incidents per province (bar) ──────────────────────────────────────
    var prov = pluck(d.by_province, 'province', 'cnt');
    var chartProvince = new ApexCharts(document.querySelector('#chart-incidents-province'), {
      chart:       { type: 'bar', height: 288, width: '100%', toolbar: { show: false } },
      series:      [{ name: 'Incidents', data: prov.vals }],
      colors:      ['#002C76'],
      plotOptions: { bar: { borderRadius: 6, columnWidth: '48%' } },
      dataLabels:  { enabled: false },
      xaxis:       { categories: prov.keys },
      tooltip:     { y: { formatter: function (val) { return val + ' incidents'; } } },
      grid:        { strokeDashArray: 4 }
    });
    chartProvince.render();
    allCharts.push(chartProvince);

    // ── Incidents by residence top-10 (horizontal bar) ───────────────────
    var res = pluck(d.by_residence, 'residence', 'cnt');
    var chartResidence = new ApexCharts(document.querySelector('#chart-incidents-residence'), {
      chart:       { type: 'bar', height: 288, width: '100%' },
      series:      [{ name: 'Incidents', data: res.vals }],
      plotOptions: { bar: { horizontal: true, barHeight: '44%', borderRadius: 6 } },
      xaxis:       { categories: res.keys },
      colors:      ['#06b6d4'],
      dataLabels:  { enabled: false }
    });
    chartResidence.render();
    allCharts.push(chartResidence);

    // ── Contributing factors top-5 (horizontal bar) ──────────────────────
    var fac = pluck(d.by_factors, 'factors', 'cnt');
    var chartFactors = new ApexCharts(document.querySelector('#chart-contributing-factors'), {
      chart:       { type: 'bar', height: 224, width: '100%' },
      series:      [{ name: 'Count', data: fac.vals }],
      plotOptions: { bar: { horizontal: true, barHeight: '48%', borderRadius: 6 } },
      xaxis:       { categories: fac.keys },
      colors:      ['#002C76'],
      dataLabels:  { enabled: false }
    });
    chartFactors.render();
    allCharts.push(chartFactors);

    // ── Incidents by location category (donut) ────────────────────────────
    var loc = pluck(d.by_location, 'location_category', 'cnt');
    if (loc.vals.length > 0) {
      var chartLocation = new ApexCharts(document.querySelector('#chart-incidents-location'), {
        chart:      { type: 'donut', height: 224, width: '100%' },
        series:     loc.vals,
        labels:     loc.keys,
        colors:     ['#06b6d4','#60a5fa','#3b82f6','#a78bfa','#f97316','#9ca3af'],
        legend:     { position: 'bottom' },
        dataLabels: { enabled: false }
      });
      chartLocation.render();
      allCharts.push(chartLocation);
    }

    // ── Incidents by occasion / holiday (bar) ────────────────────────────
    var occ = pluck(d.by_occasion, 'occasion', 'cnt');
    var chartHoliday = new ApexCharts(document.querySelector('#chart-incidents-holiday'), {
      chart:       { type: 'bar', height: 224, width: '100%', toolbar: { show: false } },
      series:      [{ name: 'Incidents', data: occ.vals }],
      plotOptions: { bar: { borderRadius: 6, columnWidth: '48%' } },
      xaxis:       { categories: occ.keys },
      colors:      ['#C9282D'],
      dataLabels:  { enabled: false }
    });
    chartHoliday.render();
    allCharts.push(chartHoliday);

    // ── Incidents by sex (pie) ────────────────────────────────────────────
    var sexMap = {};
    d.by_sex.forEach(function (r) {
      var lbl = r.gender === 'm' ? 'Male' : r.gender === 'f' ? 'Female' : (r.gender || 'Unknown');
      sexMap[lbl] = (sexMap[lbl] || 0) + (+r.cnt);
    });
    var sexLabels = Object.keys(sexMap);
    var sexVals   = sexLabels.map(function (k) { return sexMap[k]; });
    if (sexVals.length > 0) {
      var chartSex = new ApexCharts(document.querySelector('#chart-incidents-sex'), {
        chart:      { type: 'pie', height: 288, width: '100%' },
        series:     sexVals,
        labels:     sexLabels,
        colors:     ['#1581BF','#F875AA','#9ca3af'],
        dataLabels: { enabled: false },
        legend:     { position: 'bottom' }
      });
      chartSex.render();
      allCharts.push(chartSex);
    }

    // ── Incidents by age group (bar) ─────────────────────────────────────
    var age = pluck(d.by_age_group, 'age_group', 'cnt');
    var chartAge = new ApexCharts(document.querySelector('#chart-incidents-age'), {
      chart:       { type: 'bar', height: 288, width: '100%' },
      series:      [{ name: 'Incidents', data: age.vals }],
      xaxis:       { categories: age.keys },
      colors:      ['#2C4E80'],
      plotOptions: { bar: { borderRadius: 6, columnWidth: '48%' } },
      dataLabels:  { enabled: false }
    });
    chartAge.render();
    allCharts.push(chartAge);

    // ── Remarks status donut — always unfiltered distribution ────────────
    // This chart is intentionally NOT filtered so it always shows the full
    // breakdown and acts as a reference even when a filter is active.
    var rem = pluck(d.by_remarks, 'remarks', 'cnt');
    if (rem.vals.length > 0) {
      var chartRemarks = new ApexCharts(document.querySelector('#chart-remarks-status'), {
        chart:      { type: 'donut', height: 288, width: '100%' },
        series:     rem.vals,
        labels:     rem.keys,
        colors:     ['#C9282D','#10b981','#FFDE15','#9ca3af','#a78bfa'],
        legend:     { position: 'bottom' },
        dataLabels: { enabled: false }
      });
      chartRemarks.render();
      allCharts.push(chartRemarks);
    }
  }

  // ── Fetch data and render ────────────────────────────────────────────────
  function loadData(remarksFilter) {
    var url = '/incident-report/chart-data';
    if (remarksFilter) url += '?remarks=' + encodeURIComponent(remarksFilter);
    fetch(url)
      .then(function (r) { return r.json(); })
      .then(function (d) { renderDashboard(d); })
      .catch(function (err) { console.error('Dashboard chart load failed:', err); });
  }

  // Initial load (no filter)
  loadData('');

  // Dropdown change → refetch with selected remarks value
  document.getElementById('remarks-filter').addEventListener('change', function () {
    loadData(this.value);
  });

});
</script>
<?= $this->endSection() ?>

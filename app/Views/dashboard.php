<?= $this->extend('layouts/main_tailwind') ?>



<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 md:pt-10 pb-10">

  <!-- Top stats removed by request -->

  <!-- Monthly Sales summary (no stats) -->
  <div class="grid grid-cols-1 gap-6">
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-semibold text-slate-900">Monthly Sales (summary)</h3>
        <div class="text-xs text-slate-400">Data shown as table (charts removed)</div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-slate-600">
          <thead>
            <tr class="text-xs text-slate-400 uppercase"><th class="py-2">Month</th><th class="py-2">Sales</th></tr>
          </thead>
          <tbody>
            <tr><td class="py-2">Jan</td><td class="py-2">$420</td></tr>
            <tr><td class="py-2">Feb</td><td class="py-2">$520</td></tr>
            <tr class="bg-slate-50"><td class="py-2 font-semibold">Mar</td><td class="py-2 font-semibold text-indigo-600">$620</td></tr>
            <tr><td class="py-2">Apr</td><td class="py-2">$480</td></tr>
            <tr><td class="py-2">May</td><td class="py-2">$590</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- keep original lower charts (incidents, remarks, by-age, etc.) -->
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
<script>
// Charts removed by request — no chart libraries are loaded on this page.
// Keep this script area for small DOM updates or future data wiring.
document.addEventListener('DOMContentLoaded', function () {
  // placeholder: dashboard running in "charts removed" mode
});
</script>
<?= $this->endSection() ?>

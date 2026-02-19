<?= $this->extend('layouts/main_tailwind') ?>



<?= $this->section('content') ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 md:pt-28 pb-10">
  <!-- Header wrapper for system title -->
  <div class="bg-white rounded-2xl border border-slate-100 p-4 mb-6 flex items-center justify-between">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold">L</div>
      <div>
        <div class="text-sm text-slate-500">Welcome to</div>
        <div class="text-lg font-semibold text-slate-900">LIGTAS Admin</div>
      </div>
    </div>
    <div class="text-sm text-slate-400">Last updated: <?= date('M d, Y') ?></div>
  </div>

  <!-- Top stat cards (TailAdmin style) -->
  <div class="mb-8">
    <div class="overflow-x-auto -mx-4 px-4 sm:mx-0 sm:px-0">
      <div class="flex gap-6 min-w-max sm:grid sm:grid-cols-4">
        <div class="w-56 shrink-0 sm:w-auto bg-white rounded-2xl border border-slate-200 p-4 sm:p-6 shadow-sm relative">
          <div class="absolute top-4 right-4 w-10 h-10 bg-slate-50 border border-slate-100 rounded-md flex items-center justify-center"><?= svg_icon('home', 'w-5 h-5 text-indigo-600') ?></div>
          <div class="pt-4">
            <div class="text-sm text-slate-500">Total Visitors</div>
            <div class="mt-2 text-2xl font-semibold text-slate-900">4,564</div>
            <div class="text-xs text-slate-400 mt-1">Today</div>
          </div>
          <div class="mt-4 text-sm text-indigo-600 font-medium">View data →</div>
        </div>

        <div class="w-56 shrink-0 sm:w-auto bg-white rounded-2xl border border-slate-200 p-4 sm:p-6 shadow-sm relative">
          <div class="absolute top-4 right-4 w-10 h-10 bg-slate-50 border border-slate-100 rounded-md flex items-center justify-center"><?= svg_icon('cloud-upload', 'w-5 h-5 text-slate-600') ?></div>
          <div class="pt-4">
            <div class="text-sm text-slate-500">Revenue</div>
            <div class="mt-2 text-2xl font-semibold text-slate-900">$7,564</div>
            <div class="text-xs text-slate-400 mt-1">Monthly</div>
          </div>
          <div class="mt-4 text-sm text-indigo-600 font-medium">View data →</div>
        </div>

        <div class="w-56 shrink-0 sm:w-auto bg-white rounded-2xl border border-slate-200 p-4 sm:p-6 shadow-sm relative">
          <div class="absolute top-4 right-4 w-10 h-10 bg-slate-50 border border-slate-100 rounded-md flex items-center justify-center"><?= svg_icon('users', 'w-5 h-5 text-indigo-600') ?></div>
          <div class="pt-4">
            <div class="text-sm text-slate-500">Orders</div>
            <div class="mt-2 text-2xl font-semibold text-slate-900">7,891+</div>
            <div class="text-xs text-slate-400 mt-1">All time</div>
          </div>
          <div class="mt-4 text-sm text-indigo-600 font-medium">View data →</div>
        </div>

        <div class="w-56 shrink-0 sm:w-auto bg-white rounded-2xl border border-slate-200 p-4 sm:p-6 shadow-sm relative">
          <div class="absolute top-4 right-4 w-10 h-10 bg-slate-50 border border-slate-100 rounded-md flex items-center justify-center"><?= svg_icon('files', 'w-5 h-5 text-indigo-600') ?></div>
          <div class="pt-4">
            <div class="text-sm text-slate-500">Items</div>
            <div class="mt-2 text-2xl font-semibold text-slate-900">486</div>
            <div class="text-xs text-slate-400 mt-1">In inventory</div>
          </div>
          <div class="mt-4 text-sm text-indigo-600 font-medium">View data →</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Main content (no graphs) -->
  <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-semibold text-slate-900">Statistics</h3>
        <div class="text-xs text-slate-400">Overview</div>
      </div>

      <div class="mt-6 grid grid-cols-3 gap-3 h-48 items-end">
        <div class="h-32 bg-indigo-50 rounded-md flex items-end justify-center"><div class="w-8 h-3 bg-indigo-600 rounded-t"></div></div>
        <div class="h-40 bg-indigo-50 rounded-md flex items-end justify-center"><div class="w-8 h-10 bg-indigo-600 rounded-t"></div></div>
        <div class="h-28 bg-indigo-50 rounded-md flex items-end justify-center"><div class="w-8 h-6 bg-indigo-600 rounded-t"></div></div>
      </div>

      <div class="mt-6 grid grid-cols-3 gap-4 text-sm text-slate-600">
        <div>
          <div class="text-xs text-slate-400">Target</div>
          <div class="font-semibold text-slate-900 mt-1">$7.8k</div>
        </div>
        <div>
          <div class="text-xs text-slate-400">Last Week</div>
          <div class="font-semibold text-slate-900 mt-1">$1.4k</div>
        </div>
        <div>
          <div class="text-xs text-slate-400">Open Campaigns</div>
          <div class="font-semibold text-slate-900 mt-1">17</div>
        </div>
      </div>
    </div>

    <!-- Right column — static widgets -->
    <div class="space-y-6">
      <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-6 shadow-sm">
        <div class="flex items-center justify-between mb-3">
          <h3 class="text-sm font-semibold text-slate-900">Stock</h3>
          <div class="text-xs text-slate-400">Today</div>
        </div>
        <div class="flex items-center gap-4">
          <div class="w-24 h-24 rounded-full bg-gradient-to-br from-indigo-50 to-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xl">45</div>
          <div class="flex-1">
            <div class="text-sm text-slate-500">Total sales made today</div>
            <div class="mt-3 grid grid-cols-2 gap-2 text-sm text-slate-600">
              <div>Target <div class="font-semibold">$7.8k</div></div>
              <div>Last week <div class="font-semibold">$1.4k</div></div>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-6 shadow-sm">
        <div class="flex items-center justify-between mb-3">
          <h3 class="text-sm font-semibold text-slate-900">Total Revenue</h3>
          <div class="text-xs text-slate-400">Monthly</div>
        </div>
        <div class="text-2xl font-bold text-slate-900">$7,841.12</div>
        <div class="mt-2 text-sm text-slate-500">17 Open Campaign</div>
      </div>
    </div>
  </section>

  <!-- Secondary info grid -->
  <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-6 shadow-sm">
      <h4 class="text-sm font-semibold text-slate-900">Incidents by Year</h4>
      <div class="mt-4 grid grid-cols-2 gap-3 text-sm text-slate-700">
        <div>2024 <div class="text-lg font-bold">410</div></div>
        <div>2023 <div class="text-lg font-bold">702</div></div>
        <div>2022 <div class="text-lg font-bold">625</div></div>
        <div>2021 <div class="text-lg font-bold">598</div></div>
      </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-6 shadow-sm">
      <h4 class="text-sm font-semibold text-slate-900">Contributing Factors</h4>
      <ul class="mt-4 space-y-2 text-sm text-slate-600">
        <li>Unable to Swim — 52.3%</li>
        <li>Lack of Supervision — 31.2%</li>
        <li>Intoxication — 18.5%</li>
      </ul>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-6 shadow-sm">
      <h4 class="text-sm font-semibold text-slate-900">Incidents by Location</h4>
      <ul class="mt-4 space-y-2 text-sm text-slate-600">
        <li>Beach — 312</li>
        <li>Resort — 124</li>
        <li>River — 67</li>
      </ul>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script>
  // Dashboard intentionally has no graphs — scripts removed.
</script>
<?= $this->endSection() ?>

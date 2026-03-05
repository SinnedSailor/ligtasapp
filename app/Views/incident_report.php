<?= $this->extend('layouts/main_tailwind') ?>
<?php $hasInitialRows = !empty($initialRows); ?>

<?= $this->section('pageStyles'); ?>
<style>
    .file-name {
        color: #6c757d;
        font-size: 0.9rem;
        margin-left: 10px;
    }

    .table-input {
        width: 100%;
        min-width: 120px;
        padding: 6px 8px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        font-size: 0.85rem;
    }

    /* sortable column headers */
    .sortable {
        cursor: pointer;
        user-select: none;
    }

    .sort-arrow {
        margin-left: 4px;
        font-size: 0.75rem;
    }

    .empty-message {
        text-align: center;
        padding: 40px;
        color: #999;
        font-size: 0.95rem;
    }

    .table-responsive {
        overflow-x: auto;
        overflow-y: visible;
    }

    .table-responsive table {
        width: max-content;
        min-width: 100%;
        table-layout: auto;
    }

    /* apply to any table used in this view so cells default to centered */
table th,
    table td {
        text-align: center;
        white-space: normal;
        overflow: visible;
        text-overflow: unset;
        word-break: normal;
        overflow-wrap: break-word;
        vertical-align: top;
        line-height: 1.35;
        hyphens: auto;
    }

    .table-responsive th {
        font-weight: 600;
        line-height: 1.2;
        padding-top: 12px;
        padding-bottom: 12px;
    }

    .table-responsive table {
        border-collapse: collapse;
    }

    .table-responsive th,
    .table-responsive td {
        border: 1px solid #e3e6ef;
        /* ensure data cells have same padding as header cells (px-6 py-3) */
        padding: 0.75rem 1.5rem;
    }

    /* Attachment area improvements */
    #incidentUploadFileListPictures, #incidentUploadFileListDocuments {
        max-height: 260px;
        overflow-y: auto;
        padding-right: 6px;
    }

    #incidentUploadFileList {
        margin-top: 8px;
    }

    #incidentUploadFileList .upload-file-item {
        background: transparent;
        padding: 6px 4px;
        border-radius: 6px;
    }

    #incidentUploadFileList .upload-file-item .progress {
        height: 6px;
    }

    #incidentUploadFileList .upload-file-item .small {
        font-size: 0.75rem;
    }

    /* Make thumbnail icons tidy in attachment viewer */
    #attachmentList .list-group-item img { border-radius:4px; }
    #attachmentList .list-group-item { gap: 12px; }

    @media (max-width: 991.98px) {
        .col-tertiary {
            display: none;
        }
    }

    @media (max-width: 767.98px) {
        .col-secondary {
            display: none;
        }
    }

    @media (max-width: 575.98px) {
        .col-primary {
            display: none;
        }
    }

    .pagination-controls {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
        margin-top: 12px;
    }

    .pagination-controls .page-info {
        color: #6c757d;
        font-size: 0.9rem;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-pending {
        background: rgba(245, 158, 11, 0.15);
        color: #b45309;
    }

    .status-approved {
        background: rgba(16, 185, 129, 0.15);
        color: #047857;
    }

    .status-rejected {
        background: rgba(220, 38, 38, 0.12);
        color: #b91c1c;
    }

    html.modal-open,
    body.modal-open {
        height: 100%;
        overflow: hidden;
    }

    body.modal-open .page-body-wrapper,
    body.modal-open .main-panel {
        height: 100%;
        overflow: hidden;
    }

    /* ensure modal inner content can scroll when it becomes too tall */
    #incidentModal > div {
        max-height: 90vh;
        overflow-y: auto;
    }

    /* always leave some space at bottom of page content */
    .content-wrapper {
        padding-bottom: 0.5rem !important;
    }
</style>
    <!-- CSRF token for AJAX -->
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
<?php
    $roleName = $roleName ?? (session()->get('role_name') ?? '');
    $isLgu = strtoupper(trim((string) $roleName)) === 'LGU';
    $isProvince = strtoupper(trim((string) $roleName)) === 'PROVINCE';
    $isFocal = strtoupper(trim((string) $roleName)) === 'FOCAL';
    $isAdmin = $isAdmin ?? (bool) session()->get('is_admin');
    $canReview = $isProvince || $isAdmin;
    $provinceList = $provinces ?? [];
?>
<div class="px-4 sm:px-6 lg:px-8 mt-6 pb-8">
    <div class="w-full mb-8">
        <!-- Upload Container (with enforced bottom margin) -->
        <div class="bg-white rounded-2xl shadow p-6" style="margin-bottom:2rem;">
            <h4 class="text-lg font-semibold">Incident Report</h4>
            <div class="flex justify-between items-center flex-wrap gap-3 mt-4 mb-4">
                <div class="flex items-center gap-3">
                    <input type="file" id="excelFile" accept=".xlsx,.xls,.csv" class="hidden" />
                    <button id="importButton" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-full text-sm hover:bg-blue-700 active:bg-blue-800" onclick="document.getElementById('excelFile').click()" style="<?= $hasInitialRows ? 'display:none;' : '' ?>">
                        <?= svg_icon('cloud-upload', 'w-4 h-4 mr-2') ?> Import Excel File
                    </button>
                    <span class="text-gray-500 text-sm file-name" id="fileName" style="<?= $hasInitialRows ? 'display:none;' : '' ?>">No file selected</span>
                </div>
                <div class="flex flex-wrap gap-2">
                    <?php if ($isLgu || $isAdmin): ?>
                        <button class="inline-flex items-center px-3 py-1.5 text-white rounded-full text-sm" style="background-color:#1C4D8D;" onclick="openIncidentModal()"
                                onmouseover="this.style.backgroundColor='#163c6c'" onmouseout="this.style.backgroundColor='#1C4D8D'" onmousedown="this.style.backgroundColor='#0f2a4e'" onmouseup="this.style.backgroundColor='#163c6c'">
                            <?= svg_icon('plus','w-4 h-4 mr-2') ?> Add Incident
                        </button>
                    <?php endif; ?>
                    <button id="saveButton" class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white rounded-full text-sm hover:bg-green-700 active:bg-green-800" onclick="openSaveModal()" style="<?= $hasInitialRows ? 'display:none;' : '' ?>">
                        <?= svg_icon('check','w-4 h-4 mr-2') ?> Save to Database
                    </button>
                    <button id="generateReportButton" class="inline-flex items-center px-3 py-1.5 text-white rounded-full text-sm" style="background-color:#0065F8;" onmouseover="this.style.backgroundColor='#0053C5'" onmouseout="this.style.backgroundColor='#0065F8'" onmousedown="this.style.backgroundColor='#0040A1'" onmouseup="this.style.backgroundColor='#0053C5'" onclick="downloadIncidentReport()">
                        <?= svg_icon('chart','w-4 h-4 mr-2') ?> Generate Report
                    </button>
                </div>
            </div>
            <?php if ($isLgu): ?>
                <div class="mb-4">
                    <div class="bg-blue-50 border border-blue-200 text-blue-800 rounded-md p-3 text-sm">Upload at least one photo or document per incident. Attachments are reviewed by Province users.</div>
                </div>
            <?php endif; ?>
        </div>
        <!-- Table Container -->
        <div class="bg-white rounded-2xl shadow p-6" style="margin-top:2rem; margin-bottom:2rem;">
            <!-- negative horizontal margins cancel the card padding so the table can reach the rounded corners -->
        <div class="overflow-x-auto -mx-6 px-6 pb-6">
                <!-- ensure table spans its container so the right edge aligns with the scroll wrapper -->
                <table class="min-w-full w-full table-auto divide-y divide-gray-200 rounded-2xl overflow-hidden mb-6">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-center align-middle text-xs font-medium sortable" data-col="N" onclick="setSort('N')" style="background:#002c76;color:#fff;min-width:60px;white-space:normal;word-break:break-word;">N<span class="sort-arrow"></span></th>
                            <th class="px-6 py-3 text-center align-middle text-xs font-medium sortable" data-col="Month of Incident" onclick="setSort('Month of Incident')" style="background:#002c76;color:#fff;min-width:120px;white-space:normal;word-break:break-word;">MONTH OF INCIDENT<span class="sort-arrow"></span></th>
                            <th class="px-6 py-3 text-center align-middle text-xs font-medium sortable" data-col="Year of Incident" onclick="setSort('Year of Incident')" style="background:#002c76;color:#fff;min-width:120px;white-space:normal;word-break:break-word;">YEAR OF INCIDENT<span class="sort-arrow"></span></th>
                            <th class="px-6 py-3 text-center align-middle text-xs font-medium sortable" data-col="Province" onclick="setSort('Province')" style="background:#002c76;color:#fff;min-width:120px;white-space:normal;word-break:break-word;">PROVINCE<span class="sort-arrow"></span></th>
                            <th class="px-6 py-3 text-center align-middle text-xs font-medium sortable" data-col="Municipality/City where Incidence Occurred" onclick="setSort('Municipality/City where Incidence Occurred')" style="background:#002c76;color:#fff;min-width:160px;white-space:normal;word-break:break-word;">MUNICIPALITY/CITY<span class="sort-arrow"></span></th>
                            <th class="px-6 py-3 text-center align-middle text-xs font-medium sortable" data-col="Name of Victim" onclick="setSort('Name of Victim')" style="background:#002c76;color:#fff;min-width:160px;white-space:normal;word-break:break-word;">NAME OF VICTIM<span class="sort-arrow"></span></th>
                            <th class="px-6 py-3 text-center align-middle text-xs font-medium sortable" data-col="Location Category" onclick="setSort('Location Category')" style="background:#002c76;color:#fff;min-width:120px;white-space:normal;word-break:break-word;">LOCATION CATEGORY<span class="sort-arrow"></span></th>
                            <th class="px-6 py-3 text-center align-middle text-xs font-medium sortable" data-col="Age of the Person" onclick="setSort('Age of the Person')" style="background:#002c76;color:#fff;min-width:100px;white-space:normal;word-break:break-word;">AGE<span class="sort-arrow"></span></th>
                            <th class="px-6 py-3 text-center align-middle text-xs font-medium sortable" data-col="Gender of the Person" onclick="setSort('Gender of the Person')" style="background:#002c76;color:#fff;min-width:80px;white-space:normal;word-break:break-word;">SEX<span class="sort-arrow"></span></th>
                            <th class="px-6 py-3 text-center align-middle text-xs font-medium sortable" data-col="Occasion" onclick="setSort('Occasion')" style="background:#002c76;color:#fff;min-width:180px;white-space:normal;word-break:break-word;">OCCASION<span class="sort-arrow"></span></th>
                            <th class="px-6 py-3 text-center align-middle text-xs font-medium sortable" data-col="Other Factors" onclick="setSort('Other Factors')" style="background:#002c76;color:#fff;min-width:140px;white-space:normal;word-break:break-word;">OTHER FACTORS<span class="sort-arrow"></span></th>
                            <th class="px-6 py-3 text-left align-middle text-xs font-medium sortable" data-col="Person's Residence" onclick="setSort(\"Person's Residence\")" style="background:#002c76;color:#fff;min-width:140px;white-space:normal;word-break:break-word;">PERSON'S RESIDENCE<span class="sort-arrow"></span></th>
                            <th class="px-6 py-3 text-center align-middle text-xs font-medium sortable" data-col="Occupation of the Victim" onclick="setSort('Occupation of the Victim')" style="background:#002c76;color:#fff;min-width:140px;white-space:normal;word-break:break-word;">OCCUPATION OF THE VICTIM<span class="sort-arrow"></span></th>
                            <th class="px-6 py-3 text-center align-middle text-xs font-medium sortable" data-col="Remarks" onclick="setSort('Remarks')" style="background:#002c76;color:#fff;min-width:100px;white-space:normal;word-break:break-word;">REMARKS<span class="sort-arrow"></span></th>
                            <th class="px-6 py-3 text-center text-xs font-medium" style="background:#002c76;color:#fff;min-width:100px;white-space:normal;word-break:break-word;">ATTACHMENTS</th>
                            <?php if (! $isFocal): ?>
                                <th class="px-6 py-3 text-center text-xs font-medium" style="background:#002c76;color:#fff;min-width:100px;white-space:normal;word-break:break-word;">Review</th>
                                <th class="px-6 py-3 text-center text-xs font-medium" style="background:#002c76;color:#fff;min-width:100px;white-space:normal;word-break:break-word;">Actions</th>
                            <?php endif; ?>
                        </tr>
                        <tr>
                            <th class="px-6 py-3 text-center text-xs text-gray-400">&nbsp;</th>
                            <th class="px-6 py-3 text-center align-middle text-xs text-gray-400">(Use numerical representation, e.g.: 1 for January, 12 for December)</th>
                            <th class="px-6 py-3 text-center align-middle text-xs text-gray-400">(Input full year, e.g.: 2025)</th>
                            <th class="px-6 py-3 text-center align-middle text-xs text-gray-400">Province name</th>
                            <th class="px-6 py-3 text-center align-middle text-xs text-gray-400">Municipality/City</th>
                            <th class="px-6 py-3 text-center text-xs text-gray-400">Last Name<br>First Name<br>Middle Name</th>
                            <th class="px-6 py-3 text-center text-xs text-gray-400">(e.g.: Resort, Tourist Spot, Beach, River)</th>
                            <th class="px-6 py-3 text-center text-xs text-gray-400">(Input whole number, e.g.: 25)</th>
                            <th class="px-6 py-3 text-center text-xs text-gray-400">(Sex assigned at birth)</th>
                            <th class="px-6 py-3 text-center text-xs text-gray-400">Choose between Summer Vacation, Holy Week, Halloween, Holiday Season, Disaster-Related,<br><span style='color:red'>Regular Days (Family Gathering, Outing/Picnic, etc), Work-Related</span></th>
                            <th class="px-6 py-3 text-center text-xs text-gray-400">(e.g.: Accident, Alcohol Intoxication, <span style='color:red'>Medical Condition</span>)</th>
                            <th class="px-6 py-3 text-center text-xs text-gray-400">(From what part does the case belong to? e.g.: Bakun, Benguet)<br>Region</th>
                            <th class="px-6 py-3 text-center text-xs text-gray-400">(e.g.: Student, Fisherfolk, Farmer, etc.)</th>
                            <th class="px-6 py-3 text-center text-xs text-gray-400">&nbsp;</th>
                            <?php if (! $isFocal): ?>
                                <th class="px-6 py-3 text-center text-xs text-gray-400">&nbsp;</th>
                                <th class="px-6 py-3 text-center text-xs text-gray-400">&nbsp;</th>
                                <th class="px-6 py-3 text-center text-xs text-gray-400">&nbsp;</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="bg-white divide-y divide-gray-100">
                        <tr>
                            <td colspan="<?= $isFocal ? 13 : 15 ?>" class="px-4 py-6 text-center text-sm text-gray-400 empty-message rounded-b-2xl">No data yet. Upload an Excel file or click "Add Incident" to add data.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="pagination-controls mt-4 hidden" id="paginationControls">
                <button id="prevPage" type="button" class="px-3 py-1.5 border border-gray-300 rounded-md text-sm">Prev</button>
                <button id="nextPage" type="button" class="px-3 py-1.5 border border-gray-300 rounded-md text-sm">Next</button>
                <span class="page-info" id="pageInfo"></span>
                <div class="ml-auto flex items-center gap-2">
                    <label class="page-info" for="pageSize">Rows per page</label>
                    <select id="pageSize" class="border border-gray-300 rounded-md text-sm px-2 py-1 bg-white" style="width:auto;">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="20" selected>20</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <!-- bottom spacer to ensure visible gap when scrolling -->
    <div class="h-4"></div>

<div id="saveConfirmModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/40 w-screen h-screen" aria-hidden="true">
  <div class="bg-white rounded-2xl shadow-lg max-w-md w-full p-6 mx-4">
    <div class="flex items-start justify-between mb-4">
      <h3 class="text-lg font-semibold">Confirm Save</h3>
      <button type="button" class="text-slate-400 hover:text-slate-600" onclick="hideModal('saveConfirmModal')" aria-label="Close">&times;</button>
    </div>
    <div class="text-sm text-slate-600 mb-6">This will save the current imported rows into the database. Continue?</div>
    <div class="flex justify-end gap-3">
      <button type="button" class="inline-flex items-center px-3 py-1.5 bg-gray-300 text-gray-700 rounded-full hover:bg-gray-400 active:bg-gray-500" onclick="hideModal('saveConfirmModal')"><?= svg_icon('x','w-4 h-4 mr-2') ?>Cancel</button>
      <button type="button" class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white rounded-full hover:bg-green-700 active:bg-green-800" onclick="confirmSaveToDatabase()"><?= svg_icon('check','w-4 h-4 mr-2') ?>Yes, Save</button>
    </div>
  </div>
</div>

<div id="importSuccessModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/40 w-screen h-screen" aria-hidden="true">
  <div class="bg-white rounded-2xl shadow-lg max-w-md w-full p-6 mx-4">
    <div class="flex items-start justify-between mb-4">
      <h3 class="text-lg font-semibold">Import Complete</h3>
      <button type="button" class="text-slate-400 hover:text-slate-600" onclick="hideModal('importSuccessModal')" aria-label="Close">&times;</button>
    </div>
    <div class="text-sm text-slate-700 mb-6"><div id="importSuccessMessage">Import completed.</div></div>
    <div class="flex justify-end">
      <button type="button" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-full hover:bg-blue-700 active:bg-blue-800" onclick="hideModal('importSuccessModal')"><?= svg_icon('check','w-4 h-4 mr-2') ?>OK</button>
    </div>
  </div>
</div>

<div id="saveResultModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/40 w-screen h-screen" aria-hidden="true">
  <div class="bg-white rounded-2xl shadow-lg max-w-md w-full p-6 mx-4">
    <div class="flex items-start justify-between mb-4">
      <h3 class="text-lg font-semibold">Save Result</h3>
      <button type="button" class="text-slate-400 hover:text-slate-600" onclick="hideModal('saveResultModal')" aria-label="Close">&times;</button>
    </div>
    <div class="text-sm text-slate-700 mb-6"><div id="saveResultMessage">Save completed.</div></div>
    <div class="flex justify-end">
      <button type="button" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-full hover:bg-blue-700 active:bg-blue-800" onclick="hideModal('saveResultModal')"><?= svg_icon('check','w-4 h-4 mr-2') ?>OK</button>
    </div>
  </div>
</div>

<!-- Attachment notice modal (Tailwind) -->
<div id="attachmentModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/40 w-screen h-screen" aria-hidden="true">
  <div class="bg-white rounded-2xl shadow-lg max-w-md w-full p-4 mx-4">
    <div class="flex items-center justify-between mb-3">
      <h5 class="text-lg font-semibold">Upload Notice</h5>
      <button type="button" class="text-slate-400 hover:text-slate-600" onclick="hideModal('attachmentModal')" aria-label="Close">&times;</button>
    </div>
    <div class="text-sm text-slate-700 mb-4"><div id="attachmentModalMessage">Please upload at least one file.</div></div>
    <div class="flex justify-end">
      <button type="button" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-full hover:bg-blue-700 active:bg-blue-800" onclick="hideModal('attachmentModal')"><?= svg_icon('check','w-4 h-4 mr-2') ?>OK</button>


<!-- Review confirm modal (Tailwind) -->
<div id="reviewConfirmModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/40 w-screen h-screen" aria-hidden="true">
  <div class="bg-white rounded-2xl shadow-lg max-w-md w-full p-4 mx-4">
    <div class="flex items-center justify-between mb-3">
      <h5 class="text-lg font-semibold">Confirm Review</h5>
      <button type="button" class="text-slate-400 hover:text-slate-600" onclick="hideModal('reviewConfirmModal')" aria-label="Close">&times;</button>
    </div>
    <div class="text-sm text-slate-700 mb-4"><div id="reviewConfirmMessage">Are you sure you want to update this incident?</div></div>
    <div class="flex justify-end gap-2">
      <button type="button" class="inline-flex items-center px-3 py-1.5 bg-gray-300 text-gray-700 rounded-full hover:bg-gray-400 active:bg-gray-500" onclick="hideModal('reviewConfirmModal')"><?= svg_icon('x','w-4 h-4 mr-2') ?>Cancel</button>
      <button type="button" id="reviewConfirmAction" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-full hover:bg-blue-700 active:bg-blue-800" onclick="submitReview()"><?= svg_icon('check','w-4 h-4 mr-2') ?>Confirm</button>
    </div>
  </div>
</div>

<div id="incidentModal" class="modal-overlay fixed inset-0 z-50 hidden flex items-center justify-center bg-black/40 w-screen h-screen" aria-hidden="true">
  <div class="bg-white rounded-2xl shadow-lg max-w-4xl w-full p-6 mx-4 max-h-[90vh] overflow-y-auto">
    <div class="flex items-start justify-between mb-4">
      <h3 class="text-lg font-semibold">Add Incident</h3>
      <button type="button" class="text-slate-400 hover:text-slate-600" onclick="hideModal('incidentModal')" aria-label="Close">&times;</button>
    </div>

    <form id="incidentForm">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div class="col-span-1">
          <label for="incidentMonth" class="text-sm text-slate-600 block mb-1">Month of Incident</label>
          <select id="incidentMonth" class="block w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
            <option value="" disabled selected class="text-slate-400">Select month</option>
            <?php for ($month = 1; $month <= 12; $month++): ?>
              <option value="<?= $month ?>"><?= $month ?></option>
            <?php endfor; ?>
          </select>
        </div>

        <div class="col-span-1">
          <label for="incidentYear" class="text-sm text-slate-600 block mb-1">Year of Incident</label>
          <select id="incidentYear" class="block w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
            <option value="" disabled selected class="text-slate-400">Select year</option>
            <?php for ($y = date('Y'); $y >= 2000; $y--): ?>
              <option value="<?= $y ?>"><?= $y ?></option>
            <?php endfor; ?>
          </select>
        </div>

        <!-- the province/municipality inputs were previously removed; bring them back -->
        <div class="col-span-1">
          <label for="incidentProvince" class="text-sm text-slate-600 block mb-1">Province</label>
          <select id="incidentProvince" class="block w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
            <option value="" disabled selected class="text-slate-400">Select province</option>
            <?php foreach ($provinceList as $prov): ?>
              <option value="<?= esc($prov) ?>"><?= esc($prov) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-span-1">
          <label for="incidentMunicipality" class="text-sm text-slate-600 block mb-1">Municipality/City where Incident Occurred</label>
          <select id="incidentMunicipality" class="block w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
            <option value="" disabled selected class="text-slate-400">Select municipality</option>
          </select>
        </div>

        <div class="md:col-span-1">
          <label for="incidentVictim" class="text-sm text-slate-600 block mb-1">Name of Victim</label>
          <input type="text" id="incidentVictim" class="block w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-300" />
        </div>

        <div class="col-span-1">
          <label for="incidentLocation" class="text-sm text-slate-600 block mb-1">Location Category</label>
          <!-- using datalist combo: suggestions from existing data, but free text allowed -->
          <input list="locationCategoryList" type="text" id="incidentLocation" class="block w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-300" />
          <datalist id="locationCategoryList"></datalist>
        </div>

        <div class="col-span-1">
          <label for="incidentAge" class="text-sm text-slate-600 block mb-1">Age of the Person</label>
          <input type="text" id="incidentAge" class="block w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-300" />
        </div>

        <div class="col-span-1">
          <label for="incidentGender" class="text-sm text-slate-600 block mb-1">Sex</label>
          <select id="incidentGender" class="block w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
            <option value="" disabled selected class="text-slate-400">Select sex</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
          </select>
        </div>

        <div class="md:col-span-1">
          <label for="incidentOccasion" class="text-sm text-slate-600 block mb-1">Occasion</label>
          <input list="occasionList" type="text" id="incidentOccasion" class="block w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-300" />
          <datalist id="occasionList"></datalist>
        </div>

        <div class="md:col-span-1">
          <label for="incidentFactors" class="text-sm text-slate-600 block mb-1">Other Factors</label>
          <input list="factorsList" type="text" id="incidentFactors" class="block w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-300" />
          <datalist id="factorsList"></datalist>
        </div>

        <div class="md:col-span-1">
          <label for="incidentResidence" class="text-sm text-slate-600 block mb-1">Person's Residence</label>
          <input type="text" id="incidentResidence" class="block w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-300" />
        </div>

        <div class="md:col-span-1">
          <label for="incidentOccupation" class="text-sm text-slate-600 block mb-1">Occupation of the Victim</label>
          <input list="occupationList" type="text" id="incidentOccupation" class="block w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-300" />
          <datalist id="occupationList"></datalist>
        </div>

        <div class="md:col-span-3">
          <label for="incidentRemarks" class="text-sm text-slate-600 block mb-1">Remarks</label>
          <select id="incidentRemarks" class="block w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
            <option value="">Select remarks status</option>
            <option value="Alive">Alive</option>
            <option value="Deceased">Deceased</option>
            <option value="Missing">Missing</option>
          </select>
        </div>
      </div>

      <?php if ($isLgu || $isAdmin): ?>
        <div class="border-t pt-3 mt-3" id="incidentAttachmentSection">
          <div class="flex items-center justify-between mb-2">
            <h6 class="mb-0">Attachments</h6>
            <span class="text-gray-500 text-sm" id="incidentAttachmentStatus"></span>
          </div>
          <div class="text-gray-500 text-sm" id="incidentAttachmentHint"></div>
          <div class="flex flex-wrap gap-4 mt-2 items-end">
            <div class="flex flex-col w-full md:w-auto">
                <label for="incidentPicturesInput" class="text-sm text-slate-600 mb-1">Photos</label>
                <!-- only button area clickable: inline-block + max width -->
                <input type="file" id="incidentPicturesInput" class="inline-block w-auto max-w-xs text-sm text-slate-700" accept=".jpg,.jpeg,.png" multiple />
                <!-- list for queued pictures -->
                <div id="incidentUploadFileListPictures" class="mt-1"></div>
            </div>
            <div class="flex flex-col w-full md:w-auto">
                <label for="incidentDocumentsInput" class="text-sm text-slate-600 mb-1">Documents</label>
                <input type="file" id="incidentDocumentsInput" class="inline-block w-auto max-w-xs text-sm text-slate-700" accept=".pdf" multiple />
                <div id="incidentUploadFileListDocuments" class="mt-1"></div>
            </div>
            <button type="button" id="incidentUploadButton" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white rounded-full text-sm hover:bg-indigo-700 active:bg-indigo-800" onclick="uploadIncidentAttachments()"><?= svg_icon('cloud-upload','w-4 h-4 mr-2') ?>Upload Attachments</button>
          </div>
        </div>
      <?php endif; ?>

      <!-- modal footer with save/cancel -->
      <div class="mt-4 flex justify-end gap-2">
        <!-- start disabled; JS will enable when form is valid -->
        <button type="button" id="incidentSaveButton" disabled
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 opacity-50 cursor-not-allowed">
          Add Incident
        </button>
        <button type="button" class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-700 rounded-full hover:bg-gray-400" onclick="hideModal('incidentModal')"><?= svg_icon('x','w-4 h-4 mr-2') ?>Cancel</button>
      </div>
    </form>
  </div>
</div> 

<!-- Attachment viewer modal (Tailwind) -->
<div id="attachmentViewerModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/40 w-screen h-screen" aria-hidden="true">
  <div class="bg-white rounded-2xl shadow-lg max-w-4xl w-full p-4 mx-4">
    <div class="flex items-center justify-between mb-3">
      <h5 class="text-lg font-semibold">Incident Attachments</h5>
      <button type="button" class="text-slate-400 hover:text-slate-600" onclick="hideModal('attachmentViewerModal')" aria-label="Close">&times;</button>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div id="attachmentList" class="space-y-2 md:col-span-1"></div>
      <div id="attachmentPreview" class="md:col-span-2 border rounded-2xl p-4 flex items-center justify-center" style="min-height:320px; background: #f8fafc;">
        <span class="text-gray-500">Select a file to preview.</span>
      </div>
    </div>
    <div class="flex justify-between items-center gap-2 mt-4">
      <a id="attachmentDownload" href="#" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-600 text-white rounded-full hover:bg-blue-700 active:bg-blue-800"><?= svg_icon('download','w-4 h-4 mr-2') ?>Download</a>
      <div class="flex justify-end gap-2">
        <button type="button" class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full" onclick="hideModal('attachmentViewerModal'); closeAttachmentViewer && closeAttachmentViewer()"><?= svg_icon('x','w-4 h-4 mr-2') ?>Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Excel Error Modal -->
<div id="excelErrorModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40" aria-hidden="true">
  <div class="bg-white rounded-2xl shadow-lg max-w-md w-full p-6 mx-4">
    <div class="flex items-start justify-between mb-4">
      <h3 class="text-lg font-semibold">Excel Import Error</h3>
      <button type="button" class="text-slate-400 hover:text-slate-600" onclick="hideModal('excelErrorModal')" aria-label="Close">&times;</button>
    </div>
    <div class="text-sm text-slate-700 mb-6"><div id="excelErrorMessage">Error reading file.</div></div>
    <div class="flex justify-end">
      <button type="button" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-full hover:bg-blue-700 active:bg-blue-800" onclick="hideModal('excelErrorModal')"><?= svg_icon('check','w-4 h-4 mr-2') ?>OK</button>
    </div>
  </div>
</div>

</div>
<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script src="https://cdn.sheetjs.com/xlsx-0.18.5/package/dist/xlsx.full.min.js"></script>
<script>
    let tableData = [];
    const importUrl = "<?= base_url('/incident-report/import') ?>";
    const attachmentUploadUrl = "<?= base_url('/incident-report/attachments/upload') ?>";
    const tempAttachmentUploadUrl = "<?= base_url('/incident-report/attachments/upload-temp') ?>";
    const tempAttachmentRemoveUrl = "<?= base_url('/incident-report/attachments/remove-temp') ?>";
    const attachmentListUrl = "<?= base_url('/incident-report/attachments') ?>";
    const attachmentViewUrl = "<?= base_url('/incident-report/attachments/view') ?>";
    const attachmentPreviewDataUrl = "<?= base_url('/incident-report/attachments/preview') ?>";
    const attachmentDownloadUrl = "<?= base_url('/incident-report/attachments/download') ?>";

    /* Helper: fetch attachment as image blob (credentials: same-origin) and validate content-type.
       This avoids broken <img> placeholders when the direct image request fails (ensures cookies/credentials are sent
       and lets us show a friendly fallback). */
    async function fetchAttachmentImageBlob(url) {
        const res = await fetch(url, { credentials: 'same-origin' });
        console.debug('[fetchAttachmentImageBlob] response', url, 'status=', res.status, 'content-type=', res.headers.get('content-type'));
        if (!res.ok) throw new Error('Fetch failed: ' + res.status);
        const ct = res.headers.get('content-type') || '';
        if (!ct.startsWith('image/')) throw new Error('Not an image (content-type=' + ct + ')');
        const blob = await res.blob();
        console.debug('[fetchAttachmentImageBlob] blob size:', blob.size, 'for', url);
        return blob;
    }

    // save the vertical scroll offset when the user leaves or reloads the page
    window.addEventListener('beforeunload', () => {
        sessionStorage.setItem('incidentScrollY', window.scrollY);
        sessionStorage.setItem('incidentCurrentPage', currentPage);
    });

    async function fetchAttachmentPreviewJson(attachmentId) {
        const res = await fetch(`${attachmentPreviewDataUrl}/${attachmentId}`, { credentials: 'same-origin' });
        // 202 means conversion queued - surface that to the caller so it can poll/notify
        if (res.status === 202) {
            const j = await res.json().catch(() => null) || { converting: true };
            return j;
        }

        if (!res.ok) {
            const txt = await res.text().catch(() => null);
            throw new Error('Preview API failed: ' + res.status + (txt ? ' - ' + txt.slice(0, 300) : ''));
        }

        const json = await res.json();
        return json;
    }

    // Polling helper: repeatedly call preview API until content is ready or attempts exhausted
    async function pollPreview(attachmentId, onReady, onPending, attemptsLeft = 6, delayMs = 2500) {
        try {
            const json = await fetchAttachmentPreviewJson(attachmentId);
            if (json && json.converting) {
                if (typeof onPending === 'function') onPending(json);
                if (attemptsLeft > 0) {
                    setTimeout(() => pollPreview(attachmentId, onReady, onPending, attemptsLeft - 1, delayMs), delayMs);
                }
                return;
            }

            if (json && json.data) {
                if (typeof onReady === 'function') onReady(json);
                return;
            }

            throw new Error('Preview API returned unexpected payload.');
        } catch (err) {
            if (attemptsLeft > 0) {
                // transient error — retry
                setTimeout(() => pollPreview(attachmentId, onReady, onPending, attemptsLeft - 1, delayMs), delayMs);
                return;
            }
            if (typeof onPending === 'function') onPending({ error: err });
        }
    }
    const approveUrl = "<?= base_url('/incident-report/approve') ?>";
    const rejectUrl = "<?= base_url('/incident-report/reject') ?>";
    const canUploadAttachments = <?= $isLgu ? 'true' : 'false' ?>;
    const canReviewIncidents = <?= $canReview ? 'true' : 'false' ?>;
    const isFocal = <?= $isFocal ? 'true' : 'false' ?>;
    const municipalities = <?= json_encode($municipalities ?? []) ?>;
    // categories loaded from server for initial suggestions
    const initialLocationCategories = <?= json_encode($locationCategories ?? []) ?>;
    const initialOccasions = <?= json_encode($occasions ?? []) ?>;
    const initialOccupations = <?= json_encode($occupations ?? []) ?>;
    const initialFactors = <?= json_encode($otherFactors ?? []) ?>;
    const columns = [
        'N',
        'Month of Incident',
        'Year of Incident',
        'Province',
        'Municipality/City where Incidence Occurred',
        'Name of Victim',
        'Location Category',
        'Age of the Person',
        'Gender of the Person',
        'Occasion',
        'Other Factors',
        "Person's Residence",
        'Occupation of the Victim',
        'Remarks'
    ];
    // sorting state
    let sortColumn = null;
    let sortDirection = 'asc';

    function setSort(col) {
        if (sortColumn === col) {
            // toggle direction
            sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            sortColumn = col;
            sortDirection = 'asc';
        }
        // update arrow indicators
        document.querySelectorAll('th.sortable').forEach(th => {
            const arrow = th.querySelector('.sort-arrow');
            if (!arrow) return;
            if (th.dataset.col === sortColumn) {
                arrow.textContent = sortDirection === 'asc' ? '▲' : '▼';
            } else {
                arrow.textContent = '';
            }
        });
        renderTable();
    }
    //icons
    const iconCheck   = `<?= svg_icon('check', 'w-4 h-4') ?>`;
    const iconX       = `<?= svg_icon('x', 'w-4 h-4') ?>`;
    const iconPencil  = `<?= svg_icon('pencil', 'w-4 h-4') ?>`;
    const iconTrash   = `<?= svg_icon('trash', 'w-4 h-4') ?>`;
    const iconEye     = `<?= svg_icon('eye', 'w-4 h-4 mr-2') ?>`;
    const editableColumns = columns.filter(col => col !== 'N');
    const columnAliases = {
        'Year': 'Year of Incident',
        'Municipality': 'Municipality/City where Incidence Occurred',
        'Municipality/City': 'Municipality/City where Incidence Occurred',
        'Municipality/City where Incident Occurred': 'Municipality/City where Incidence Occurred',
        'Age': 'Age of the Person',
        'Gender': 'Gender of the Person',
        'Sex': 'Gender of the Person', // Excel files often use "Sex" header
        'Factors': 'Other Factors',
        'Occupation': 'Occupation of the Victim',
        // support split name columns commonly used in templates
        'Last Name': 'Name of Victim',
        'First Name': 'Name of Victim',
        'Middle Name': 'Name of Victim'
    };
    const normalizedHeaderMap = buildNormalizedHeaderMap();
    const serverRows = <?= json_encode($initialRows ?? []) ?>;
    const importButton = document.getElementById('importButton');

    // apply default sort once the DOM is ready
    // show incidents in natural numeric order by default; users can click other
    // column headers to re-sort.  Formerly the table defaulted to sorting by
    // year which caused the N column to look "out of order" when viewing
    // mixed years.
    document.addEventListener('DOMContentLoaded', () => {
        // restore pagination page and scroll from prior visit if available
        const lastPage = sessionStorage.getItem('incidentCurrentPage');
        if (lastPage !== null) {
            currentPage = parseInt(lastPage, 10) || 1;
        }

        setSort('N');

        const lastPos = sessionStorage.getItem('incidentScrollY');
        if (lastPos !== null) {
            window.scrollTo(0, parseInt(lastPos, 10) || 0);
        }
    });
    const saveButton = document.getElementById('saveButton');
    const fileNameLabel = document.getElementById('fileName');
    const paginationControls = document.getElementById('paginationControls');
    const prevPageButton = document.getElementById('prevPage');
    const nextPageButton = document.getElementById('nextPage');
    const pageInfoLabel = document.getElementById('pageInfo');
    const pageSizeSelect = document.getElementById('pageSize');
    const incidentModalLabel = document.getElementById('incidentModalLabel');
    const incidentSaveButton = document.getElementById('incidentSaveButton');
    const incidentAttachmentHint = document.getElementById('incidentAttachmentHint');
    const incidentAttachmentStatus = document.getElementById('incidentAttachmentStatus');
    const incidentPicturesInput = document.getElementById('incidentPicturesInput');
    const incidentDocumentsInput = document.getElementById('incidentDocumentsInput');
    const incidentUploadButton = document.getElementById('incidentUploadButton');
    const incidentAgeInput = document.getElementById('incidentAge');
    if (incidentAgeInput) {
        // Prevent non-digit keystrokes, sanitize input and block non-numeric pastes
        incidentAgeInput.addEventListener('keydown', (e) => {
            // allow control/meta keys and navigation
            const controlKeys = ['Backspace','Tab','ArrowLeft','ArrowRight','Delete','Home','End'];
            if (e.ctrlKey || e.metaKey || controlKeys.includes(e.key)) return;
            // allow digits only
            if (!/^\d$/.test(e.key)) e.preventDefault();
        });
        incidentAgeInput.addEventListener('input', (e) => {
            const el = e.target;
            // strip non-digits
            let v = String(el.value || '').replace(/\D+/g, '');
            if (v === '') { el.value = ''; return; }
            // clamp to 0-120
            let n = parseInt(v, 10);
            if (n > 120) n = 120;
            el.value = String(n);
        });
        incidentAgeInput.addEventListener('paste', (e) => {
            const pasted = (e.clipboardData || window.clipboardData).getData('text') || '';
            if (!/^\d+$/.test(pasted)) {
                e.preventDefault();
            }
        });
    }

    let currentPage = 1;
    let pageSize = 20;
    let currentIncidentIndex = null;
    // Per-file upload state for attachments modal
    let currentUploadFiles = [];
    // Session token for attachments uploaded before the incident is saved
    let currentAttachmentSession = '';


    const incidentFieldMap = [
        { id: 'incidentMonth', column: 'Month of Incident' },
        { id: 'incidentYear', column: 'Year of Incident' },
        { id: 'incidentProvince', column: 'Province' },
        { id: 'incidentMunicipality', column: 'Municipality/City where Incidence Occurred' },
        { id: 'incidentVictim', column: 'Name of Victim' },
        { id: 'incidentLocation', column: 'Location Category' },
        { id: 'incidentAge', column: 'Age of the Person' },
        { id: 'incidentGender', column: 'Gender of the Person' },
        { id: 'incidentOccasion', column: 'Occasion' },
        { id: 'incidentFactors', column: 'Other Factors' },
        { id: 'incidentResidence', column: "Person's Residence" },
        { id: 'incidentOccupation', column: 'Occupation of the Victim' },
        { id: 'incidentRemarks', column: 'Remarks' },
    ];

    // Modal stacking & scroll-lock for the app's custom modals (replaces legacy Bootstrap modal events)
    (function() {
        function refreshModalStack() {
            const modals = Array.from(document.querySelectorAll('.modal-overlay.active, .modal.show'));
            modals.forEach((modal, idx) => {
                const z = 1050 + idx * 20;
                modal.style.zIndex = z;
                // try to find a backdrop element if present and adjust it
                const backdrop = modal.querySelector('.modal-backdrop') || document.querySelector('.modal-backdrop.show');
                if (backdrop) backdrop.style.zIndex = z - 10;
            });
            // enable/disable scroll lock depending on open modals
            setScrollLock(modals.length > 0);
        }

        // Observe class changes on modal containers so stacking updates when modals are toggled
        const containers = Array.from(document.querySelectorAll('.modal-overlay, .modal'));
        if (containers.length > 0) {
            const mo = new MutationObserver(() => refreshModalStack());
            containers.forEach(el => mo.observe(el, { attributes: true, attributeFilter: ['class'] }));
        }

        // ensure correct stacking on initial load
        document.addEventListener('DOMContentLoaded', refreshModalStack);
    })();

    const scrollLockKeys = ['ArrowUp', 'ArrowDown', 'PageUp', 'PageDown', 'Home', 'End', ' '];
    let scrollLocked = false;

    function isInOpenModal(target) {
        if (!(target && target.closest)) return false;
        // our custom modals use .modal-overlay (or could also target specific IDs)
        // consider any element within a visible overlay as inside a modal
        if (target.closest('.modal.show') || target.closest('.modal-overlay.active') || target.closest('#incidentModal')) {
            return true;
        }
        return false;
    }

    function preventScroll(event) {
        if (isInOpenModal(event.target)) {
            return;
        }
        event.preventDefault();
    }

    function preventScrollKeys(event) {
        // Allow normal typing when the event target is editable (input/textarea/contenteditable)
        // or when the target is inside an open modal — only block navigation/scroll keys
        const target = event.target;
        const tag = target && target.tagName ? target.tagName.toLowerCase() : '';
        const isEditable = !!(target && (target.isContentEditable || tag === 'input' || tag === 'textarea' || (target.closest && target.closest('.modal.show'))));
        if (isEditable) {
            return;
        }

        if (scrollLockKeys.includes(event.key)) {
            event.preventDefault();
        }
    }

    function setScrollLock(locked) {
        if (locked && !scrollLocked) {
            scrollLocked = true;
            document.addEventListener('wheel', preventScroll, { passive: false });
            document.addEventListener('touchmove', preventScroll, { passive: false });
            document.addEventListener('keydown', preventScrollKeys, false);
        } else if (!locked && scrollLocked) {
            scrollLocked = false;
            document.removeEventListener('wheel', preventScroll, { passive: false });
            document.removeEventListener('touchmove', preventScroll, { passive: false });
            document.removeEventListener('keydown', preventScrollKeys, false);
        }
    }



    function setImportSaveState(hasData) {
        const showButtons = !hasData;
        if (importButton) {
            importButton.disabled = hasData;
            importButton.style.display = showButtons ? '' : 'none';
        }
        if (saveButton) {
            saveButton.disabled = hasData;
            saveButton.style.display = showButtons ? '' : 'none';
        }
        if (fileNameLabel) {
            fileNameLabel.style.display = showButtons ? '' : 'none';
        }
    }

    function setButtonLoading(button, isLoading, loadingText) {
        if (!button) {
            return;
        }

        if (!button.dataset.originalHtml) {
            button.dataset.originalHtml = button.innerHTML;
        }

        if (isLoading) {
            button.disabled = true;
            if (loadingText !== undefined) {
                button.innerHTML = loadingText;
            }
        } else {
            button.disabled = false;
            button.innerHTML = button.dataset.originalHtml;
        }
    }

    // Inline modal message helpers (for validation / server errors)
    function showIncidentFormMessage(message, type = 'danger') {
        const el = document.getElementById('incidentFormAlert');
        if (!el) return;
        const bsType = (type === 'error' ? 'danger' : type);
        el.className = 'alert alert-' + bsType;
        el.textContent = message;
        el.classList.remove('d-none');
        el.style.display = 'block';
    }

    function hideIncidentFormMessage() {
        const el = document.getElementById('incidentFormAlert');
        if (!el) return;
        el.textContent = '';
        el.classList.add('d-none');
        el.style.display = 'none';
    }

    // Upload progress helpers (show/hide + update)
    function showUploadProgress(percent) {
        const container = document.getElementById('incidentUploadProgressContainer');
        const bar = document.getElementById('incidentUploadProgressBar');
        const text = document.getElementById('incidentUploadProgressText');
        if (!container || !bar) return;
        container.classList.remove('d-none');
        const p = Math.max(0, Math.min(100, Math.round(percent)));
        bar.style.width = p + '%';
        bar.setAttribute('aria-valuenow', p);
        bar.textContent = p + '%';
        if (text) text.textContent = `Uploading ${p}%`;
    }

    function hideUploadProgress() {
        const container = document.getElementById('incidentUploadProgressContainer');
        const bar = document.getElementById('incidentUploadProgressBar');
        const text = document.getElementById('incidentUploadProgressText');
        if (!container || !bar) return;
        container.classList.add('d-none');
        bar.style.width = '0%';
        bar.setAttribute('aria-valuenow', 0);
        bar.textContent = '0%';
        if (text) text.textContent = '';
    }

    // Render the per-file upload list UI grouped by type (pictures / documents)
    function renderUploadFileList() {
        const picsContainer = document.getElementById('incidentUploadFileListPictures');
        const docsContainer = document.getElementById('incidentUploadFileListDocuments');
        if (!picsContainer || !docsContainer) return;

        if (!currentUploadFiles || currentUploadFiles.length === 0) {
            picsContainer.innerHTML = '';
            docsContainer.innerHTML = '';
            return;
        }

        function renderListFor(filterFn) {
            const list = currentUploadFiles.filter(filterFn);
            if (!list.length) return '<div class="empty-message text-xs text-gray-500">No files</div>';

            return list.map(f => {
                const progress = Math.max(0, Math.min(100, Math.round(f.progress || 0)));
                const statusLabel = (f.status === 'queued') ? 'Queued' : (f.status === 'uploading' ? 'Uploading' : (f.status === 'success' ? 'Uploaded' : (f.status === 'error' ? 'Error' : 'Cancelled')));
                const showCancel = (f.status === 'uploading' || f.status === 'queued');
                const showRetry = (f.status === 'error' || f.status === 'cancelled');

                return `
                    <div class="upload-file-item d-flex items-center justify-between mb-1" data-file-id="${f.id}">
                        <div class="flex-1 min-w-0">
                            <div class="text-xs font-medium truncate">${escapeHtml(f.file.name)}</div>
                        </div>
                        <div class="ml-2 flex-shrink-0 space-x-1">
                            <button type="button" class="text-gray-400 hover:text-gray-600 text-xs" onclick="cancelSingleUpload('${f.id}')" ${showCancel ? '' : 'style="display:none;"'}>✕</button>
                        </div>
                    </div>
                `;
            }).join('');
        }

        picsContainer.innerHTML = renderListFor(f => f.type === 'photo');
        docsContainer.innerHTML = renderListFor(f => f.type === 'document');
    }

    function updateFileUploadUI(id) {
        const entry = currentUploadFiles.find(x => x.id === id);
        if (!entry) return;
        const progressEl = document.querySelector(`[data-file-progress-id="${id}"]`);
        const statusEl = document.querySelector(`[data-file-status-id="${id}"]`);
        const container = document.querySelector(`[data-file-id="${id}"]`);
        if (progressEl) {
            const p = Math.max(0, Math.min(100, Math.round(entry.progress || 0)));
            progressEl.style.width = p + '%';
            progressEl.setAttribute('aria-valuenow', p);
            progressEl.textContent = p + '%';
        }
        if (statusEl) {
            const label = (entry.status === 'queued') ? 'Queued' : (entry.status === 'uploading' ? 'Uploading' : (entry.status === 'success' ? 'Uploaded' : (entry.status === 'error' ? 'Error' : 'Cancelled')));
            statusEl.textContent = label;
        }
        if (container) {
            const cancelBtn = container.querySelector('button[onclick^="cancelSingleUpload"]');
            const retryBtn = container.querySelector('button[onclick^="retrySingleUpload"]');
            if (cancelBtn) cancelBtn.style.display = (entry.status === 'uploading' || entry.status === 'queued') ? '' : 'none';
            if (retryBtn) retryBtn.style.display = (entry.status === 'error' || entry.status === 'cancelled') ? '' : 'none';
        }
        // update overall progress bar
        updateOverallProgress();
    }

    function startFileUploadEntry(entry, incidentN) {
        return new Promise((resolve, reject) => {
            const csrfToken = getCookie('csrf_cookie_name');
            entry.status = 'uploading';
            entry.progress = 0;
            updateFileUploadUI(entry.id);

            const formData = new FormData();
            const isTemp = !incidentN;
            if (isTemp) {
                // include or create session token
                entry.sessionToken = entry.sessionToken || currentAttachmentSession || String(Date.now()) + '-' + Math.floor(Math.random() * 10000);
                currentAttachmentSession = entry.sessionToken;
                formData.append('session_token', entry.sessionToken);
            } else {
                formData.append('incident_n', incidentN);
            }
            formData.append('attachments[]', entry.file);

            const xhr = new XMLHttpRequest();
            entry.xhr = xhr;

            const url = (!incidentN) ? tempAttachmentUploadUrl : attachmentUploadUrl;
            xhr.open('POST', url, true);
            if (csrfToken) {
                try { xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken); } catch (e) { /* ignore */ }
            }

            xhr.upload.onprogress = function (e) {
                if (e.lengthComputable) {
                    entry.progress = Math.round((e.loaded / e.total) * 100);
                    updateFileUploadUI(entry.id);
                }
            };

            xhr.onload = function () {
                entry.xhr = null;
                let body = null;
                try { body = xhr.responseText ? JSON.parse(xhr.responseText) : {}; } catch (err) { body = { message: xhr.responseText }; }

                if (xhr.status >= 200 && xhr.status < 300) {
                    entry.status = 'success';
                    entry.progress = 100;

                    // If server returned session info (temp upload), store it on the entry
                    if (body && body.session_token) {
                        entry.sessionToken = body.session_token;
                        // if server returned file info, attach stored_name
                        if (Array.isArray(body.files) && body.files.length > 0) {
                            entry.storedName = body.files[0].stored_name || entry.storedName;
                        }
                    }

                    updateFileUploadUI(entry.id);

                    // update attachments count from server response (only when uploading to an existing incident)
                    const attachmentsCount = body.attachments_count || null;
                    if (attachmentsCount !== null && tableData[currentIncidentIndex]) {
                        tableData[currentIncidentIndex].attachments_count = attachmentsCount;
                        renderTable();
                    }

                    resolve(body);
                } else {
                    entry.status = 'error';
                    updateFileUploadUI(entry.id);
                    reject(body || { message: 'Upload failed' });
                }
            };

            xhr.onerror = function () {
                entry.xhr = null;
                entry.status = 'error';
                updateFileUploadUI(entry.id);
                reject({ message: 'Network error' });
            };

            xhr.onabort = function () {
                entry.xhr = null;
                entry.status = 'cancelled';
                updateFileUploadUI(entry.id);
                reject({ message: 'Cancelled' });
            };

            try {
                xhr.send(formData);
            } catch (err) {
                entry.xhr = null;
                entry.status = 'error';
                updateFileUploadUI(entry.id);
                reject(err);
            }
        });
    }

    function cancelFileUpload(id) {
        const entry = currentUploadFiles.find(x => x.id === id);
        if (!entry) return;
        // If it is currently uploading, abort XHR
        if (entry.xhr && entry.status === 'uploading') {
            try { entry.xhr.abort(); } catch (e) { /* ignore */ }
            return;
        }

        // If this was a successful temp upload, request server to remove the temp file
        if (entry.status === 'success' && entry.sessionToken && entry.storedName) {
            const csrfToken = getCookie('csrf_cookie_name');
            fetch(tempAttachmentRemoveUrl, {
                method: 'POST',
                headers: csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {},
                body: new URLSearchParams({ session_token: entry.sessionToken, stored_name: entry.storedName })
            }).then(r => r.json()).then(() => {
                entry.status = 'cancelled';
                updateFileUploadUI(entry.id);
            }).catch(() => {
                entry.status = 'error';
                updateFileUploadUI(entry.id);
            });
            return;
        }

        if (entry.status === 'queued') {
            entry.status = 'cancelled';
            updateFileUploadUI(entry.id);
            updateSaveButtonState();
            return;
        }

        // If already uploaded to final incident, user should delete via attachment viewer — do not allow here
        if (entry.status === 'success' && !entry.sessionToken) {
            showIncidentFormMessage('This file is already attached to the incident. Remove it from the attachment viewer instead.', 'warning');
            return;
        }
    }

    function retryFileUpload(id) {
        const entry = currentUploadFiles.find(x => x.id === id);
        if (!entry) return Promise.reject(new Error('Entry not found'));
        if (entry.status === 'error' || entry.status === 'cancelled') {
            entry.status = 'queued';
            entry.progress = 0;
            updateFileUploadUI(entry.id);
            updateSaveButtonState();
            return startFileUploadEntry(entry, tableData[currentIncidentIndex] ? tableData[currentIncidentIndex].N : null);
        }
        return Promise.reject(new Error('Cannot retry in current state'));
    }

    function cancelSingleUpload(id) { cancelFileUpload(id); }
    function retrySingleUpload(id) { retryFileUpload(id).catch(err => showIncidentFormMessage(err.message || 'Retry failed', 'danger')); }

    function cancelAllUploads() {
        currentUploadFiles.forEach(f => {
            if (f.status === 'uploading' && f.xhr) {
                try { f.xhr.abort(); } catch (e) { /* ignore */ }
            } else if (f.status === 'queued') {
                f.status = 'cancelled';
            }
        });
        renderUploadFileList();
        updateOverallProgress();
    }

    async function clearUploadFileList() {
        // If we have temp-uploaded files on the server, try to remove them
        const toRemove = currentUploadFiles.filter(f => f.status === 'success' && f.sessionToken && f.storedName);
        const csrfToken = getCookie('csrf_cookie_name');
        for (const f of toRemove) {
            try {
                await fetch(tempAttachmentRemoveUrl, {
                    method: 'POST',
                    headers: csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {},
                    body: new URLSearchParams({ session_token: f.sessionToken, stored_name: f.storedName })
                });
            } catch (e) {
                // ignore individual failures
            }
        }

        currentUploadFiles = [];
        currentAttachmentSession = '';
        // clear native inputs as well
        if (incidentPicturesInput) incidentPicturesInput.value = '';
        if (incidentDocumentsInput) incidentDocumentsInput.value = '';
        renderUploadFileList();
        hideUploadProgress();
        updateSaveButtonState();
    }

    // Add selected files (from inputs) to the client-side upload queue. type is 'photo' or 'document'
    function addFilesToQueue(files, type) {
        if (!files || !files.length) return;
        const now = Date.now();
        const startIndex = currentUploadFiles.length;
        const entries = files.map((file, i) => ({
            id: `${now}-${startIndex + i}`,
            file: file,
            progress: 0,
            status: 'queued',
            xhr: null,
            sessionToken: currentAttachmentSession || null,
            type: type === 'document' ? 'document' : 'photo'
        }));
        currentUploadFiles = currentUploadFiles.concat(entries);
        renderUploadFileList();
        updateOverallProgress();
        // make sure the hint updates when files are queued
        const row = (currentIncidentIndex !== null) ? tableData[currentIncidentIndex] : null;
        updateIncidentAttachmentSection(row);
    }

    function updateOverallProgress() {
        if (!currentUploadFiles || currentUploadFiles.length === 0) {
            hideUploadProgress();
            updateSaveButtonState();
            return;
        }
        const total = currentUploadFiles.length;
        const sum = currentUploadFiles.reduce((acc, f) => acc + (f.progress || (f.status === 'success' ? 100 : 0)), 0);
        const avg = Math.round(sum / total);
        showUploadProgress(avg);
        updateSaveButtonState();
    }

    // small helpers
    function escapeHtml(s) { return String(s).replace(/[&<>\"'`]/g, function (m) { return ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','`':'&#96;' })[m]; }); }
    function formatBytes(bytes) { if (bytes === 0) return '0 B'; const sizes = ['B','KB','MB','GB','TB']; const i = Math.floor(Math.log(bytes)/Math.log(1024)); return (bytes/Math.pow(1024,i)).toFixed(1) + ' ' + sizes[i]; }

    // validate save button enablement based on required fields & upload state
    function canSaveIncident() {
        if (!incidentSaveButton) return false;
        // IDs of fields we consider required for a "complete" incident
        const required = [
            'incidentMonth',
            'incidentYear',
            'incidentProvince',
            'incidentMunicipality',
            'incidentVictim',
            'incidentLocation',
            'incidentAge',
            'incidentGender'
        ];
        for (const id of required) {
            const val = document.getElementById(id)?.value || '';
            if (!val.trim()) {
                return false;
            }
        }
        // attachments continue to be optional
        return true;
    }

    function updateSaveButtonState() {
        if (!incidentSaveButton) return;
        const ok = canSaveIncident();
        incidentSaveButton.disabled = !ok;
        incidentSaveButton.classList.toggle('opacity-50', !ok);
        incidentSaveButton.classList.toggle('cursor-not-allowed', !ok);
        if (ok) {
            // blue primary styling
            incidentSaveButton.classList.remove('bg-gray-400','hover:bg-gray-400');
            incidentSaveButton.classList.add('bg-blue-600','hover:bg-blue-700');
        } else {
            // greyed-out background, no hover change
            incidentSaveButton.classList.remove('bg-blue-600','hover:bg-blue-700');
            incidentSaveButton.classList.add('bg-gray-400','hover:bg-gray-400');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // move modals out of the scrolling panel so fixed positioning works
        ['incidentModal','saveConfirmModal','importSuccessModal','saveResultModal','attachmentModal','reviewConfirmModal','attachmentViewerModal'].forEach(id => {
            const m = document.getElementById(id);
            if (m && m.parentNode && m.parentNode !== document.body) {
                document.body.appendChild(m);
            }
        });

        if (pageSizeSelect) {
            pageSize = parseInt(pageSizeSelect.value, 10) || pageSize;
            pageSizeSelect.addEventListener('change', function() {
                pageSize = parseInt(pageSizeSelect.value, 10) || pageSize;
                currentPage = 1;
                sessionStorage.setItem('incidentCurrentPage', currentPage);
                renderTable();
            });
        }

        if (prevPageButton) {
            prevPageButton.addEventListener('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    sessionStorage.setItem('incidentCurrentPage', currentPage);
                    renderTable();
                }
            });
        }

        if (nextPageButton) {
            nextPageButton.addEventListener('click', function() {
                const totalPages = getTotalPages();
                if (currentPage < totalPages) {
                    currentPage++;
                    sessionStorage.setItem('incidentCurrentPage', currentPage);
                    renderTable();
                }
            });
        }

        // Clear inline incident-form messages when user edits fields inside the modal
        incidentFieldMap.forEach(field => {
            const el = document.getElementById(field.id);
            if (el) {
                el.addEventListener('input', () => {
                    hideIncidentFormMessage();
                    updateSaveButtonState();
                });
                el.addEventListener('change', () => {
                    hideIncidentFormMessage();
                    updateSaveButtonState();
                });
            }
        });

        // wire picture/document inputs to queue selected files
        if (incidentPicturesInput) {
            incidentPicturesInput.addEventListener('change', function(e) {
                const files = Array.from(e.target.files || []);
                if (files.length) addFilesToQueue(files, 'photo');
                // clear native input so same file can be re-selected later
                incidentPicturesInput.value = '';
            });
        }
        if (incidentDocumentsInput) {
            incidentDocumentsInput.addEventListener('change', function(e) {
                const files = Array.from(e.target.files || []);
                if (files.length) addFilesToQueue(files, 'document');
                incidentDocumentsInput.value = '';
            });
        }

        if (serverRows.length > 0) {
            tableData = serverRows.map(mapServerRow);
            renderTable();
        } else {
            // still populate suggestions from any pre-known categories
            updateLocationCategoryList();
        }
        setImportSaveState(false);
        updateIncidentMunicipalities();

        // wire save button once DOM ready
        if (incidentSaveButton) {
            incidentSaveButton.addEventListener('click', saveIncidentFromModal);
            updateSaveButtonState();
        }

        const fileInput = document.getElementById('excelFile');
        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;

                if (typeof XLSX === 'undefined') {
                    alert('Excel parser failed to load. Please disable browser shields or allow external scripts, then refresh and try again.');
                    return;
                }

                setButtonLoading(importButton, true, 'Importing...');
                setButtonLoading(saveButton, true);

                document.getElementById('fileName').textContent = `Selected: ${file.name}`;

                const reader = new FileReader();
                reader.onload = function(event) {
                    try {
                        const data = new Uint8Array(event.target.result);
                        const workbook = XLSX.read(data, { type: 'array' });
                        const worksheet = workbook.Sheets[workbook.SheetNames[0]];
                        const jsonData = parseWorksheetRows(worksheet);
                        const normalizedRows = jsonData.map(normalizeRow);

                        tableData = tableData.concat(normalizedRows);
                        renderTable();
                        showImportSuccess(jsonData.length);
                            // Automatically save to database after import
                            saveToDatabase();
                            // Auto-refresh after import and save
                            setTimeout(() => { window.location.reload(); }, 1500);
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Excel Import Error',
                            html: 'Error reading file: ' + error.message,
                            confirmButtonText: 'OK',
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        });
                        document.getElementById('fileName').textContent = 'No file selected';
                    } finally {
                        setButtonLoading(importButton, false);
                        setButtonLoading(saveButton, false);
                    }
                };
                reader.onerror = function() {
                    setButtonLoading(importButton, false);
                    setButtonLoading(saveButton, false);
                    Swal.fire({
                        icon: 'error',
                        title: 'Excel Import Error',
                        html: 'Error reading file. Please try again.',
                        confirmButtonText: 'OK',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });
                };
                reader.readAsArrayBuffer(file);
            });
        }
    });

    function renderTable() {
        const tbody = document.getElementById('tableBody');
        // remember scroll position so the user doesn't get carried to the top
        const savedScroll = window.scrollY;

        if (tableData.length === 0) {
            tbody.innerHTML = '<tr><td colspan="18" class="empty-message">No data yet. Upload an Excel file or click "Add Incident" to add data.</td></tr>';
            updatePaginationControls();
            // restore scroll in case the message changed layout
            window.scrollTo(0, savedScroll);
            // also refresh datalist so it still contains any initial suggestions
            updateLocationCategoryList();
            updateOccasionList();
            updateOccupationList();
            updateFactorsList();
            return;
        }

        // apply sorting if requested
        let dataToRender = tableData;
        if (sortColumn) {
            dataToRender = [...tableData].sort((a, b) => {
                let vaRaw = a[sortColumn] || '';
                let vbRaw = b[sortColumn] || '';
                let va = vaRaw.toString().toLowerCase();
                let vb = vbRaw.toString().toLowerCase();

                // try numeric comparison first
                const na = parseFloat(vaRaw);
                const nb = parseFloat(vbRaw);
                if (!isNaN(na) && !isNaN(nb)) {
                    if (na < nb) return sortDirection === 'asc' ? -1 : 1;
                    if (na > nb) return sortDirection === 'asc' ? 1 : -1;
                    // fall through to tie-breaker below
                } else {
                    if (va < vb) return sortDirection === 'asc' ? -1 : 1;
                    if (va > vb) return sortDirection === 'asc' ? 1 : -1;
                    // fall through to tie-breaker below
                }

                // when values are equal (or not comparable) use the N column as a
                // stable secondary key so that rows maintain their incident number
                // order even when sorted by another field like year.
                const naN = parseFloat(a['N'] || 0);
                const nbN = parseFloat(b['N'] || 0);
                if (naN < nbN) return -1;
                if (naN > nbN) return 1;
                return 0;
            });
        }

        if (currentPage > getTotalPages()) {
            currentPage = getTotalPages();
        }

        const startIndex = (currentPage - 1) * pageSize;
        const endIndex = startIndex + pageSize;
        const pageRows = dataToRender.slice(startIndex, endIndex);

        let html = '';
        pageRows.forEach((row, pageIndex) => {
            const index = startIndex + pageIndex;
            row['N'] = row['N'] || (index + 1);

            html += `<tr id="row-${index}" data-review-status="${row.review_status || ''}" data-attachments="${row.attachments_count || 0}">`;
            columns.forEach(col => {
                const value = row[col] || '';
                const colClass = getColumnClass(col);

                if (col === 'N') {
                    html += `<td class="${colClass}"><span>${row['N']}</span></td>`;
                } else if (col === '') {
                    html += `<td class="${colClass}"><span>${String(value).toUpperCase()}</span></td>`;
                } else {
                    html += `<td class="${colClass}"><span>${value}</span></td>`;
                }
            });
            const attachmentsCount = row.attachments_count || 0;
            const reviewStatus = row.review_status || 'pending';
            const statusClass = reviewStatus === 'approved' ? 'status-approved' : (reviewStatus === 'rejected' ? 'status-rejected' : 'status-pending');

            const viewButton = `<button class="inline-flex items-center gap-2 px-3 py-1 bg-gray-400 text-white rounded-full text-sm hover:bg-gray-500 mt-1" onclick="openAttachmentViewer(${row['N']})">${iconEye}View</button>`;

            let reviewControls = '';
            if (canReviewIncidents) {
                
                const approveBtn = `<button aria-label="Approve" class="inline-flex items-center justify-center px-2 py-1 bg-green-600 text-white rounded text-sm hover:bg-green-700 active:bg-green-800" onclick="reviewIncident(${row['N']}, 'approve')">${iconCheck}</button>`;
                const rejectBtn  = `<button aria-label="Reject"  class="inline-flex items-center justify-center px-2 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700 active:bg-red-800" onclick="reviewIncident(${row['N']}, 'reject')">${iconX}</button>`;
                reviewControls = `
                    <div class="flex gap-1 flex-wrap">
                        ${approveBtn}
                        ${rejectBtn}
                    </div>
                `;
            }

            // attachments cell always shown; exclude status pill for focal users
            let attachmentsCell = '';
            if (isFocal) {
                attachmentsCell = `<div class="text-gray-500 text-sm">${attachmentsCount} file(s)</div>${viewButton}`;
            } else {
                attachmentsCell = `<div><span class="status-pill ${statusClass}">${reviewStatus || 'pending'}</span></div>
                    <div class="text-gray-500 text-sm">${attachmentsCount} file(s)</div>
                    ${viewButton}`;
            }

            html += `<td>${attachmentsCell}</td>`;
            if (!isFocal) {
                html += `<td>${reviewControls || '<span class="text-gray-500">-</span>'}</td>`;
                html += `<td>
                    <button aria-label="Edit" class="inline-flex items-center justify-center px-2 py-1 bg-blue-800 text-white rounded text-sm hover:bg-blue-900 active:bg-blue-950" onclick="openIncidentModal(${index})">${iconPencil}</button>
                    <button aria-label="Delete" class="inline-flex items-center justify-center px-2 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700 active:bg-red-800" onclick="deleteRow(${index})">${iconTrash}</button>
                </td>`;
            }
        });

        tbody.innerHTML = html;
        updatePaginationControls();
        // restore the scroll position after re-render
        window.scrollTo(0, savedScroll);
        // refresh datalist suggestions whenever table updates
        updateLocationCategoryList();
        updateOccasionList();
        updateOccupationList();
        updateFactorsList();
    }


    // helper: rebuild the datalist options used for the location category field
    function updateLocationCategoryList() {
        const listEl = document.getElementById('locationCategoryList');
        if (!listEl) return;
        // start with initial categories sent from the server
        const set = new Set(initialLocationCategories || []);
        // add any categories already present in tableData
        tableData.forEach(r => {
            const cat = (r['Location Category'] || '').trim();
            if (cat) set.add(cat);
        });
        // rebuild options
        listEl.innerHTML = '';
        Array.from(set).sort().forEach(cat => {
            const opt = document.createElement('option');
            opt.value = cat;
            listEl.appendChild(opt);
        });
    }

    function updateOccasionList() {
        const listEl = document.getElementById('occasionList');
        if (!listEl) return;
        const set = new Set(initialOccasions || []);
        tableData.forEach(r => {
            const occ = (r['Occasion'] || '').trim();
            if (occ) set.add(occ);
        });
        listEl.innerHTML = '';
        Array.from(set).sort().forEach(occ => {
            const opt = document.createElement('option');
            opt.value = occ;
            listEl.appendChild(opt);
        });
    }

    function updateOccupationList() {
        const listEl = document.getElementById('occupationList');
        if (!listEl) return;
        const set = new Set(initialOccupations || []);
        tableData.forEach(r => {
            const occ = (r['Occupation of the Victim'] || '').trim();
            if (occ) set.add(occ);
        });
        listEl.innerHTML = '';
        Array.from(set).sort().forEach(occ => {
            const opt = document.createElement('option');
            opt.value = occ;
            listEl.appendChild(opt);
        });
    }

    function updateFactorsList() {
        const listEl = document.getElementById('factorsList');
        if (!listEl) return;
        const set = new Set(initialFactors || []);
        tableData.forEach(r => {
            const f = (r['Other Factors'] || '').trim();
            if (f) set.add(f);
        });
        listEl.innerHTML = '';
        Array.from(set).sort().forEach(f => {
            const opt = document.createElement('option');
            opt.value = f;
            listEl.appendChild(opt);
        });
    }

    function getColumnClass(col) {
        if (col === 'Name of Victim') {
            return 'col-primary';
        }

        if (col === 'Location Category' || col === 'Age of the Person' || col === 'Gender of the Person' || col === 'Occasion') {
            return 'col-secondary';
        }

        if (col === 'Other Factors' || col === "Person's Residence" || col === 'Occupation of the Victim' || col === 'Remarks') {
            return 'col-tertiary';
        }

        return '';
    }


    function getTotalPages() {
        if (!pageSize || pageSize <= 0) {
            return 1;
        }

        return Math.max(1, Math.ceil(tableData.length / pageSize));
    }

    function updatePaginationControls() {
        if (!paginationControls) {
            return;
        }

        const totalPages = getTotalPages();
        const hasRows = tableData.length > 0;
        paginationControls.style.display = hasRows ? 'flex' : 'none';

        if (pageInfoLabel) {
            const start = tableData.length === 0 ? 0 : (currentPage - 1) * pageSize + 1;
            const end = Math.min(currentPage * pageSize, tableData.length);
            pageInfoLabel.textContent = `Showing ${start}-${end} of ${tableData.length} rows`;
        }

        if (prevPageButton) {
            prevPageButton.disabled = currentPage <= 1;
        }

        if (nextPageButton) {
            nextPageButton.disabled = currentPage >= totalPages;
        }
    }

    function openIncidentModal(index) {
        console.log('openIncidentModal invoked, index=', index);
        currentIncidentIndex = (index === 0 || index) ? index : null;
        const isEdit = currentIncidentIndex !== null;
        const row = isEdit ? tableData[currentIncidentIndex] : null;
        // temporarily disable save button until state calculated
        if (incidentSaveButton) {
            incidentSaveButton.disabled = true;
            incidentSaveButton.classList.add('opacity-50','cursor-not-allowed','bg-gray-400','hover:bg-gray-400');
            incidentSaveButton.classList.remove('bg-blue-600','hover:bg-blue-700');
        }

        incidentFieldMap.forEach(field => {
            const input = document.getElementById(field.id);
            if (!input) {
                return;
            }
            input.value = row ? (row[field.column] || '') : '';
        });

        // Ensure the currently stored year (if any) exists as an option in the select
        const yearValue = row ? (row['Year of Incident'] || '') : '';
        // only attempt to add an option when the field is a <select>
        if (yearValue) {
            const yearEl = document.getElementById('incidentYear');
            if (yearEl && yearEl.tagName && yearEl.tagName.toLowerCase() === 'select') {
                ensureYearOption(yearValue);
            }
        }
        // if we're editing, make sure the province select has the current value
        const provVal = row ? (row['Province'] || '') : '';
        if (provVal) {
            ensureProvinceOption(provVal);
        }
        // populate municipality dropdown and preserve existing value (if any)
        const muniVal = row ? (row['Municipality/City where Incidence Occurred'] || '') : '';
        updateIncidentMunicipalities(muniVal);


        if (incidentModalLabel) {
            incidentModalLabel.textContent = isEdit ? `Edit Incident ${row && row['N'] ? '#' + row['N'] : ''}` : 'Add Incident';
        }

        // clear any previous inline message
        const incidentFormAlert = document.getElementById('incidentFormAlert');
        if (incidentFormAlert) {
            incidentFormAlert.textContent = '';
            incidentFormAlert.classList.add('d-none');
        }

        if (incidentSaveButton) {
            incidentSaveButton.textContent = isEdit ? 'Update Incident' : 'Add Incident';
        }

        if (incidentPicturesInput) incidentPicturesInput.value = '';
        if (incidentDocumentsInput) incidentDocumentsInput.value = '';

        // Reset any pre-save attachment session when opening the modal to add a new incident
        if (!isEdit) {
            currentAttachmentSession = '';
            currentUploadFiles = [];
            renderUploadFileList();
            hideUploadProgress();
        }

        updateIncidentAttachmentSection(row);
        updateSaveButtonState();

        showModal('incidentModal');
    }

    function updateIncidentAttachmentSection(row) {
        if (!incidentAttachmentHint) {
            return;
        }

        if (!canUploadAttachments) {
            incidentAttachmentHint.textContent = '';
            return;
        }

        const hasIncident = !!(row && row['N']);
        const isLocal = !!(row && row._local);
        const canUpload = hasIncident && !isLocal;

        const queuedCount = currentUploadFiles ? currentUploadFiles.length : 0;
        // display only a brief summary; names appear adjacent to each input via the queue lists
        if (queuedCount > 0) {
            incidentAttachmentHint.textContent = `${queuedCount} file(s) selected.` +
                (canUpload ? ' They will be uploaded when you save the incident (or click the upload button).' : ' Click "Upload Attachments" to upload them.');
            return;
        }

        // Show hint for saved incidents, or indicate pre-saved uploaded files when adding
        if (canUpload) {
            incidentAttachmentHint.textContent = 'Upload photos or documents for this incident.';
        } else if (currentAttachmentSession && queuedCount > 0) {
            const uploadedPhotos = currentUploadFiles.filter(f => f.type === 'photo' && f.status === 'success').length;
            const uploadedDocs = currentUploadFiles.filter(f => f.type === 'document' && f.status === 'success').length;
            incidentAttachmentHint.textContent = `${uploadedPhotos} photo(s), ${uploadedDocs} document(s) uploaded (will be attached when you save the incident).`;
        } else {
            incidentAttachmentHint.textContent = 'Save the incident to the database before uploading attachments.';
        }

        if (incidentAttachmentStatus) {
            const count = row && row.attachments_count ? row.attachments_count : 0;
            const status = row && row.review_status ? row.review_status : 'pending';
            incidentAttachmentStatus.textContent = hasIncident ? `${count} file(s), status: ${status}` : '';
        }

        if (incidentPicturesInput) incidentPicturesInput.disabled = false;
        if (incidentDocumentsInput) incidentDocumentsInput.disabled = false;

        if (incidentUploadButton) {
            incidentUploadButton.disabled = false;
        }
        // ensure save button is up to date when attachments change
        updateSaveButtonState();
    }

    async function saveIncidentFromModal() {
        const genderField = incidentFieldMap.find(field => field.column === 'Gender of the Person');
        if (genderField) {
            const genderInput = document.getElementById(genderField.id);
            if (genderInput && genderInput.value) {
                const gender = genderInput.value;
                if (gender !== 'Male' && gender !== 'Female' && gender !== '') {
                    showIncidentFormMessage('Sex must be "Male" or "Female" only!', 'danger');
                    return;
                }
            }
        }

        const isEdit = currentIncidentIndex !== null;
        const row = isEdit ? tableData[currentIncidentIndex] : { 'N': tableData.length + 1, _local: true };

        incidentFieldMap.forEach(field => {
            const input = document.getElementById(field.id);
            if (!input) {
                return;
            }
            row[field.column] = input.value;
        });

        // Map frontend fields to backend/database fields (used for both create and update)
        function mapIncidentFields(row) {
            return {
                month_of_incident: row['Month of Incident'],
                year_of_incident: row['Year of Incident'],
                province: row['Province'],
                municipality: row['Municipality/City where Incidence Occurred'],
                name_of_victim: row['Name of Victim'],
                location_category: row['Location Category'],
                age: row['Age of the Person'],
                gender: row['Gender of the Person'],
                occasion: row['Occasion'],
                factors: row['Other Factors'],
                residence: row["Person's Residence"],
                occupation: row['Occupation of the Victim'],
                remarks: row['Remarks'],
            };
        }

        // Client-side validation: ensure required fields are present before adding/saving
        const requiredCols = ['Month of Incident', 'Year of Incident', 'Province', 'Municipality/City where Incidence Occurred'];
        const missing = requiredCols.filter(c => !row[c] || String(row[c]).trim() === '');
        if (missing.length) {
            showIncidentFormMessage('Please fill Month, Year, Province and Municipality/City before saving the incident.', 'warning');
            // focus first missing input (if present)
            const firstMissing = missing[0];
            const fieldMap = incidentFieldMap.find(f => f.column === firstMissing);
            if (fieldMap) {
                const el = document.getElementById(fieldMap.id);
                if (el) el.focus();
            }
            return;
        }

        // Validate age (if provided) — only whole numbers between 0 and 120 allowed
        const ageValStr = (row['Age of the Person'] || '').toString().trim();
        if (ageValStr !== '') {
            if (!/^\d+$/.test(ageValStr)) {
                showIncidentFormMessage('Age must be a whole number (0–120).', 'danger');
                const ageEl = document.getElementById('incidentAge'); if (ageEl) ageEl.focus();
                return;
            }
            const ageNum = parseInt(ageValStr, 10);
            if (ageNum < 0 || ageNum > 120) {
                showIncidentFormMessage('Age must be between 0 and 120.', 'danger');
                const ageEl = document.getElementById('incidentAge'); if (ageEl) ageEl.focus();
                return;
            }
            // normalize value
            row['Age of the Person'] = String(ageNum);
        }

        if (isEdit && row['N']) {
            try {
                // if the user queued files but didn't manually click "Upload",
                // make sure they get uploaded before we submit the update.
                if (currentUploadFiles && currentUploadFiles.length > 0) {
                    await uploadIncidentAttachments();
                }

                // Get CSRF token from meta tag
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const mappedData = mapIncidentFields(row);
                const response = await fetch(`/incident-report/update/${row['N']}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(mappedData)
                });
                const result = await response.json();
                if (!response.ok) {
                    showIncidentFormMessage(result.message || 'Update failed.', 'danger');
                    return;
                }

                // after a successful update, if there are still queued files (maybe upload errored)
                // try again so attachments are never lost
                if (currentUploadFiles && currentUploadFiles.length > 0) {
                    await uploadIncidentAttachments();
                }
            } catch (error) {
                showIncidentFormMessage('Update failed: ' + error.message, 'danger');
                return;
            }
        }

        if (!isEdit) {
            // Persist new incident to server immediately (so it won't be lost on refresh)
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const mappedData = mapIncidentFields(row);
                // include any pre-save attachment session so server can associate temp files
                if (currentAttachmentSession) {
                    mappedData.attachment_session = currentAttachmentSession;
                }
                const resp = await fetch('/incident-report/create', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(mappedData)
                });

                const result = await resp.json();
                if (!resp.ok) {
                    showIncidentFormMessage(result.message || 'Failed to save incident to server.', 'danger');
                    return;
                }

                const server = result.incident || {};

                // Update local row with authoritative values from server
                row['N'] = server.n || row['N'] || (tableData.length + 1);
                row['Month of Incident'] = server.month_of_incident || row['Month of Incident'];
                row['Year of Incident'] = server.year_of_incident || row['Year of Incident'];
                row['Province'] = server.province || row['Province'];
                row['Municipality/City where Incidence Occurred'] = server.municipality || row['Municipality/City where Incidence Occurred'];
                // Prefer server-provided decrypted name when available; otherwise keep the user's input
                if (server.name_of_victim !== undefined && server.name_of_victim !== null && String(server.name_of_victim).trim() !== '') {
                    row['Name of Victim'] = server.name_of_victim;
                }

                row['Occupation of the Victim'] = server.occupation || row['Occupation of the Victim'];
                row['review_status'] = server.review_status || 'pending';
                row['attachments_count'] = server.attachments_count || 0;
                row._local = false;

                // Merge into existing local row if present (don't overwrite non-empty client values with empty server values)
                const existingIndex = tableData.findIndex(r => String(r['N']) === String(row['N']));
                if (existingIndex >= 0) {
                    tableData[existingIndex] = Object.assign({}, tableData[existingIndex], row);
                    currentIncidentIndex = existingIndex;
                } else {
                    tableData.push(row);
                    currentIncidentIndex = tableData.findIndex(r => String(r['N']) === String(row['N']));
                }
                currentPage = getTotalPages();

                // If there was a pre-save attachment session, server already associated those temp files with the new incident.
                if (currentAttachmentSession) {
                    // server moved temp files into the created incident; clear client-side temp state
                    currentAttachmentSession = '';
                    currentUploadFiles = [];
                    renderUploadFileList();
                }

                // If the user queued/selected files but didn't upload them pre-save, upload them now to the newly created incident
                if (currentUploadFiles && currentUploadFiles.length > 0) {
                    await uploadIncidentAttachments();
                }
            } catch (err) {
                showIncidentFormMessage('Save failed: ' + (err.message || err), 'danger');
                return;
            }
        } else {
            tableData[currentIncidentIndex] = row;
        }

        renderTable();

        hideModal('incidentModal');
    }

    function ensureProvinceOption(value) {
        const provinceInput = document.getElementById('incidentProvince');
        if (!provinceInput || !value) {
            return;
        }

        const options = Array.from(provinceInput.options).map(option => option.value);
        if (!options.includes(value)) {
            const option = document.createElement('option');
            option.value = value;
            option.textContent = value;
            provinceInput.appendChild(option);
        }
    }

    function ensureYearOption(value) {
        // Historically the year field was a <select>, so we would add an
        // option if the stored value wasn't present.  The input has since been
        // changed to a plain text field, so calling this helper unguarded
        // resulted in a TypeError (`yearInput.options` undefined) and broke the
        // modal when editing an existing row.  Only manipulate options if the
        // element actually supports them.
        const yearInput = document.getElementById('incidentYear');
        if (!yearInput || !value) {
            return;
        }

        // if it's a <select> we may need to add the value as an option so it
        // remains selectable; otherwise for an <input> there's nothing to do
        if (yearInput.tagName && yearInput.tagName.toLowerCase() === 'select') {
            const options = Array.from(yearInput.options).map(option => option.value);
            if (!options.includes(String(value))) {
                const option = document.createElement('option');
                option.value = String(value);
                option.textContent = String(value);
                // append so it becomes selectable; we keep existing order as-is
                yearInput.appendChild(option);
            }
        }
    }

    function updateIncidentMunicipalities(preferredValue) {
        const provinceInput = document.getElementById('incidentProvince');
        const municipalityInput = document.getElementById('incidentMunicipality');
        if (!provinceInput || !municipalityInput) {
            return;
        }

        const province = provinceInput.value;
        let selectedValue;

        if (preferredValue !== undefined) {
            // explicit value provided (e.g. when loading/editing a row)
            selectedValue = preferredValue;
        } else {
            // if the current municipality does not belong to the selected province,
            // we clear it so that changing province resets the municipality field
            const current = municipalityInput.value;
            if (province && municipalities[province] && municipalities[province].includes(current)) {
                selectedValue = current;
            } else {
                selectedValue = '';
            }
        }

        municipalityInput.innerHTML = '<option value="">Select municipality</option>';

        if (province && municipalities[province]) {
            municipalities[province].forEach((municipality) => {
                const option = document.createElement('option');
                option.value = municipality;
                option.textContent = municipality;
                if (municipality === selectedValue) {
                    option.selected = true;
                }
                municipalityInput.appendChild(option);
            });
        }

        // keep any explicitly-supplied value even if it isn't part of the
        // standard list for the selected province.  This covers the case where
        // we are populating the form for an existing incident row and the data
        // contains an unexpected municipality name.
        if (preferredValue !== undefined && selectedValue &&
            (!municipalities[province] || !municipalities[province].includes(selectedValue))) {
            const option = document.createElement('option');
            option.value = selectedValue;
            option.textContent = selectedValue;
            option.selected = true;
            municipalityInput.appendChild(option);
        }
    }

    const incidentProvinceSelect = document.getElementById('incidentProvince');
    if (incidentProvinceSelect) {
        // reset municipality whenever the province changes
        incidentProvinceSelect.addEventListener('change', () => updateIncidentMunicipalities());
        incidentProvinceSelect.addEventListener('input', () => updateIncidentMunicipalities());
    }

    function normalizeRow(row) {
        const normalized = { N: row['N'] ?? '', _local: true };
        editableColumns.forEach(col => {
            normalized[col] = '';
        });

        // Append parts when multiple input columns map to the same canonical header
        // (e.g. Last Name / First Name / Middle Name => Name of Victim)
        Object.keys(row).forEach(key => {
            const target = resolveHeader(key);
            if (!editableColumns.includes(target)) return;

            const part = String(row[key] ?? '').trim();
            if (part === '') return;

            if (!normalized[target]) {
                normalized[target] = part;
            } else {
                normalized[target] = (String(normalized[target]) + ' ' + part).trim();
            }
        });

        // Normalize spacing for all columns
        editableColumns.forEach(col => {
            normalized[col] = String(normalized[col] || '').replace(/\s+/g, ' ').trim();
        });

        return normalized;
    }

    function normalizeHeader(value) {
        if (value === null || value === undefined) {
            return '';
        }

        return String(value)
            .replace(/\u00a0/g, ' ')
            .replace(/[\r\n\t]+/g, ' ')
            .replace(/\s+/g, ' ')
            .trim()
            .toLowerCase();
    }

    function mapServerRow(row) {
        // convert backend gender codes to full labels so the UI can show and
        // edit them consistently.  The API stores 'm' / 'f' but the select uses
        // "Male" / "Female" and imported spreadsheets might contain either.
        let genderVal = row.gender || '';
        if (typeof genderVal === 'string') {
            const lc = genderVal.trim().toLowerCase();
            if (lc === 'm' || lc === 'male') genderVal = 'Male';
            else if (lc === 'f' || lc === 'female') genderVal = 'Female';
        }

        return {
            'N': row.n || '',
            'Month of Incident': row.month_of_incident || '',
            'Year of Incident': row.year_of_incident || '',
            'Province': row.province || '',
            'Municipality/City where Incidence Occurred': row.municipality || '',
            'Name of Victim': row.name_of_victim || '',
            'Location Category': row.location_category || '',
                        'Age of the Person': row.age || '',
            'Gender of the Person': genderVal,
            'Occasion': row.occasion || '',
            'Other Factors': row.factors || '',
            "Person's Residence": row.residence || '',
            'Occupation of the Victim': row.occupation || '',
            'Remarks': row.remarks || '',
            'review_status': row.review_status || '',
            'attachments_count': row.attachments_count || 0,
            '_local': false
        };
    }

    function buildNormalizedHeaderMap() {
        const map = {};
        columns.forEach(col => {
            map[normalizeHeader(col)] = col;
        });
        Object.keys(columnAliases).forEach(alias => {
            map[normalizeHeader(alias)] = columnAliases[alias];
        });

        return map;
    }

    function resolveHeader(rawHeader) {
        const normalized = normalizeHeader(rawHeader);
        return normalizedHeaderMap[normalized] || rawHeader;
    }

    function parseWorksheetRows(worksheet) {
        const rows = XLSX.utils.sheet_to_json(worksheet, { header: 1, defval: '' });
        const headerInfo = findHeaderRow(rows);
        if (!headerInfo) {
            return XLSX.utils.sheet_to_json(worksheet, { defval: '' });
        }

        const { headerRowIndex, headers } = headerInfo;
        // detect whether the next row is a sub-header row (e.g. Last / First / Middle)
        const possibleSubheader = rows[headerRowIndex + 1] || [];
        const isSubheaderRow = possibleSubheader.some(c => typeof c === 'string' && /\b(last|first|middle|surname|given|family)\b/i.test(String(c)));

        const dataRows = isSubheaderRow ? rows.slice(headerRowIndex + 2) : rows.slice(headerRowIndex + 1);
        const results = [];

        dataRows.forEach(row => {
            if (!row || row.length === 0) {
                return;
            }

            const record = {};
            headers.forEach((header, colIndex) => {
                if (!header) {
                    return;
                }

                const cellValue = row[colIndex] ?? '';

                // If the same canonical header appears multiple times (e.g. Last/First/Middle
                // all resolved to "Name of Victim"), append non-empty parts rather than overwrite.
                if (record[header] && String(cellValue).trim() !== '') {
                    record[header] = (String(record[header]) + ' ' + String(cellValue)).trim();
                    return;
                }

                // If header is the merged "Name of Victim" and the sheet used a sub-row
                // for Last/First/Middle, read adjacent columns as name parts.
                if (header === 'Name of Victim' && isSubheaderRow) {
                    const parts = [];
                    if (String(row[colIndex] || '').trim() !== '') parts.push(String(row[colIndex]).trim());
                    if (String(row[colIndex + 1] || '').trim() !== '') parts.push(String(row[colIndex + 1]).trim());
                    if (String(row[colIndex + 2] || '').trim() !== '') parts.push(String(row[colIndex + 2]).trim());
                    if (parts.length) {
                        record[header] = parts.join(' ');
                        return; // consumed composite columns
                    }
                }

                record[header] = cellValue;
            });

            if (isInstructionRow(record)) {
                return;
            }

            const hasValue = Object.values(record).some(value => String(value).trim() !== '');
            if (hasValue) {
                // convert any simple gender codes to consistent labels for the UI
                if (record['Gender of the Person']) {
                    let g = String(record['Gender of the Person']).trim().toLowerCase();
                    if (g === 'm' || g === 'male') {
                        record['Gender of the Person'] = 'Male';
                    } else if (g === 'f' || g === 'female') {
                        record['Gender of the Person'] = 'Female';
                    }
                }
                results.push(record);
            }
        });

        return results;
    }

    function isInstructionRow(record) {
        const fieldsToCheck = [
            'Month of Incident',
            'Year of Incident',
            'Province',
            'Municipality/City where Incidence Occurred',
            'Location Category',
            'Age of the Person',
            'Gender of the Person',
            'Occasion',
            'Other Factors',
            "Person's Residence",
            'Occupation of the Victim'
        ];

        const combined = fieldsToCheck
            .map(field => normalizeHeader(record[field] ?? ''))
            .join(' ');

        return combined.includes('input') || combined.includes('use numerical');
    }

    function findHeaderRow(rows) {
        if (!rows || rows.length === 0) {
            return null;
        }

        const maxScan = Math.min(rows.length, 20);
        let bestMatch = null;

        for (let i = 0; i < maxScan; i++) {
            const row = rows[i];
            if (!row || row.length === 0) {
                continue;
            }

            let score = 0;
            const headers = row.map(cell => resolveHeader(cell));
            headers.forEach(header => {
                if (editableColumns.includes(header) || header === 'N') {
                    score++;
                }
            });

            if (!bestMatch || score > bestMatch.score) {
                bestMatch = { score, headerRowIndex: i, headers };
            }
        }

        if (!bestMatch || bestMatch.score < 3) {
            return null;
        }

        return bestMatch;
    }

    function getCookie(name) {
        const matches = document.cookie.match(new RegExp('(?:^|; )' + name.replace(/([.$?*|{}()\[\]\\\/\+^])/g, '\\$1') + '=([^;]*)'));
        return matches ? decodeURIComponent(matches[1]) : '';
    }

    async function saveToDatabase() {
        if (tableData.length === 0) {
            alert('No data to save.');
            return;
        }

        try {
            setButtonLoading(saveButton, true, 'Saving...');
            setButtonLoading(importButton, true);
            const csrfToken = getCookie('csrf_cookie_name');
            const response = await fetch(importUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {})
                },
                body: JSON.stringify({ rows: tableData })
            });

            const result = await response.json();
            if (!response.ok) {
                showSaveResult(result.message || 'Import failed.');
                return;
            }

            let message = `Saved. Inserted: ${result.inserted}, Updated: ${result.updated}, Skipped: ${result.skipped}.`;
            if (result.errors && result.errors.length) {
                const missingRows = result.errors.filter(e => e.missing).length;
                const invalidMonthRows = result.errors.filter(e => e.invalid && e.invalid.includes('month_of_incident')).length;
                if (missingRows) {
                    message += `\n${missingRows} rows were missing required fields.`;
                }
                if (invalidMonthRows) {
                    message += `\n${invalidMonthRows} rows had invalid month values (must be 1-12).`;
                }
            }
            showSaveResult(message);
        } catch (error) {
            showSaveResult('Import failed: ' + error.message);
        } finally {
            setButtonLoading(saveButton, false);
            setButtonLoading(importButton, false);
        }
    }

    // Tailwind modal helpers (show/hide by toggling hidden)
    function showModal(id) {
        const el = document.getElementById(id);
        if (!el) return;
        el.classList.remove('hidden');
        el.style.display = 'flex';
        // enforce full-screen offsets in case Tailwind classes aren't applied
        el.style.top = '0';
        el.style.left = '0';
        el.style.right = '0';
        el.style.bottom = '0';
        el.style.position = 'fixed';
        el.style.width = '100vw';
        el.style.height = '100vh';
        el.style.zIndex = '9999';
        el.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
        try { setScrollLock(true); } catch (e) { /* noop */ }
    }

    function hideModal(id) {
        const el = document.getElementById(id);
        if (!el) return;
        el.classList.add('hidden');
        el.style.display = 'none';
        el.setAttribute('aria-hidden', 'true');
        // restore scrolling
        document.body.classList.remove('overflow-hidden');
        try { setScrollLock(false); } catch (e) { /* noop */ }
    }

    function openSaveModal() {
        showModal('saveConfirmModal');
    }

    function confirmSaveToDatabase() {
        hideModal('saveConfirmModal');
        saveToDatabase();
    }

    function showImportSuccess(count) {
        const messageEl = document.getElementById('importSuccessMessage');
        if (messageEl) messageEl.textContent = `Successfully imported ${count} rows.`;

        showModal('importSuccessModal');
    }

    function showSaveResult(message) {
        const messageEl = document.getElementById('saveResultMessage');
        if (messageEl) messageEl.textContent = message;

        showModal('saveResultModal');
        const modalEl = document.getElementById('saveResultModal');
        const okBtn = modalEl ? modalEl.querySelector('button') : null;
        if (okBtn) okBtn.addEventListener('click', () => window.location.reload(), { once: true });
    }

    function showAttachmentModal(message) {
        const messageEl = document.getElementById('attachmentModalMessage');
        if (messageEl) {
            messageEl.textContent = message;
        }

        showModal('attachmentModal');
    }

    async function openAttachmentViewer(incidentN) {
        // keep scroll stored whenever we navigate around or show modals so
        // the user returns to the same spot if the page is reloaded.
        sessionStorage.setItem('incidentScrollY', window.scrollY);
        if (!incidentN) {
            showAttachmentModal('Incident number is missing.');
            return;
        }

        try {
            const response = await fetch(`${attachmentListUrl}/${incidentN}`);
            const result = await response.json();
            if (!response.ok) {
                showAttachmentModal(result.message || 'Failed to load attachments.');
                return;
            }

            const attachments = result.attachments || [];
            const counts = result.counts || { photo: 0, document: 0 };
            const listEl = document.getElementById('attachmentList');
            const previewEl = document.getElementById('attachmentPreview');
            const downloadEl = document.getElementById('attachmentDownload');
            const countsEl = document.getElementById('attachmentCounts');

            if (countsEl) {
                const p = counts.photo || 0;
                const d = counts.document || 0;
                countsEl.textContent = `${p} photo${p !== 1 ? 's' : ''}, ${d} document${d !== 1 ? 's' : ''}`;
            }

            listEl.innerHTML = '';
            previewEl.innerHTML = '<span class="text-gray-500">Select a file to preview.</span>';
            downloadEl.href = '#';

            if (attachments.length === 0) {
                listEl.innerHTML = '<div class="text-gray-500 text-sm">No attachments uploaded yet.</div>'; 
            } else {
                attachments.forEach((item, index) => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md flex items-center';

                    const viewUrl = `${attachmentViewUrl}/${item.id}`;
                    const mimeType = item.mime_type || '';
                    const isImage = mimeType.startsWith('image/');

                    const thumbHtml = isImage
                        ? `<img data-src="${viewUrl}" src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='64' height='48'></svg>" alt="${escapeHtml(item.original_name)}" style="width:64px;height:48px;object-fit:cover;border-radius:4px;margin-right:12px;display:inline-block;background:#f1f3f5;" />`
                        : `<div style="width:64px;height:48px;display:inline-flex;align-items:center;justify-content:center;background:#f1f3f5;border-radius:4px;margin-right:12px;"><i class="mdi mdi-file-document-outline" style="font-size:20px;color:#6c757d;"></i></div>`;

                    const meta = `<div style="flex:1;min-width:0;text-align:left;">
                                    <div class="fw-semibold small text-truncate">${escapeHtml(item.original_name)}</div>
                                    <div class="small text-muted">${item.file_kind || ''} • ${formatBytes(item.size_bytes || 0)}</div>
                                  </div>`;

                    button.innerHTML = thumbHtml + meta;

                    // If this is an image thumbnail, load it via fetch->blob first. This
                    // avoids the browser-level <img> failure we're seeing where the
                    // direct img request sometimes errors even though the server returns 200.
                    (function loadThumbnail() {
                        const t = button.querySelector('img[data-src]');
                        if (!t) return;

                        // show a neutral background while fetching
                        t.style.background = '#f1f3f5';

                        // Use the preview JSON API (guarantees we get a decodable data URI)
                        pollPreview(item.id,
                            (json) => {
                                t.src = `data:${json.mime_type};base64,${json.data}`;
                                t.addEventListener('error', () => console.warn('Thumbnail data URL failed to render:' , item.id), { once: true });
                            },
                            (info) => {
                                // show converting indicator or keep placeholder
                                if (info && info.converting) {
                                    t.style.opacity = '0.6';
                                }
                            }
                        );
                    })();

                    // Log thumbnail load failures and probe the server response so we can debug
                    // why images sometimes show the broken-image icon in the UI.
                    const thumbImg = button.querySelector('img');
                    if (thumbImg) {
                        thumbImg.addEventListener('error', async () => {
                            console.warn('Attachment thumbnail failed to load:', viewUrl);
                            try {
                                const probe = await fetch(viewUrl, { credentials: 'same-origin' });
                                console.warn('Probe response for', viewUrl, 'status=', probe.status, 'content-type=', probe.headers.get('content-type'));
                                const sample = await probe.text().catch(() => null);
                                if (sample) console.debug('Probe body (first 300 chars):', sample.substring(0, 300));
                            } catch (probeErr) {
                                console.error('Probe fetch error for thumbnail:', probeErr);
                            }
                        }, { once: true });
                    }

                    button.addEventListener('click', async () => {
                        const downloadUrl = `${attachmentDownloadUrl}/${item.id}`;
                        downloadEl.href = downloadUrl;

                        const isPdf = mimeType === 'application/pdf';
                        const isOffice = mimeType === 'application/msword' || mimeType === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';

                        if (isImage) {
                            // Image: use preview API (base64) so browser decodes reliably
                            previewEl.innerHTML = `<div class="text-center p-4 text-muted">Loading preview…</div>`;

                            await pollPreview(item.id,
                                (json) => {
                                    const dataUrl = `data:${json.mime_type};base64,${json.data}`;
                                    previewEl.innerHTML = `<img src="${dataUrl}" alt="${escapeHtml(item.original_name)}" style="max-width:100%; max-height:520px;" />`;
                                },
                                (info) => {
                                    if (info && info.converting) {
                                        previewEl.innerHTML = `<div class="text-center p-4 text-muted">Converting preview…</div>`;
                                    } else if (info && info.error) {
                                        previewEl.innerHTML = `<div class="text-center p-4 text-muted">Preview not available.</div>`;
                                    }
                                }
                            );

                        } else if (isPdf) {
                            // PDFs: render directly
                            previewEl.innerHTML = `<iframe src="${viewUrl}" style="width:100%; height:520px; border:0;" title="${escapeHtml(item.original_name)}"></iframe>`;

                        } else if (isOffice) {
                            // Office documents: server converts to PDF; poll preview API for the PDF and show it in an iframe
                            previewEl.innerHTML = `<div class="text-center p-4 text-muted">Preparing preview…</div>`;

                            await pollPreview(item.id,
                                (json) => {
                                    const dataUrl = `data:${json.mime_type};base64,${json.data}`;
                                    previewEl.innerHTML = `<iframe src="${dataUrl}" style="width:100%; height:520px; border:0;" title="${escapeHtml(item.original_name)}"></iframe>`;
                                },
                                (info) => {
                                    if (info && info.converting) {
                                        previewEl.innerHTML = `<div class="text-center p-4 text-muted">Converting preview…</div>`;
                                    } else {
                                        previewEl.innerHTML = `<div class="text-center p-4 text-muted">Preview not available.</div>`;
                                    }
                                }
                            );

                        } else {
                            previewEl.innerHTML = `
                                <div class="text-center p-4">
                                    <div class="text-gray-500 mb-2">Preview not available for this file type.</div>
                                    <a class="inline-flex items-center gap-2 px-3 py-1.5 border border-blue-600 text-blue-600 rounded-md text-sm" href="${downloadUrl}" target="_blank" rel="noopener">Download ${item.original_name}</a>
                                </div>
                            `;
                        }

                        listEl.querySelectorAll('button').forEach(el => el.classList.remove('active'));
                        button.classList.add('active');
                    });

                    if (index === 0) {
                        setTimeout(() => button.click(), 0);
                    }

                    listEl.appendChild(button);
                });
            }

            showModal('attachmentViewerModal');
        } catch (error) {
            showAttachmentModal('Failed to load attachments: ' + error.message);
        }
    }

    let pendingReview = { incidentN: null, action: null };

    function showReviewConfirm(incidentN, action) {
        const messageEl = document.getElementById('reviewConfirmMessage');
        const actionLabel = action === 'approve' ? 'approve' : 'reject';
        if (messageEl) {
            messageEl.textContent = `Are you sure you want to ${actionLabel} this incident?`;
        }

        pendingReview = { incidentN, action };

        showModal('reviewConfirmModal');
    }

    function submitReview() {
        const { incidentN, action } = pendingReview;
        if (!incidentN || !action) {
            return;
        }

        hideModal('reviewConfirmModal');
        performReview(incidentN, action);
    }

    function deleteRow(index) {
        if (confirm('Are you sure you want to delete this row?')) {
            tableData.splice(index, 1);
            renderTable();
            alert('Row deleted successfully!');
        }
    }

    async function uploadIncidentAttachments() {
        // Upload entries from the client-side queue (currentUploadFiles).
        // If queue is empty, try to pick up files from the native inputs.

        const row = (currentIncidentIndex !== null) ? tableData[currentIncidentIndex] : null;
        const incidentN = row && !row._local ? row['N'] : null;
        const isPreSave = !incidentN; // true when adding a new incident or editing a local row

        // If user selected files in native inputs but didn't 'queue' them yet, add them now
        if (!currentUploadFiles || currentUploadFiles.length === 0) {
            if (incidentPicturesInput && incidentPicturesInput.files && incidentPicturesInput.files.length > 0) {
                addFilesToQueue(Array.from(incidentPicturesInput.files), 'photo');
            }
            if (incidentDocumentsInput && incidentDocumentsInput.files && incidentDocumentsInput.files.length > 0) {
                addFilesToQueue(Array.from(incidentDocumentsInput.files), 'document');
            }
        }

        if (!currentUploadFiles || currentUploadFiles.length === 0) {
            showIncidentFormMessage('Please select at least one file to upload.', 'warning');
            return;
        }

        // Prevent starting another upload while one is in progress
        if (currentUploadFiles.some(f => f.status === 'uploading')) {
            showIncidentFormMessage('An upload is already in progress. Please wait or cancel it first.', 'warning');
            return;
        }

        // Prepare session token for pre-save uploads (only if at least one queued file has no sessionToken)
        if (isPreSave) {
            currentAttachmentSession = currentAttachmentSession || String(Date.now()) + '-' + Math.floor(Math.random() * 10000);
            currentUploadFiles.forEach(f => { if (!f.sessionToken) f.sessionToken = currentAttachmentSession; });
        }

        renderUploadFileList();
        showUploadProgress(0);
        setButtonLoading(incidentUploadButton, true, 'Uploading...');

        // Start uploads for queued files only
        const queued = currentUploadFiles.filter(e => e.status === 'queued' || e.status === 'error' || e.status === 'cancelled');
        const uploadPromises = queued.map(entry => {
            entry.status = 'queued';
            entry.progress = 0;
            updateFileUploadUI(entry.id);
            return startFileUploadEntry(entry, incidentN).catch(err => err);
        });

        const results = await Promise.allSettled(uploadPromises);

        const anySuccess = results.some(r => r.status === 'fulfilled');
        if (anySuccess) {
            // regardless of whether this was a pre-save upload or not, refresh the hint
            if (row) {
                row.review_status = 'pending';
                renderTable();
            }
            updateIncidentAttachmentSection(row);

            showAttachmentModal('Attachments uploaded.');
            showIncidentFormMessage('Attachments uploaded.', 'success');
            setTimeout(hideIncidentFormMessage, 2000);
        } else {
            // all uploads failed
            showAttachmentModal('Failed to upload attachments.');
            showIncidentFormMessage('Failed to upload attachments.', 'danger');
        }

        // Keep queue visible so user can retry failed ones; clear native inputs
        if (incidentPicturesInput) incidentPicturesInput.value = '';
        if (incidentDocumentsInput) incidentDocumentsInput.value = '';
        updateOverallProgress();
        setButtonLoading(incidentUploadButton, false);
    }

    async function performReview(incidentN, action) {
        if (!incidentN) {
            return;
        }

        const endpoint = action === 'approve' ? `${approveUrl}/${incidentN}` : `${rejectUrl}/${incidentN}`;
        try {
            const response = await fetch(endpoint, { method: 'POST' });
            const result = await response.json();
            if (!response.ok) {
                showAttachmentModal(result.message || 'Update failed.');
                return;
            }

            // update the in-memory data so the new status is reflected immediately
            tableData = tableData.map((row) => {
                // incidentN may be a number while row['N'] may be a string (coming from server),
                // so use loose equality or coerce to number to ensure a match.
                if (row['N'] == incidentN) {
                    return {
                        ...row,
                        review_status: result.status || (action === 'approve' ? 'approved' : 'rejected'),
                    };
                }
                return row;
            });

            renderTable();
            showAttachmentModal('Incident updated successfully.');
        } catch (error) {
            showAttachmentModal('Update failed: ' + error.message);
        }
    }

    function reviewIncident(incidentN, action) {
        showReviewConfirm(incidentN, action);
    }

    const reviewConfirmAction = document.getElementById('reviewConfirmAction');
    if (reviewConfirmAction) {
        reviewConfirmAction.addEventListener('click', submitReview);
    }
</script>
<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script>
    function downloadIncidentReport() {
        // Download as CSV
        window.open('/incident-report/generateReport?format=csv', '_blank');
    }
</script>
<?= $this->endSection() ?>

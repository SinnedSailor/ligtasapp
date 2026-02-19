
<?= $this->extend('layouts/staradmin') ?>
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

    .table-responsive th,
    .table-responsive td {
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
    }

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
</style>
    <!-- CSRF token for AJAX -->
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
    $roleName = $roleName ?? (session()->get('role_name') ?? '');
    $isLgu = strtoupper(trim((string) $roleName)) === 'LGU';
    $isProvince = strtoupper(trim((string) $roleName)) === 'PROVINCE';
    $isAdmin = $isAdmin ?? (bool) session()->get('is_admin');
    $canReview = $isProvince || $isAdmin;
    $provinceList = $provinces ?? [];
?>
<div class="page-header">
    <h3 class="page-title">Incident Report</h3>
</div>

<div class="px-0">
    <div class="max-w-full bg-white rounded-2xl shadow p-6">
        <h4 class="text-lg font-semibold">Data Management</h4>
        <div class="flex flex-wrap items-center gap-3 mt-4 mb-4">
            <div class="flex items-center gap-3">
                <input type="file" id="excelFile" accept=".xlsx,.xls,.csv" class="hidden" />
                <button id="importButton" class="px-3 py-1.5 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700" onclick="document.getElementById('excelFile').click()" style="<?= $hasInitialRows ? 'display:none;' : '' ?>">
                    📥 Import Excel File
                </button>
                <span class="text-gray-500 text-sm file-name" id="fileName" style="<?= $hasInitialRows ? 'display:none;' : '' ?>">No file selected</span>
            </div>
        </div>

        <?php if ($isLgu): ?>
            <div class="mb-4">
                <div class="bg-blue-50 border border-blue-200 text-blue-800 rounded-md p-3 text-sm">Upload at least one photo or document per incident. Attachments are reviewed by Province users.</div>
            </div>
        <?php endif; ?>

        <div class="flex flex-wrap gap-2 mb-4">
            <?php if ($isLgu || $isAdmin): ?>
                <button class="px-3 py-1.5 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700" onclick="openIncidentModal()">
                    ➕ Add Incident
                </button>
            <?php endif; ?>
            <button id="saveButton" class="px-3 py-1.5 bg-green-600 text-white rounded-md text-sm hover:bg-green-700" onclick="openSaveModal()" style="<?= $hasInitialRows ? 'display:none;' : '' ?>">
                💾 Save to Database
            </button>
            <button id="generateReportButton" class="px-3 py-1.5 bg-sky-500 text-white rounded-md text-sm hover:bg-sky-600" onclick="downloadIncidentReport()">
                📊 Generate Report
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-600">N</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-600">Month of Incident</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-600">Year of Incident</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-600">Province</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-600">Municipality/City where Incidence Occurred</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 col-primary">Name of Victim</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 col-secondary">Location Category</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 col-secondary">Age of the Person</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 col-secondary">Sex</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 col-secondary">Occasion</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 col-tertiary">Other Factors</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 col-tertiary">Person's Residence</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 col-tertiary">Occupation of the Victim</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 col-tertiary">Remarks</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-600">Attachments</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-600">Review</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody id="tableBody" class="bg-white divide-y divide-gray-100">
                    <tr>
                        <td colspan="16" class="px-4 py-6 text-center text-sm text-gray-400 empty-message">No data yet. Upload an Excel file or click "Add Incident" to add data.</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="pagination-controls mt-4 hidden" id="paginationControls">
            <button id="prevPage" type="button" class="px-3 py-1.5 border border-gray-300 rounded-md text-sm">Prev</button>
                    <button id="nextPage" type="button" class="px-3 py-1.5 border border-gray-300 rounded-md text-sm">Next</button>
                    <span class="page-info" id="pageInfo"></span>
                    <div class="ms-auto d-flex align-items-center gap-2">
                        <label class="page-info" for="pageSize">Rows per page</label>
                        <select id="pageSize" class="form-select form-select-sm" style="width:auto;">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="saveConfirmModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40" aria-hidden="true">
  <div class="bg-white rounded-2xl shadow-lg max-w-md w-full p-6 mx-4">
    <div class="flex items-start justify-between mb-4">
      <h3 class="text-lg font-semibold">Confirm Save</h3>
      <button type="button" class="text-slate-400 hover:text-slate-600" onclick="hideModal('saveConfirmModal')" aria-label="Close">&times;</button>
    </div>
    <div class="text-sm text-slate-600 mb-6">This will save the current imported rows into the database. Continue?</div>
    <div class="flex justify-end gap-3">
      <button type="button" class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-md" onclick="hideModal('saveConfirmModal')">Cancel</button>
      <button type="button" class="px-3 py-1.5 bg-green-600 text-white rounded-md" onclick="confirmSaveToDatabase()">Yes, Save</button>
    </div>
  </div>
</div>

<div id="importSuccessModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40" aria-hidden="true">
  <div class="bg-white rounded-2xl shadow-lg max-w-md w-full p-6 mx-4">
    <div class="flex items-start justify-between mb-4">
      <h3 class="text-lg font-semibold">Import Complete</h3>
      <button type="button" class="text-slate-400 hover:text-slate-600" onclick="hideModal('importSuccessModal')" aria-label="Close">&times;</button>
    </div>
    <div class="text-sm text-slate-700 mb-6"><div id="importSuccessMessage">Import completed.</div></div>
    <div class="flex justify-end">
      <button type="button" class="px-3 py-1.5 bg-blue-600 text-white rounded-md" onclick="hideModal('importSuccessModal')">OK</button>
    </div>
  </div>
</div>

<div id="saveResultModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40" aria-hidden="true">
  <div class="bg-white rounded-2xl shadow-lg max-w-md w-full p-6 mx-4">
    <div class="flex items-start justify-between mb-4">
      <h3 class="text-lg font-semibold">Save Result</h3>
      <button type="button" class="text-slate-400 hover:text-slate-600" onclick="hideModal('saveResultModal')" aria-label="Close">&times;</button>
    </div>
    <div class="text-sm text-slate-700 mb-6"><div id="saveResultMessage">Save completed.</div></div>
    <div class="flex justify-end">
      <button type="button" class="px-3 py-1.5 bg-blue-600 text-white rounded-md" onclick="hideModal('saveResultModal')">OK</button>
    </div>
  </div>
</div>
</div>

<!-- Attachment notice modal (Tailwind) -->
<div id="attachmentModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40" aria-hidden="true">
  <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-4 mx-4">
    <div class="flex items-center justify-between mb-3">
      <h5 class="text-lg font-semibold">Upload Notice</h5>
      <button type="button" class="text-slate-400 hover:text-slate-600" onclick="hideModal('attachmentModal')" aria-label="Close">&times;</button>
    </div>
    <div class="text-sm text-slate-700 mb-4"><div id="attachmentModalMessage">Please upload at least one file.</div></div>
    <div class="flex justify-end">
      <button type="button" class="px-3 py-1.5 bg-blue-600 text-white rounded-md" onclick="hideModal('attachmentModal')">OK</button>


<!-- Review confirm modal (Tailwind) -->
<div id="reviewConfirmModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40" aria-hidden="true">
  <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-4 mx-4">
    <div class="flex items-center justify-between mb-3">
      <h5 class="text-lg font-semibold">Confirm Review</h5>
      <button type="button" class="text-slate-400 hover:text-slate-600" onclick="hideModal('reviewConfirmModal')" aria-label="Close">&times;</button>
    </div>
    <div class="text-sm text-slate-700 mb-4"><div id="reviewConfirmMessage">Are you sure you want to update this incident?</div></div>
    <div class="flex justify-end gap-2">
      <button type="button" class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-md" onclick="hideModal('reviewConfirmModal')">Cancel</button>
      <button type="button" id="reviewConfirmAction" class="px-3 py-1.5 bg-blue-600 text-white rounded-md" onclick="submitReview()">Confirm</button>
    </div>
  </div>
</div>

<div id="incidentModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40" aria-hidden="true">
  <div class="bg-white rounded-2xl shadow-lg max-w-4xl w-full p-6 mx-4">
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
          <input type="text" id="incidentYear" class="block w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-300" />
        </div>

        <div class="col-span-1">
          <label for="incidentProvince" class="text-sm text-slate-600 block mb-1">Province</label>
          <select id="incidentProvince" class="block w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-300">
            <option value="" disabled selected class="text-slate-400">Select province</option>
            <?php foreach ($provinceList as $province): ?>
              <option value="<?= esc($province) ?>"><?= esc($province) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="md:col-span-2">
          <label for="incidentMunicipality" class="text-sm text-slate-600 block mb-1">Municipality / City where Incident Occurred</label>
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
          <input type="text" id="incidentLocation" class="block w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-300" />
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
          <input type="text" id="incidentOccasion" class="block w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-300" />
        </div>

        <div class="md:col-span-1">
          <label for="incidentFactors" class="text-sm text-slate-600 block mb-1">Other Factors</label>
          <input type="text" id="incidentFactors" class="block w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-300" />
        </div>

        <div class="md:col-span-1">
          <label for="incidentResidence" class="text-sm text-slate-600 block mb-1">Person's Residence</label>
          <input type="text" id="incidentResidence" class="block w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-300" />
        </div>

        <div class="md:col-span-1">
          <label for="incidentOccupation" class="text-sm text-slate-600 block mb-1">Occupation of the Victim</label>
          <input type="text" id="incidentOccupation" class="block w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-300" />
        </div>

        <div class="md:col-span-3">
          <label for="incidentRemarks" class="text-sm text-slate-600 block mb-1">Remarks</label>
          <input type="text" id="incidentRemarks" class="block w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-300" />
        </div>
      </div>

      <?php if ($isLgu || $isAdmin): ?>
        <div class="border-t pt-3 mt-3" id="incidentAttachmentSection">
          <div class="flex items-center justify-between mb-2">
            <h6 class="mb-0">Attachments</h6>
            <span class="text-muted text-sm" id="incidentAttachmentStatus"></span>
          </div>
          <div class="text-muted text-sm" id="incidentAttachmentHint"></div>
          <div class="flex flex-wrap gap-2 mt-2">
            <input type="file" id="incidentAttachments" class="block w-full text-sm text-slate-700" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" multiple />
            <button type="button" id="incidentUploadButton" class="px-3 py-1.5 border border-indigo-600 text-indigo-600 rounded-md text-sm hover:bg-indigo-50" onclick="uploadIncidentAttachments()">Upload Attachments</button>
          </div>
        </div>
      <?php endif; ?>

    </form>
  </div>
</div> 

<!-- Attachment viewer modal (Tailwind) -->
<div id="attachmentViewerModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40" aria-hidden="true">
  <div class="bg-white rounded-lg shadow-lg max-w-4xl w-full p-4 mx-4">
    <div class="flex items-center justify-between mb-3">
      <h5 class="text-lg font-semibold">Incident Attachments</h5>
      <button type="button" class="text-slate-400 hover:text-slate-600" onclick="hideModal('attachmentViewerModal')" aria-label="Close">&times;</button>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div id="attachmentList" class="space-y-2 md:col-span-1"></div>
      <div id="attachmentPreview" class="md:col-span-2 border rounded p-4 flex items-center justify-center" style="min-height:320px; background: #f8fafc;">
        <span class="text-muted">Select a file to preview.</span>
      </div>
    </div>
    <div class="flex justify-between items-center gap-2 mt-4">
      <a id="attachmentDownload" href="#" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-3 py-1.5 border border-blue-600 text-blue-600 rounded-md hover:bg-blue-50">Download</a>
      <div class="flex justify-end gap-2">
        <button type="button" class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-md" onclick="hideModal('attachmentViewerModal'); closeAttachmentViewer && closeAttachmentViewer()">Close</button>
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
    const attachmentListUrl = "<?= base_url('/incident-report/attachments') ?>";
    const attachmentViewUrl = "<?= base_url('/incident-report/attachments/view') ?>";
    const attachmentDownloadUrl = "<?= base_url('/incident-report/attachments/download') ?>";
    const approveUrl = "<?= base_url('/incident-report/approve') ?>";
    const rejectUrl = "<?= base_url('/incident-report/reject') ?>";
    const canUploadAttachments = <?= $isLgu ? 'true' : 'false' ?>;
    const canReviewIncidents = <?= $canReview ? 'true' : 'false' ?>;
    const municipalities = <?= json_encode($municipalities ?? []) ?>;
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
    const editableColumns = columns.filter(col => col !== 'N');
    const columnAliases = {
        'Year': 'Year of Incident',
        'Municipality': 'Municipality/City where Incidence Occurred',
        'Municipality/City': 'Municipality/City where Incidence Occurred',
        'Municipality/City where Incident Occurred': 'Municipality/City where Incidence Occurred',
        'Age': 'Age of the Person',
        'Gender': 'Gender of the Person',
        'Factors': 'Other Factors',
        'Occupation': 'Occupation of the Victim'
    };
    const normalizedHeaderMap = buildNormalizedHeaderMap();
    const serverRows = <?= json_encode($initialRows ?? []) ?>;
    const importButton = document.getElementById('importButton');
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
    const incidentAttachmentsInput = document.getElementById('incidentAttachments');
    const incidentUploadButton = document.getElementById('incidentUploadButton');
    let currentPage = 1;
    let pageSize = 10;
    let currentIncidentIndex = null;

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

    if (typeof bootstrap !== 'undefined') {
        document.addEventListener('show.bs.modal', function(event) {
            const modal = event.target;
            const openModals = document.querySelectorAll('.modal.show').length;
            const zIndex = 1050 + (openModals * 20);
            modal.style.zIndex = zIndex;
            setTimeout(() => {
                const backdrops = document.querySelectorAll('.modal-backdrop.show');
                const backdrop = backdrops[backdrops.length - 1];
                if (backdrop) {
                    backdrop.style.zIndex = zIndex - 10;
                }
            }, 0);
        });
    }

    const scrollLockKeys = ['ArrowUp', 'ArrowDown', 'PageUp', 'PageDown', 'Home', 'End', ' '];
    let scrollLocked = false;

    function isInOpenModal(target) {
        return !!(target && target.closest && target.closest('.modal.show'));
    }

    function preventScroll(event) {
        if (isInOpenModal(event.target)) {
            return;
        }
        event.preventDefault();
    }

    function preventScrollKeys(event) {
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

    if (typeof bootstrap !== 'undefined') {
        document.addEventListener('shown.bs.modal', function() {
            setScrollLock(true);
        });
        document.addEventListener('hidden.bs.modal', function() {
            if (document.querySelectorAll('.modal.show').length === 0) {
                setScrollLock(false);
            }
        });
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

    document.addEventListener('DOMContentLoaded', function() {
        if (pageSizeSelect) {
            pageSize = parseInt(pageSizeSelect.value, 10) || pageSize;
            pageSizeSelect.addEventListener('change', function() {
                pageSize = parseInt(pageSizeSelect.value, 10) || pageSize;
                currentPage = 1;
                renderTable();
            });
        }

        if (prevPageButton) {
            prevPageButton.addEventListener('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    renderTable();
                }
            });
        }

        if (nextPageButton) {
            nextPageButton.addEventListener('click', function() {
                const totalPages = getTotalPages();
                if (currentPage < totalPages) {
                    currentPage++;
                    renderTable();
                }
            });
        }

        if (serverRows.length > 0) {
            tableData = serverRows.map(mapServerRow);
            renderTable();
        }
        setImportSaveState(false);
        updateIncidentMunicipalities();

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
                        alert('Error reading file: ' + error.message);
                        document.getElementById('fileName').textContent = 'No file selected';
                    } finally {
                        setButtonLoading(importButton, false);
                        setButtonLoading(saveButton, false);
                    }
                };
                reader.onerror = function() {
                    setButtonLoading(importButton, false);
                    setButtonLoading(saveButton, false);
                    alert('Error reading file. Please try again.');
                };
                reader.readAsArrayBuffer(file);
            });
        }
    });

    function renderTable() {
        const tbody = document.getElementById('tableBody');

        if (tableData.length === 0) {
            tbody.innerHTML = '<tr><td colspan="17" class="empty-message">No data yet. Upload an Excel file or click "Add Incident" to add data.</td></tr>';
            updatePaginationControls();
            return;
        }

        if (currentPage > getTotalPages()) {
            currentPage = getTotalPages();
        }

        const startIndex = (currentPage - 1) * pageSize;
        const endIndex = startIndex + pageSize;
        const pageRows = tableData.slice(startIndex, endIndex);

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

            const viewButton = `<button class="inline-flex items-center gap-2 px-3 py-1 border rounded text-sm text-gray-700 bg-white hover:bg-gray-50 mt-1" onclick="openAttachmentViewer(${row['N']})">View</button>`;

            let reviewControls = '';
            if (canReviewIncidents) {
                reviewControls = `
                    <div class="flex gap-1 flex-wrap">
                        <button class="inline-flex items-center gap-2 px-3 py-1 bg-green-600 text-white rounded text-sm" onclick="reviewIncident(${row['N']}, 'approve')">Approve</button>
                        <button class="inline-flex items-center gap-2 px-3 py-1 bg-red-600 text-white rounded text-sm" onclick="reviewIncident(${row['N']}, 'reject')">Reject</button>
                    </div>
                `;
            }

            html += `<td>
                <div><span class="status-pill ${statusClass}">${reviewStatus || 'pending'}</span></div>
                <div class="text-muted" style="font-size:12px;">${attachmentsCount} file(s)</div>
                ${viewButton}
            </td>
            <td>
                ${reviewControls || '<span class="text-muted">-</span>'}
            </td>
            <td>
                <button class="inline-flex items-center gap-2 px-3 py-1 bg-blue-800 text-white rounded text-sm" onclick="openIncidentModal(${index})">Edit</button>
                <button class="inline-flex items-center gap-2 px-3 py-1 bg-red-600 text-white rounded text-sm" onclick="deleteRow(${index})">Delete</button>
            </td></tr>`;
        });

        tbody.innerHTML = html;
        updatePaginationControls();
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
        currentIncidentIndex = (index === 0 || index) ? index : null;
        const isEdit = currentIncidentIndex !== null;
        const row = isEdit ? tableData[currentIncidentIndex] : null;

        incidentFieldMap.forEach(field => {
            const input = document.getElementById(field.id);
            if (!input) {
                return;
            }
            input.value = row ? (row[field.column] || '') : '';
        });

        const provinceInput = document.getElementById('incidentProvince');
        const municipalityInput = document.getElementById('incidentMunicipality');
        const provinceValue = row ? (row['Province'] || '') : '';
        const municipalityValue = row ? (row['Municipality/City where Incidence Occurred'] || '') : '';
        if (provinceInput) {
            ensureProvinceOption(provinceValue);
            provinceInput.value = provinceValue;
        }
        if (municipalityInput) {
            updateIncidentMunicipalities(municipalityValue);
        }

        if (incidentModalLabel) {
            incidentModalLabel.textContent = isEdit ? `Edit Incident ${row && row['N'] ? '#' + row['N'] : ''}` : 'Add Incident';
        }

        if (incidentSaveButton) {
            incidentSaveButton.textContent = isEdit ? 'Update Incident' : 'Add Incident';
        }

        if (incidentAttachmentsInput) {
            incidentAttachmentsInput.value = '';
        }

        updateIncidentAttachmentSection(row);

        if (typeof bootstrap === 'undefined') {
            showModal('incidentModal');
            return;
        }

        const modalElement = document.getElementById('incidentModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        modal.show();
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

        incidentAttachmentHint.textContent = canUpload
            ? 'Upload photos or documents for this incident.'
            : 'Save the incident to the database before uploading attachments.';

        if (incidentAttachmentStatus) {
            const count = row && row.attachments_count ? row.attachments_count : 0;
            const status = row && row.review_status ? row.review_status : 'pending';
            incidentAttachmentStatus.textContent = hasIncident ? `${count} file(s), status: ${status}` : '';
        }

        if (incidentAttachmentsInput) {
            incidentAttachmentsInput.disabled = false;
        }

        if (incidentUploadButton) {
            incidentUploadButton.disabled = false;
        }
    }

    async function saveIncidentFromModal() {
        const genderField = incidentFieldMap.find(field => field.column === 'Gender of the Person');
        if (genderField) {
            const genderInput = document.getElementById(genderField.id);
            if (genderInput && genderInput.value) {
                const gender = genderInput.value;
                if (gender !== 'Male' && gender !== 'Female' && gender !== '') {
                    alert('Sex must be "Male" or "Female" only!');
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

        if (isEdit && row['N']) {
            // Map frontend fields to backend/database fields
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
                    // add other fields as needed
                };
            }
            try {
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
                    alert(result.message || 'Update failed.');
                    return;
                }
            } catch (error) {
                alert('Update failed: ' + error.message);
                return;
            }
        }

        if (!isEdit) {
            tableData.push(row);
            currentPage = getTotalPages();
        } else {
            tableData[currentIncidentIndex] = row;
        }

        renderTable();

        if (typeof bootstrap !== 'undefined') {
            const modalElement = document.getElementById('incidentModal');
            const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
            modal.hide();
        }
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

    function updateIncidentMunicipalities(preferredValue) {
        const provinceInput = document.getElementById('incidentProvince');
        const municipalityInput = document.getElementById('incidentMunicipality');
        if (!provinceInput || !municipalityInput) {
            return;
        }

        const province = provinceInput.value;
        const selectedValue = preferredValue !== undefined ? preferredValue : municipalityInput.value;
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

        if (selectedValue && (!municipalities[province] || !municipalities[province].includes(selectedValue))) {
            const option = document.createElement('option');
            option.value = selectedValue;
            option.textContent = selectedValue;
            option.selected = true;
            municipalityInput.appendChild(option);
        }
    }

    const incidentProvinceSelect = document.getElementById('incidentProvince');
    if (incidentProvinceSelect) {
        incidentProvinceSelect.addEventListener('change', () => updateIncidentMunicipalities());
        incidentProvinceSelect.addEventListener('input', () => updateIncidentMunicipalities());
    }

    function normalizeRow(row) {
        const normalized = { N: row['N'] ?? '', _local: true };
        editableColumns.forEach(col => {
            normalized[col] = '';
        });

        Object.keys(row).forEach(key => {
            const target = resolveHeader(key);
            if (editableColumns.includes(target)) {
                normalized[target] = row[key] ?? '';
            }
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
        return {
            'N': row.n || '',
            'Month of Incident': row.month_of_incident || '',
            'Year of Incident': row.year_of_incident || '',
            'Province': row.province || '',
            'Municipality/City where Incidence Occurred': row.municipality || '',
            'Name of Victim': row.name_of_victim || '',
            'Location Category': row.location_category || '',
            'Age of the Person': row.age || '',
            'Gender of the Person': row.gender || '',
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
        const dataRows = rows.slice(headerRowIndex + 1);
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
                record[header] = row[colIndex] ?? '';
            });

            if (isInstructionRow(record)) {
                return;
            }

            const hasValue = Object.values(record).some(value => String(value).trim() !== '');
            if (hasValue) {
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
        el.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
    }

    function hideModal(id) {
        const el = document.getElementById(id);
        if (!el) return;
        el.classList.add('hidden');
        el.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');
    }

    function openSaveModal() {
        if (typeof bootstrap !== 'undefined') {
            const modalElement = document.getElementById('saveConfirmModal');
            const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
            modal.show();
            return;
        }

        showModal('saveConfirmModal');
    }

    function confirmSaveToDatabase() {
        if (typeof bootstrap !== 'undefined') {
            const modalElement = document.getElementById('saveConfirmModal');
            const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
            modal.hide();
        } else {
            hideModal('saveConfirmModal');
        }

        saveToDatabase();
    }

    function showImportSuccess(count) {
        const messageEl = document.getElementById('importSuccessMessage');
        if (messageEl) messageEl.textContent = `Successfully imported ${count} rows.`;

        if (typeof bootstrap === 'undefined') {
            showModal('importSuccessModal');
            return;
        }

        const modalElement = document.getElementById('importSuccessModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        modal.show();
    }

    function showSaveResult(message) {
        const messageEl = document.getElementById('saveResultMessage');
        if (messageEl) messageEl.textContent = message;

        if (typeof bootstrap === 'undefined') {
            const modalId = 'saveResultModal';
            showModal(modalId);
            const el = document.getElementById(modalId);
            el.addEventListener('click', () => window.location.reload(), { once: true });
            return;
        }

        const modalElement = document.getElementById('saveResultModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        modalElement.addEventListener('hidden.bs.modal', () => {
            window.location.reload();
        }, { once: true });
        modal.show();
    }

    function showAttachmentModal(message) {
        const messageEl = document.getElementById('attachmentModalMessage');
        if (messageEl) {
            messageEl.textContent = message;
        }

        if (typeof bootstrap === 'undefined') {
            showModal('attachmentModal');
            return;
        }

        const modalElement = document.getElementById('attachmentModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        modal.show();
    }

    async function openAttachmentViewer(incidentN) {
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
            const listEl = document.getElementById('attachmentList');
            const previewEl = document.getElementById('attachmentPreview');
            const downloadEl = document.getElementById('attachmentDownload');

            listEl.innerHTML = '';
            previewEl.innerHTML = '<span class="text-muted">Select a file to preview.</span>';
            downloadEl.href = '#';

            if (attachments.length === 0) {
                listEl.innerHTML = '<div class="text-muted">No attachments uploaded yet.</div>';
            } else {
                attachments.forEach((item, index) => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'list-group-item list-group-item-action';
                    button.textContent = `${item.original_name} (${item.file_kind})`;
                    button.addEventListener('click', () => {
                        const viewUrl = `${attachmentViewUrl}/${item.id}`;
                        const downloadUrl = `${attachmentDownloadUrl}/${item.id}`;
                        downloadEl.href = downloadUrl;

                        const mimeType = item.mime_type || '';
                        const isImage = mimeType.startsWith('image/');
                        const isPdf = mimeType === 'application/pdf';
                        const isOffice = mimeType === 'application/msword'
                            || mimeType === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';

                        if (isImage) {
                            previewEl.innerHTML = `<img src="${viewUrl}" alt="${item.original_name}" style="max-width:100%; max-height:520px;" />`;
                        } else if (isPdf || isOffice) {
                            previewEl.innerHTML = `<iframe src="${viewUrl}" style="width:100%; height:520px; border:0;" title="${item.original_name}"></iframe>`;
                        } else {
                            previewEl.innerHTML = `
                                <div class="text-center p-4">
                                    <div class="text-muted mb-2">Preview not available for this file type.</div>
                                    <a class="inline-flex items-center gap-2 px-3 py-1.5 border border-blue-600 text-blue-600 rounded-md text-sm" href="${downloadUrl}" target="_blank" rel="noopener">Download ${item.original_name}</a>
                                </div>
                            `;
                        }

                        listEl.querySelectorAll('.list-group-item').forEach(el => el.classList.remove('active'));
                        button.classList.add('active');
                    });

                    if (index === 0) {
                        setTimeout(() => button.click(), 0);
                    }

                    listEl.appendChild(button);
                });
            }

            if (typeof bootstrap === 'undefined') {
                showModal('attachmentViewerModal');
                return;
            }

            const modalElement = document.getElementById('attachmentViewerModal');
            const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
            modal.show();
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

        if (typeof bootstrap === 'undefined') {
            showModal('reviewConfirmModal');
            return;
        }

        const modalElement = document.getElementById('reviewConfirmModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        modal.show();
    }

    function submitReview() {
        const { incidentN, action } = pendingReview;
        if (!incidentN || !action) {
            return;
        }

        if (typeof bootstrap !== 'undefined') {
            const modalElement = document.getElementById('reviewConfirmModal');
            const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
            modal.hide();
        } else {
            hideModal('reviewConfirmModal');
        }

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
        if (currentIncidentIndex === null) {
            showAttachmentModal('Please save the incident before uploading attachments.');
            return;
        }

        const row = tableData[currentIncidentIndex];
        const incidentN = row ? row['N'] : null;
        if (!incidentN || row._local) {
            showAttachmentModal('Please save the incident to the database before uploading attachments.');
            return;
        }

        if (!incidentAttachmentsInput || !incidentAttachmentsInput.files || incidentAttachmentsInput.files.length === 0) {
            showAttachmentModal('Please select at least one file to upload.');
            return;
        }

        const formData = new FormData();
        formData.append('incident_n', incidentN);
        Array.from(incidentAttachmentsInput.files).forEach((file) => {
            formData.append('attachments[]', file);
        });

        try {
            const response = await fetch(attachmentUploadUrl, {
                method: 'POST',
                body: formData,
            });
            const result = await response.json();
            if (!response.ok) {
                showAttachmentModal(result.message || 'Upload failed.');
                return;
            }

            row.attachments_count = result.attachments_count || row.attachments_count || 0;
            row.review_status = 'pending';
            incidentAttachmentsInput.value = '';
            renderTable();
            updateIncidentAttachmentSection(row);
            showAttachmentModal('Attachments uploaded successfully.');
        } catch (error) {
            showAttachmentModal('Upload failed: ' + error.message);
        }
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

            tableData = tableData.map((row) => {
                if (row['N'] === incidentN) {
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

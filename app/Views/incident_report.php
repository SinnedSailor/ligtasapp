
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

<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Data Management</h4>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <div class="file-input-wrapper">
                        <input type="file" id="excelFile" accept=".xlsx,.xls,.csv" class="d-none" />
                        <button id="importButton" class="btn btn-primary btn-sm" onclick="document.getElementById('excelFile').click()" style="<?= $hasInitialRows ? 'display:none;' : '' ?>">
                            <i class="ti-import"></i> Import Excel File
                        </button>
                    </div>
                    <span class="file-name" id="fileName" style="<?= $hasInitialRows ? 'display:none;' : '' ?>">No file selected</span>
                </div>

                <?php if ($isLgu): ?>
                    <div class="alert alert-info">
                        Upload at least one photo or document per incident. Attachments are reviewed by Province users.
                    </div>
                <?php endif; ?>

                <div class="d-flex flex-wrap gap-2 mb-3">
                    <?php if ($isLgu || $isAdmin): ?>
                        <button class="btn btn-primary btn-sm" onclick="openIncidentModal()">
                            <i class="ti-plus"></i> Add Incident
                        </button>
                    <?php endif; ?>
                    <button id="saveButton" class="btn btn-success btn-sm" onclick="openSaveModal()" style="<?= $hasInitialRows ? 'display:none;' : '' ?>">
                        <i class="ti-save"></i> Save to Database
                    </button>
                    <button id="generateReportButton" class="btn btn-info btn-sm" onclick="downloadIncidentReport()">
                        <i class="ti-bar-chart"></i> Generate Report
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>N</th>
                                <th>Month of Incident</th>
                                <th>Year of Incident</th>
                                <th>Province</th>
                                <th>Municipality/City where Incidence Occurred</th>
                                <th class="col-primary">Name of Victim</th>
                                <th class="col-secondary">Location Category</th>
                                <th class="col-secondary">Age of the Person</th>
                                <th class="col-secondary">Sex</th>
                                <th class="col-secondary">Occasion</th>
                                <th class="col-tertiary">Other Factors</th>
                                <th class="col-tertiary">Person's Residence</th>
                                <th class="col-tertiary">Occupation of the Victim</th>
                                <th class="col-tertiary">Remarks</th>
                                <th>Attachments</th>
                                <th>Review</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <tr>
                                <td colspan="16" class="empty-message">No data yet. Upload an Excel file or click "Add Incident" to add data.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="pagination-controls" id="paginationControls" style="display:none;">
                    <button class="btn btn-outline-secondary btn-sm" id="prevPage" type="button">Prev</button>
                    <button class="btn btn-outline-secondary btn-sm" id="nextPage" type="button">Next</button>
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

<div class="modal fade" id="saveConfirmModal" tabindex="-1" aria-labelledby="saveConfirmLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="saveConfirmLabel">Confirm Save</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                This will save the current imported rows into the database. Continue?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="confirmSaveToDatabase()">Yes, Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="importSuccessModal" tabindex="-1" aria-labelledby="importSuccessLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importSuccessLabel">Import Complete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="importSuccessMessage">Import completed.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="saveResultModal" tabindex="-1" aria-labelledby="saveResultLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="saveResultLabel">Save Result</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="saveResultMessage">Save completed.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="attachmentModal" tabindex="-1" aria-labelledby="attachmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="attachmentModalLabel">Upload Notice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="attachmentModalMessage">Please upload at least one file.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="reviewConfirmModal" tabindex="-1" aria-labelledby="reviewConfirmLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reviewConfirmLabel">Confirm Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="reviewConfirmMessage">Are you sure you want to update this incident?</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="reviewConfirmAction">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="incidentModal" tabindex="-1" aria-labelledby="incidentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="incidentModalLabel">Add Incident</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="incidentForm">
                    <div id="incidentFormAlert" class="alert d-none" role="alert" style="display:none;"></div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" for="incidentMonth">Month of Incident</label>
                            <select class="form-select form-select-sm" id="incidentMonth" style="background-color: #fff; color: #222; font-weight: 500;">
                                <option value="" disabled selected style="color: #888; font-weight: 400;">Select month</option>
                                <?php for ($month = 1; $month <= 12; $month++): ?>
                                    <option value="<?= $month ?>" style="color: #222; font-weight: 500;">
                                        <?= $month ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="incidentYear">Year of Incident</label>
                            <input type="text" class="form-control form-control-sm" id="incidentYear" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="incidentProvince">Province</label>
                            <select class="form-select form-select-sm" id="incidentProvince" style="background-color: #fff; color: #222; font-weight: 500;">
                                <option value="" disabled selected style="color: #888; font-weight: 400;">Select province</option>
                                <?php foreach ($provinceList as $province): ?>
                                    <option value="<?= esc($province) ?>" style="color: #222; font-weight: 500;">
                                        <?= esc($province) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="incidentMunicipality">Municipality/City where Incidence Occurred</label>
                            <select class="form-select form-select-sm" id="incidentMunicipality" style="background-color: #fff; color: #222; font-weight: 500;">
                                <option value="" disabled selected style="color: #888; font-weight: 400;">Select municipality</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="incidentVictim">Name of Victim</label>
                            <input type="text" class="form-control form-control-sm" id="incidentVictim" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="incidentLocation">Location Category</label>
                            <input type="text" class="form-control form-control-sm" id="incidentLocation" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="incidentAge">Age of the Person</label>
                            <input type="text" class="form-control form-control-sm" id="incidentAge" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="incidentGender">Sex</label>
                            <select class="form-select form-select-sm" id="incidentGender" style="background-color: #fff; color: #222; font-weight: 500;">
                                <option value="" disabled selected style="color: #888; font-weight: 400;">Select sex</option>
                                <option value="Male" style="color: #222; font-weight: 500;">Male</option>
                                <option value="Female" style="color: #222; font-weight: 500;">Female</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="incidentOccasion">Occasion</label>
                            <input type="text" class="form-control form-control-sm" id="incidentOccasion" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="incidentFactors">Other Factors</label>
                            <input type="text" class="form-control form-control-sm" id="incidentFactors" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="incidentResidence">Person's Residence</label>
                            <input type="text" class="form-control form-control-sm" id="incidentResidence" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="incidentOccupation">Occupation of the Victim</label>
                            <input type="text" class="form-control form-control-sm" id="incidentOccupation" />
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="incidentRemarks">Remarks</label>
                            <input type="text" class="form-control form-control-sm" id="incidentRemarks" />
                        </div>
                    </div>

                    <?php if ($isLgu || $isAdmin): ?>
                        <div class="border-top pt-3 mt-3" id="incidentAttachmentSection">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="mb-0">Attachments</h6>
                                <span class="text-muted" id="incidentAttachmentStatus" style="font-size: 0.85rem;"></span>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="small fw-semibold mb-1">Pictures</div>
                                    <div class="text-muted mb-2" style="font-size:0.85rem;">JPEG / PNG only. You can queue multiple photos.</div>
                                    <div class="d-flex gap-2">
                                        <input type="file" id="incidentPicturesInput" class="form-control form-control-sm" accept=".jpg,.jpeg,.png" multiple />
                                        <!-- kept the main upload button as the primary action -->
                                    </div>
                                    <div id="incidentUploadFileListPictures" class="mt-3"></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="small fw-semibold mb-1">Documents</div>
                                    <div class="text-muted mb-2" style="font-size:0.85rem;">PDF / DOC / DOCX only. You can queue multiple documents.</div>
                                    <div class="d-flex gap-2">
                                        <input type="file" id="incidentDocumentsInput" class="form-control form-control-sm" accept=".pdf,.doc,.docx" multiple />
                                    </div>
                                    <div id="incidentUploadFileListDocuments" class="mt-3"></div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between mt-3 mb-1">
                                <div class="small text-muted" id="incidentAttachmentHint" style="font-size: 0.85rem;"></div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearUploadFileList()">Clear</button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="incidentUploadButton" onclick="uploadIncidentAttachments()">Upload Attachments</button>
                                </div>
                            </div>

                            <!-- Combined per-file upload list (renders pictures/documents grouped) -->
                            <div id="incidentUploadFileList" class="mt-2"></div>

                            <!-- Upload progress (reflects combined progress of both types) -->
                            <div id="incidentUploadProgressContainer" class="w-100 mt-2 d-none">
                                <div class="progress" style="height:14px;">
                                    <div id="incidentUploadProgressBar" class="progress-bar" role="progressbar" style="width:0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                                </div>
                                <div id="incidentUploadProgressText" class="small text-muted mt-1"></div>
                            </div>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="incidentSaveButton" onclick="saveIncidentFromModal()">Save Incident</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="attachmentViewerModal" tabindex="-1" aria-labelledby="attachmentViewerLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="attachmentViewerLabel">Incident Attachments</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="attachmentList" class="list-group mb-3"></div>
                <div id="attachmentPreview" class="border rounded" style="min-height: 320px; background: #f8fafc; display: flex; align-items: center; justify-content: center;">
                    <span class="text-muted">Select a file to preview.</span>
                </div>
            </div>
            <div class="modal-footer">
                <a class="btn btn-outline-primary" id="attachmentDownload" href="#" target="_blank" rel="noopener">Download</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
    const incidentPicturesInput = document.getElementById('incidentPicturesInput');
    const incidentDocumentsInput = document.getElementById('incidentDocumentsInput');
    const incidentUploadButton = document.getElementById('incidentUploadButton');
    let currentPage = 1;
    let pageSize = 10;
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
        const container = document.getElementById('incidentUploadFileList');
        const picsContainer = document.getElementById('incidentUploadFileListPictures');
        const docsContainer = document.getElementById('incidentUploadFileListDocuments');
        if (!container || !picsContainer || !docsContainer) return;

        if (!currentUploadFiles || currentUploadFiles.length === 0) {
            container.innerHTML = '';
            picsContainer.innerHTML = '';
            docsContainer.innerHTML = '';
            return;
        }

        const uploadingCount = currentUploadFiles.filter(f => f.status === 'uploading' || f.status === 'queued').length;
        const globalHeader = `
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="small text-muted">${currentUploadFiles.length} file(s) queued</div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-danger" id="incidentUploadCancelAllBtn" onclick="cancelAllUploads()" ${uploadingCount === 0 ? 'disabled' : ''}>Cancel all</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearUploadFileList()">Clear</button>
                </div>
            </div>
        `;

        function renderListFor(filterFn) {
            const list = currentUploadFiles.filter(filterFn);
            if (!list.length) return '<div class="empty-message">No files</div>';

            return list.map(f => {
                const progress = Math.max(0, Math.min(100, Math.round(f.progress || 0)));
                const statusLabel = (f.status === 'queued') ? 'Queued' : (f.status === 'uploading' ? 'Uploading' : (f.status === 'success' ? 'Uploaded' : (f.status === 'error' ? 'Error' : 'Cancelled')));
                const showCancel = (f.status === 'uploading' || f.status === 'queued');
                const showRetry = (f.status === 'error' || f.status === 'cancelled');

                return `
                    <div class="upload-file-item d-flex align-items-center justify-content-between mb-2" data-file-id="${f.id}">
                        <div style="flex:1; min-width:0;">
                            <div class="fw-semibold small text-truncate">${escapeHtml(f.file.name)} <small class="text-muted">(${formatBytes(f.file.size)})</small></div>
                            <div class="progress mt-1" style="height:8px;">
                                <div class="progress-bar" role="progressbar" style="width:${progress}%" aria-valuenow="${progress}" aria-valuemin="0" aria-valuemax="100" data-file-progress-id="${f.id}">${progress}%</div>
                            </div>
                        </div>
                        <div class="ms-3 text-end" style="min-width:140px;">
                            <div class="small" data-file-status-id="${f.id}">${statusLabel}</div>
                            <div class="btn-group btn-group-sm mt-1">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="cancelSingleUpload('${f.id}')" ${showCancel ? '' : 'style="display:none;"'}>Cancel</button>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="retrySingleUpload('${f.id}')" ${showRetry ? '' : 'style="display:none;"'}>Retry</button>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        picsContainer.innerHTML = renderListFor(f => f.type === 'photo');
        docsContainer.innerHTML = renderListFor(f => f.type === 'document');
        container.innerHTML = globalHeader + '<div class="row"><div class="col-md-6">' + (picsContainer.innerHTML) + '</div><div class="col-md-6">' + (docsContainer.innerHTML) + '</div></div>';
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
    }

    function updateOverallProgress() {
        if (!currentUploadFiles || currentUploadFiles.length === 0) {
            hideUploadProgress();
            return;
        }
        const total = currentUploadFiles.length;
        const sum = currentUploadFiles.reduce((acc, f) => acc + (f.progress || (f.status === 'success' ? 100 : 0)), 0);
        const avg = Math.round(sum / total);
        showUploadProgress(avg);
    }

    // small helpers
    function escapeHtml(s) { return String(s).replace(/[&<>\"'`]/g, function (m) { return ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','`':'&#96;' })[m]; }); }
    function formatBytes(bytes) { if (bytes === 0) return '0 B'; const sizes = ['B','KB','MB','GB','TB']; const i = Math.floor(Math.log(bytes)/Math.log(1024)); return (bytes/Math.pow(1024,i)).toFixed(1) + ' ' + sizes[i]; }

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

        // Clear inline incident-form messages when user edits fields inside the modal
        incidentFieldMap.forEach(field => {
            const el = document.getElementById(field.id);
            if (el) {
                el.addEventListener('input', hideIncidentFormMessage);
                el.addEventListener('change', hideIncidentFormMessage);
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

            const viewButton = `<button class="btn btn-sm btn-outline-secondary mt-1" onclick="openAttachmentViewer(${row['N']})">View</button>`;

            let reviewControls = '';
            if (canReviewIncidents) {
                reviewControls = `
                    <div class="d-flex gap-1 flex-wrap">
                        <button class="btn btn-sm btn-success" onclick="reviewIncident(${row['N']}, 'approve')">Approve</button>
                        <button class="btn btn-sm btn-danger" onclick="reviewIncident(${row['N']}, 'reject')">Reject</button>
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
                <button class="btn btn-inverse-primary btn-sm" onclick="openIncidentModal(${index})">Edit</button>
                <button class="btn btn-inverse-danger btn-sm" onclick="deleteRow(${index})">Delete</button>
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

        if (typeof bootstrap === 'undefined') {
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

        // Show hint for saved incidents, or indicate pre-saved uploaded files when adding
        if (canUpload) {
            incidentAttachmentHint.textContent = 'Upload photos or documents for this incident.';
        } else if (currentAttachmentSession && currentUploadFiles && currentUploadFiles.length > 0) {
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

        if (isEdit && row['N']) {
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
                    showIncidentFormMessage(result.message || 'Update failed.', 'danger');
                    return;
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

    function openSaveModal() {
        if (typeof bootstrap === 'undefined') {
            if (confirm('This will save the current imported rows into the database. Continue?')) {
                confirmSaveToDatabase();
            }
            return;
        }

        const modalElement = document.getElementById('saveConfirmModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        modal.show();
    }

    function confirmSaveToDatabase() {
        if (typeof bootstrap !== 'undefined') {
            const modalElement = document.getElementById('saveConfirmModal');
            const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
            modal.hide();
        }

        saveToDatabase();
    }

    function showImportSuccess(count) {
        const messageEl = document.getElementById('importSuccessMessage');
        if (messageEl) {
            messageEl.textContent = `Successfully imported ${count} rows.`;
        }

        if (typeof bootstrap === 'undefined') {
            alert(`Successfully imported ${count} rows.`);
            return;
        }

        const modalElement = document.getElementById('importSuccessModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        modal.show();
    }

    function showSaveResult(message) {
        const messageEl = document.getElementById('saveResultMessage');
        if (messageEl) {
            messageEl.textContent = message;
        }

        if (typeof bootstrap === 'undefined') {
            alert(message);
            window.location.reload();
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
            alert(message);
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
                                    <a class="btn btn-outline-primary" href="${downloadUrl}" target="_blank" rel="noopener">Download ${item.original_name}</a>
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
            if (confirm(`Are you sure you want to ${actionLabel} this incident?`)) {
                submitReview();
            }
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
            if (!isPreSave && row) {
                row.review_status = 'pending';
                renderTable();
                updateIncidentAttachmentSection(row);
            }
            showAttachmentModal('Attachments uploaded.');
            showIncidentFormMessage('Attachments uploaded.', 'success');
            setTimeout(hideIncidentFormMessage, 2000);
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


<?= $this->extend('layouts/main_tailwind') ?>

<?= $this->section('pageStyles') ?>
<style>
    .docs-header {
        margin-bottom: 1.5rem;
    }

    .documents-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }

    .document-card {
        background: #fff;
        border-radius: 10px;
        border: 1px solid #e9ecef;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .document-card h5 {
        color: #002C76;
        margin-bottom: 10px;
    }

    .document-card p.text-muted {
        min-height: 44px;
        margin-bottom: 0;
    }

    .document-card .form-control {
        margin-top: 10px;
    }

    .document-card .form-control[type="file"] {
        text-align: center;
    }

    .submit-section {
        margin-top: 20px;
        text-align: right;
    }

    .submit-btn {
        background: #002C76;
        color: #fff;
        border: none;
        padding: 10px 24px;
        border-radius: 6px;
        font-weight: 600;
    }

    .submit-btn[disabled] {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .status-badge {
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .status-pending {
        background: rgba(245, 158, 11, 0.15);
        color: #B45309;
    }

    .status-approved {
        background: rgba(16, 185, 129, 0.15);
        color: #047857;
    }

    .status-rejected {
        background: rgba(220, 38, 38, 0.12);
        color: #B91C1C;
    }

    .section-title {
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 12px;
    }

    .table-docs th {
        color: #002C76;
        font-weight: 600;
    }

    .table-docs td {
        vertical-align: middle;
    }

    .doc-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .preview-modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.6);
        z-index: 3000;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .preview-modal.show {
        display: flex;
    }

    .preview-card {
        background: #fff;
        border-radius: 12px;
        width: min(1000px, 100%);
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.25);
    }

    .preview-header {
        padding: 16px 20px;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .preview-title {
        font-weight: 600;
        color: #0f172a;
    }

    .preview-body {
        padding: 0;
        background: #f8fafc;
        flex: 1;
    }

    .preview-body iframe {
        width: 100%;
        height: 65vh;
        border: none;
    }

    .preview-footer {
        padding: 16px 20px;
        border-top: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
    }

    .close-preview {
        background: #e2e8f0;
        border: none;
        border-radius: 8px;
        padding: 6px 12px;
        font-weight: 600;
        color: #0f172a;
    }

    html.modal-open,
    body.modal-open {
        height: 100%;
        overflow: hidden;
    }

    .preview-modal {
        overscroll-behavior: contain;
    }

    .doc-filter-card {
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: 16px;
        background: #fff;
        margin-top: 20px;
    }

    .filter-row {
        display: grid;
        gap: 16px;
    }

    .filter-group {
        display: grid;
        gap: 8px;
    }

    .filter-label {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .filter-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .filter-btn {
        border: 1px solid #cbd5f5;
        background: #f8fafc;
        color: #0f172a;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .filter-btn:hover {
        border-color: #002C76;
        color: #002C76;
    }

    .filter-btn.active {
        background: #002C76;
        border-color: #002C76;
        color: #fff;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-wrapper" style="margin: 24px;">
    <?php
    $roleName = $roleName ?? (session()->get('role_name') ?? 'No Role');
    $isAdmin = $isAdmin ?? (bool) session()->get('is_admin');
    $isLgu = $roleName === 'LGU';
    $isProvince = $roleName === 'PROVINCE';
    $isFocal = $roleName === 'FOCAL';
    $canReview = $isProvince || $isAdmin;
    $canViewApproved = $isFocal || $isAdmin;
    $docLabels = [
        'ordinance' => 'Ordinance',
        'pops' => 'POPS Plan',
        'budget' => 'Annual Budget Report',
    ];
    $statusClasses = [
        'pending' => 'status-pending',
        'approved' => 'status-approved',
        'rejected' => 'status-rejected',
    ];
?>

    

    <?php if (session()->getFlashdata('success')): ?>
        <div class="mb-4 rounded-md bg-green-50 border border-green-200 text-green-700 px-4 py-3">
            <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>


    <?php if (session()->getFlashdata('error')): ?>
        <div class="mb-4 rounded-md bg-red-50 border border-red-200 text-red-700 px-4 py-3">
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>


    <?php if ($isLgu || $canReview || $canViewApproved): ?>
        <div class="doc-upload-card mt-6">
            <div class="section-title">Upload Documents</div>
            <form action="<?= base_url('/documents/upload') ?>" method="post" enctype="multipart/form-data" id="documentUploadForm">
                <div class="documents-grid">
                    <div class="document-card">
                        <h5> Ordinance</h5>
                        <p class="text-muted">Local ordinance documents related to water safety regulations.</p>
                        <input type="file" name="ordinance_files[]" class="w-full rounded-md border border-gray-200 p-2 text-sm text-gray-700" multiple accept=".pdf,.doc,.docx">
                    </div>

                    <div class="document-card">
                        <h5> POPS Plan</h5>
                        <p class="text-muted">Peace and Order and Public Safety Plan documents.</p>
                        <input type="file" name="pops_files[]" class="w-full rounded-md border border-gray-200 p-2 text-sm text-gray-700" multiple accept=".pdf,.doc,.docx">
                    </div>

                    <div class="document-card">
                        <h5> Annual Budget Report</h5>
                        <p class="text-muted">Annual budget reports for LIGTAS (Local Incident Gathering and Tracking for Aquatic Safety).</p>
                        <input type="file" name="budget_files[]" class="w-full rounded-md border border-gray-200 p-2 text-sm text-gray-700" multiple accept=".pdf,.doc,.docx">
                    </div>
                </div>

                <div class="submit-section">
                    <button class="submit-btn" type="submit" id="submitDocumentsButton">
                        Submit Documents
                    </button>
                </div>
            </form>
        </div>

    <?php elseif ($canReview || $canViewApproved): ?>
        <div class="mb-4 rounded-md bg-blue-50 border border-blue-100 text-blue-700 px-4 py-3">
            Documents are managed by role-based review. Only approved documents are visible to FOCAL users.
        </div>
    <?php else: ?>
        <div class="mb-4 rounded-md bg-yellow-50 border border-yellow-100 text-yellow-800 px-4 py-3">
            Your account does not have access to the document workflow. Please contact an administrator.
        </div>
    <?php endif; ?>

    <?php if ($isLgu): ?>
        <div class="bg-white rounded-md shadow p-4 mt-4">
            <div class="section-title">My Submissions</div>
            <?php if (!empty($myDocuments)): ?>
                <div class="table-responsive">
                    <table class="w-full table-docs">
                        <div class="table-filter-card mb-2 p-2 bg-slate-50 rounded-md flex flex-wrap gap-2">
                            <select class="filter-input-my filter-doc-type rounded-md border border-gray-200 px-2 py-1 text-sm">
                                <option value="">All Types</option>
                                <option value="ordinance">Ordinance</option>
                                <option value="pops">POPS</option>
                                <option value="budget">Budget</option>
                            </select>
                            
                            <input type="date" class="filter-input-my filter-date rounded-md border border-gray-200 px-2 py-1 text-sm" />
                        </div>
                        <thead>
                            <tr>
                                <th>Document Type</th>
                                <th>File</th>
                                <th>Status</th>
                                <th>Uploaded</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($myDocuments as $doc): ?>
                                <tr class="doc-filter-row" data-doc-type="<?= esc($doc['doc_type']) ?>" data-province="<?= esc($doc['province'] ?? '') ?>" data-municipality="<?= esc($doc['municipality'] ?? '') ?>">
                                    <td><?= esc($docLabels[$doc['doc_type']] ?? strtoupper($doc['doc_type'])) ?></td>
                                    <td><?= esc($doc['original_name']) ?></td>
                                    <td>
                                        <span class="status-badge <?= esc($statusClasses[$doc['status']] ?? 'status-pending') ?>">
                                            <?= esc(strtoupper($doc['status'])) ?>
                                        </span>
                                    </td>
                                    <td><?= esc(date('M d, Y', strtotime($doc['created_at']))) ?></td>
                                    <td>
                                        <div class="doc-actions">
                                            <button class="inline-flex items-center gap-2 px-3 py-1 border rounded text-sm text-gray-700 bg-white hover:bg-gray-50 doc-preview" type="button" data-doc-id="<?= $doc['id'] ?>" data-doc-name="<?= esc($doc['original_name']) ?>">
                                                View
                                            </button>
                                            <a class="inline-flex items-center gap-2 px-3 py-1 border border-blue-200 rounded text-sm text-blue-700 hover:bg-blue-50" href="<?= base_url('/documents/download/' . $doc['id']) ?>">
                                                Download
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted mb-0">No submissions yet.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($canReview): ?>
        <div class="bg-white rounded-md shadow p-4 mt-4">
            <div class="section-title">Pending Documents for Review</div>
            <?php if (!empty($pendingDocuments)): ?>
                <div class="table-responsive">
                    <table class="w-full table-docs">
                        <div class="table-filter-card mb-2 p-2 bg-slate-50 rounded-md flex flex-wrap gap-2">
                            <select class="filter-input-pending filter-doc-type rounded-md border border-gray-200 px-2 py-1 text-sm">
                                <option value="">All Types</option>
                                <option value="ordinance">Ordinance</option>
                                <option value="pops">POPS</option>
                                <option value="budget">Budget</option>
                            </select>
                            <select class="filter-input-pending filter-province rounded-md border border-gray-200 px-2 py-1 text-sm">
                                <option value="">All Provinces</option>
                                <?php foreach (($provinces ?? []) as $province): ?>
                                    <option value="<?= esc($province) ?>"><?= esc($province) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select class="filter-input-pending filter-municipality rounded-md border border-gray-200 px-2 py-1 text-sm">
                                <option value="">All Municipalities</option>
                            </select>
                            <input type="date" class="filter-input-pending filter-date rounded-md border border-gray-200 px-2 py-1 text-sm" />
                            // ...existing code...
                        </div>
                        <thead>
                            <tr>
                                <th>Document Type</th>
                                <th>File</th>
                                <th>Submitted By</th>
                                <th>Location</th>
                                <th>Uploaded</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingDocuments as $doc): ?>
                                <tr class="doc-filter-row" data-doc-type="<?= esc($doc['doc_type']) ?>" data-province="<?= esc($doc['province'] ?? '') ?>" data-municipality="<?= esc($doc['municipality'] ?? '') ?>">
                                    <td><?= esc($docLabels[$doc['doc_type']] ?? strtoupper($doc['doc_type'])) ?></td>
                                    <td><?= esc($doc['original_name']) ?></td>
                                    <td><?= esc(trim($doc['first_name'] . ' ' . $doc['last_name'])) ?></td>
                                    <td><?= esc(trim(($doc['municipality'] ?? '') . ', ' . ($doc['province'] ?? ''), ' ,')) ?></td>
                                    <td><?= esc(date('M d, Y', strtotime($doc['created_at']))) ?></td>
                                    <td>
                                        <div class="doc-actions">
                                            <button class="inline-flex items-center gap-2 px-2 py-1 border border-slate-200 rounded text-sm text-slate-700 hover:bg-slate-50 doc-preview" type="button" data-doc-id="<?= $doc['id'] ?>" data-doc-name="<?= esc($doc['original_name']) ?>">
                                                View
                                            </button>
                                            <a class="inline-flex items-center gap-2 px-3 py-1 border border-blue-200 rounded text-sm text-blue-700 hover:bg-blue-50" href="<?= base_url('/documents/download/' . $doc['id']) ?>">Download</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted mb-0">No pending documents right now.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($canViewApproved): ?>
        <div class="bg-white rounded-md shadow p-4 mt-4">
            <div class="section-title">Approved Documents</div>
            <?php if (!empty($approvedDocuments)): ?>
                <div class="table-responsive">
                    <table class="w-full table-docs">
                        <div class="table-filter-card mb-2 p-2 bg-slate-50 rounded-md flex flex-wrap gap-2">
                            <select class="filter-input-approved filter-doc-type rounded-md border border-gray-200 px-2 py-1 text-sm">
                                <option value="">All Types</option>
                                <option value="ordinance">Ordinance</option>
                                <option value="pops">POPS</option>
                                <option value="budget">Budget</option>
                            </select>
                            <select class="filter-input-approved filter-province rounded-md border border-gray-200 px-2 py-1 text-sm">
                                <option value="">All Provinces</option>
                                <?php foreach (($provinces ?? []) as $province): ?>
                                    <option value="<?= esc($province) ?>"><?= esc($province) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select class="filter-input-approved filter-municipality rounded-md border border-gray-200 px-2 py-1 text-sm">
                                <option value="">All Municipalities</option>
                            </select>
                            <input type="date" class="filter-input-approved filter-date rounded-md border border-gray-200 px-2 py-1 text-sm" />
                            // ...existing code...
                        </div>
                        <thead>
                            <tr>
                                <th>Document Type</th>
                                <th>File</th>
                                <th>Submitted By</th>
                                <th>Location</th>
                                <th>Approved</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($approvedDocuments as $doc): ?>
                                <tr class="doc-filter-row" data-doc-type="<?= esc($doc['doc_type']) ?>" data-province="<?= esc($doc['province'] ?? '') ?>" data-municipality="<?= esc($doc['municipality'] ?? '') ?>">
                                    <td><?= esc($docLabels[$doc['doc_type']] ?? strtoupper($doc['doc_type'])) ?></td>
                                    <td><?= esc($doc['original_name']) ?></td>
                                    <td><?= esc(trim($doc['first_name'] . ' ' . $doc['last_name'])) ?></td>
                                    <td><?= esc(trim(($doc['municipality'] ?? '') . ', ' . ($doc['province'] ?? ''), ' ,')) ?></td>
                                    <td><?= esc($doc['reviewed_at'] ? date('M d, Y', strtotime($doc['reviewed_at'])) : '-') ?></td>
                                    <td>
                                        <div class="doc-actions">
                                            <button class="inline-flex items-center gap-2 px-3 py-1 border rounded text-sm text-gray-700 bg-white hover:bg-gray-50 doc-preview" type="button" data-doc-id="<?= $doc['id'] ?>" data-doc-name="<?= esc($doc['original_name']) ?>">
                                                View
                                            </button>
                                            <a class="inline-flex items-center gap-2 px-3 py-1 border border-blue-200 rounded text-sm text-blue-700 hover:bg-blue-50" href="<?= base_url('/documents/download/' . $doc['id']) ?>">Download</a>
                                                Download
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted mb-0">No approved documents yet.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="preview-modal" id="documentPreviewModal" aria-hidden="true">
        <div class="preview-card">
            <div class="preview-header">
                <div class="preview-title" id="previewTitle">Document Preview</div>
                <button class="close-preview" type="button" id="closePreview">Close</button>
            </div>
            <div class="preview-body">
                <iframe id="previewFrame" src="" title="Document Preview"></iframe>
            </div>
            <div class="preview-footer">
                <div class="doc-actions">
                    <a class="inline-flex items-center gap-2 px-3 py-1 border border-blue-200 rounded text-sm text-blue-700 hover:bg-blue-50" id="previewDownload" href="#">Download</a>
                </div>
                <?php if ($canReview): ?>
                    <div class="doc-actions">
                        <form method="post" id="previewApproveForm" action="#">
                            <button class="inline-flex items-center gap-2 px-3 py-1 bg-green-600 text-white rounded text-sm" type="submit">Approve</button>
                        </form>
                        <form method="post" id="previewRejectForm" action="#">
                            <button class="inline-flex items-center gap-2 px-3 py-1 bg-red-600 text-white rounded text-sm" type="submit">Reject</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script>
    const municipalityMap = <?= json_encode($municipalities ?? []) ?>;
    const previewModal = document.getElementById('documentPreviewModal');
    const previewFrame = document.getElementById('previewFrame');
    const previewTitle = document.getElementById('previewTitle');
    const previewDownload = document.getElementById('previewDownload');
    const closePreview = document.getElementById('closePreview');
    const approveForm = document.getElementById('previewApproveForm');
    const rejectForm = document.getElementById('previewRejectForm');

    document.querySelectorAll('.doc-preview').forEach((button) => {
        button.addEventListener('click', () => {
            const docId = button.getAttribute('data-doc-id');
            const docName = button.getAttribute('data-doc-name');
            previewTitle.textContent = docName || 'Document Preview';
            previewFrame.src = `<?= base_url('/documents/view') ?>/${docId}`;
            previewDownload.href = `<?= base_url('/documents/download') ?>/${docId}`;

            if (approveForm) {
                approveForm.action = `<?= base_url('/documents/approve') ?>/${docId}`;
            }
            if (rejectForm) {
                rejectForm.action = `<?= base_url('/documents/reject') ?>/${docId}`;
            }

            previewModal.classList.add('show');
            previewModal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('modal-open');
            document.documentElement.classList.add('modal-open');
        });
    });

    function closeModal() {
        previewModal.classList.remove('show');
        previewModal.setAttribute('aria-hidden', 'true');
        previewFrame.src = '';
        document.body.classList.remove('modal-open');
        document.documentElement.classList.remove('modal-open');
    }

    closePreview.addEventListener('click', closeModal);
    previewModal.addEventListener('click', (event) => {
        if (event.target === previewModal) {
            closeModal();
        }
    });

    const uploadForm = document.getElementById('documentUploadForm');
    const submitButton = document.getElementById('submitDocumentsButton');

    if (uploadForm && submitButton) {
        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (submitButton.disabled) {
                return;
            }
            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to upload/submit the documents?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, submit',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitButton.disabled = true;
                    submitButton.textContent = 'Submitting...';
                    uploadForm.setAttribute('aria-busy', 'true');
                    uploadForm.submit();
                }
            });
        });
    }

    // --- Per-column filter logic for each table ---
    function setupColumnFilters(tableClass, docTypeIdx, statusIdx, dateIdx, provinceIdx, municipalityIdx, locationIdx) {
        const table = document.querySelector(tableClass);
        if (!table) return;
        const filterDocType = table.querySelector('.filter-doc-type');
        const filterStatus = table.querySelector('.filter-status');
        const filterDate = table.querySelector('.filter-date');
        const filterProvince = table.querySelector('.filter-province');
        const filterMunicipality = table.querySelector('.filter-municipality');
        const rows = table.querySelectorAll('tbody tr.doc-filter-row');

        function updateMunicipalityOptions() {
            if (!filterProvince || !filterMunicipality) return;
            const selectedProvince = filterProvince.value;
            filterMunicipality.innerHTML = '<option value="">All Municipalities</option>';
            if (selectedProvince && municipalityMap[selectedProvince]) {
                municipalityMap[selectedProvince].forEach(function(muni) {
                    const opt = document.createElement('option');
                    opt.value = muni;
                    opt.textContent = muni;
                    filterMunicipality.appendChild(opt);
                });
            }
        }

        if (filterProvince && filterMunicipality) {
            filterProvince.addEventListener('change', function() {
                updateMunicipalityOptions();
                filterRows();
            });
            filterMunicipality.addEventListener('change', filterRows);
            updateMunicipalityOptions();
        }

        if (filterDocType) filterDocType.addEventListener('change', filterRows);
        if (filterStatus) filterStatus.addEventListener('change', filterRows);
        if (filterDate) filterDate.addEventListener('change', filterRows);

        function filterRows() {
            const docTypeVal = filterDocType ? filterDocType.value : '';
            const statusVal = filterStatus ? filterStatus.value : '';
            const dateVal = filterDate ? filterDate.value : '';
            const provinceVal = filterProvince ? filterProvince.value : '';
            const municipalityVal = filterMunicipality ? filterMunicipality.value : '';

            rows.forEach(function(row) {
                let matches = true;
                const tds = row.querySelectorAll('td');
                // Document Type
                if (docTypeVal && tds[docTypeIdx] && tds[docTypeIdx].textContent.toLowerCase() !== docTypeVal) matches = false;
                // Status
                if (statusVal && tds[statusIdx] && !tds[statusIdx].textContent.toLowerCase().includes(statusVal)) matches = false;
                // Date
                if (dateVal && tds[dateIdx]) {
                    const rowDate = new Date(tds[dateIdx].textContent);
                    const filterDate = new Date(dateVal);
                    if (rowDate.toDateString() !== filterDate.toDateString()) matches = false;
                }
                // Province/Municipality (for tables with location column)
                if (provinceIdx !== null && tds[locationIdx]) {
                    const loc = tds[locationIdx].textContent;
                    if (provinceVal && !loc.includes(provinceVal)) matches = false;
                    if (municipalityVal && !loc.includes(municipalityVal)) matches = false;
                }
                row.style.display = matches ? '' : 'none';
            });
        }
    }

    // My Submissions: [docType, file, status, uploaded, action]
    setupColumnFilters('.table-docs:nth-of-type(1)', 0, 2, 3, null, null, null);
    // Pending Documents: [docType, file, submitted by, location, uploaded, actions]
    setupColumnFilters('.table-docs:nth-of-type(2)', 0, 4, 4, 3, 4, 3);
    // Approved Documents: [docType, file, submitted by, location, approved, action]
    setupColumnFilters('.table-docs:nth-of-type(3)', 0, 4, 4, 3, 4, 3);
</script>
<?= $this->endSection() ?>
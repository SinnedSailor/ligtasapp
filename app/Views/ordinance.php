<?= $this->extend('layouts/staradmin') ?>

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
        color: #09637E;
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
        background: #09637E;
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
        color: #0B5FB3;
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
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
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

<div class="page-header docs-header">
    <h3 class="page-title">
        <?= $isFocal ? 'View Documents' : ($isProvince ? 'Review Documents' : 'Upload Documents') ?>
    </h3>
    <p class="text-muted">Ordinance, POPS Plan, and Annual Budget Report workflow.</p>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
        <?= esc(session()->getFlashdata('success')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
        <?= esc(session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<?php if ($isLgu): ?>
    <div class="alert alert-info">
        Upload PDF, DOC, or DOCX files (Max 10MB each). Submitted documents are reviewed by the Province role.
    </div>

    <form action="<?= base_url('/documents/upload') ?>" method="post" enctype="multipart/form-data" id="documentUploadForm">
        <div class="documents-grid">
            <div class="document-card">
                <h5><i class="ti-files"></i> Ordinance</h5>
                <p class="text-muted">Local ordinance documents related to water safety regulations.</p>
                <input type="file" name="ordinance_files[]" class="form-control" multiple accept=".pdf,.doc,.docx">
            </div>

            <div class="document-card">
                <h5><i class="ti-shield"></i> POPS Plan</h5>
                <p class="text-muted">Peace and Order and Public Safety Plan documents.</p>
                <input type="file" name="pops_files[]" class="form-control" multiple accept=".pdf,.doc,.docx">
            </div>

            <div class="document-card">
                <h5><i class="ti-wallet"></i> Annual Budget Report</h5>
                <p class="text-muted">Annual budget reports for water safety programs.</p>
                <input type="file" name="budget_files[]" class="form-control" multiple accept=".pdf,.doc,.docx">
            </div>
        </div>

        <div class="submit-section">
            <button class="submit-btn" type="submit" id="submitDocumentsButton">
                <i class="ti-check"></i>
                Submit Documents
            </button>
        </div>
    </form>
<?php elseif ($canReview || $canViewApproved): ?>
    <div class="alert alert-info">
        Documents are managed by role-based review. Only approved documents are visible to FOCAL users.
    </div>
<?php else: ?>
    <div class="alert alert-warning">
        Your account does not have access to the document workflow. Please contact an administrator.
    </div>
<?php endif; ?>

<?php if ($isLgu): ?>
    <div class="card mt-4">
        <div class="card-body">
            <div class="section-title">My Submissions</div>
            <?php if (!empty($myDocuments)): ?>
                <div class="table-responsive">
                    <table class="table table-hover table-docs">
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
                                <tr>
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
                                            <button class="btn btn-sm btn-outline-secondary doc-preview" type="button" data-doc-id="<?= $doc['id'] ?>" data-doc-name="<?= esc($doc['original_name']) ?>">
                                                View
                                            </button>
                                            <a class="btn btn-sm btn-outline-primary" href="<?= base_url('/documents/download/' . $doc['id']) ?>">
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
    </div>
<?php endif; ?>

<?php if ($canReview): ?>
    <div class="card mt-4">
        <div class="card-body">
            <div class="section-title">Pending Documents for Review</div>
            <?php if (!empty($pendingDocuments)): ?>
                <div class="table-responsive">
                    <table class="table table-hover table-docs">
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
                                <tr>
                                    <td><?= esc($docLabels[$doc['doc_type']] ?? strtoupper($doc['doc_type'])) ?></td>
                                    <td><?= esc($doc['original_name']) ?></td>
                                    <td><?= esc(trim($doc['first_name'] . ' ' . $doc['last_name'])) ?></td>
                                    <td><?= esc(trim(($doc['municipality'] ?? '') . ', ' . ($doc['province'] ?? ''), ' ,')) ?></td>
                                    <td><?= esc(date('M d, Y', strtotime($doc['created_at']))) ?></td>
                                    <td>
                                        <div class="doc-actions">
                                            <button class="btn btn-sm btn-outline-secondary doc-preview" type="button" data-doc-id="<?= $doc['id'] ?>" data-doc-name="<?= esc($doc['original_name']) ?>">
                                                View
                                            </button>
                                            <a class="btn btn-sm btn-outline-primary" href="<?= base_url('/documents/download/' . $doc['id']) ?>">Download</a>
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
    </div>
<?php endif; ?>

<?php if ($canViewApproved): ?>
    <div class="card mt-4">
        <div class="card-body">
            <div class="section-title">Approved Documents</div>
            <?php if (!empty($approvedDocuments)): ?>
                <div class="table-responsive">
                    <table class="table table-hover table-docs">
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
                                <tr>
                                    <td><?= esc($docLabels[$doc['doc_type']] ?? strtoupper($doc['doc_type'])) ?></td>
                                    <td><?= esc($doc['original_name']) ?></td>
                                    <td><?= esc(trim($doc['first_name'] . ' ' . $doc['last_name'])) ?></td>
                                    <td><?= esc(trim(($doc['municipality'] ?? '') . ', ' . ($doc['province'] ?? ''), ' ,')) ?></td>
                                    <td><?= esc($doc['reviewed_at'] ? date('M d, Y', strtotime($doc['reviewed_at'])) : '-') ?></td>
                                    <td>
                                        <div class="doc-actions">
                                            <button class="btn btn-sm btn-outline-secondary doc-preview" type="button" data-doc-id="<?= $doc['id'] ?>" data-doc-name="<?= esc($doc['original_name']) ?>">
                                                View
                                            </button>
                                            <a class="btn btn-sm btn-outline-primary" href="<?= base_url('/documents/download/' . $doc['id']) ?>">
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
                <a class="btn btn-sm btn-outline-primary" id="previewDownload" href="#">Download</a>
            </div>
            <?php if ($canReview): ?>
                <div class="doc-actions">
                    <form method="post" id="previewApproveForm" action="#">
                        <button class="btn btn-sm btn-success" type="submit">Approve</button>
                    </form>
                    <form method="post" id="previewRejectForm" action="#">
                        <button class="btn btn-sm btn-danger" type="submit">Reject</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script>
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
        uploadForm.addEventListener('submit', () => {
            if (submitButton.disabled) {
                return;
            }

            submitButton.disabled = true;
            submitButton.textContent = 'Submitting...';
            uploadForm.setAttribute('aria-busy', 'true');
        });
    }
</script>
<?= $this->endSection() ?>

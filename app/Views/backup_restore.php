<?= $this->extend('layouts/staradmin') ?>

<?= $this->section('pageStyles') ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        width: 100%;
    }

    .page-header {
        margin-bottom: 30px;
    }

    .page-header h1 {
        color: #09637E;
        font-size: 28px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .page-header p {
        color: #666;
        font-size: 14px;
    }

    .backup-restore-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(420px, 1fr));
        gap: 30px;
        margin-bottom: 30px;
    }

    .action-card {
        background: #fff;
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s;
    }

    .action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
    }

    .action-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #09637E;
    }

    .action-icon {
        font-size: 36px;
        color: #09637E;
    }

    .action-title h2 {
        color: #09637E;
        font-size: 22px;
        margin-bottom: 5px;
    }

    .action-title p {
        color: #666;
        font-size: 13px;
    }

    .action-content {
        margin-bottom: 25px;
    }

    .action-content p {
        color: #666;
        line-height: 1.6;
        margin-bottom: 15px;
    }

    .action-features {
        list-style: none;
        margin-bottom: 20px;
        padding-left: 0;
    }

    .action-features li {
        color: #666;
        padding: 8px 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .action-features li i {
        color: #28a745;
        font-size: 18px;
    }

    .action-button {
        width: 100%;
        padding: 15px;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .btn-backup {
        background: #28a745;
        color: #fff;
    }

    .btn-backup:hover {
        background: #218838;
        transform: translateY(-2px);
    }

    .btn-restore {
        background: #09637E;
        color: #fff;
    }

    .btn-restore:hover {
        background: #075267;
        transform: translateY(-2px);
    }

    .backup-history {
        background: #fff;
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .backup-history h3 {
        color: #09637E;
        font-size: 20px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .history-table {
        width: 100%;
        border-collapse: collapse;
    }

    .history-table thead {
        background: #f8f9fa;
    }

    .history-table th {
        padding: 12px;
        text-align: left;
        color: #333;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }

    .history-table td {
        padding: 12px;
        border-bottom: 1px solid #dee2e6;
        color: #666;
    }

    .history-table tr:hover {
        background: #f8f9fa;
    }

    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-success {
        background: #d4edda;
        color: #155724;
    }

    .history-actions {
        display: flex;
        gap: 5px;
    }

    .action-btn-small {
        padding: 6px 12px;
        border: none;
        border-radius: 4px;
        font-size: 12px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-download {
        background: #17a2b8;
        color: #fff;
    }

    .btn-download:hover {
        background: #138496;
    }

    .btn-delete {
        background: #C9282D;
        color: #fff;
    }

    .btn-delete:hover {
        background: #a01f24;
    }

    .alert {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .alert-info {
        background: #d1ecf1;
        color: #0c5460;
        border: 1px solid #bee5eb;
    }

    .alert-warning {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="page-header">
        <h1>
            <i class="bi bi-cloud-arrow-up-fill"></i>
            Backup & Restore
        </h1>
        <p>Protect your data with automated backups and restore when needed</p>
    </div>

    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i>
        <span>Regular backups ensure your data is safe. We recommend backing up before making significant changes.</span>
    </div>

    <div class="backup-restore-grid">
        <div class="action-card">
            <div class="action-header">
                <div class="action-icon">
                    <i class="bi bi-cloud-arrow-up"></i>
                </div>
                <div class="action-title">
                    <h2>Create Backup</h2>
                    <p>Save current system state</p>
                </div>
            </div>
            <div class="action-content">
                <p>Create a complete backup of your system data including:</p>
                <ul class="action-features">
                    <li><i class="bi bi-check-circle-fill"></i> User accounts and profiles</li>
                    <li><i class="bi bi-check-circle-fill"></i> Incident reports and data</li>
                    <li><i class="bi bi-check-circle-fill"></i> Ordinance documents</li>
                    <li><i class="bi bi-check-circle-fill"></i> POPS Plan files</li>
                    <li><i class="bi bi-check-circle-fill"></i> System configurations</li>
                </ul>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    <span>Backup may take several minutes depending on data size.</span>
                </div>
            </div>
            <button class="action-button btn-backup" onclick="createBackup()">
                <i class="bi bi-cloud-arrow-up"></i>
                Create Backup Now
            </button>
        </div>

        <div class="action-card">
            <div class="action-header">
                <div class="action-icon">
                    <i class="bi bi-cloud-arrow-down"></i>
                </div>
                <div class="action-title">
                    <h2>Restore Data</h2>
                    <p>Recover from previous backup</p>
                </div>
            </div>
            <div class="action-content">
                <p>Restore your system to a previous state from a backup file:</p>
                <ul class="action-features">
                    <li><i class="bi bi-check-circle-fill"></i> Select from backup history</li>
                    <li><i class="bi bi-check-circle-fill"></i> Preview backup contents</li>
                    <li><i class="bi bi-check-circle-fill"></i> Partial or full restore</li>
                    <li><i class="bi bi-check-circle-fill"></i> Verify data integrity</li>
                    <li><i class="bi bi-check-circle-fill"></i> Rollback option available</li>
                </ul>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    <span>Restoring will replace current data. Create a backup first!</span>
                </div>
            </div>
            <button class="action-button btn-restore" onclick="document.getElementById('restoreFile').click()">
                <i class="bi bi-cloud-arrow-down"></i>
                Upload Backup File
            </button>
            <input type="file" id="restoreFile" style="display: none;" accept=".zip,.backup" onchange="handleRestore(event)">
        </div>
    </div>

    <div class="backup-history">
        <h3>
            <i class="bi bi-clock-history"></i>
            Backup History
        </h3>
        <table class="history-table">
            <thead>
                <tr>
                    <th>Date & Time</th>
                    <th>File Name</th>
                    <th>Size</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="backupHistoryBody">
                <tr>
                    <td>2026-02-03 10:30 AM</td>
                    <td>IWAS_backup_20260203_1030.zip</td>
                    <td>45.2 MB</td>
                    <td><span class="status-badge status-success">Success</span></td>
                    <td class="history-actions">
                        <button class="action-btn-small btn-download" onclick="downloadBackup('20260203_1030')">
                            <i class="bi bi-download"></i> Download
                        </button>
                        <button class="action-btn-small btn-restore" onclick="restoreBackup('20260203_1030')">
                            <i class="bi bi-arrow-counterclockwise"></i> Restore
                        </button>
                        <button class="action-btn-small btn-delete" onclick="deleteBackup('20260203_1030')">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </td>
                </tr>
                <tr>
                    <td>2026-02-02 03:15 PM</td>
                    <td>IWAS_backup_20260202_1515.zip</td>
                    <td>43.8 MB</td>
                    <td><span class="status-badge status-success">Success</span></td>
                    <td class="history-actions">
                        <button class="action-btn-small btn-download" onclick="downloadBackup('20260202_1515')">
                            <i class="bi bi-download"></i> Download
                        </button>
                        <button class="action-btn-small btn-restore" onclick="restoreBackup('20260202_1515')">
                            <i class="bi bi-arrow-counterclockwise"></i> Restore
                        </button>
                        <button class="action-btn-small btn-delete" onclick="deleteBackup('20260202_1515')">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </td>
                </tr>
                <tr>
                    <td>2026-02-01 09:00 AM</td>
                    <td>IWAS_backup_20260201_0900.zip</td>
                    <td>42.5 MB</td>
                    <td><span class="status-badge status-success">Success</span></td>
                    <td class="history-actions">
                        <button class="action-btn-small btn-download" onclick="downloadBackup('20260201_0900')">
                            <i class="bi bi-download"></i> Download
                        </button>
                        <button class="action-btn-small btn-restore" onclick="restoreBackup('20260201_0900')">
                            <i class="bi bi-arrow-counterclockwise"></i> Restore
                        </button>
                        <button class="action-btn-small btn-delete" onclick="deleteBackup('20260201_0900')">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script>
    function createBackup() {
        if (confirm('Create a backup of all system data? This may take several minutes.')) {
            alert('Creating backup... Please wait.');

            setTimeout(() => {
                alert('Backup created successfully!\n\nFile: IWAS_backup_' + new Date().toISOString().slice(0, 10).replace(/-/g, '') + '.zip\nSize: ~45 MB');
            }, 2000);
        }
    }

    function handleRestore(event) {
        const file = event.target.files[0];
        if (!file) return;

        if (confirm(`Restore from backup file: ${file.name}?\n\nWARNING: This will replace all current data!`)) {
            alert('Restoring from backup... Please wait.\n\nDo not close this window.');

            setTimeout(() => {
                alert('Backup restored successfully!\n\nThe system will now reload.');
            }, 3000);
        }
    }

    function downloadBackup(id) {
        alert('Downloading backup file: IWAS_backup_' + id + '.zip\n\nIn production, this would download the actual backup file.');
    }

    function restoreBackup(id) {
        if (confirm('Restore from this backup?\n\nWARNING: Current data will be replaced!')) {
            alert('Restoring backup from ' + id + '...\n\nPlease wait.');

            setTimeout(() => {
                alert('Backup restored successfully!');
            }, 2000);
        }
    }

    function deleteBackup(id) {
        if (confirm('Delete this backup?\n\nThis action cannot be undone.')) {
            alert('Backup deleted: IWAS_backup_' + id + '.zip');
        }
    }
</script>
<?= $this->endSection() ?>

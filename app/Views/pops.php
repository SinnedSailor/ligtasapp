<?= $this->extend('layouts/staradmin') ?>

<?= $this->section('pageStyles') ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    .container {
        max-width: 100%;
        margin: 0;
        padding: 30px;
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

    .upload-card {
        background: #fff;
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }

    .upload-area {
        border: 2px dashed #FFD700;
        border-radius: 10px;
        padding: 40px;
        text-align: center;
        background: #f8f9fa;
        transition: all 0.3s;
        cursor: pointer;
    }

    .upload-area:hover {
        background: #fffaf0;
        border-color: #FFD700;
    }

    .upload-area.dragover {
        background: #fffef5;
        border-color: #FFD700;
    }

    .upload-icon {
        font-size: 48px;
        color: #09637E;
        margin-bottom: 20px;
    }

    .upload-text {
        color: #333;
        font-size: 18px;
        margin-bottom: 10px;
        font-weight: 600;
    }

    .upload-hint {
        color: #666;
        font-size: 14px;
    }

    .file-input {
        display: none;
    }

    .upload-btn {
        background: #09637E;
        color: #fff;
        border: none;
        padding: 12px 30px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        font-weight: 600;
        margin-top: 20px;
        transition: all 0.3s;
    }

    .upload-btn:hover {
        background: #075267;
        transform: translateY(-2px);
    }

    .files-list {
        margin-top: 30px;
    }

    .files-list h3 {
        color: #333;
        font-size: 20px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .file-item {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 15px 20px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.3s;
    }

    .file-item:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border-color: #09637E;
    }

    .file-info {
        display: flex;
        align-items: center;
        gap: 15px;
        flex: 1;
    }

    .file-icon {
        font-size: 24px;
        color: #09637E;
    }

    .file-details {
        flex: 1;
    }

    .file-name {
        color: #333;
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 5px;
    }

    .file-meta {
        color: #999;
        font-size: 12px;
    }

    .file-actions {
        display: flex;
        gap: 10px;
    }

    .action-btn {
        padding: 8px 16px;
        border-radius: 5px;
        border: none;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-delete {
        background: #C9282D;
        color: #fff;
    }

    .btn-delete:hover {
        background: #9d1f22;
    }

    .submit-btn {
        background: #09637E;
        color: #fff;
        border: none;
        padding: 15px 40px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 18px;
        font-weight: 600;
        margin-top: 20px;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .submit-btn:hover {
        background: #075267;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(9, 99, 126, 0.4);
    }

    .submit-btn:disabled {
        background: #ccc;
        cursor: not-allowed;
        transform: none;
    }

    .alert {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .alert-warning {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeeba;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="page-header">
        <h1>
            <i class="bi bi-shield-check"></i>
            POPS Plan Upload
        </h1>
        <p>Peace and Order and Public Safety Plan - Upload required POPS documents</p>
    </div>

    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle"></i>
        <span>Upload your POPS Plan documents (PDF, DOC, DOCX) to complete the submission.</span>
    </div>

    <div class="upload-card">
        <div class="upload-area" id="uploadArea" onclick="document.getElementById('fileInput').click()">
            <div class="upload-icon">
                <i class="bi bi-cloud-arrow-up"></i>
            </div>
            <div class="upload-text">Click to upload or drag and drop</div>
            <div class="upload-hint">Supported formats: PDF, DOC, DOCX (Max 10MB each)</div>
            <input type="file" id="fileInput" class="file-input" multiple accept=".pdf,.doc,.docx" onchange="handleFiles(this.files)">
                        <button type="button" class="btn btn-outline-secondary btn-sm mt-2" onclick="document.getElementById('fileInput').value = null">Clear</button>
            <button class="upload-btn">Browse Files</button>
        </div>

        <div class="files-list" id="filesList" style="display: none;">
            <h3>
                <i class="bi bi-files"></i>
                Uploaded Documents (<span id="fileCount">0</span>)
            </h3>
            <div id="filesContainer"></div>

            <button class="submit-btn" id="submitBtn" disabled onclick="submitDocuments()">
                <i class="bi bi-check-circle"></i>
                <span>Submit POPS Plan</span>
            </button>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script>
    let uploadedFiles = [];

    const uploadArea = document.getElementById('uploadArea');

    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        handleFiles(e.dataTransfer.files);
    });

    function handleFiles(files) {
        const validTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        const maxSize = 10 * 1024 * 1024;

        for (let file of files) {
            if (!validTypes.includes(file.type)) {
                alert(`${file.name} is not a supported file type. Please upload PDF, DOC, or DOCX files.`);
                continue;
            }

            if (file.size > maxSize) {
                alert(`${file.name} exceeds the 10MB size limit.`);
                continue;
            }

            uploadedFiles.push({
                name: file.name,
                size: formatFileSize(file.size),
                date: new Date().toLocaleDateString(),
                file: file
            });
        }

        renderFiles();
    }

    function formatFileSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(2) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }

    function renderFiles() {
        const container = document.getElementById('filesContainer');
        const filesList = document.getElementById('filesList');
        const fileCount = document.getElementById('fileCount');
        const submitBtn = document.getElementById('submitBtn');

        if (uploadedFiles.length > 0) {
            filesList.style.display = 'block';
            fileCount.textContent = uploadedFiles.length;
            submitBtn.disabled = false;

            container.innerHTML = uploadedFiles.map((file, index) => `
                <div class="file-item">
                    <div class="file-info">
                        <div class="file-icon">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <div class="file-details">
                            <div class="file-name">${file.name}</div>
                            <div class="file-meta">${file.size} • Uploaded on ${file.date}</div>
                        </div>
                    </div>
                    <div class="file-actions">
                        <button class="action-btn btn-delete" onclick="deleteFile(${index})">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </div>
                </div>
            `).join('');
        } else {
            filesList.style.display = 'none';
        }
    }

    function deleteFile(index) {
        uploadedFiles.splice(index, 1);
        renderFiles();
    }

    function submitDocuments() {
        if (uploadedFiles.length > 0) {
            alert('POPS Plan documents submitted successfully!');
            window.location.href = '<?= base_url('/dashboard') ?>';
        }
    }
</script>
<?= $this->endSection() ?>

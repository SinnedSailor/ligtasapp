<?= $this->extend('layouts/staradmin') ?>
<?= $this->section('pageStyles') ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container max-w-5xl mx-auto px-4 py-12">
    <div class="mb-6">
        <div class="flex items-center gap-4 mb-2">
            <i class="bi bi-shield-check text-3xl text-blue-900"></i>
            <h1 class="text-3xl font-extrabold text-blue-900">POPS Plan Upload</h1>
        </div>
        <p class="text-gray-500 text-sm">Peace and Order and Public Safety Plan - Upload required POPS documents</p>
    </div>

    <div class="alert alert-warning flex items-center gap-2 rounded-lg border border-yellow-200 bg-yellow-50 text-yellow-800 mb-6 p-4">
        <i class="bi bi-exclamation-triangle text-xl"></i>
        <span>Upload your POPS Plan documents (PDF, DOC, DOCX) to complete the submission.</span>
    </div>

    <div class="upload-card bg-white rounded-2xl shadow p-8 mb-6">
        <div class="upload-area border-2 border-dashed border-yellow-400 rounded-2xl p-10 text-center bg-gray-100 hover:bg-yellow-50 transition cursor-pointer mb-6" id="uploadArea" onclick="document.getElementById('fileInput').click()">
            <div class="upload-icon text-5xl text-blue-900 mb-4">
                <i class="bi bi-cloud-arrow-up"></i>
            </div>
            <div class="upload-text text-lg font-semibold text-gray-700 mb-2">Click to upload or drag and drop</div>
            <div class="upload-hint text-gray-500 text-sm mb-2">Supported formats: PDF, DOC, DOCX (Max 10MB each)</div>
            <input type="file" id="fileInput" class="file-input hidden" multiple accept=".pdf,.doc,.docx" onchange="handleFiles(this.files)">
            <button class="upload-btn btn-primary mt-4" type="button">Browse Files</button>
        </div>

        <div class="files-list mt-8" id="filesList" style="display: none;">
            <h3 class="flex items-center gap-2 text-lg font-semibold mb-4">
                <i class="bi bi-files"></i>
                Uploaded Documents (<span id="fileCount">0</span>)
            </h3>
            <div id="filesContainer"></div>
            <button class="submit-btn btn-primary flex items-center gap-2 mt-6" id="submitBtn" disabled onclick="submitDocuments()">
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
    uploadArea.classList.add('bg-yellow-50', 'border-yellow-400');
});
uploadArea.addEventListener('dragleave', () => {
    uploadArea.classList.remove('bg-yellow-50', 'border-yellow-400');
});
uploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadArea.classList.remove('bg-yellow-50', 'border-yellow-400');
    handleFiles(e.dataTransfer.files);
});

function handleFiles(files) {
    const validTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    const maxSize = 10 * 1024 * 1024; // 10MB
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
            <div class="file-item flex items-center justify-between bg-white border border-gray-200 rounded-md p-4 mb-2 shadow-sm hover:shadow transition">
                <div class="file-info flex items-center gap-3 flex-1">
                    <div class="file-icon text-2xl text-blue-900"><i class="bi bi-file-earmark-text"></i></div>
                    <div class="file-details flex-1">
                        <div class="file-name font-semibold text-gray-700 text-sm mb-1">${file.name}</div>
                        <div class="file-meta text-gray-400 text-xs">${file.size} • Uploaded on ${file.date}</div>
                    </div>
                </div>
                <div class="file-actions flex gap-2">
                    <button class="action-btn btn-delete bg-red-700 text-white rounded px-4 py-2 hover:bg-red-800 transition" onclick="deleteFile(${index})">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                </div>
            </div>
        `).join('');
    } else {
        filesList.style.display = 'none';
        container.innerHTML = '';
        submitBtn.disabled = true;
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

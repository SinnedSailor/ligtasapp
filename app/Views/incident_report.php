<?= $this->extend('layouts/staradmin') ?>
<?php $hasInitialRows = !empty($initialRows); ?>

<?= $this->section('pageStyles') ?>
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
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header">
    <h3 class="page-title">Incident Report</h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Incident Report</li>
        </ol>
    </nav>
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

                <div class="d-flex flex-wrap gap-2 mb-3">
                    <button class="btn btn-primary btn-sm" onclick="addNewRow()">
                        <i class="ti-plus"></i> Add New Row
                    </button>
                    <button id="saveButton" class="btn btn-success btn-sm" onclick="openSaveModal()" style="<?= $hasInitialRows ? 'display:none;' : '' ?>">
                        <i class="ti-save"></i> Save to Database
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
                                <th class="col-secondary">Gender of the Person</th>
                                <th class="col-secondary">Occasion</th>
                                <th class="col-tertiary">Other Factors</th>
                                <th class="col-tertiary">Person's Residence</th>
                                <th class="col-tertiary">Occupation of the Victim</th>
                                <th class="col-tertiary">Remarks</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <tr>
                                <td colspan="16" class="empty-message">No data yet. Upload an Excel file or click "Add New Row" to add data.</td>
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

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script src="https://cdn.sheetjs.com/xlsx-0.18.5/package/dist/xlsx.full.min.js"></script>
<script>
    let tableData = [];
    const importUrl = "<?= base_url('/incident-report/import') ?>";
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
    let currentPage = 1;
    let pageSize = 10;

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
            setImportSaveState(true);
        } else {
            setImportSaveState(false);
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
            tbody.innerHTML = '<tr><td colspan="16" class="empty-message">No data yet. Upload an Excel file or click "Add New Row" to add data.</td></tr>';
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

            html += `<tr id="row-${index}">`;
            columns.forEach(col => {
                const value = row[col] || '';
                const colClass = getColumnClass(col);

                if (col === 'N') {
                    html += `<td class="${colClass}"><span>${row['N']}</span></td>`;
                } else if (col === 'Gender of the Person') {
                    const safeKey = col.replace(/[ '\/]/g, '_');
                    html += `<td class="${colClass}">
                        <span class="display-${index}-${safeKey}">${String(value).toUpperCase()}</span>
                        <select class="input-${index}-${safeKey} table-input" style="display:none;">
                            <option value="">Select</option>
                            <option value="m" ${String(value).toLowerCase() === 'm' ? 'selected' : ''}>M</option>
                            <option value="f" ${String(value).toLowerCase() === 'f' ? 'selected' : ''}>F</option>
                        </select>
                    </td>`;
                } else {
                    const safeKey = col.replace(/[ '\/]/g, '_');
                    html += `<td class="${colClass}">
                        <span class="display-${index}-${safeKey}">${value}</span>
                        <input type="text" class="input-${index}-${safeKey} table-input" value="${value}" style="display:none;" />
                    </td>`;
                }
            });
            html += `<td>
                <button class="btn btn-inverse-primary btn-sm" onclick="editRow(${index})" id="edit-${index}">Edit</button>
                <button class="btn btn-inverse-success btn-sm" onclick="saveRow(${index})" style="display:none;" id="save-${index}">Save</button>
                <button class="btn btn-inverse-secondary btn-sm" onclick="cancelEdit(${index})" style="display:none;" id="cancel-${index}">Cancel</button>
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

    function addNewRow() {
        const newRow = { 'N': tableData.length + 1 };
        editableColumns.forEach(col => {
            newRow[col] = '';
        });
        tableData.push(newRow);
        currentPage = getTotalPages();
        renderTable();

        const newIndex = tableData.length - 1;
        setTimeout(() => editRow(newIndex), 100);
    }

    function editRow(index) {
        editableColumns.forEach(col => {
            const colKey = col.replace(/[ '\/]/g, '_');
            const display = document.querySelector(`.display-${index}-${colKey}`);
            const input = document.querySelector(`.input-${index}-${colKey}`);
            if (display) display.style.display = 'none';
            if (input) input.style.display = 'block';
        });

        document.getElementById(`edit-${index}`).style.display = 'none';
        document.getElementById(`save-${index}`).style.display = 'inline-block';
        document.getElementById(`cancel-${index}`).style.display = 'inline-block';
    }

    function saveRow(index) {
        const genderKey = 'Gender of the Person'.replace(/[ '\/]/g, '_');
        const genderInput = document.querySelector(`.input-${index}-${genderKey}`);
        if (genderInput && genderInput.value) {
            const gender = genderInput.value.toLowerCase();
            if (gender !== 'm' && gender !== 'f' && gender !== '') {
                alert('Gender must be "m" or "f" only!');
                return;
            }
        }

        editableColumns.forEach(col => {
            const colKey = col.replace(/[ '\/]/g, '_');
            const input = document.querySelector(`.input-${index}-${colKey}`);
            if (input) {
                tableData[index][col] = input.value;
            }
        });

        tableData[index]['N'] = tableData[index]['N'] || (index + 1);

        renderTable();
        alert('Row saved successfully!');
    }

    function cancelEdit(index) {
        renderTable();
    }

    function normalizeRow(row) {
        const normalized = { N: row['N'] ?? '' };
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
            'Remarks': row.remarks || ''
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
                message += `\n${result.errors.length} rows were missing required fields.`;
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

    function deleteRow(index) {
        if (confirm('Are you sure you want to delete this row?')) {
            tableData.splice(index, 1);
            renderTable();
            alert('Row deleted successfully!');
        }
    }
</script>
<?= $this->endSection() ?>

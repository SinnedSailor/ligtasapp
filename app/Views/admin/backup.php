<?= $this->extend('layouts/main_tailwind') ?>

<?= $this->section('content') ?>
<div class="container max-w-lg mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center">
            <?= svg_icon('archive','w-6 h-6 mr-2') ?>Backup &amp; Restore
        </h1>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="mb-4">
            <div class="flex items-start justify-between gap-4 bg-green-50 border border-green-200 text-green-800 rounded-md p-4">
                <div><?= session()->getFlashdata('success') ?></div>
                <button type="button" class="text-green-800 font-bold" onclick="this.closest('.mb-4').remove()">&times;</button>
            </div>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="mb-4">
            <div class="flex items-start justify-between gap-4 bg-red-50 border border-red-200 text-red-800 rounded-md p-4">
                <div><?= session()->getFlashdata('error') ?></div>
                <button type="button" class="text-red-800 font-bold" onclick="this.closest('.mb-4').remove()">&times;</button>
            </div>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl shadow p-6">
        <h5 class="text-lg font-semibold text-gray-700 mb-4">Administrator Backup</h5>
        <?php if (isset($lastBackup) && $lastBackup): ?>
            <p class="text-sm text-gray-500 mb-2">Last backed up: <strong><span id="lastBackupDate"><?= esc($lastBackup) ?></span></strong></p>
        <?php else: ?>
            <p class="text-sm text-gray-500 mb-2">Last backed up: <em id="lastBackupDate">never</em></p>
        <?php endif; ?>
        <p class="text-sm text-gray-600 mb-4">Download a JSON snapshot of the administrator account. Keep this file in a safe place; it can be used to restore the account if it is accidentally deleted or modified.</p>
        <!-- download in new tab and update the timestamp client‑side so the view reflects the action immediately -->
        <a id="backupDownload" href="<?= base_url('admin/backup/export') ?>" target="_blank" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 inline-block">
            Download Backup
        </a>
        <script>
            // when the user clicks the download link, update the displayed "last backed up" time
            document.addEventListener('DOMContentLoaded', function() {
                var link = document.getElementById('backupDownload');
                if (!link) return;

                link.addEventListener('click', function() {
                    // format should match PHP's date('F j, Y g:i a')
                    var now = new Date();
                    var options = { month: 'long', day: 'numeric', year: 'numeric' };
                    var datePart = now.toLocaleDateString('en-US', options);
                    var hours = now.getHours() % 12;
                    if (hours === 0) hours = 12;
                    var minutes = now.getMinutes().toString().padStart(2, '0');
                    var ampm = now.getHours() < 12 ? 'am' : 'pm';
                    var timePart = hours + ':' + minutes + ' ' + ampm;
                    var display = datePart + ' ' + timePart;

                    var elem = document.getElementById('lastBackupDate');
                    if (elem) {
                        // if the existing element is <em> (never case), replace with a strong wrapper
                        if (elem.tagName.toLowerCase() === 'em') {
                            var strong = document.createElement('strong');
                            var newSpan = document.createElement('span');
                            newSpan.id = 'lastBackupDate';
                            newSpan.textContent = display;
                            strong.appendChild(newSpan);
                            elem.parentNode.replaceChild(strong, elem);
                        } else {
                            elem.textContent = display;
                        }
                    }
                });
            });
        </script>
    </div>

    <div class="bg-white rounded-2xl shadow p-6 mt-8">
        <h5 class="text-lg font-semibold text-gray-700 mb-4">Restore from Backup</h5>
        <p class="text-sm text-gray-600 mb-4">Choose a previously generated JSON file to restore the administrator record.</p>
        <form method="post" action="<?= base_url('admin/backup/restore') ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="mb-4">
                <input type="file" name="backup_file" required accept="application/json" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
            </div>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Restore</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

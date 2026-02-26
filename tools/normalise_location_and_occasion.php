<?php
// tools/normalise_location_and_occasion.php
// Run with: php normalise_location_and_occasion.php

require __DIR__ . '/../vendor/autoload.php';

$model = new \App\Models\IncidentReportModel();

echo "Normalising location_category and occasion values...\n";

$rows = $model->findAll();
$updated = 0;
foreach ($rows as $r) {
    $cleanLoc = isset($r['location_category']) && $r['location_category'] !== null
        ? trim(mb_convert_case($r['location_category'], MB_CASE_TITLE, 'UTF-8'))
        : null;
    $cleanOcc = isset($r['occasion']) && $r['occasion'] !== null
        ? trim(mb_convert_case($r['occasion'], MB_CASE_TITLE, 'UTF-8'))
        : null;
    $cleanJob = isset($r['occupation']) && $r['occupation'] !== null
        ? trim(mb_convert_case($r['occupation'], MB_CASE_TITLE, 'UTF-8'))
        : null;

    if (($cleanLoc !== $r['location_category']) || ($cleanOcc !== $r['occasion']) || ($cleanJob !== $r['occupation'])) {
        $model->update($r['id'], [
            'location_category' => $cleanLoc,
            'occasion' => $cleanOcc,
            'occupation' => $cleanJob,
        ]);
        $updated++;
    }
}

echo "Done. Rows updated: $updated\n";

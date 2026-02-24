<?php
require __DIR__ . '/../app/Controllers/IncidentReport.php';

$ctrl = new \App\Controllers\IncidentReport();
$ref = new ReflectionMethod($ctrl, 'mapRow');
$ref->setAccessible(true);

$examples = [
    ['Sex' => 'Male'],
    ['Sex' => 'male'],
    ['Sex' => 'M'],
    ['Sex' => 'Female'],
    ['Sex' => 'FEMALE'],
    ['Sex' => 'f'],
    ['Sex' => ''],
    ['Sex' => null],
];

foreach ($examples as $ex) {
    $mapped = $ref->invoke($ctrl, $ex, 0);
    echo json_encode($ex) . " => " . json_encode($mapped) . "\n";
}

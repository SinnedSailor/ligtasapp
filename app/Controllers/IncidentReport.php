<?php

namespace App\Controllers;

use App\Models\IncidentReportModel;

class IncidentReport extends BaseController
{
    private IncidentReportModel $incidentReportModel;
    public function __construct()
    {
        $this->incidentReportModel = new IncidentReportModel();
    }

    public function import()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON([
                'message' => 'Unauthorized.',
            ]);
        }

        $payload = $this->request->getJSON(true);
        $rows = $payload['rows'] ?? null;
        if (!is_array($rows)) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'Invalid payload. Expected rows array.',
            ]);
        }

        $required = ['month_of_incident', 'year_of_incident', 'province', 'municipality'];

        $inserted = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            if (!is_array($row)) {
                $skipped++;
                continue;
            }

            $mapped = $this->mapRow($row, $index);
            $mapped['row_hash'] = $this->hashRow($mapped);

            $missing = $this->getMissingFields($mapped, $required);
            if (!empty($missing)) {
                $skipped++;
                $errors[] = [
                    'row' => $index + 1,
                    'missing' => $missing,
                ];
                continue;
            }

            $existingRow = $this->incidentReportModel
                ->where('n', $mapped['n'])
                ->orWhere('row_hash', $mapped['row_hash'])
                ->first();

            if ($existingRow) {
                $skipped++;
                continue;
            }

            $this->incidentReportModel->insert($mapped);
            $inserted++;
        }

        return $this->response->setJSON([
            'message' => 'Import completed.',
            'inserted' => $inserted,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors' => $errors,
        ]);
    }

    private function mapRow(array $row, int $index): array
    {
        $map = [
            'N' => 'n',
            'Month of Incident' => 'month_of_incident',
            'Year of Incident' => 'year_of_incident',
            'Year' => 'year_of_incident',
            'Province' => 'province',
            'Municipality/City where Incidence Occurred' => 'municipality',
            'Municipality/City where Incident Occurred' => 'municipality',
            'Municipality' => 'municipality',
            'Name of Victim' => 'name_of_victim',
            'Location Category' => 'location_category',
            'Age of the Person' => 'age',
            'Age' => 'age',
            'Gender of the Person' => 'gender',
            'Gender' => 'gender',
            'Occasion' => 'occasion',
            'Other Factors' => 'factors',
            'Factors' => 'factors',
            "Person's Residence" => 'residence',
            'Occupation of the Victim' => 'occupation',
            'Occupation' => 'occupation',
            'Remarks' => 'remarks',
        ];

        $data = [];
        foreach ($row as $key => $value) {
            if (!isset($map[$key])) {
                continue;
            }
            $field = $map[$key];
            $data[$field] = $value;
        }

        $n = $this->toInt($data['n'] ?? null);
        $data['n'] = $n === null ? ($index + 1) : $n;
        $data['month_of_incident'] = $this->toString($data['month_of_incident'] ?? '');
        $data['year_of_incident'] = $this->toInt($data['year_of_incident'] ?? null);
        $data['province'] = $this->toString($data['province'] ?? '');
        $data['municipality'] = $this->toString($data['municipality'] ?? '');
        $data['name_of_victim'] = $this->toString($data['name_of_victim'] ?? '');
        $data['location_category'] = $this->toString($data['location_category'] ?? '');
        $data['age'] = $this->toInt($data['age'] ?? null);
        $data['gender'] = $this->normalizeGender($data['gender'] ?? '');
        $data['occasion'] = $this->toString($data['occasion'] ?? '');
        $data['factors'] = $this->toString($data['factors'] ?? '');
        $data['residence'] = $this->toString($data['residence'] ?? '');
        $data['occupation'] = $this->toString($data['occupation'] ?? '');
        $data['remarks'] = $this->toString($data['remarks'] ?? '');

        return $data;
    }

    private function hashRow(array $row): string
    {
        $payload = [
            'month_of_incident' => $row['month_of_incident'] ?? '',
            'year_of_incident' => $row['year_of_incident'] ?? '',
            'province' => $row['province'] ?? '',
            'municipality' => $row['municipality'] ?? '',
            'name_of_victim' => $row['name_of_victim'] ?? '',
            'location_category' => $row['location_category'] ?? '',
            'age' => $row['age'] ?? '',
            'gender' => $row['gender'] ?? '',
            'occasion' => $row['occasion'] ?? '',
            'factors' => $row['factors'] ?? '',
            'residence' => $row['residence'] ?? '',
            'occupation' => $row['occupation'] ?? '',
            'remarks' => $row['remarks'] ?? '',
        ];

        return hash('sha256', json_encode($payload));
    }

    private function toString($value): string
    {
        if ($value === null) {
            return '';
        }

        return trim((string) $value);
    }

    private function toInt($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        return null;
    }

    private function normalizeGender($value): string
    {
        $value = strtolower($this->toString($value));

        if ($value === 'm' || $value === 'male') {
            return 'm';
        }

        if ($value === 'f' || $value === 'female') {
            return 'f';
        }

        return $value;
    }

    private function getMissingFields(array $data, array $required): array
    {
        $missing = [];
        foreach ($required as $field) {
            $value = $data[$field] ?? null;
            if ($value === null || $value === '') {
                $missing[] = $field;
            }
        }

        return $missing;
    }
}

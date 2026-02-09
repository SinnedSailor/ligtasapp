<?php

namespace App\Models;

use CodeIgniter\Model;

class IncidentReportModel extends Model
{
    protected $table = 'incident_reports';
    protected $primaryKey = 'n';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'row_hash',
        'n',
        'month_of_incident',
        'year_of_incident',
        'province',
        'municipality',
        'name_of_victim',
        'location_category',
        'age',
        'gender',
        'occasion',
        'factors',
        'residence',
        'region',
        'occupation',
        'remarks',
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}

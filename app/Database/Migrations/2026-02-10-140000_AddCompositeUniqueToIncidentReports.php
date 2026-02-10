<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCompositeUniqueToIncidentReports extends Migration
{
    public function up()
    {
        $this->forge->addKey([
            'name_of_victim',
            'municipality',
            'province',
            'year_of_incident',
        ], false, true);
        $this->forge->processIndexes('incident_reports');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE `incident_reports` DROP INDEX `incident_reports_name_of_victim_municipality_province_year_of_incident`');
    }
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIncidentReportIdPrimaryKey extends Migration
{
    public function up()
    {
        $this->db->query('ALTER TABLE incident_reports DROP PRIMARY KEY');
        $this->db->query('ALTER TABLE incident_reports ADD COLUMN id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST');
        $this->db->query('UPDATE incident_reports SET n = id WHERE n IS NULL');
        $this->db->query('ALTER TABLE incident_reports MODIFY n INT(11) UNSIGNED NOT NULL');
        $this->db->query('CREATE UNIQUE INDEX incident_reports_n_unique ON incident_reports (n)');
    }

    public function down()
    {
        $this->db->query('DROP INDEX incident_reports_n_unique ON incident_reports');
        $this->db->query('ALTER TABLE incident_reports DROP PRIMARY KEY');
        $this->db->query('ALTER TABLE incident_reports DROP COLUMN id');
        $this->db->query('ALTER TABLE incident_reports ADD PRIMARY KEY (n)');
    }
}

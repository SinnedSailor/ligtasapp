<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MakeIncidentReportNPrimary extends Migration
{
    public function up()
    {
        $this->db->query("UPDATE incident_reports SET n = id WHERE n IS NULL");

        $this->db->query(
            "DELETE r1 FROM incident_reports r1
            INNER JOIN incident_reports r2
                ON r1.n = r2.n AND r1.id > r2.id"
        );

        $this->db->query('ALTER TABLE incident_reports MODIFY id INT(11) UNSIGNED NOT NULL');
        $this->db->query('ALTER TABLE incident_reports DROP PRIMARY KEY');
        $this->db->query('ALTER TABLE incident_reports DROP COLUMN id');
        $this->db->query('ALTER TABLE incident_reports MODIFY n INT(11) UNSIGNED NOT NULL');
        $this->db->query('ALTER TABLE incident_reports ADD PRIMARY KEY (n)');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE incident_reports DROP PRIMARY KEY');
        $this->db->query('ALTER TABLE incident_reports ADD COLUMN id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST');
        $this->db->query('ALTER TABLE incident_reports MODIFY n INT(11) UNSIGNED NULL');
    }
}

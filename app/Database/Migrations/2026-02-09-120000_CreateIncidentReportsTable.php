<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateIncidentReportsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'n' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'month_of_incident' => [
                'type' => 'VARCHAR',
                'constraint' => 40,
                'null' => true,
            ],
            'year_of_incident' => [
                'type' => 'INT',
                'constraint' => 4,
                'unsigned' => true,
                'null' => true,
            ],
            'province' => [
                'type' => 'VARCHAR',
                'constraint' => 120,
                'null' => true,
            ],
            'municipality' => [
                'type' => 'VARCHAR',
                'constraint' => 160,
                'null' => true,
            ],
            'name_of_victim' => [
                'type' => 'VARCHAR',
                'constraint' => 160,
                'null' => true,
            ],
            'location_category' => [
                'type' => 'VARCHAR',
                'constraint' => 120,
                'null' => true,
            ],
            'age' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'gender' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
            ],
            'occasion' => [
                'type' => 'VARCHAR',
                'constraint' => 160,
                'null' => true,
            ],
            'factors' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'residence' => [
                'type' => 'VARCHAR',
                'constraint' => 160,
                'null' => true,
            ],
            'region' => [
                'type' => 'VARCHAR',
                'constraint' => 120,
                'null' => true,
            ],
            'occupation' => [
                'type' => 'VARCHAR',
                'constraint' => 160,
                'null' => true,
            ],
            'remarks' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('n');
        $this->forge->createTable('incident_reports');
    }

    public function down()
    {
        $this->forge->dropTable('incident_reports');
    }
}

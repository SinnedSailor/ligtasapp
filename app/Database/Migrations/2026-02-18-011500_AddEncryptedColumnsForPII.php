<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * @property \CodeIgniter\Database\Forge $forge
 * @property \CodeIgniter\Database\BaseConnection $db
 */
class AddEncryptedColumnsForPII extends Migration
{
    public function up()
    {
        // Add encrypted columns to users
        $this->forge->addColumn('users', [
            'first_name_enc' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'first_name',
            ],
            'last_name_enc' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'first_name_enc',
            ],
            'email_enc' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'email_hash',
            ],
            'contact_number_enc' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'contact_number',
            ],
        ]);

        // Add encrypted column for incident_reports
        if ($this->db->tableExists('incident_reports')) {
            $this->forge->addColumn('incident_reports', [
                'name_of_victim_enc' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'name_of_victim',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('first_name_enc', 'users')) {
            $this->forge->dropColumn('users', ['first_name_enc', 'last_name_enc', 'email_enc', 'contact_number_enc']);
        }

        if ($this->db->tableExists('incident_reports') && $this->db->fieldExists('name_of_victim_enc', 'incident_reports')) {
            $this->forge->dropColumn('incident_reports', ['name_of_victim_enc']);
        }
    }
}

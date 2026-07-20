<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOperationTable extends Migration
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
            'id_client' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'destinataire_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'id_type_operation' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'montant' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'frais' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0,
            ],
            'date_operation DATETIME DEFAULT CURRENT_TIMESTAMP',
        ]);

        $this->forge->addKey('id', true);

        $this->forge->addForeignKey('id_client', 'client', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('destinataire_id', 'client', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('id_type_operation', 'type_operation', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('operation');
    }

    public function down()
    {
        $this->forge->dropTable('operation');
    }
}
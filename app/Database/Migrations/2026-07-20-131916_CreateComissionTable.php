<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCommissionTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_autre_operateur' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'pourcent_commit' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'id_type_operation' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
        ]);

        $this->forge->addKey('id', true);

        $this->forge->addForeignKey('id_autre_operateur', 'autre_operateur', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_type_operation', 'type_operation', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('commission');
    }

    public function down()
    {
        $this->forge->dropTable('commission');
    }
}
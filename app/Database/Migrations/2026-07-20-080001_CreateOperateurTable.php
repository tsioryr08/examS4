<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOperateurTable extends Migration
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
            'libelle' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'code' => [
                'type' => 'VARCHAR',
                'constraint' => 3,
            ],
            'pourcent_promo' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('code');
        $this->forge->createTable('operateur');
    }

    public function down()
    {
        $this->forge->dropTable('operateur');
    }
}
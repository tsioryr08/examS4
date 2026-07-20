<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialSeeder extends Seeder
{
    public function run()
    {
        // ============================================
        // Operateurs
        // ============================================
        $this->db->table('operateur')->insertBatch([
            ['libelle' => 'Airtel',  'code' => '033'],
            ['libelle' => 'Orange', 'code' => '037'],
        ]);

        // ============================================
        // Types d'operation
        // ============================================
        $this->db->table('type_operation')->insertBatch([
            ['nom' => 'depot'],     // id 1
            ['nom' => 'retrait'],   // id 2
            ['nom' => 'transfert'], // id 3
        ]);

        // ============================================
        // Bareme de frais - Orange (id_operateur = 1)
        // ============================================

        // Retrait (id_type_operation = 2)
        $this->db->table('bareme_frais')->insertBatch([
            ['id_operateur' => 1, 'id_type_operation' => 2, 'montant_min' => 100,    'montant_max' => 1000,    'frais' => 50],
            ['id_operateur' => 1, 'id_type_operation' => 2, 'montant_min' => 1001,   'montant_max' => 5000,    'frais' => 50],
            ['id_operateur' => 1, 'id_type_operation' => 2, 'montant_min' => 5001,   'montant_max' => 10000,   'frais' => 100],
            ['id_operateur' => 1, 'id_type_operation' => 2, 'montant_min' => 10001,  'montant_max' => 25000,   'frais' => 200],
            ['id_operateur' => 1, 'id_type_operation' => 2, 'montant_min' => 25001,  'montant_max' => 50000,   'frais' => 400],
            ['id_operateur' => 1, 'id_type_operation' => 2, 'montant_min' => 50001,  'montant_max' => 100000,  'frais' => 800],
            ['id_operateur' => 1, 'id_type_operation' => 2, 'montant_min' => 100001, 'montant_max' => 250000,  'frais' => 1500],
            ['id_operateur' => 1, 'id_type_operation' => 2, 'montant_min' => 250001, 'montant_max' => 500000,  'frais' => 2500],
            ['id_operateur' => 1, 'id_type_operation' => 2, 'montant_min' => 500001, 'montant_max' => 1000000, 'frais' => 3000],
        ]);

        // Transfert (id_type_operation = 3)
        $this->db->table('bareme_frais')->insertBatch([
            ['id_operateur' => 1, 'id_type_operation' => 3, 'montant_min' => 100,    'montant_max' => 1000,    'frais' => 100],
            ['id_operateur' => 1, 'id_type_operation' => 3, 'montant_min' => 1001,   'montant_max' => 5000,    'frais' => 150],
            ['id_operateur' => 1, 'id_type_operation' => 3, 'montant_min' => 5001,   'montant_max' => 10000,   'frais' => 250],
            ['id_operateur' => 1, 'id_type_operation' => 3, 'montant_min' => 10001,  'montant_max' => 25000,   'frais' => 400],
            ['id_operateur' => 1, 'id_type_operation' => 3, 'montant_min' => 25001,  'montant_max' => 50000,   'frais' => 700],
            ['id_operateur' => 1, 'id_type_operation' => 3, 'montant_min' => 50001,  'montant_max' => 100000,  'frais' => 1200],
            ['id_operateur' => 1, 'id_type_operation' => 3, 'montant_min' => 100001, 'montant_max' => 250000,  'frais' => 2000],
            ['id_operateur' => 1, 'id_type_operation' => 3, 'montant_min' => 250001, 'montant_max' => 500000,  'frais' => 3500],
            ['id_operateur' => 1, 'id_type_operation' => 3, 'montant_min' => 500001, 'montant_max' => 1000000, 'frais' => 5000],
        ]);

        // ============================================
        // Bareme de frais - Airtel (id_operateur = 2)
        // ============================================

        // Retrait (id_type_operation = 2)
        $this->db->table('bareme_frais')->insertBatch([
            ['id_operateur' => 2, 'id_type_operation' => 2, 'montant_min' => 100,    'montant_max' => 1000,    'frais' => 60],
            ['id_operateur' => 2, 'id_type_operation' => 2, 'montant_min' => 1001,   'montant_max' => 5000,    'frais' => 60],
            ['id_operateur' => 2, 'id_type_operation' => 2, 'montant_min' => 5001,   'montant_max' => 10000,   'frais' => 120],
            ['id_operateur' => 2, 'id_type_operation' => 2, 'montant_min' => 10001,  'montant_max' => 25000,   'frais' => 220],
            ['id_operateur' => 2, 'id_type_operation' => 2, 'montant_min' => 25001,  'montant_max' => 50000,   'frais' => 420],
            ['id_operateur' => 2, 'id_type_operation' => 2, 'montant_min' => 50001,  'montant_max' => 100000,  'frais' => 820],
            ['id_operateur' => 2, 'id_type_operation' => 2, 'montant_min' => 100001, 'montant_max' => 250000,  'frais' => 1600],
            ['id_operateur' => 2, 'id_type_operation' => 2, 'montant_min' => 250001, 'montant_max' => 500000,  'frais' => 2600],
            ['id_operateur' => 2, 'id_type_operation' => 2, 'montant_min' => 500001, 'montant_max' => 1000000, 'frais' => 3200],
        ]);

        // Transfert (id_type_operation = 3)
        $this->db->table('bareme_frais')->insertBatch([
            ['id_operateur' => 2, 'id_type_operation' => 3, 'montant_min' => 100,    'montant_max' => 1000,    'frais' => 120],
            ['id_operateur' => 2, 'id_type_operation' => 3, 'montant_min' => 1001,   'montant_max' => 5000,    'frais' => 170],
            ['id_operateur' => 2, 'id_type_operation' => 3, 'montant_min' => 5001,   'montant_max' => 10000,   'frais' => 270],
            ['id_operateur' => 2, 'id_type_operation' => 3, 'montant_min' => 10001,  'montant_max' => 25000,   'frais' => 420],
            ['id_operateur' => 2, 'id_type_operation' => 3, 'montant_min' => 25001,  'montant_max' => 50000,   'frais' => 720],
            ['id_operateur' => 2, 'id_type_operation' => 3, 'montant_min' => 50001,  'montant_max' => 100000,  'frais' => 1250],
            ['id_operateur' => 2, 'id_type_operation' => 3, 'montant_min' => 100001, 'montant_max' => 250000,  'frais' => 2100],
            ['id_operateur' => 2, 'id_type_operation' => 3, 'montant_min' => 250001, 'montant_max' => 500000,  'frais' => 3600],
            ['id_operateur' => 2, 'id_type_operation' => 3, 'montant_min' => 500001, 'montant_max' => 1000000, 'frais' => 5200],
        ]);

        // ============================================
        // Clients de test
        // ============================================
        $this->db->table('client')->insertBatch([
            [
                'numero'       => '0331234567',
                'nom'          => 'RAFALIMANANA',
                'prenom'       => 'Tsiory Fandresena',
                'solde'        => 50000,
                'id_operateur' => 1,
            ],
            [
                'numero'       => '0339876543',
                'nom'          => 'RAKOTONJANAHARY',
                'prenom'       => 'Hajaniaina Olivier',
                'solde'        => 100000,
                'id_operateur' => 1,
            ],
            [
                'numero'       => '0371112233',
                'nom'          => 'RASOANAIVO',
                'prenom'       => 'Marie',
                'solde'        => 20000,
                'id_operateur' => 2,
            ],
        ]);
    }
}
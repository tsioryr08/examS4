## pour afficher une erreur dans une vue 
> <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>
'error' vient du controller via le else 
- return redirect()->to('/login')->with('error', 'Identifiants incorrects.');

## ecriture de donnees 
- creation fichier via commande : php spark make:seeder InitialSeeder
<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nom'   => 'Rafalimanana Tsiory',
                'email' => 'tsiory@example.com',
            ],
            [
                'nom'   => 'Jean Rakoto',
                'email' => 'jean.rakoto@example.com',
            ],
            [
                'nom'   => 'Marie Rasoa',
                'email' => 'marie.rasoa@example.com',
            ],
        ];

        // insertBatch() insere toutes les lignes d'un coup
        $this->db->table('user')->insertBatch($data);
    }
}

- executer: php spark db:seed InitialSeeder 
- verif : sqlite3 writable/database.db puis select * from user;

## si plusieurs roles mifampistofoka 
- ca d abord car un user a un role 
<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['id' => 1, 'nom' => 'admin'],
            ['id' => 2, 'nom' => 'client'],
            ['id' => 3, 'nom' => 'caissier'],
        ];

        $this->db->table('role')->insertBatch($data);
    }
}

- apres , l'user en question 
<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nom'     => 'Rafalimanana Tsiory',
                'email'   => 'tsiory@example.com',
                'role_id' => 1, // admin
            ],
            [
                'nom'     => 'Jean Rakoto',
                'email'   => 'jean.rakoto@example.com',
                'role_id' => 3, // caissier
            ],
            [
                'nom'     => 'Marie Rasoa',
                'email'   => 'marie.rasoa@example.com',
                'role_id' => 2, // client
            ],
        ];

        $this->db->table('user')->insertBatch($data);
    }
} 

- creer le seeder principal:
<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialSeeder extends Seeder
{
    public function run()
    {
        $this->call('RoleSeeder');   // d'abord les roles
        $this->call('UserSeeder');   // ensuite les users qui en dependent
    }
}
- lancer le tout d'un coup : php spark db:seed InitialSeeder 
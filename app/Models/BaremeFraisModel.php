<?php

namespace App\Models;

use CodeIgniter\Model;

class BaremeFraisModel extends Model
{
    protected $table            = 'bareme_frais';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
    protected $allowedFields    = [
        'id_operateur', 'id_type_operation', 'montant_min', 'montant_max', 'frais',
    ];

    protected $validationRules = [
        'id_operateur'      => 'required|integer',
        'id_type_operation' => 'required|integer',
        'montant_min'       => 'required|decimal',
        'montant_max'       => 'required|decimal',
        'frais'             => 'required|decimal',
    ];

    public function getBaremesDetailles(?int $idOperateur = null, ?int $idTypeOperation = null)
    {
        $builder = $this->select('bareme_frais.*, operateur.libelle as operateur_libelle, operateur.code as operateur_code, type_operation.nom as type_nom')
            ->join('operateur', 'operateur.id = bareme_frais.id_operateur')
            ->join('type_operation', 'type_operation.id = bareme_frais.id_type_operation');

        if ($idOperateur) {
            $builder->where('bareme_frais.id_operateur', $idOperateur);
        }
        if ($idTypeOperation) {
            $builder->where('bareme_frais.id_type_operation', $idTypeOperation);
        }

        return $builder->orderBy('bareme_frais.id_operateur')
            ->orderBy('bareme_frais.id_type_operation')
            ->orderBy('bareme_frais.montant_min')
            ->findAll();
    }

    public function tranchesChevauchent(int $idOperateur, int $idTypeOperation, float $min, float $max, ?int $ignoreId = null): bool
    {
        $builder = $this->where('id_operateur', $idOperateur)
            ->where('id_type_operation', $idTypeOperation)
            ->groupStart()
                ->groupStart()
                    ->where('montant_min <=', $max)
                    ->where('montant_max >=', $min)
                ->groupEnd()
            ->groupEnd();

        if ($ignoreId) {
            $builder->where('id !=', $ignoreId);
        }

        return $builder->countAllResults() > 0;
    }

    // Regroupe les barèmes par opérateur puis par type d'opération
public function getBaremesGroupes(?int $idOperateur = null): array
{
    $lignes = $this->select('bareme_frais.*, operateur.libelle as operateur_libelle, type_operation.nom as type_nom')
        ->join('operateur', 'operateur.id = bareme_frais.id_operateur')
        ->join('type_operation', 'type_operation.id = bareme_frais.id_type_operation')
        ->orderBy('operateur.libelle')
        ->orderBy('type_operation.nom')
        ->orderBy('bareme_frais.montant_min');

    if ($idOperateur) {
        $lignes->where('bareme_frais.id_operateur', $idOperateur);
    }

    $lignes = $lignes->findAll();

    $groupes = [];
    foreach ($lignes as $ligne) {
        // Cle de regroupement = libelle, pas id_operateur, pour fusionner 033/037
        $cle = $ligne['operateur_libelle'];

        if (! isset($groupes[$cle])) {
            $groupes[$cle] = [
                'libelle' => $ligne['operateur_libelle'],
                'types'   => [],
            ];
        }

        $groupes[$cle]['types'][$ligne['type_nom']][] = $ligne;
    }

    return $groupes;
}
}
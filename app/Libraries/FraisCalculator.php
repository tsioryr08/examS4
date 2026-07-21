<?php

namespace App\Libraries;

use App\Models\BaremeFraisModel;
use App\Models\OperateurModel;
use App\Models\TypeOperationModel;

class FraisCalculator
{
    protected BaremeFraisModel $baremeModel;
    protected OperateurModel $operateurModel;
    protected TypeOperationModel $typeModel;

    public function __construct()
    {
        $this->baremeModel    = new BaremeFraisModel();
        $this->operateurModel = new OperateurModel();
        $this->typeModel      = new TypeOperationModel();
    }

    public function calculer(int $idOperateur, int $idTypeOperation, float $montant, ?int $idOperateurDestinataire = null): float
    {
        $idsMemeOperateur = $this->getIdsMemeOperateur($idOperateur);

        $bareme = $this->baremeModel
            ->whereIn('id_operateur', $idsMemeOperateur)
            ->where('id_type_operation', $idTypeOperation)
            ->where('montant_min <=', $montant)
            ->where('montant_max >=', $montant)
            ->first();

        if ($bareme === null) {
            return 0.0;
        }

        $frais = (float) $bareme['frais'];

        $operateur = $this->operateurModel->find($idOperateur);

        $promotion = (float) ($operateur['promotion'] ?? 0);
        if ($promotion > 0) {
            $frais -= $frais * ($promotion / 100);
        }

        $type = $this->typeModel->find($idTypeOperation);
        $estTransfert = $type && strtolower($type['nom']) === 'transfert';

        // "Meme operateur" = meme libelle, meme si code destinataire different
        if ($estTransfert && $idOperateurDestinataire !== null) {
            $memeOperateurLogique = in_array($idOperateurDestinataire, $idsMemeOperateur, true);

            if ($memeOperateurLogique) {
                $reduction = (float) ($operateur['reduction_meme_operateur'] ?? 0);
                if ($reduction > 0) {
                    $frais -= $frais * ($reduction / 100);
                }
            }
        }

        return round($frais, 2);
    }

    // Retourne tous les id d'operateur partageant le meme libelle (ex: Airtel 033 et 037 -> [1, 2])
    private function getIdsMemeOperateur(int $idOperateur): array
    {
        $operateur = $this->operateurModel->find($idOperateur);
        if (!$operateur) {
            return [$idOperateur];
        }

        return $this->operateurModel
            ->where('libelle', $operateur['libelle'])
            ->findColumn('id') ?? [$idOperateur];
    }
}
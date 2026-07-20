<?php

namespace App\Libraries;

use App\Models\BaremeFraisModel;

class FraisCalculator
{
    protected BaremeFraisModel $baremeModel;

    public function __construct()
    {
        $this->baremeModel = new BaremeFraisModel();
    }

    public function calculer(int $idOperateur, int $idTypeOperation, float $montant): float
    {
        $bareme = $this->baremeModel
            ->where('id_operateur', $idOperateur)
            ->where('id_type_operation', $idTypeOperation)
            ->where('montant_min <=', $montant)
            ->where('montant_max >=', $montant)
            ->first();

        if ($bareme === null) {
            return 0.0;
        }

        return (float) $bareme['frais'];
    }
}
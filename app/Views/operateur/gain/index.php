<?= $this->extend('operateur/layout') ?>
<?= $this->section('content') ?>

<h2>Situation des gains</h2>

<form method="get" class="row g-2 mb-3">
    <div class="col-auto">
        <select name="id_operateur" class="form-select" onchange="this.form.submit()">
            <option value="">-- Tous les opérateurs --</option>
            <?php foreach ($operateurs as $op): ?>
                <option value="<?= $op['id'] ?>" <?= ($idOperateur == $op['id']) ? 'selected' : '' ?>>
                    <?= esc($op['libelle']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-auto">
        <input type="date" name="date_debut" class="form-control" value="<?= esc($dateDebut) ?>">
    </div>
    <div class="col-auto">
        <input type="date" name="date_fin" class="form-control" value="<?= esc($dateFin) ?>">
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-primary">Filtrer</button>
    </div>
</form>

<!-- ====================================== -->
<!-- GAINS PROPRES (Retrait + Transfert) -->
<!-- ====================================== -->
<div class="card mt-4">
    <div class="card-header" style="background-color: rgba(95, 115, 83, 0.08); border-bottom: 2px solid var(--mm-olive-600);">
        <h5 class="mb-0" style="color: var(--mm-olive-900);">Gains propres de l'opérateur</h5>
    </div>
    <div class="card-body">
        <table class="table table-bordered mb-0">
            <thead>
                <tr>
                    <th>Type d'opération</th>
                    <th>Nombre d'opérations</th>
                    <th>Total des frais gagnés</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($gainsPropres)): ?>
                    <tr>
                        <td colspan="3" class="text-center text-muted">Aucun gain propre</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($gainsPropres as $g): ?>
                    <tr>
                        <td><?= esc($g['type_nom']) ?></td>
                        <td><?= $g['nb_operations'] ?></td>
                        <td><strong><?= number_format($g['total_frais'], 0, ',', ' ') ?> Ar</strong></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ====================================== -->
<!-- COMMISSIONS DES TRANSFERTS INTER-OP -->
<!-- ====================================== -->
<div class="card mt-4">
    <div class="card-header" style="background-color: rgba(95, 115, 83, 0.08); border-bottom: 2px solid var(--mm-olive-600);">
        <h5 class="mb-0" style="color: var(--mm-olive-900);">Commissions des transferts vers autres opérateurs</h5>
    </div>
    <div class="card-body">
        <table class="table table-bordered mb-0">
            <thead>
                <tr>
                    <th>Autre opérateur</th>
                    <th>Type d'opération</th>
                    <th>Montant total transféré</th>
                    <th>Commission %</th>
                    <th>Commission gagnée</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($gainsCommissionInterOp)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted">Aucune commission inter-opérateurs</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($gainsCommissionInterOp as $g): ?>
                    <tr>
                        <td><?= esc($g['libelle']) ?> (<?= esc($g['code']) ?>)</td>
                        <td><?= esc($g['type_nom']) ?></td>
                        <td><?= number_format($g['montant_total'], 0, ',', ' ') ?> Ar</td>
                        <td><?= $g['pourcent_commit'] ?>%</td>
                        <td><strong><?= number_format($g['commission_montant'], 0, ',', ' ') ?> Ar</strong></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ====================================== -->
<!-- MONTANTS À ENVOYER PAR OPÉRATEUR -->
<!-- ====================================== -->
<div class="card mt-4">
    <div class="card-header" style="background-color: rgba(95, 115, 83, 0.08); border-bottom: 2px solid var(--mm-olive-600);">
        <h5 class="mb-0" style="color: var(--mm-olive-900);">Montants à envoyer à chaque opérateur</h5>
    </div>
    <div class="card-body">
        <table class="table table-bordered mb-0">
            <thead>
                <tr>
                    <th>Opérateur destinataire</th>
                    <th>Montant total</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($montantsAEnvoyer)): ?>
                    <tr>
                        <td colspan="2" class="text-center text-muted">Aucun montant à envoyer</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($montantsAEnvoyer as $m): ?>
                    <tr>
                        <td><?= esc($m['libelle']) ?> (<?= esc($m['code']) ?>)</td>
                        <td><strong><?= number_format($m['montant_total'], 0, ',', ' ') ?> Ar</strong></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ====================================== -->
<!-- TOTAL GLOBAL -->
<!-- ====================================== -->
<div class="card mt-4 border-dark">
    <div class="card-header" style="background-color: rgba(95, 115, 83, 0.08); border-bottom: 2px solid var(--mm-olive-600);">
        <h5 class="mb-0" style="color: var(--mm-olive-900);">Récapitulatif total</h5>
    </div>
    <div class="card-body text-center">
        <h4>Total général : <strong><?= number_format($total, 0, ',', ' ') ?> Ar</strong></h4>
    </div>
</div>

<?= $this->endSection() ?>
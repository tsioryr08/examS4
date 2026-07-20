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

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Type d'opération</th>
            <th>Nombre d'opérations</th>
            <th>Total des frais gagnés</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($gains as $g): ?>
        <tr>
            <td><?= esc($g['type_nom']) ?></td>
            <td><?= $g['nb_operations'] ?></td>
            <td><?= number_format($g['total_frais'], 0, ',', ' ') ?> Ar</td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2">Total général</th>
            <th><?= number_format($total, 0, ',', ' ') ?> Ar</th>
        </tr>
    </tfoot>
</table>

<?= $this->endSection() ?>
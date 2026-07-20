<?= $this->extend('operateur/layout') ?>
<?= $this->section('content') ?>

<h2>Situation des comptes clients</h2>

<form method="get" class="mb-3">
    <select name="id_operateur" class="form-select" style="max-width:300px" onchange="this.form.submit()">
        <option value="">-- Tous les opérateurs --</option>
        <?php foreach ($operateurs as $op): ?>
            <option value="<?= $op['id'] ?>" <?= ($idOperateur == $op['id']) ? 'selected' : '' ?>>
                <?= esc($op['libelle']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Numéro</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Opérateur</th>
            <th>Solde</th>
            <th>Date création</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($clients as $c): ?>
        <tr>
            <td><?= esc($c['numero']) ?></td>
            <td><?= esc($c['nom']) ?></td>
            <td><?= esc($c['prenom']) ?></td>
            <td><?= esc($c['operateur_libelle']) ?> (<?= esc($c['operateur_code']) ?>)</td>
            <td><?= number_format($c['solde'], 0, ',', ' ') ?> Ar</td>
            <td><?= esc($c['date_creation']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="4">Solde total</th>
            <th colspan="2"><?= number_format($soldeTotal, 0, ',', ' ') ?> Ar</th>
        </tr>
    </tfoot>
</table>

<?= $this->endSection() ?>
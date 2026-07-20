<?= $this->extend('operateur/layout') ?>
<?= $this->section('content') ?>

<h2><?= $bareme ? 'Modifier' : 'Nouvelle' ?> tranche de frais</h2>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
        <ul>
        <?php foreach (session()->getFlashdata('errors') as $err): ?>
            <li><?= esc($err) ?></li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" action="<?= $bareme ? '/baremes/update/' . $bareme['id'] : '/baremes/store' ?>">
    <?= csrf_field() ?>

    <div class="mb-3">
        <label>Opérateur</label>
        <select name="id_operateur" class="form-select" required>
            <?php foreach ($operateurs as $op): ?>
                <option value="<?= $op['id'] ?>" <?= (old('id_operateur', $bareme['id_operateur'] ?? '') == $op['id']) ? 'selected' : '' ?>>
                    <?= esc($op['libelle']) ?> (<?= esc($op['code']) ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label>Type d'opération</label>
        <select name="id_type_operation" class="form-select" required>
            <?php foreach ($types as $t): ?>
                <option value="<?= $t['id'] ?>" <?= (old('id_type_operation', $bareme['id_type_operation'] ?? '') == $t['id']) ? 'selected' : '' ?>>
                    <?= esc($t['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label>Montant min</label>
        <input type="number" step="0.01" name="montant_min" class="form-control" value="<?= old('montant_min', $bareme['montant_min'] ?? '') ?>" required>
    </div>

    <div class="mb-3">
        <label>Montant max</label>
        <input type="number" step="0.01" name="montant_max" class="form-control" value="<?= old('montant_max', $bareme['montant_max'] ?? '') ?>" required>
    </div>

    <div class="mb-3">
        <label>Frais</label>
        <input type="number" step="0.01" name="frais" class="form-control" value="<?= old('frais', $bareme['frais'] ?? '') ?>" required>
    </div>

    <button type="submit" class="btn btn-primary">Enregistrer</button>
    <a href="/baremes" class="btn btn-secondary">Annuler</a>
</form>

<?= $this->endSection() ?>
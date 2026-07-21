<?= $this->extend('operateur/layout') ?>
<?= $this->section('content') ?>

<h2><?= $operateur ? 'Modifier' : 'Nouvel' ?> opérateur</h2>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
        <ul>
        <?php foreach (session()->getFlashdata('errors') as $err): ?>
            <li><?= esc($err) ?></li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" action="<?= $operateur ? '/operateurs/update/' . $operateur['id'] : '/operateurs/store' ?>">
    <?= csrf_field() ?>

    <div class="mb-3">
        <label>Libellé</label>
        <input type="text" name="libelle" class="form-control" value="<?= old('libelle', $operateur['libelle'] ?? '') ?>" required>
    </div>

    <div class="mb-3">
        <label>Préfixe (code)</label>
        <input type="text" name="code" class="form-control" value="<?= old('code', $operateur['code'] ?? '') ?>" placeholder="ex: 033" required>
    </div>

    <button type="submit" class="btn btn-primary">Enregistrer</button>
    <a href="/operateurs" class="btn btn-outline-primary">Annuler</a>
</form>

<?= $this->endSection() ?>
<?= $this->extend('operateur/layout') ?>
<?= $this->section('content') ?>

<h2><?= $type ? 'Modifier' : 'Nouveau' ?> type d'opération</h2>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
        <ul>
        <?php foreach (session()->getFlashdata('errors') as $err): ?>
            <li><?= esc($err) ?></li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" action="<?= $type ? '/types-operation/update/' . $type['id'] : '/types-operation/store' ?>">
    <?= csrf_field() ?>

    <div class="mb-3">
        <label>Nom du type</label>
        <input type="text" name="nom" class="form-control" value="<?= old('nom', $type['nom'] ?? '') ?>" placeholder="ex: depot, retrait, transfert" required>
    </div>

    <button type="submit" class="btn btn-primary">Enregistrer</button>
    <a href="/types-operation" class="btn btn-secondary">Annuler</a>
</form>

<?= $this->endSection() ?>
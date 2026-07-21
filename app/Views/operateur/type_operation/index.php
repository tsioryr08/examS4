<?= $this->extend('operateur/layout') ?>
<?= $this->section('content') ?>

<h2>Types d'opération</h2>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<a href="/types-operation/create" class="btn btn-primary mb-3">+ Nouveau type</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Nom</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($types as $t): ?>
        <tr>
            <td><?= esc($t['nom']) ?></td>
            <td>
                <a href="/types-operation/edit/<?= $t['id'] ?>" class="btn btn-sm btn-outline-primary">Modifier</a>
                <a href="/types-operation/delete/<?= $t['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer ce type ?')">Supprimer</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?= $this->endSection() ?>
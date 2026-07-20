<?= $this->extend('operateur/layout') ?>
<?= $this->section('content') ?>

<h2>Configuration des opérateurs</h2>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<a href="/operateurs/create" class="btn btn-primary mb-3">+ Nouvel opérateur</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Libellé</th>
            <th>Préfixe (code)</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($operateurs as $op): ?>
        <tr>
            <td><?= esc($op['libelle']) ?></td>
            <td><?= esc($op['code']) ?></td>
            <td>
                <a href="/operateurs/edit/<?= $op['id'] ?>" class="btn btn-sm btn-warning">Modifier</a>
                <a href="/operateurs/delete/<?= $op['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cet opérateur ?')">Supprimer</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?= $this->endSection() ?>
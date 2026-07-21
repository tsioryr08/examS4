<?= $this->extend('operateur/layout') ?>
<?= $this->section('content') ?>

<h2>Barème de frais</h2>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <form method="get" class="d-flex gap-2">
        <select name="id_operateur" class="form-select" onchange="this.form.submit()">
            <option value="">-- Tous les opérateurs --</option>
            <?php foreach ($operateurs as $op): ?>
                <option value="<?= $op['id'] ?>" <?= (($_GET['id_operateur'] ?? '') == $op['id']) ? 'selected' : '' ?>>
                    <?= esc($op['libelle']) ?> (<?= esc($op['code']) ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </form>
    <a href="/baremes/create" class="btn btn-primary">+ Nouvelle tranche</a>
</div>

<?php if (empty($groupes)): ?>
    <p class="text-muted">Aucune tranche de frais configurée.</p>
<?php endif; ?>

<?php foreach ($groupes as $groupe): ?>
<div class="card mb-4">
    <div class="card-header" style="background-color: rgba(95, 115, 83, 0.08); border-bottom: 2px solid var(--mm-olive-600);">
<strong><?= esc($groupe['libelle']) ?></strong>
    </div>
    <div class="card-body">
        <div class="row">
            <?php foreach (['depot' => 'Dépôt', 'retrait' => 'Retrait', 'transfert' => 'Transfert'] as $cle => $label): ?>
            <div class="col-md-4 border-end">
                <h5 class="text-center"><?= $label ?></h5>
                <?php if (empty($groupe['types'][$cle])): ?>
                    <p class="text-muted text-center small">Aucun frais défini</p>
                <?php else: ?>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Montant</th>
                                <th>Frais</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($groupe['types'][$cle] as $ligne): ?>
                            <tr>
                                <td>
                                    <?= number_format($ligne['montant_min'], 0, ',', ' ') ?>
                                    -
                                    <?= number_format($ligne['montant_max'], 0, ',', ' ') ?>
                                </td>
                                <td><?= number_format($ligne['frais'], 0, ',', ' ') ?> Ar</td>
                                <td class="text-nowrap">
                                    <a href="/baremes/edit/<?= $ligne['id'] ?>" class="btn btn-sm btn-outline-primary">✎</a>
                                    <a href="/baremes/delete/<?= $ligne['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer cette tranche ?')">✕</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?= $this->endSection() ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon compte - Mobile Money</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link href="<?= base_url('assets/css/mobile-money.css') ?>" rel="stylesheet">
</head>
<body class="mm-page">
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Bonjour, <?= esc($client['numero']) ?></h4>
        <a href="<?= site_url('logout') ?>" class="btn btn-outline-secondary btn-sm">Déconnexion</a>
    </div>

    <?php if (!empty($notifications)): ?>
    <?php foreach ($notifications as $notif): ?>
        <div class="mm-notification d-flex align-items-center gap-2 px-3 py-2 mb-2 rounded" style="font-size: 0.85rem;">
            <span>💸</span>
            <span>
                Vous avez reçu <strong><?= number_format($notif['montant'], 0, ',', ' ') ?> Ar</strong>
                de <strong><?= esc($notif['numero']) ?></strong>
            </span>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>


    <div class="card mm-balance-card mb-4">
        <div class="card-body">
            <h6 class="card-subtitle mb-1">Solde actuel</h6>
            <h2 class="card-title mb-0"><?= number_format($client['solde'], 0, ',', ' ') ?> Ar</h2>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-4">
            <a href="<?= site_url('depot') ?>" class="btn btn-success w-100">Dépôt</a>
        </div>
        <div class="col-4">
            <a href="<?= site_url('retrait') ?>" class="btn btn-warning w-100">Retrait</a>
        </div>
        <div class="col-4">
            <a href="<?= site_url('transfert') ?>" class="btn btn-info w-100 text-white">Transfert</a>
        </div>
    </div>

    <h5>Historique récent</h5>
    <table class="table table-striped bg-white">
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Montant</th>
                <th>Frais</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($historique)): ?>
                <tr><td colspan="4" class="text-center text-muted">Aucune opération pour le moment</td></tr>
            <?php else: ?>
                <?php foreach ($historique as $op): ?>
                    <tr>
                        <td><?= esc($op['date_operation']) ?></td>
                        <td><?= esc($op['id_type_operation']) ?></td>
                        <td><?= number_format($op['montant'], 0, ',', ' ') ?> Ar</td>
                        <td><?= number_format($op['frais'], 0, ',', ' ') ?> Ar</td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

</div>
</body>
</html>

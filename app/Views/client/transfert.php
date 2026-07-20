<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Transfert - Mobile Money</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card shadow-sm" style="width: 350px;">
        <div class="card-body">
            <h4 class="card-title text-center mb-4">Transfert</h4>

            <p class="text-muted text-center">Solde disponible : <?= number_format($client['solde'], 0, ',', ' ') ?> Ar</p>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <form action="<?= site_url('transfert/valider') ?>" method="post">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Numéro du destinataire</label>
                    <input type="text" name="numero_destinataire" class="form-control"
                           placeholder="Ex: 0339876543" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Montant à transférer (Ar)</label>
                    <input type="number" name="montant" class="form-control" min="1" required>
                </div>
                <button type="submit" class="btn btn-info w-100 text-white">Valider le transfert</button>
                <a href="<?= site_url('dashboard') ?>" class="btn btn-link w-100 mt-2">Annuler</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
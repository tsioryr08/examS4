<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Transfert - Mobile Money</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link href="<?= base_url('assets/css/mobile-money.css') ?>" rel="stylesheet">
</head>
<body class="mm-page">
<div class="mm-shell">
    <div class="card mm-card mm-fade-in" style="width: 350px;">
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

                <div class="mb-3 form-check">
                    <input type="checkbox" name="inclure_frais_retrait" value="1" class="form-check-input" id="inclureFraisRetrait">
                    <label class="form-check-label" for="inclureFraisRetrait">
                        Inclure les frais de retrait du destinataire
                    </label>
                </div>

                <button type="submit" class="btn btn-info w-100 text-white">Valider le transfert</button>
                <a href="<?= site_url('dashboard') ?>" class="btn btn-link w-100 mt-2">Annuler</a>
            </form>

            <hr>
            <a href="<?= site_url('transfert/multiple') ?>" class="btn btn-info w-100 btn-sm">
                Envoi multiple vers plusieurs numéros
            </a>
        </div>
    </div>
</div>
</body>
</html>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Envoi multiple - Mobile Money</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card shadow-sm" style="width: 400px;">
        <div class="card-body">
            <h4 class="card-title text-center mb-4">Envoi multiple</h4>
            <p class="text-muted text-center">Solde disponible : <?= number_format($client['solde'], 0, ',', ' ') ?> Ar</p>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <form action="<?= site_url('transfert/multiple/valider') ?>" method="post">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label class="form-label">Montant total à répartir (Ar)</label>
                    <input type="number" name="montant_total" class="form-control" min="1" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Numéros destinataires (un par ligne)</label>
                    <textarea name="numeros" class="form-control" rows="5"
                              placeholder="0339876543&#10;0331112233&#10;0334445566" required></textarea>
                    <small class="text-muted">Le montant sera divisé également entre chaque numéro.</small>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" name="inclure_frais_retrait" value="1" class="form-check-input" id="inclureFrais">
                    <label class="form-check-label" for="inclureFrais">
                        Inclure les frais de retrait pour tous les destinataires
                    </label>
                </div>
                <button type="submit" class="btn btn-info w-100 text-white">Envoyer</button>
                <a href="<?= site_url('dashboard') ?>" class="btn btn-link w-100 mt-2">Annuler</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
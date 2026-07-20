<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Mobile Money</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link href="<?= base_url('assets/css/mobile-money.css') ?>" rel="stylesheet">
</head>
<body class="mm-page">
    <div class="mm-shell">
        <div class="card mm-card mm-fade-in" style="width: 350px;">
            <div class="card-body">
                <h4 class="card-title text-center mb-4">Connexion</h4>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger">
                        <?= esc(session()->getFlashdata('error')) ?>
                    </div>
                <?php endif; ?>

                <form action="<?= site_url('login/verifier') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label" for="numero">Numéro de téléphone</label>
                        <input id="numero" type="text" name="numero" class="form-control"
                               placeholder="Ex: 0331234567" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                </form>
                <div class="d-flex">
                    <a href="<?= site_url('operateurs') ?>" class="btn btn-outline-secondary w-100">Accéder à l'espace opérateur</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

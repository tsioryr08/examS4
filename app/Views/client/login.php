<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Pay-EO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link href="<?= base_url('assets/css/mobile-money.css') ?>" rel="stylesheet">
</head>
<body class="mm-page">
    <div class="mm-shell">
        <div class="card mm-card mm-fade-in" style="width: 350px;">
            <div class="card-body">
                <div class="text-center mb-5">
                    <h5 style="color: var(--mm-olive-900); font-weight: 700; margin-bottom: 0.5rem;">Pay-EO</h5>
                    <p style="color: var(--mm-terracotta-600); font-size: 0.9rem; margin-bottom: 0;">Mobile money</p>
                </div>

                <div class="mb-3">
                    <a href="<?= site_url('operateurs') ?>" class="btn btn-outline-primary w-100 btn-sm">Accéder à l'espace opérateur</a>
                </div>

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
            </div>
        </div>
    </div>
</body>
</html>

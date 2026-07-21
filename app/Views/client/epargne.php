<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Epargne</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">

</head>
<body class="bg-light">

<div>
    <div>
        <div>
            <h4>Epargne auto repart</h4>
            <p>
                solde epargne actuelle : <strong><?= number_format($client['epargne'],0, ',', ' ')?> Ar</strong>
            </p>

            <?php if(session()-> getFlashData('error')); ?>
             <div><?= esc(session()->getFlashData('error'))?></div>

             <form action="<?= site_url('epargne/valider') ?>" method="post"></form>
<div>
    <p>pourcentage epargne</p>
        <input type="number" name="pourcentage" value = "<?esc($client['pourcent_epargne']) ?>" required>
</div>
    <button type="submit">enregistrer</button>
            </div>
    </div>
</div>
    
</body>
</html>
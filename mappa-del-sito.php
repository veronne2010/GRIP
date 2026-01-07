<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/ente.php';
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mappa del Sito - <?= $ente_nome ?> - GRIP</title>
</head>
<body>
<header>
    <?php include SITE_ROOT . '/assets/header/slim.php'; ?>
    <?php include SITE_ROOT . '/assets/header/centrale.php'; ?>
</header>
    <br /><h4 align="center">Mappa del Sito per <?= $ente_nome ?> - GRIP</h4><br />
    <nav align="center">
        <a href="/mappa-del-sito.php">Mappa del Sito</a><br /><br />
<a href="/index.php">Home</a><br /><br />
<a href="/applicazione/">Dashboard</a><br /><br />
<a href="/applicazione/pazienti/">Pazienti</a><br /><br />
<a href="/applicazione/pazienti/cartelle">Cartelle</a><br /><br />
<a href="/privacy.php">Privacy Policy</a><br /><br />
<a href="/login/">Login</a><br /><br />
<a href="/applicazione/logout.php">Logout</a><br /><br /><br />
    </nav>
<footer>
    <?php include SITE_ROOT . '/assets/footer/footer_login.php'; ?>
</footer>
</body>
</html>
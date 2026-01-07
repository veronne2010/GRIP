<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/ente.php';
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GRIP</title>
    <link rel="stylesheet" href="/bootstrap-italia/dist/css/bootstrap-italia.min.css">
    <link rel="stylesheet" href="/custom/style.css">
    <script src="/bootstrap-italia/dist/js/bootstrap-italia.bundle.min.js" defer></script>
    <script src="/custom/script.js" defer></script>
</head>
<body>
    <header>
        <?php include SITE_ROOT . '/assets/header/slim_accettato.php'; ?>
        <?php include SITE_ROOT . '/assets/header/centrale_app.php'; ?>
    </header>
    <?php include SITE_ROOT . '/assets/toolbar/toolbar_pz.php'; ?>
    <main class="main-content">
        <?php include SITE_ROOT . '/applicazione/pazienti/nuovo/nuovo.php'; ?>
    </main>
    <footer>
        <?php include SITE_ROOT . '/assets/footer/footer_login.php'; ?>
    </footer>
</body>
</html>
<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GRIP</title>
    <link rel="stylesheet" href="/bootstrap-italia/dist/css/bootstrap-italia.min.css">
    <script src="/bootstrap-italia/dist/js/bootstrap-italia.bundle.min.js" defer></script>
</head>
<body>
    <header>
        <?php include SITE_ROOT . '/assets/header/slim_accettato.php'; ?>
        <?php include SITE_ROOT . '/assets/header/centrale_app.php'; ?>
    </header>
    <main>
        <?php include SITE_ROOT . '/applicazione/index/schede/toolbar-admin.php'; ?>
    </main>
    <footer>
        <?php include SITE_ROOT . '/assets/footer/footer_login.php'; ?>
    </footer>
</body>
</html>
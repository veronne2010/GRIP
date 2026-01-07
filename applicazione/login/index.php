<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $password = $_POST['password'];

    // Query per email o codice personale
    $stmt = $pdo->prepare("
        SELECT id_utente, nome_cognome, iniziali, email, codice_personale, password_hash, id_ruolo, id_struttura, attivo 
        FROM utenti 
        WHERE (email = ? OR codice_personale = ?) AND attivo = 1
    ");
    $stmt->execute([$login, $login]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        // login OK
        $_SESSION['user_id'] = $user['id_utente'];
        $_SESSION['username'] = $user['nome_cognome'];
        $_SESSION['iniziali'] = $user['iniziali'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['codice_personale'] = $user['codice_personale'];
        $_SESSION['id_ruolo'] = $user['id_ruolo'];
        $_SESSION['id_struttura'] = $user['id_struttura'];

        // Aggiorna ultimo accesso
        $stmt = $pdo->prepare("UPDATE utenti SET ultimo_accesso = NOW() WHERE id_utente = ?");
        $stmt->execute([$user['id_utente']]);

        session_regenerate_id(true);

        header("Location: /applicazione/");
        exit;
    } else {
        $error = "Credenziali non valide o utente inattivo";
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GRIP - Login Utenti</title>
    <link rel="stylesheet" href="/bootstrap-italia/dist/css/bootstrap-italia.min.css">
    <script src="/bootstrap-italia/dist/js/bootstrap-italia.bundle.min.js" defer></script>
</head>
<body>
    <header>
        <?php include SITE_ROOT . '/assets/header/slim_login.php'; ?>
        <?php include SITE_ROOT . '/assets/header/centrale_login.php'; ?>
    </header>
    <main>
        <br />
        <br />
        <?php include SITE_ROOT . '/applicazione/login/login.php'; ?>
        <br />
        <br />
    </main>
   <footer>
        <?php include SITE_ROOT . '/assets/footer/footer_login.php'; ?>
    </footer>
</body>
</html>
<?php
// account.php - Pagina Account Utente GRIP
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/ente.php';
session_start();

if (!isset($_SESSION['utente_id'])) {
    header('Location: /applicazione/login.php');
    exit;
}

$utente_id = $_SESSION['utente_id'];

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $stmt = $pdo->prepare("SELECT username, nome, cognome, email, ruolo, created_at FROM utenti WHERE id = :id");
    $stmt->execute(['id' => $utente_id]);
    $utente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$utente) {
        die('Utente non trovato');
    }

} catch (PDOException $e) {
    die('Errore DB: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Account Utente | GRIP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/grip.css">
</head>
<body>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/layout/header.php'; ?>

<main class="container">
    <h1>Account Utente</h1>

    <section class="card">
        <h2>Dati personali</h2>
        <table class="table">
            <tr>
                <th>Username</th>
                <td><?= htmlspecialchars($utente['username']) ?></td>
            </tr>
            <tr>
                <th>Nome</th>
                <td><?= htmlspecialchars($utente['nome']) ?></td>
            </tr>
            <tr>
                <th>Cognome</th>
                <td><?= htmlspecialchars($utente['cognome']) ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?= htmlspecialchars($utente['email']) ?></td>
            </tr>
            <tr>
                <th>Ruolo</th>
                <td><?= htmlspecialchars($utente['ruolo']) ?></td>
            </tr>
            <tr>
                <th>Account creato il</th>
                <td><?= htmlspecialchars(date('d/m/Y', strtotime($utente['created_at']))) ?></td>
            </tr>
        </table>
    </section>

    <section class="card">
        <h2>Sicurezza</h2>
        <div class="actions">
            <a href="/account/cambia_password.php" class="btn">Cambia password</a>
            <a href="/logout.php" class="btn btn-danger">Logout</a>
        </div>
    </section>
</main>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/layout/footer.php'; ?>

</body>
</html>
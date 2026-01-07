<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/ente.php';
?>
<?php
// header.php - Header dinamico

// db_config.php - Configurazione database

// Connessione al database
try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Errore di connessione: " . $e->getMessage());
}

// Per test, usa il primo utente del database
$id_utente = 1; // Cambia questo con l'ID dell'utente che vuoi visualizzare

// Query per recuperare il nome dell'utente
$sql = "SELECT nome_cognome FROM utenti WHERE id_utente = :id_utente AND attivo = 1";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id_utente' => $id_utente]);
$utente = $stmt->fetch();

if (!$utente) {
    $nome_cognome = "Utente";
} else {
    $nome_cognome = $utente['nome_cognome'];
}
?>
<div class="it-header-slim-wrapper">
  <div class="container-xxl">
    <div class="row">
      <div class="col-12">
        <div class="it-header-slim-wrapper-content">
          <a class="d-lg-block navbar-brand">Gestione Record Informazioni Pazienti - GRIP</a>
          <div class="it-header-slim-right-zone">
            <a class="btn btn-primary btn-icon btn-full">
              <span class="rounded-icon">
                <svg class="icon icon-primary">
                  <use href="/bootstrap-italia/dist/svg/sprites.svg#it-user"></use>
                </svg>
              </span>
              <span class="d-none d-lg-block"><?= htmlspecialchars($nome_cognome) ?></span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
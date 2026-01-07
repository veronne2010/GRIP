<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/ente.php';
?>
<?php
// cartelle_cliniche.php - Pagina dinamica delle cartelle cliniche

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

// Query per recuperare tutti i pazienti con le loro cartelle cliniche
$sql = "SELECT 
    p.id_paziente,
    p.nome,
    p.cognome,
    p.codice_fiscale,
    s.nome_struttura,
    cc.id_cartella,
    cc.data_apertura,
    cc.stato
FROM pazienti p
LEFT JOIN strutture s ON p.id_struttura = s.id_struttura
LEFT JOIN cartelle_cliniche cc ON p.id_paziente = cc.id_paziente AND cc.attiva = 1
WHERE p.attivo = 1
ORDER BY p.cognome, p.nome";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$pazienti = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cartelle Cliniche - GRIP</title>
    <link rel="stylesheet" href="/bootstrap-italia/dist/css/bootstrap-italia.min.css">
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
  <div class="row">
    <div class="col-sm">
          <h4 class="it-card-profile-name">
            <a>Cartelle Cliniche</a>
          </h4>
    </div>
    <div class="col-sm"></div>
    <div class="col-sm">
        <button type="button" class="btn btn-outline-primary" onclick="location.href='crea_cartella.php'">Crea Cartella</button>
    </div>
  </div>
</div>
<br />
<br />

<div class="container">
<?php if (empty($pazienti)): ?>
    <div class="alert alert-info" role="alert">
        <strong>Nessun paziente trovato.</strong>
    </div>
<?php else: ?>
<table class="table table-hover">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Nome</th>
      <th scope="col">Cognome</th>
      <th scope="col">Struttura</th>
      <th scope="col">Codice Fiscale</th>
      <th scope="col">Cartella</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($pazienti as $index => $paziente): ?>
    <tr>
      <th scope="row"><?= $index + 1 ?></th>
      <td><?= htmlspecialchars($paziente['nome']) ?></td>
      <td><?= htmlspecialchars($paziente['cognome']) ?></td>
      <td><?= htmlspecialchars($paziente['nome_struttura'] ?? 'N/D') ?></td>
      <td><?= htmlspecialchars($paziente['codice_fiscale']) ?></td>
      <td>
        <?php if ($paziente['id_cartella']): ?>
          <!-- Ha già una cartella clinica -->
          <a href="modifica_cartella.php?id=<?= $paziente['id_cartella'] ?>" title="Modifica cartella">
            <svg class="icon icon-primary"><use href="/bootstrap-italia/dist/svg/sprites.svg#it-pencil"></use></svg>
          </a>
          &nbsp;&nbsp;
          <a href="visualizza_cartella.php?id=<?= $paziente['id_cartella'] ?>" title="Visualizza cartella">
            <svg class="icon icon-primary"><use href="/bootstrap-italia/dist/svg/sprites.svg#it-password-visible"></use></svg>
          </a>
        <?php else: ?>
          <!-- Non ha ancora una cartella clinica -->
          <a href="crea_cartella.php?id_paziente=<?= $paziente['id_paziente'] ?>" class="btn btn-sm btn-outline-primary" title="Crea cartella">
            <svg class="icon icon-sm"><use href="/bootstrap-italia/dist/svg/sprites.svg#it-plus"></use></svg>
            Crea
          </a>
        <?php endif; ?>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>
</div>

<script src="/bootstrap-italia/dist/js/bootstrap-italia.bundle.min.js"></script>
</body>
</html>
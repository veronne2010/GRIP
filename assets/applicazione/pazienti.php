<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/ente.php';
?>
<?php
// pazienti.php - Pagina dinamica dei pazienti



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

// Query per recuperare tutti i pazienti
$sql = "SELECT 
    p.id_paziente,
    p.nome,
    p.cognome,
    p.codice_fiscale,
    s.nome_struttura
FROM pazienti p
LEFT JOIN strutture s ON p.id_struttura = s.id_struttura
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
    <title>Pazienti - GRIP</title>
    <link rel="stylesheet" href="/bootstrap-italia/dist/css/bootstrap-italia.min.css">
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
  <div class="row">
    <div class="col-sm">
          <h4 class="it-card-profile-name">
            <a>Pazienti</a>
          </h4>
    </div>
    <div class="col-sm"></div>
    <div class="col-sm">
        <button type="button" class="btn btn-outline-primary" onclick="location.href='/applicazione/pazienti/nuovo'">Nuovo Paziente</button>
    </div>
  </div>
</div>
<br />
<br />

<div class="container">
<?php if (empty($pazienti)): ?>
    <div class="alert alert-info" role="alert">
        <strong>Nessun paziente trovato.</strong> Clicca su "Nuovo Paziente" per aggiungerne uno.
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
      <th scope="col">Azioni</th>
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
        <button type="button" class="btn btn-sm btn-outline-primary" onclick="location.href='visualizza_paziente.php?id=<?= $paziente['id_paziente'] ?>'">
          <svg class="icon icon-sm"><use href="/bootstrap-italia/dist/svg/sprites.svg#it-search"></use></svg>
          Visualizza
        </button>
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.href='modifica_paziente.php?id=<?= $paziente['id_paziente'] ?>'">
          <svg class="icon icon-sm"><use href="/bootstrap-italia/dist/svg/sprites.svg#it-pencil"></use></svg>
          Modifica
        </button>
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
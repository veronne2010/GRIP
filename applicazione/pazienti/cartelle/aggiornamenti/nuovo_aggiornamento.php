<?php
// nuovo_aggiornamento.php - Form per inserire un nuovo aggiornamento

require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/ente.php';

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

// Recupera le cartelle cliniche attive con i dati del paziente
$sql = "SELECT 
    cc.id_cartella,
    p.nome,
    p.cognome,
    p.codice_fiscale,
    s.nome_struttura
FROM cartelle_cliniche cc
INNER JOIN pazienti p ON cc.id_paziente = p.id_paziente
LEFT JOIN strutture s ON p.id_struttura = s.id_struttura
WHERE cc.attiva = 1 AND cc.stato != 'chiusa'
ORDER BY p.cognome, p.nome";

$stmt = $pdo->query($sql);
$cartelle = $stmt->fetchAll();

$errori = [];
$successo = false;
$id_utente_corrente = 1; // Cambierà quando avrai il sistema di login

// Pre-selezione cartella se arriva da URL
$id_cartella_preselezionata = isset($_GET['id_cartella']) ? (int)$_GET['id_cartella'] : null;

// Gestione del submit del form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_cartella = $_POST['id_cartella'] ?? null;
    $data_seduta = $_POST['data_seduta'] ?? '';
    $note = trim($_POST['note'] ?? '');
    
    // Validazioni
    if (empty($id_cartella)) {
        $errori[] = "Seleziona una cartella clinica";
    }
    
    if (empty($data_seduta)) {
        $errori[] = "La data della seduta è obbligatoria";
    }
    
    if (empty($note)) {
        $errori[] = "Le note sono obbligatorie";
    }
    
    // Se non ci sono errori, inserisci nel database
    if (empty($errori)) {
        try {
            $sql = "INSERT INTO aggiornamenti (id_cartella, id_utente, data_seduta, note) 
                    VALUES (:id_cartella, :id_utente, :data_seduta, :note)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'id_cartella' => $id_cartella,
                'id_utente' => $id_utente_corrente,
                'data_seduta' => $data_seduta,
                'note' => $note
            ]);
            
            $id_aggiornamento_nuovo = $pdo->lastInsertId();
            $successo = true;
            
            header("refresh:2;url=visualizza_aggiornamento.php?id=$id_aggiornamento_nuovo");
            
        } catch (PDOException $e) {
            $errori[] = "Errore durante l'inserimento: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuovo Aggiornamento - GRIP</title>
    <link rel="stylesheet" href="/bootstrap-italia/dist/css/bootstrap-italia.min.css">
</head>
<body>

<header>
    <?php include SITE_ROOT . '/assets/header/slim_accettato.php'; ?>
    <?php include SITE_ROOT . '/assets/header/centrale_app.php'; ?>
</header>

<?php include SITE_ROOT . '/assets/toolbar/toolbar_ag.php'; ?>

<?php if ($successo): ?>
<div class="container mt-3">
    <div class="alert alert-success" role="alert">
        <strong>Aggiornamento inserito con successo!</strong> Verrai reindirizzato...
    </div>
</div>
<?php endif; ?>

<?php if (!empty($errori)): ?>
<div class="container mt-3">
    <div class="alert alert-danger" role="alert">
        <strong>Errori:</strong>
        <ul class="mb-0">
            <?php foreach ($errori as $errore): ?>
                <li><?= htmlspecialchars($errore) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php endif; ?>

<br />
<div class="container">
  <div class="row">
    <div class="col-sm">
          <h4 class="it-card-profile-name">
            <a>Nuovo Aggiornamento</a>
          </h4>
    </div>
    <div class="col-sm"></div>
    <div class="col-sm text-end">
        <button type="button" class="btn btn-outline-secondary" onclick="location.href='/applicazione/pazienti/cartelle/aggiornamenti/'">
            <svg class="icon icon-sm"><use href="/bootstrap-italia/dist/svg/sprites.svg#it-arrow-left"></use></svg>
            Annulla
        </button>
    </div>
  </div>
</div>
<br />

<?php if (empty($cartelle)): ?>
<div class="container">
    <div class="alert alert-warning" role="alert">
        <strong>Attenzione!</strong> Non ci sono cartelle cliniche attive. 
        <a href="crea_cartella.php" class="alert-link">Crea una cartella clinica</a> prima di aggiungere aggiornamenti.
    </div>
</div>
<?php else: ?>

<div class="container">
<form method="POST" action="">
  <div class="row">
    <div class="form-group col-md-12 mb-3">
        <label for="Cartella">Paziente / Cartella Clinica <span class="text-danger">*</span></label>
        <select id="Cartella" name="id_cartella" class="form-select" required>
            <option value="">Seleziona un paziente</option>
            <?php foreach ($cartelle as $cartella): ?>
                <option value="<?= $cartella['id_cartella'] ?>"
                        <?= ($id_cartella_preselezionata == $cartella['id_cartella']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cartella['cognome'] . ' ' . $cartella['nome']) ?> 
                    - CF: <?= htmlspecialchars($cartella['codice_fiscale']) ?>
                    <?php if ($cartella['nome_struttura']): ?>
                        - <?= htmlspecialchars($cartella['nome_struttura']) ?>
                    <?php endif; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
  </div>
  
  <div class="row">
    <div class="form-group col-md-12 mb-3">
      <label for="data_seduta">Data Seduta <span class="text-danger">*</span></label>
      <input type="date" class="form-control" id="data_seduta" name="data_seduta" 
             value="<?= htmlspecialchars($_POST['data_seduta'] ?? date('Y-m-d')) ?>" required>
    </div>
  </div>
  
  <div class="row">
    <div class="form-group col-md-12 mb-3">
      <label for="note">Note della Seduta <span class="text-danger">*</span></label>
      <textarea class="form-control" id="note" name="note" rows="10" required 
                placeholder="Inserisci le note della seduta..."><?= htmlspecialchars($_POST['note'] ?? '') ?></textarea>
      <small class="form-text">Descrivi gli argomenti trattati, i progressi e le osservazioni</small>
    </div>
  </div>
  
  <br />
  <div class="row">
    <div class="col-sm">
    </div>
    <div class="col-sm">
        <button type="submit" class="btn btn-primary">
            <svg class="icon icon-sm icon-white"><use href="/bootstrap-italia/dist/svg/sprites.svg#it-check"></use></svg>
            Salva Aggiornamento
        </button>
    </div>
    <div class="col-sm">
    </div>
  </div>
</form>
</div>

<?php endif; ?>

<script src="/bootstrap-italia/dist/js/bootstrap-italia.bundle.min.js"></script>
<br />
<br />
<footer>
    <?php include SITE_ROOT . '/assets/footer/footer_login.php'; ?>
</footer>
</body>
</html>
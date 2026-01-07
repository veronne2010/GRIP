<?php
// modifica_aggiornamento.php - Form per modificare un aggiornamento

require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/ente.php';

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

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: /applicazione/pazienti/cartelle/aggiornamenti/');
    exit;
}

$id_aggiornamento = $_GET['id'];

$errori = [];
$successo = false;

// Recupera i dati dell'aggiornamento
$sql = "SELECT 
    a.*,
    p.nome AS paziente_nome,
    p.cognome AS paziente_cognome,
    p.codice_fiscale
FROM aggiornamenti a
INNER JOIN cartelle_cliniche cc ON a.id_cartella = cc.id_cartella
INNER JOIN pazienti p ON cc.id_paziente = p.id_paziente
WHERE a.id_aggiornamento = :id AND a.attivo = 1";

$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id_aggiornamento]);
$aggiornamento = $stmt->fetch();

if (!$aggiornamento) {
    header('Location: /applicazione/pazienti/cartelle/aggiornamenti/');
    exit;
}

// Gestione del submit del form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data_seduta = $_POST['data_seduta'] ?? '';
    $note = trim($_POST['note'] ?? '');
    
    // Validazioni
    if (empty($data_seduta)) {
        $errori[] = "La data della seduta è obbligatoria";
    }
    
    if (empty($note)) {
        $errori[] = "Le note sono obbligatorie";
    }
    
    // Se non ci sono errori, aggiorna il database
    if (empty($errori)) {
        try {
            $sql = "UPDATE aggiornamenti 
                    SET data_seduta = :data_seduta, 
                        note = :note
                    WHERE id_aggiornamento = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'data_seduta' => $data_seduta,
                'note' => $note,
                'id' => $id_aggiornamento
            ]);
            
            $successo = true;
            
            // Ricarica i dati aggiornati
            $stmt = $pdo->prepare("SELECT a.*, p.nome AS paziente_nome, p.cognome AS paziente_cognome, p.codice_fiscale 
                                   FROM aggiornamenti a 
                                   INNER JOIN cartelle_cliniche cc ON a.id_cartella = cc.id_cartella 
                                   INNER JOIN pazienti p ON cc.id_paziente = p.id_paziente 
                                   WHERE a.id_aggiornamento = :id");
            $stmt->execute(['id' => $id_aggiornamento]);
            $aggiornamento = $stmt->fetch();
            
            header("refresh:2;url=visualizza_aggiornamento.php?id=$id_aggiornamento");
            
        } catch (PDOException $e) {
            $errori[] = "Errore durante l'aggiornamento: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Aggiornamento - GRIP</title>
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
        <strong>Aggiornamento modificato con successo!</strong> Verrai reindirizzato...
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
            <a>Modifica Aggiornamento</a>
          </h4>
    </div>
    <div class="col-sm"></div>
    <div class="col-sm text-end">
        <button type="button" class="btn btn-outline-secondary" onclick="location.href='visualizza_aggiornamento.php?id=<?= $id_aggiornamento ?>'">
            <svg class="icon icon-sm"><use href="/bootstrap-italia/dist/svg/sprites.svg#it-arrow-left"></use></svg>
            Annulla
        </button>
    </div>
  </div>
</div>
<br />

<div class="container">
    <!-- Info Paziente (non modificabile) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info" role="alert">
                <strong>Paziente:</strong> 
                <?= htmlspecialchars($aggiornamento['paziente_cognome'] . ' ' . $aggiornamento['paziente_nome']) ?> 
                - CF: <?= htmlspecialchars($aggiornamento['codice_fiscale']) ?>
            </div>
        </div>
    </div>

    <form method="POST" action="">
      <div class="row">
        <div class="form-group col-md-12 mb-3">
          <label for="data_seduta">Data Seduta <span class="text-danger">*</span></label>
          <input type="date" class="form-control" id="data_seduta" name="data_seduta" 
                 value="<?= htmlspecialchars($aggiornamento['data_seduta']) ?>" required>
        </div>
      </div>
      
      <div class="row">
        <div class="form-group col-md-12 mb-3">
          <label for="note">Note della Seduta <span class="text-danger">*</span></label>
          <textarea class="form-control" id="note" name="note" rows="10" required><?= htmlspecialchars($aggiornamento['note']) ?></textarea>
        </div>
      </div>
      
      <br />
      <div class="row">
        <div class="col-sm">
        </div>
        <div class="col-sm">
            <button type="submit" class="btn btn-primary">
                <svg class="icon icon-sm icon-white"><use href="/bootstrap-italia/dist/svg/sprites.svg#it-check"></use></svg>
                Salva Modifiche
            </button>
        </div>
        <div class="col-sm">
        </div>
      </div>
    </form>
</div>

<script src="/bootstrap-italia/dist/js/bootstrap-italia.bundle.min.js"></script>
<br />
<br />
<footer>
    <?php include SITE_ROOT . '/assets/footer/footer_login.php'; ?>
</footer>
</body>
</html>
<?php
// modifica_cartella.php - Form per modificare una cartella clinica

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

// Verifica se è stato passato l'ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: cartelle_cliniche.php');
    exit;
}

$id_cartella = $_GET['id'];

$errori = [];
$successo = false;

// Recupera i dati della cartella clinica
$sql = "SELECT 
    cc.*,
    p.nome AS paziente_nome,
    p.cognome AS paziente_cognome,
    p.codice_fiscale
FROM cartelle_cliniche cc
INNER JOIN pazienti p ON cc.id_paziente = p.id_paziente
WHERE cc.id_cartella = :id_cartella AND cc.attiva = 1";

$stmt = $pdo->prepare($sql);
$stmt->execute(['id_cartella' => $id_cartella]);
$cartella = $stmt->fetch();

if (!$cartella) {
    header('Location: cartelle_cliniche.php');
    exit;
}

// Gestione del submit del form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data_apertura = $_POST['data_apertura'] ?? '';
    $stato = trim($_POST['stato'] ?? '');
    $note = trim($_POST['note'] ?? '');
    
    // Validazioni
    if (empty($data_apertura)) {
        $errori[] = "La data di apertura è obbligatoria";
    }
    
    if (empty($stato)) {
        $errori[] = "Lo stato è obbligatorio";
    }
    
    // Se non ci sono errori, aggiorna il database
    if (empty($errori)) {
        try {
            $sql = "UPDATE cartelle_cliniche 
                    SET data_apertura = :data_apertura, 
                        stato = :stato, 
                        note = :note
                    WHERE id_cartella = :id_cartella";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'data_apertura' => $data_apertura,
                'stato' => $stato,
                'note' => $note,
                'id_cartella' => $id_cartella
            ]);
            
            $successo = true;
            
            // Ricarica i dati aggiornati
            $stmt = $pdo->prepare("SELECT cc.*, p.nome AS paziente_nome, p.cognome AS paziente_cognome, p.codice_fiscale 
                                   FROM cartelle_cliniche cc 
                                   INNER JOIN pazienti p ON cc.id_paziente = p.id_paziente 
                                   WHERE cc.id_cartella = :id_cartella");
            $stmt->execute(['id_cartella' => $id_cartella]);
            $cartella = $stmt->fetch();
            
            header("refresh:2;url=visualizza_cartella.php?id=$id_cartella");
            
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
    <title>Modifica Cartella Clinica - GRIP</title>
    <link rel="stylesheet" href="/bootstrap-italia/dist/css/bootstrap-italia.min.css">
</head>
<body>

<header>
    <?php include SITE_ROOT . '/assets/header/slim_accettato.php'; ?>
    <?php include SITE_ROOT . '/assets/header/centrale_app.php'; ?>
</header>

<?php include SITE_ROOT . '/assets/toolbar/toolbar_cc.php'; ?>
<?php if ($successo): ?>
<div class="container mt-3">
    <div class="alert alert-success" role="alert">
        <strong>Cartella clinica aggiornata con successo!</strong> Verrai reindirizzato alla visualizzazione...
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
            <a>Modifica Cartella Clinica #<?= $id_cartella ?></a>
          </h4>
    </div>
    <div class="col-sm"></div>
    <div class="col-sm text-end">
        <button type="button" class="btn btn-outline-secondary" onclick="location.href='visualizza_cartella.php?id=<?= $id_cartella ?>'">
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
                <?= htmlspecialchars($cartella['paziente_cognome'] . ' ' . $cartella['paziente_nome']) ?> 
                - CF: <?= htmlspecialchars($cartella['codice_fiscale']) ?>
            </div>
        </div>
    </div>

    <form method="POST" action="">
      <div class="row">
        <div class="form-group col-md-6 mb-3">
          <label for="data_apertura">Data Apertura <span class="text-danger">*</span></label>
          <input type="date" class="form-control" id="data_apertura" name="data_apertura" 
                 value="<?= htmlspecialchars($cartella['data_apertura']) ?>" required>
        </div>
        
        <div class="form-group col-md-6 mb-3">
            <label for="stato">Stato <span class="text-danger">*</span></label>
            <select id="stato" name="stato" class="form-select" required>
                <option value="aperta" <?= ($cartella['stato'] == 'aperta') ? 'selected' : '' ?>>Aperta</option>
                <option value="in_lavorazione" <?= ($cartella['stato'] == 'in_lavorazione') ? 'selected' : '' ?>>In Lavorazione</option>
                <option value="completata" <?= ($cartella['stato'] == 'completata') ? 'selected' : '' ?>>Completata</option>
                <option value="chiusa" <?= ($cartella['stato'] == 'chiusa') ? 'selected' : '' ?>>Chiusa</option>
            </select>
        </div>
      </div>
      
      <div class="row">
        <div class="form-group col-md-12 mb-3">
          <label for="note">Note</label>
          <textarea class="form-control" id="note" name="note" rows="6"><?= htmlspecialchars($cartella['note']) ?></textarea>
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
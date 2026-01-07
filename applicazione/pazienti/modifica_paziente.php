<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/ente.php';
?>
<?php
// modifica_paziente.php - Form per modificare un paziente


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
    header('Location: pazienti.php');
    exit;
}

$id_paziente = $_GET['id'];

// Recupera le strutture per il select
$sql_strutture = "SELECT id_struttura, nome_struttura FROM strutture ORDER BY nome_struttura";
$stmt_strutture = $pdo->query($sql_strutture);
$strutture = $stmt_strutture->fetchAll();

// Recupera gli psicologi
$sql_psicologi = "SELECT u.id_utente, u.nome_cognome 
                  FROM utenti u
                  INNER JOIN ruoli r ON u.id_ruolo = r.id_ruolo
                  WHERE LOWER(r.ruolo) = 'psicologo' AND u.attivo = 1
                  ORDER BY u.nome_cognome";
$stmt_psicologi = $pdo->query($sql_psicologi);
$psicologi = $stmt_psicologi->fetchAll();

$errori = [];
$successo = false;

// Recupera i dati del paziente
$sql = "SELECT * FROM pazienti WHERE id_paziente = :id_paziente AND attivo = 1";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id_paziente' => $id_paziente]);
$paziente = $stmt->fetch();

if (!$paziente) {
    header('Location: pazienti.php');
    exit;
}

// Gestione del submit del form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $cognome = trim($_POST['cognome'] ?? '');
    $codice_fiscale = strtoupper(trim($_POST['codice_fiscale'] ?? ''));
    $id_struttura = $_POST['id_struttura'] ?? null;
    
    // Validazioni
    if (empty($nome)) {
        $errori[] = "Il nome è obbligatorio";
    }
    if (empty($cognome)) {
        $errori[] = "Il cognome è obbligatorio";
    }
    if (empty($codice_fiscale)) {
        $errori[] = "Il codice fiscale è obbligatorio";
    }
    if (empty($id_struttura)) {
        $errori[] = "La struttura è obbligatoria";
    }
    
    // Controlla se il codice fiscale è già usato da un altro paziente
    if (!empty($codice_fiscale)) {
        $check = $pdo->prepare(
            "SELECT id_paziente FROM pazienti 
             WHERE codice_fiscale = :cf AND id_paziente != :id"
        );
        $check->execute([
            'cf' => $codice_fiscale,
            'id' => $id_paziente
        ]);
        if ($check->fetch()) {
            $errori[] = "Il codice fiscale è già presente nel sistema";
        }
    }
    
    // Se non ci sono errori, aggiorna il database
    if (empty($errori)) {
        try {
            $sql = "UPDATE pazienti 
                    SET nome = :nome, 
                        cognome = :cognome, 
                        codice_fiscale = :codice_fiscale, 
                        id_struttura = :id_struttura
                    WHERE id_paziente = :id_paziente";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'nome' => $nome,
                'cognome' => $cognome,
                'codice_fiscale' => $codice_fiscale,
                'id_struttura' => $id_struttura,
                'id_paziente' => $id_paziente
            ]);
            
            $successo = true;
            
            // Ricarica i dati aggiornati
            $stmt = $pdo->prepare("SELECT * FROM pazienti WHERE id_paziente = :id_paziente");
            $stmt->execute(['id_paziente' => $id_paziente]);
            $paziente = $stmt->fetch();
            
            header("refresh:2;url=visualizza_paziente.php?id=$id_paziente");
            
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
    <title>Modifica Paziente - GRIP</title>
    <link rel="stylesheet" href="/bootstrap-italia/dist/css/bootstrap-italia.min.css">
    <link rel="stylesheet" href="/custom/style.css">
    <script src="/custom/script.js" defer></script>
</head>
<body>

    <header>
        <?php include SITE_ROOT . '/assets/header/slim_accettato.php'; ?>
        <?php include SITE_ROOT . '/assets/header/centrale_app.php'; ?>
    </header>
        <?php include SITE_ROOT . '/assets/toolbar/toolbar_pz.php'; ?>
<?php if ($successo): ?>
<div class="container mt-3">
    <div class="alert alert-success" role="alert">
        <strong>Paziente aggiornato con successo!</strong> Verrai reindirizzato alla visualizzazione...
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
            <a>Modifica Paziente</a>
          </h4>
    </div>
    <div class="col-sm"></div>
    <div class="col-sm text-end">
        <button type="button" class="btn btn-outline-secondary" onclick="location.href='visualizza_paziente.php?id=<?= $id_paziente ?>'">
            <svg class="icon icon-sm"><use href="/bootstrap-italia/dist/svg/sprites.svg#it-arrow-left"></use></svg>
            Annulla
        </button>
    </div>
  </div>
</div>
<br />
<br />

<div class="container">
<form method="POST" action="">
  <div class="row">
    <div class="form-group col-md-6">
      <label for="Nome">Nome</label>
      <input type="text" class="form-control" id="Nome" name="nome" 
             value="<?= htmlspecialchars($paziente['nome']) ?>" required>
    </div>
    <div class="form-group col-md-6">
      <label for="Cognome">Cognome</label>
      <input type="text" class="form-control" id="Cognome" name="cognome" 
             value="<?= htmlspecialchars($paziente['cognome']) ?>" required>
    </div>
  </div>
  <div class="row">
    <div class="form-group col-md-6">
      <label for="Codice_fiscale">Codice Fiscale</label>
      <input type="text" class="form-control text-uppercase" id="Codice_fiscale" name="codice_fiscale" 
             value="<?= htmlspecialchars($paziente['codice_fiscale']) ?>" 
             maxlength="16" required>
    </div>
    <div class="form-group col-md-3 select-wrapper">
        <label for="Struttura">Struttura</label>
        <select id="Struttura" name="id_struttura" class="form-select" required>
            <option value="">Seleziona Struttura</option>
            <?php foreach ($strutture as $struttura): ?>
                <option value="<?= $struttura['id_struttura'] ?>"
                        <?= ($paziente['id_struttura'] == $struttura['id_struttura']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($struttura['nome_struttura']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group col-md-3 select-wrapper">
        <label for="Psicologo">Psicologo (opzionale)</label>
        <select id="Psicologo" name="id_psicologo" class="form-select">
            <option value="">Seleziona Psicologo</option>
            <?php foreach ($psicologi as $psicologo): ?>
                <option value="<?= $psicologo['id_utente'] ?>">
                    <?= htmlspecialchars($psicologo['nome_cognome']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <small class="form-text text-muted">Il campo psicologo è solo a scopo informativo</small>
    </div>
  </div>
  <br />
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
<script>
// Converti automaticamente il codice fiscale in maiuscolo
document.getElementById('Codice_fiscale').addEventListener('input', function(e) {
    e.target.value = e.target.value.toUpperCase();
});
</script>
<br />
<br />
    <footer>
        <?php include SITE_ROOT . '/assets/footer/footer_login.php'; ?>
    </footer>
</body>
</html>
<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/ente.php';

/* =========================
   Connessione DB
========================= */
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
    die("Errore DB: " . $e->getMessage());
}

/* =========================
   Pazienti senza cartella
========================= */
$sql = "
SELECT 
    p.id_paziente,
    p.nome,
    p.cognome,
    p.codice_fiscale,
    s.nome_struttura
FROM pazienti p
LEFT JOIN strutture s ON p.id_struttura = s.id_struttura
LEFT JOIN cartelle_cliniche cc 
    ON p.id_paziente = cc.id_paziente AND cc.attiva = 1
WHERE p.attivo = 1
  AND cc.id_cartella IS NULL
ORDER BY p.cognome, p.nome
";

$pazienti_disponibili = $pdo->query($sql)->fetchAll();

$errori = [];
$successo = false;
$id_utente_corrente = 1;

/* =========================
   Preselezione
========================= */
$id_paziente_preselezionato = $_GET['id_paziente'] ?? null;

/* =========================
   Submit form
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_paziente   = $_POST['id_paziente'] ?? null;
    $data_apertura = $_POST['data_apertura'] ?? null;
    $stato         = $_POST['stato'] ?? 'aperta';
    $note          = trim($_POST['note'] ?? '');

    if (!$id_paziente) {
        $errori[] = "Seleziona un paziente";
    }

    if (!$data_apertura) {
        $errori[] = "La data di apertura è obbligatoria";
    }

    if (empty($errori)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO cartelle_cliniche
                (id_paziente, id_utente_creatore, data_apertura, stato, note)
                VALUES (:p, :u, :d, :s, :n)
            ");

            $stmt->execute([
                'p' => $id_paziente,
                'u' => $id_utente_corrente,
                'd' => $data_apertura,
                's' => $stato,
                'n' => $note
            ]);

            $successo = true;
            header("refresh:2;url=/applicazione/pazienti/cartelle/visualizza_cartella.php?id=" . $pdo->lastInsertId());
        } catch (PDOException $e) {
            $errori[] = $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Crea Cartella Clinica - GRIP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="/bootstrap-italia/dist/css/bootstrap-italia.min.css">
    <link rel="stylesheet" href="/custom/style.css">
</head>

<body>

<header>
    <?php include SITE_ROOT . '/assets/header/slim_accettato.php'; ?>
    <?php include SITE_ROOT . '/assets/header/centrale_app.php'; ?>
</header>

<?php include SITE_ROOT . '/assets/toolbar/toolbar_cc.php'; ?>

<main class="container my-4">

    <!-- Titolo + Annulla -->
    <div class="row align-items-center mb-4">
        <div class="col">
            <h4 class="it-page-title">Crea Nuova Cartella Clinica</h4>
        </div>
        <div class="col-auto">
            <a href="/applicazione/pazienti/cartelle/" class="btn btn-outline-secondary">
                <svg class="icon icon-sm">
                    <use href="/bootstrap-italia/dist/svg/sprites.svg#it-arrow-left"></use>
                </svg>
                Annulla
            </a>
        </div>
    </div>

    <!-- Messaggi -->
    <?php if ($successo): ?>
        <div class="alert alert-success">
            Cartella clinica creata correttamente. Reindirizzamento in corso…
        </div>
    <?php endif; ?>

    <?php if ($errori): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errori as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (!$pazienti_disponibili): ?>
        <div class="alert alert-warning">
            Tutti i pazienti hanno già una cartella clinica attiva.
        </div>
    <?php else: ?>

    <!-- FORM -->
    <form method="post" class="it-form">

        <div class="mb-3">
            <label class="form-label">
                Paziente <span class="text-danger">*</span>
            </label>
            <select name="id_paziente" class="form-select" required>
                <option value="">Seleziona un paziente</option>
                <?php foreach ($pazienti_disponibili as $p): ?>
                    <option value="<?= $p['id_paziente'] ?>"
                        <?= $id_paziente_preselezionato == $p['id_paziente'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['cognome'].' '.$p['nome']) ?>
                        — CF <?= htmlspecialchars($p['codice_fiscale']) ?>
                        <?= $p['nome_struttura'] ? ' — '.$p['nome_struttura'] : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <small class="text-muted">
                Sono mostrati solo i pazienti senza cartella clinica attiva
            </small>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Data apertura *</label>
                <input type="date" name="data_apertura" class="form-control"
                       value="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Stato</label>
                <select name="stato" class="form-select">
                    <option value="aperta">Aperta</option>
                    <option value="in_lavorazione">In lavorazione</option>
                    <option value="completata">Completata</option>
                </select>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label">Note</label>
            <textarea name="note" rows="4" class="form-control"></textarea>
        </div>

        <div class="text-center">
            <button class="btn btn-primary btn-lg">
                <svg class="icon icon-sm icon-white">
                    <use href="/bootstrap-italia/dist/svg/sprites.svg#it-check"></use>
                </svg>
                Crea Cartella
            </button>
        </div>

    </form>
    <?php endif; ?>

</main>

<footer>
    <?php include SITE_ROOT . '/assets/footer/footer_login.php'; ?>
</footer>

<script src="/bootstrap-italia/dist/js/bootstrap-italia.bundle.min.js"></script>
</body>
</html>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/ente.php';
?>
<?php
// visualizza_paziente.php - Visualizza dettagli paziente


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

// Query per recuperare i dati del paziente
$sql = "SELECT 
    p.id_paziente,
    p.nome,
    p.cognome,
    p.codice_fiscale,
    p.created_at,
    p.updated_at,
    s.nome_struttura
FROM pazienti p
LEFT JOIN strutture s ON p.id_struttura = s.id_struttura
WHERE p.id_paziente = :id_paziente AND p.attivo = 1";

$stmt = $pdo->prepare($sql);
$stmt->execute(['id_paziente' => $id_paziente]);
$paziente = $stmt->fetch();

if (!$paziente) {
    header('Location: pazienti.php');
    exit;
}

// Formatta le date
$data_creazione = date('d/m/Y H:i', strtotime($paziente['created_at']));
$data_modifica = date('d/m/Y H:i', strtotime($paziente['updated_at']));
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizza Paziente - GRIP</title>
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

<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Dettagli Paziente</h2>
                <div>
                    <button type="button" class="btn btn-primary" onclick="location.href='modifica_paziente.php?id=<?= $paziente['id_paziente'] ?>'">
                        <svg class="icon icon-sm icon-white"><use href="/bootstrap-italia/dist/svg/sprites.svg#it-pencil"></use></svg>
                        Modifica
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="location.href='/applicazione/pazienti/'">
                        <svg class="icon icon-sm"><use href="/bootstrap-italia/dist/svg/sprites.svg#it-arrow-left"></use></svg>
                        Torna alla lista
                    </button>
                </div>
            </div>

            <article class="it-card it-card-profile it-card-border-top it-card-border-top-primary rounded shadow-sm border">
                <div class="it-card-profile-header">
                    <div class="it-card-profile">
                        <h4 class="it-card-profile-name">
                            <?= htmlspecialchars($paziente['nome'] . ' ' . $paziente['cognome']) ?>
                        </h4>
                        <p class="it-card-profile-role">Paziente</p>
                    </div>
                    <div class="avatar size-xl">
                        <a class="avatar avatar-primary size-xl">
                            <p aria-hidden="true"><?= strtoupper(substr($paziente['nome'], 0, 1) . substr($paziente['cognome'], 0, 1)) ?></p>
                        </a>
                    </div>
                </div>
                <div class="it-card-body">
                    <dl class="it-card-description-list">
                        <div>
                            <dt>ID Paziente</dt>
                            <dd><?= htmlspecialchars($paziente['id_paziente']) ?></dd>
                        </div>
                        <div>
                            <dt>Nome</dt>
                            <dd><?= htmlspecialchars($paziente['nome']) ?></dd>
                        </div>
                        <div>
                            <dt>Cognome</dt>
                            <dd><?= htmlspecialchars($paziente['cognome']) ?></dd>
                        </div>
                        <div>
                            <dt>Codice Fiscale</dt>
                            <dd><?= htmlspecialchars($paziente['codice_fiscale']) ?></dd>
                        </div>
                        <div>
                            <dt>Struttura</dt>
                            <dd><?= htmlspecialchars($paziente['nome_struttura'] ?? 'N/D') ?></dd>
                        </div>
                        <div>
                            <dt>Data Creazione</dt>
                            <dd><?= htmlspecialchars($data_creazione) ?></dd>
                        </div>
                        <div>
                            <dt>Ultima Modifica</dt>
                            <dd><?= htmlspecialchars($data_modifica) ?></dd>
                        </div>
                    </dl>
                </div>
            </article>
        </div>
    </div>
</div>
<br />
<br />
<script src="/bootstrap-italia/dist/js/bootstrap-italia.bundle.min.js"></script>
    <footer>
        <?php include SITE_ROOT . '/assets/footer/footer_login.php'; ?>
    </footer>
</body>
</html>
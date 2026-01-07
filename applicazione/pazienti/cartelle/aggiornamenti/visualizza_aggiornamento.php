<?php
// visualizza_aggiornamento.php - Visualizza dettagli aggiornamento

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

$sql = "SELECT 
    a.*,
    p.nome AS paziente_nome,
    p.cognome AS paziente_cognome,
    p.codice_fiscale,
    cc.id_cartella,
    u.nome_cognome AS psicologo_nome,
    s.nome_struttura
FROM aggiornamenti a
INNER JOIN cartelle_cliniche cc ON a.id_cartella = cc.id_cartella
INNER JOIN pazienti p ON cc.id_paziente = p.id_paziente
INNER JOIN utenti u ON a.id_utente = u.id_utente
LEFT JOIN strutture s ON p.id_struttura = s.id_struttura
WHERE a.id_aggiornamento = :id AND a.attivo = 1";

$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id_aggiornamento]);
$aggiornamento = $stmt->fetch();

if (!$aggiornamento) {
    header('Location: /applicazione/pazienti/cartelle/aggiornamenti/');
    exit;
}

$data_seduta = date('d/m/Y', strtotime($aggiornamento['data_seduta']));
$data_creazione = date('d/m/Y H:i', strtotime($aggiornamento['created_at']));
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aggiornamento - GRIP</title>
    <link rel="stylesheet" href="/bootstrap-italia/dist/css/bootstrap-italia.min.css">
</head>
<body>
<header>
    <?php include SITE_ROOT . '/assets/header/slim_accettato.php'; ?>
    <?php include SITE_ROOT . '/assets/header/centrale_app.php'; ?>
</header>

<?php include SITE_ROOT . '/assets/toolbar/toolbar_det.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Aggiornamento Seduta</h2>
                <div>
                    <button type="button" class="btn btn-primary" onclick="location.href='modifica_aggiornamento.php?id=<?= $id_aggiornamento ?>'">
                        <svg class="icon icon-sm icon-white"><use href="/bootstrap-italia/dist/svg/sprites.svg#it-pencil"></use></svg>
                        Modifica
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="location.href='/applicazione/pazienti/cartelle/aggiornamenti/'">
                        <svg class="icon icon-sm"><use href="/bootstrap-italia/dist/svg/sprites.svg#it-arrow-left"></use></svg>
                        Torna alla lista
                    </button>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <article class="it-card rounded shadow-sm border h-100">
                        <div class="it-card-body">
                            <h5 class="it-card-title">Informazioni Paziente</h5>
                            <dl class="it-card-description-list">
                                <div>
                                    <dt>Paziente</dt>
                                    <dd>
                                        <a href="/applicazione/pazienti/cartelle/visualizza_cartella.php?id=<?= $aggiornamento['id_cartella'] ?>">
                                            <?= htmlspecialchars($aggiornamento['paziente_cognome'] . ' ' . $aggiornamento['paziente_nome']) ?>
                                        </a>
                                    </dd>
                                </div>
                                <div>
                                    <dt>Codice Fiscale</dt>
                                    <dd><?= htmlspecialchars($aggiornamento['codice_fiscale']) ?></dd>
                                </div>
                                <div>
                                    <dt>Struttura</dt>
                                    <dd><?= htmlspecialchars($aggiornamento['nome_struttura'] ?? 'N/D') ?></dd>
                                </div>
                            </dl>
                        </div>
                    </article>
                </div>

                <div class="col-md-6 mb-4">
                    <article class="it-card rounded shadow-sm border h-100">
                        <div class="it-card-body">
                            <h5 class="it-card-title">Informazioni Seduta</h5>
                            <dl class="it-card-description-list">
                                <div>
                                    <dt>Data Seduta</dt>
                                    <dd><?= htmlspecialchars($data_seduta) ?></dd>
                                </div>
                                <div>
                                    <dt>Psicologo</dt>
                                    <dd><?= htmlspecialchars($aggiornamento['psicologo_nome']) ?></dd>
                                </div>
                                <div>
                                    <dt>Data Inserimento</dt>
                                    <dd><?= htmlspecialchars($data_creazione) ?></dd>
                                </div>
                            </dl>
                        </div>
                    </article>
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-4">
                    <article class="it-card rounded shadow-sm border">
                        <div class="it-card-body">
                            <h5 class="it-card-title">Note della Seduta</h5>
                            <div class="mt-3" style="white-space: pre-wrap;">
                                <?= htmlspecialchars($aggiornamento['note']) ?>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/bootstrap-italia/dist/js/bootstrap-italia.bundle.min.js"></script>
<br />
<br />
<footer>
    <?php include SITE_ROOT . '/assets/footer/footer_login.php'; ?>
</footer>
</body>
</html>
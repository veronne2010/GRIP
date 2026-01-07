<?php
// visualizza_cartella.php - Visualizza dettagli cartella clinica

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

// Query per recuperare i dati della cartella clinica
$sql = "SELECT 
    cc.id_cartella,
    cc.data_apertura,
    cc.stato,
    cc.note,
    cc.created_at,
    cc.updated_at,
    p.id_paziente,
    p.nome AS paziente_nome,
    p.cognome AS paziente_cognome,
    p.codice_fiscale,
    s.nome_struttura,
    u.nome_cognome AS creatore_nome
FROM cartelle_cliniche cc
INNER JOIN pazienti p ON cc.id_paziente = p.id_paziente
LEFT JOIN strutture s ON p.id_struttura = s.id_struttura
LEFT JOIN utenti u ON cc.id_utente_creatore = u.id_utente
WHERE cc.id_cartella = :id_cartella AND cc.attiva = 1";

$stmt = $pdo->prepare($sql);
$stmt->execute(['id_cartella' => $id_cartella]);
$cartella = $stmt->fetch();

if (!$cartella) {
    header('Location: cartelle_cliniche.php');
    exit;
}

// Formatta le date
$data_apertura = date('d/m/Y', strtotime($cartella['data_apertura']));
$data_creazione = date('d/m/Y H:i', strtotime($cartella['created_at']));
$data_modifica = date('d/m/Y H:i', strtotime($cartella['updated_at']));

// Traduci lo stato
$stati = [
    'aperta' => 'Aperta',
    'in_lavorazione' => 'In Lavorazione',
    'completata' => 'Completata',
    'chiusa' => 'Chiusa'
];
$stato_label = $stati[$cartella['stato']] ?? ucfirst($cartella['stato']);

// Colore badge stato
$stato_colori = [
    'aperta' => 'success',
    'in_lavorazione' => 'warning',
    'completata' => 'info',
    'chiusa' => 'secondary'
];
$stato_colore = $stato_colori[$cartella['stato']] ?? 'secondary';
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cartella Clinica #<?= $id_cartella ?> - GRIP</title>
    <link rel="stylesheet" href="/bootstrap-italia/dist/css/bootstrap-italia.min.css">
</head>
<body>

<header>
    <?php include SITE_ROOT . '/assets/header/slim_accettato.php'; ?>
    <?php include SITE_ROOT . '/assets/header/centrale_app.php'; ?>
</header>

<?php include SITE_ROOT . '/assets/toolbar/toolbar_cc.php'; ?>
<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>Cartella Clinica #<?= $id_cartella ?></h2>
                    <span class="badge bg-<?= $stato_colore ?>"><?= htmlspecialchars($stato_label) ?></span>
                </div>
                <div>
                    <button type="button" class="btn btn-primary" onclick="location.href='modifica_cartella.php?id=<?= $cartella['id_cartella'] ?>'">
                        <svg class="icon icon-sm icon-white"><use href="/bootstrap-italia/dist/svg/sprites.svg#it-pencil"></use></svg>
                        Modifica
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="location.href='/applicazione/pazienti/cartelle/'">
                        <svg class="icon icon-sm"><use href="/bootstrap-italia/dist/svg/sprites.svg#it-arrow-left"></use></svg>
                        Torna alla lista
                    </button>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <article class="it-card it-card-profile it-card-border-top it-card-border-top-primary rounded shadow-sm border">
                        <div class="it-card-profile-header">
                            <div class="it-card-profile">
                                <h4 class="it-card-profile-name">
                                    <?= htmlspecialchars($cartella['paziente_nome'] . ' ' . $cartella['paziente_cognome']) ?>
                                </h4>
                                <p class="it-card-profile-role">Paziente</p>
                            </div>
                            <div class="avatar size-xl">
                                <a class="avatar avatar-primary size-xl">
                                    <p aria-hidden="true"><?= strtoupper(substr($cartella['paziente_nome'], 0, 1) . substr($cartella['paziente_cognome'], 0, 1)) ?></p>
                                </a>
                            </div>
                        </div>
                        <div class="it-card-body">
                            <dl class="it-card-description-list">
                                <div>
                                    <dt>ID Paziente</dt>
                                    <dd><?= htmlspecialchars($cartella['id_paziente']) ?></dd>
                                </div>
                                <div>
                                    <dt>Codice Fiscale</dt>
                                    <dd><?= htmlspecialchars($cartella['codice_fiscale']) ?></dd>
                                </div>
                                <div>
                                    <dt>Struttura</dt>
                                    <dd><?= htmlspecialchars($cartella['nome_struttura'] ?? 'N/D') ?></dd>
                                </div>
                            </dl>
                        </div>
                    </article>
                </div>

                <div class="col-md-6 mb-4">
                    <article class="it-card rounded shadow-sm border h-100">
                        <div class="it-card-body">
                            <h5 class="it-card-title">Informazioni Cartella</h5>
                            <dl class="it-card-description-list">
                                <div>
                                    <dt>Data Apertura</dt>
                                    <dd><?= htmlspecialchars($data_apertura) ?></dd>
                                </div>
                                <div>
                                    <dt>Stato</dt>
                                    <dd><span class="badge bg-<?= $stato_colore ?>"><?= htmlspecialchars($stato_label) ?></span></dd>
                                </div>
                                <div>
                                    <dt>Creata da</dt>
                                    <dd><?= htmlspecialchars($cartella['creatore_nome']) ?></dd>
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

            <?php if (!empty($cartella['note'])): ?>
            <div class="row">
                <div class="col-12 mb-4">
                    <article class="it-card rounded shadow-sm border">
                        <div class="it-card-body">
                            <h5 class="it-card-title">Note</h5>
                            <p><?= nl2br(htmlspecialchars($cartella['note'])) ?></p>
                        </div>
                    </article>
                </div>
            </div>
            <?php endif; ?>

            <!-- Aggiornamenti della cartella -->
            <?php
            // Recupera gli aggiornamenti di questa cartella
            $sql_agg = "SELECT 
                a.id_aggiornamento,
                a.data_seduta,
                a.note,
                a.created_at,
                u.nome_cognome AS psicologo_nome
            FROM aggiornamenti a
            INNER JOIN utenti u ON a.id_utente = u.id_utente
            WHERE a.id_cartella = :id_cartella AND a.attivo = 1
            ORDER BY a.data_seduta DESC, a.created_at DESC";
            
            $stmt_agg = $pdo->prepare($sql_agg);
            $stmt_agg->execute(['id_cartella' => $id_cartella]);
            $aggiornamenti = $stmt_agg->fetchAll();
            ?>

            <div class="row">
                <div class="col-12 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>Aggiornamenti e Sedute</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                onclick="location.href='/applicazione/pazienti/cartelle/aggiornamenti/nuovo_aggiornamento.php?id_cartella=<?= $id_cartella ?>'">
                            <svg class="icon icon-sm"><use href="/bootstrap-italia/dist/svg/sprites.svg#it-plus"></use></svg>
                            Aggiungi Aggiornamento
                        </button>
                    </div>

                    <?php if (empty($aggiornamenti)): ?>
                        <div class="alert alert-info" role="alert">
                            Nessun aggiornamento presente per questa cartella.
                        </div>
                    <?php else: ?>
                        <?php foreach ($aggiornamenti as $agg): ?>
                        <article class="it-card rounded shadow-sm border mb-3">
                            <div class="it-card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="it-card-title mb-2">
                                            <svg class="icon icon-sm"><use href="/bootstrap-italia/dist/svg/sprites.svg#it-calendar"></use></svg>
                                            Seduta del <?= date('d/m/Y', strtotime($agg['data_seduta'])) ?>
                                        </h6>
                                        <p class="text-muted mb-2">
                                            <small>
                                                <svg class="icon icon-sm"><use href="/bootstrap-italia/dist/svg/sprites.svg#it-user"></use></svg>
                                                <?= htmlspecialchars($agg['psicologo_nome']) ?>
                                            </small>
                                        </p>
                                        <div class="it-card-text">
                                            <?= nl2br(htmlspecialchars($agg['note'])) ?>
                                        </div>
                                    </div>
                                    <div class="ms-3">
                                        <button type="button" class="btn btn-sm btn-outline-secondary mb-1" 
                                                onclick="location.href='/applicazione/pazienti/cartelle/aggiornamenti/modifica_aggiornamento.php?id=<?= $agg['id_aggiornamento'] ?>'">
                                            <svg class="icon icon-sm"><use href="/bootstrap-italia/dist/svg/sprites.svg#it-pencil"></use></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </article>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Pulsante Stampa -->
<div class="row no-print">
                <div class="col-12 text-center mb-4">
                    <button type="button" class="btn btn-primary" onclick="window.open('stampa_cartella.php?id=<?= $id_cartella ?>', '_blank')">
                        <svg class="icon icon-sm icon-white"><use href="/bootstrap-italia/dist/svg/sprites.svg#it-print"></use></svg>
                        Stampa Cartella
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
@media print {
    .no-print, .it-header-slim-wrapper, button {
        display: none !important;
    }
    .container {
        max-width: 100% !important;
    }
    .it-card {
        page-break-inside: avoid;
        border: 1px solid #ddd !important;
        box-shadow: none !important;
    }
    body {
        font-size: 12pt;
    }
}
</style>

<script src="/bootstrap-italia/dist/js/bootstrap-italia.bundle.min.js"></script>
<br />
<br />
<footer>
    <?php include SITE_ROOT . '/assets/footer/footer_login.php'; ?>
</footer>
</body>
</html>
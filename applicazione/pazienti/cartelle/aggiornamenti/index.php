<?php
// aggiornamenti.php - Pagina dinamica degli aggiornamenti

// db_config.php - Configurazione database
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

// Query per recuperare tutti gli aggiornamenti ordinati per data
$sql = "SELECT 
    a.id_aggiornamento,
    a.data_seduta,
    a.note,
    a.created_at,
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
WHERE a.attivo = 1
ORDER BY a.data_seduta DESC, a.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$aggiornamenti = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aggiornamenti - GRIP</title>
    <link rel="stylesheet" href="/bootstrap-italia/dist/css/bootstrap-italia.min.css">
</head>
<body>

<header>
    <?php include SITE_ROOT . '/assets/header/slim_accettato.php'; ?>
    <?php include SITE_ROOT . '/assets/header/centrale_app.php'; ?>
    <br />
    <br />
</header>

<?php include SITE_ROOT . '/assets/toolbar/toolbar_ag.php'; ?>

<div class="container">
  <div class="row">
    <div class="col-sm">
          <h4 class="it-card-profile-name">
            <a>Aggiornamenti</a>
          </h4>
          <p class="text-muted">Lista cronologica degli aggiornamenti delle sedute</p>
    </div>
    <div class="col-sm"></div>
    <div class="col-sm text-end">
        <button type="button" class="btn btn-outline-primary" onclick="location.href='nuovo_aggiornamento.php'">
            <svg class="icon icon-sm"><use href="/bootstrap-italia/dist/svg/sprites.svg#it-plus"></use></svg>
            Nuovo Aggiornamento
        </button>
    </div>
  </div>
</div>
<br />

<div class="container">
<?php if (empty($aggiornamenti)): ?>
    <div class="alert alert-info" role="alert">
        <strong>Nessun aggiornamento presente.</strong> Clicca su "Nuovo Aggiornamento" per aggiungerne uno.
    </div>
<?php else: ?>
    
    <?php foreach ($aggiornamenti as $aggiornamento): ?>
    <article class="it-card rounded shadow-sm border mb-3">
        <div class="it-card-body">
            <div class="row">
                <div class="col-md-9">
                    <h5 class="it-card-title">
                        <a href="/applicazione/pazienti/cartelle/visualizza_cartella.php?id=<?= $aggiornamento['id_cartella'] ?>">
                            <?= htmlspecialchars($aggiornamento['paziente_cognome'] . ' ' . $aggiornamento['paziente_nome']) ?>
                        </a>
                    </h5>
                    <p class="it-card-subtitle mb-2">
                        <small class="text-muted">
                            <svg class="icon icon-sm"><use href="/bootstrap-italia/dist/svg/sprites.svg#it-calendar"></use></svg>
                            Data seduta: <?= date('d/m/Y', strtotime($aggiornamento['data_seduta'])) ?>
                            &nbsp;|&nbsp;
                            <svg class="icon icon-sm"><use href="/bootstrap-italia/dist/svg/sprites.svg#it-user"></use></svg>
                            Psicologo: <?= htmlspecialchars($aggiornamento['psicologo_nome']) ?>
                            <?php if ($aggiornamento['nome_struttura']): ?>
                            &nbsp;|&nbsp;
                            <svg class="icon icon-sm"><use href="/bootstrap-italia/dist/svg/sprites.svg#it-pin"></use></svg>
                            <?= htmlspecialchars($aggiornamento['nome_struttura']) ?>
                            <?php endif; ?>
                        </small>
                    </p>
                    <div class="it-card-text">
                        <?php 
                        $note = htmlspecialchars($aggiornamento['note']);
                        // Mostra solo le prime 200 caratteri
                        if (strlen($note) > 200) {
                            echo substr($note, 0, 200) . '...';
                        } else {
                            echo $note;
                        }
                        ?>
                    </div>
                </div>
                <div class="col-md-3 text-end d-flex flex-column justify-content-center">
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-primary mb-2" 
                                onclick="location.href='visualizza_aggiornamento.php?id=<?= $aggiornamento['id_aggiornamento'] ?>'">
                            <svg class="icon icon-sm"><use href="/bootstrap-italia/dist/svg/sprites.svg#it-search"></use></svg>
                            Visualizza
                        </button>
                        <br>
                        <button type="button" class="btn btn-sm btn-outline-secondary" 
                                onclick="location.href='modifica_aggiornamento.php?id=<?= $aggiornamento['id_aggiornamento'] ?>'">
                            <svg class="icon icon-sm"><use href="/bootstrap-italia/dist/svg/sprites.svg#it-pencil"></use></svg>
                            Modifica
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </article>
    <?php endforeach; ?>
    
<?php endif; ?>
</div>

<script src="/bootstrap-italia/dist/js/bootstrap-italia.bundle.min.js"></script>
</body>
</html>
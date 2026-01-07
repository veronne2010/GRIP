<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/ente.php';
?>
<?php
// db_config.php - Configurazione database

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

// Per test, usa il primo utente del database
$id_utente = 1; // Cambia questo con l'ID dell'utente che vuoi visualizzare

// Query per recuperare i dati dell'utente
$sql = "SELECT 
    u.id_utente,
    u.nome_cognome,
    u.iniziali,
    u.email,
    u.codice_personale,
    u.ultimo_accesso,
    r.ruolo,
    s.nome_struttura
FROM utenti u
INNER JOIN ruoli r ON u.id_ruolo = r.id_ruolo
INNER JOIN strutture s ON u.id_struttura = s.id_struttura
WHERE u.id_utente = :id_utente AND u.attivo = 1";

$stmt = $pdo->prepare($sql);
$stmt->execute(['id_utente' => $id_utente]);
$utente = $stmt->fetch();

if (!$utente) {
    die("Utente non trovato");
}

// Formatta la data dell'ultimo accesso
$ultimo_accesso = $utente['ultimo_accesso'] 
    ? date('D, d F Y', strtotime($utente['ultimo_accesso']))
    : 'Mai';

// Traduci il giorno in italiano
$giorni = [
    'Mon' => 'lun', 'Tue' => 'mar', 'Wed' => 'merc', 
    'Thu' => 'gio', 'Fri' => 'ven', 'Sat' => 'sab', 'Sun' => 'dom'
];
$mesi = [
    'January' => 'gennaio', 'February' => 'febbraio', 'March' => 'marzo',
    'April' => 'aprile', 'May' => 'maggio', 'June' => 'giugno',
    'July' => 'luglio', 'August' => 'agosto', 'September' => 'settembre',
    'October' => 'ottobre', 'November' => 'novembre', 'December' => 'dicembre'
];

$ultimo_accesso = str_replace(array_keys($giorni), array_values($giorni), $ultimo_accesso);
$ultimo_accesso = str_replace(array_keys($mesi), array_values($mesi), $ultimo_accesso);

// Aggiorna l'ultimo accesso
$update_sql = "UPDATE utenti SET ultimo_accesso = NOW() WHERE id_utente = :id_utente";
$update_stmt = $pdo->prepare($update_sql);
$update_stmt->execute(['id_utente' => $id_utente]);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?= htmlspecialchars($utente['nome_cognome']) ?></title>
    <link rel="stylesheet" href="/bootstrap-italia/dist/css/bootstrap-italia.min.css">
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-12 col-md-6 col-lg-6 mb-3 mb-md-4">
            <!--start it-card-->
            <article class="it-card it-card-profile it-card-height-full it-card-border-top it-card-border-top-secondary rounded shadow-sm border">
                <div class="it-card-profile-header">
                    <div class="it-card-profile">
                        <h4 class="it-card-profile-name">
                            <a><?= htmlspecialchars($utente['nome_cognome']) ?></a>
                        </h4>
                        <p class="it-card-profile-role"><?= htmlspecialchars(ucfirst(strtolower($utente['ruolo']))) ?></p>
                    </div>
                    <div class="avatar size-xl">
                        <a class="avatar avatar-gray size-xl">
                            <p aria-hidden="true"><?= htmlspecialchars($utente['iniziali']) ?></p>
                            <span class="visually-hidden"><?= htmlspecialchars($utente['nome_cognome']) ?></span>
                        </a>
                    </div>
                </div>
                <div class="it-card-body">
                    <dl class="it-card-description-list">
                        <div>
                            <dt>Utente</dt>
                            <dd><?= htmlspecialchars($utente['codice_personale']) ?></dd>
                        </div>
                        <div>
                            <dt>Strutture:</dt>
                            <dd><a class="it-card-link"><?= htmlspecialchars($utente['nome_struttura']) ?></a></dd>
                        </div>
                        <div>
                            <dt>Email:</dt>
                            <dd><?= htmlspecialchars($utente['email']) ?></dd>
                        </div>
                    </dl>
                </div>
            </article>
            <!--end it-card-->
        </div>
        
        <div class="col-12 col-md-6 col-lg-6 mb-3 mb-md-4">
            <!--start it-card-->
            <article class="it-card it-card-banner it-card-inline rounded shadow-sm border">
                <div class="it-card-inline-content">
                    <h3 class="it-card-title">
                        Ultimo accesso il:
                    </h3>
                    <div class="it-card-body">
                        <p class="it-card-subtitle"><?= htmlspecialchars($ultimo_accesso) ?></p>
                    </div>
                </div>
                <div class="it-card-banner-icon-wrapper">
                    <svg class="icon icon-secondary icon-xl" aria-hidden="true">
                        <use href="/bootstrap-italia/dist/svg/sprites.svg#it-unlocked"></use>
                    </svg>
                </div>
            </article>
            <!--end it-card-->
        </div>
        
        <div class="mb-4 mt-4">
            <h4 class="it-card-title">Servizi disponibili</h4>
        </div>
        
        <div class="row">
            <div class="col-12 col-md-6 col-lg-6 mb-3 mb-md-4">
                <article class="it-card it-card-height-full rounded border shadow-sm mb-3">
                    <h4 class="it-card-title">
                        <a>Pazienti</a>
                    </h4>
                    <div class="it-card-body">
                        <p class="it-card-subtitle">Elenco dei Pazienti</p>
                        <footer class="it-card-related">
                            <div class="it-card-taxonomy"></div>
                        </footer>
                    </div>
                    <div class="it-card-footer" aria-label="Link correlati:">
                        <button type="button" class="btn btn-outline-secondary" onclick="location.href='/applicazione/pazienti/'">Accedi al servizio</button>
                    </div>
                </article>
            </div>
            
            <div class="col-12 col-md-6 col-lg-6 mb-3 mb-md-4">
                <article class="it-card it-card-height-full rounded border shadow-sm mb-3">
                    <h4 class="it-card-title">
                        <a>Cartelle Cliniche</a>
                    </h4>
                    <div class="it-card-body">
                        <p class="it-card-subtitle">Elenco delle Cartelle Cliniche attive</p>
                        <footer class="it-card-related">
                            <div class="it-card-taxonomy"></div>
                        </footer>
                    </div>
                    <div class="it-card-footer" aria-label="Link correlati:">
                        <button type="button" class="btn btn-outline-secondary">Accedi al servizio</button>
                    </div>
                </article>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12 col-md-6 col-lg-6 mb-3 mb-md-4">
                <article class="it-card it-card-height-full rounded border shadow-sm mb-3">
                    <h4 class="it-card-title">
                        <a>Aggiornamenti</a>
                    </h4>
                    <div class="it-card-body">
                        <p class="it-card-subtitle">Lista degli aggiornamenti per paziente</p>
                        <footer class="it-card-related">
                            <div class="it-card-taxonomy"></div>
                        </footer>
                    </div>
                    <div class="it-card-footer" aria-label="Link correlati:">
                        <button type="button" class="btn btn-outline-secondary">Accedi al servizio</button>
                    </div>
                </article>
            </div>
        </div>
    </div>
</div>

<script src="/bootstrap-italia/dist/js/bootstrap-italia.bundle.min.js"></script>
</body>
</html>
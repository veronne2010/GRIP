<?php
// check_toolbar.php - Script per determinare quale toolbar mostrare automaticamente

session_start();

// Verifica se l'utente è loggato
if (!isset($_SESSION['id_utente'])) {
    header('Location: /login.php');
    exit;
}

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

// Recupera il ruolo dal database
$sql = "SELECT r.ruolo 
        FROM utenti u
        INNER JOIN ruoli r ON u.id_ruolo = r.id_ruolo
        WHERE u.id_utente = :id AND u.attivo = 1";

$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $_SESSION['id_utente']]);
$result = $stmt->fetch();

if (!$result) {
    session_destroy();
    header('Location: /login.php');
    exit;
}

$ruolo = strtolower($result['ruolo']);
$_SESSION['ruolo'] = $ruolo;
$is_admin = ($ruolo === 'admin');

// Rileva automaticamente la sezione dal percorso corrente
$current_path = $_SERVER['REQUEST_URI'];
$toolbar_section = 'db'; // Default

// Mappa dei percorsi alle sezioni toolbar
if (strpos($current_path, '/applicazione/admin') !== false) {
    $toolbar_section = 'ad';
} elseif (strpos($current_path, '/applicazione/account') !== false) {
    $toolbar_section = 'ac';
} elseif (strpos($current_path, '/applicazione/pazienti/cartelle/aggiornamenti') !== false) {
    $toolbar_section = 'ag';
} elseif (strpos($current_path, '/applicazione/pazienti/cartelle') !== false) {
    $toolbar_section = 'cc';
} elseif (strpos($current_path, '/applicazione/pazienti') !== false) {
    $toolbar_section = 'pz';
} elseif (strpos($current_path, '/applicazione') !== false) {
    $toolbar_section = 'db';
}

// Costruisci il percorso del file toolbar
$toolbar_path = $_SERVER['DOCUMENT_ROOT'];

if ($is_admin) {
    $toolbar_file = $toolbar_path . "/toolbar_ad_{$toolbar_section}.php";
    $toolbar_fallback = $toolbar_path . "/toolbar_{$toolbar_section}.php";
} else {
    $toolbar_file = $toolbar_path . "/toolbar_{$toolbar_section}.php";
    $toolbar_fallback = null;
}

// Carica la toolbar appropriata
if (file_exists($toolbar_file)) {
    include $toolbar_file;
} elseif ($toolbar_fallback && file_exists($toolbar_fallback)) {
    include $toolbar_fallback;
} else {
    // Fallback generale
    $general_toolbar = $is_admin ? 
        $toolbar_path . '/toolbar_ad_fb.php' : 
        $toolbar_path . '/toolbar_fb.php';
    
    if (file_exists($general_toolbar)) {
        include $general_toolbar;
    } else {
        echo '<div class="alert alert-warning">Toolbar non trovata per sezione: ' . htmlspecialchars($toolbar_section) . '</div>';
    }
}
?>
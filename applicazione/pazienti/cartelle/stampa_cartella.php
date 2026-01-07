<?php
// stampa_cartella.php - Modello di stampa per cartella clinica

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
    p.id_paziente,
    p.nome AS paziente_nome,
    p.cognome AS paziente_cognome,
    p.codice_fiscale,
    s.nome_struttura,
    s.indirizzo AS struttura_indirizzo,
    s.citta AS struttura_citta,
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

// Recupera gli aggiornamenti
$sql_agg = "SELECT 
    a.id_aggiornamento,
    a.data_seduta,
    a.note,
    u.nome_cognome AS psicologo_nome
FROM aggiornamenti a
INNER JOIN utenti u ON a.id_utente = u.id_utente
WHERE a.id_cartella = :id_cartella AND a.attivo = 1
ORDER BY a.data_seduta ASC";

$stmt_agg = $pdo->prepare($sql_agg);
$stmt_agg->execute(['id_cartella' => $id_cartella]);
$aggiornamenti = $stmt_agg->fetchAll();

// Traduci lo stato
$stati = [
    'aperta' => 'Aperta',
    'in_lavorazione' => 'In Lavorazione',
    'completata' => 'Completata',
    'chiusa' => 'Chiusa'
];
$stato_label = $stati[$cartella['stato']] ?? ucfirst($cartella['stato']);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cartella Clinica #<?= $id_cartella ?> - Stampa</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #333;
            padding: 20mm;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #0066cc;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #0066cc;
            font-size: 24pt;
            margin-bottom: 5px;
        }
        
        .header h2 {
            color: #666;
            font-size: 14pt;
            font-weight: normal;
        }
        
        .cartella-info {
            background-color: #f5f5f5;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #0066cc;
        }
        
        .cartella-info p {
            margin: 5px 0;
        }
        
        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        
        .section-title {
            background-color: #0066cc;
            color: white;
            padding: 10px;
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .info-item {
            padding: 10px;
            border: 1px solid #ddd;
        }
        
        .info-item strong {
            display: block;
            color: #0066cc;
            margin-bottom: 5px;
            font-size: 10pt;
        }
        
        .info-item span {
            font-size: 11pt;
        }
        
        .aggiornamento {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            page-break-inside: avoid;
            background-color: #fafafa;
        }
        
        .aggiornamento-header {
            background-color: #e6f2ff;
            padding: 10px;
            margin: -15px -15px 15px -15px;
            border-bottom: 2px solid #0066cc;
        }
        
        .aggiornamento-header h4 {
            color: #0066cc;
            margin-bottom: 5px;
        }
        
        .aggiornamento-header p {
            color: #666;
            font-size: 10pt;
        }
        
        .aggiornamento-text {
            white-space: pre-wrap;
            line-height: 1.8;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 9pt;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        
        .no-print {
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 1000;
        }
        
        @media print {
            body {
                padding: 0;
            }
            
            .no-print {
                display: none !important;
            }
            
            .section {
                page-break-inside: avoid;
            }
            
            .aggiornamento {
                page-break-inside: avoid;
            }
            
            @page {
                margin: 20mm;
            }
        }
    </style>
</head>
<body>

<div class="no-print">
    <button onclick="window.print()" style="padding: 10px 20px; background-color: #0066cc; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;">
        🖨️ Stampa
    </button>
    <button onclick="window.close()" style="padding: 10px 20px; background-color: #666; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; margin-left: 10px;">
        ✕ Chiudi
    </button>
</div>

<div class="header">
    <h1><?= $ente_nome ?></h1>
    <h3>GRIP</h3>
    <h2>Cartella Clinica N° <?= $id_cartella ?></h2>
</div>

<div class="cartella-info">
    <p><strong>Data di stampa:</strong> <?= date('d/m/Y H:i') ?></p>
    <p><strong>Stato cartella:</strong> <?= htmlspecialchars($stato_label) ?></p>
</div>

<!-- SEZIONE DATI PAZIENTE -->
<div class="section">
    <div class="section-title">DATI PAZIENTE</div>
    
    <div class="info-grid">
        <div class="info-item">
            <strong>Nome e Cognome</strong>
            <span><?= htmlspecialchars($cartella['paziente_nome'] . ' ' . $cartella['paziente_cognome']) ?></span>
        </div>
        
        <div class="info-item">
            <strong>Codice Fiscale</strong>
            <span><?= htmlspecialchars($cartella['codice_fiscale']) ?></span>
        </div>
        
        <div class="info-item">
            <strong>ID Paziente</strong>
            <span><?= htmlspecialchars($cartella['id_paziente']) ?></span>
        </div>
        
        <div class="info-item">
            <strong>Struttura</strong>
            <span><?= htmlspecialchars($cartella['nome_struttura'] ?? 'N/D') ?></span>
        </div>
    </div>
</div>

<!-- SEZIONE INFORMAZIONI CARTELLA -->
<div class="section">
    <div class="section-title">INFORMAZIONI CARTELLA</div>
    
    <div class="info-grid">
        <div class="info-item">
            <strong>Data Apertura</strong>
            <span><?= date('d/m/Y', strtotime($cartella['data_apertura'])) ?></span>
        </div>
        
        <div class="info-item">
            <strong>Stato</strong>
            <span><?= htmlspecialchars($stato_label) ?></span>
        </div>
        
        <div class="info-item">
            <strong>Creata da</strong>
            <span><?= htmlspecialchars($cartella['creatore_nome']) ?></span>
        </div>
        
        <div class="info-item">
            <strong>Data Creazione</strong>
            <span><?= date('d/m/Y', strtotime($cartella['created_at'])) ?></span>
        </div>
    </div>
    
    <?php if (!empty($cartella['note'])): ?>
    <div style="margin-top: 15px; padding: 15px; border: 1px solid #ddd; background-color: #fffef5;">
        <strong style="display: block; color: #0066cc; margin-bottom: 10px;">Note Generali:</strong>
        <div style="white-space: pre-wrap;"><?= htmlspecialchars($cartella['note']) ?></div>
    </div>
    <?php endif; ?>
</div>

<!-- SEZIONE AGGIORNAMENTI E SEDUTE -->
<?php if (!empty($aggiornamenti)): ?>
<div class="section">
    <div class="section-title">AGGIORNAMENTI E SEDUTE (<?= count($aggiornamenti) ?>)</div>
    
    <?php foreach ($aggiornamenti as $index => $agg): ?>
    <div class="aggiornamento">
        <div class="aggiornamento-header">
            <h4>Seduta N° <?= $index + 1 ?> - <?= date('d/m/Y', strtotime($agg['data_seduta'])) ?></h4>
            <p>Psicologo: <?= htmlspecialchars($agg['psicologo_nome']) ?></p>
        </div>
        <div class="aggiornamento-text">
<?= htmlspecialchars($agg['note']) ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="footer">
    <p>Documento riservato - Trattamento dati secondo normativa privacy vigente</p>
    <p>Stampato il <?= date('d/m/Y') ?> alle ore <?= date('H:i') ?></p>
    <p>Documento <?= $ente_nome ?>, creato con GRIP</p>
</div>

<script>
// Auto-print quando richiesto
<?php if (isset($_GET['auto_print'])): ?>
window.onload = function() {
    window.print();
}
<?php endif; ?>
</script>

</body>
</html>
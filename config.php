<?php
define('SITE_ROOT', rtrim($_SERVER['DOCUMENT_ROOT'], '/'));
?>
<?php
$type = 'mysql';
$host = 'localhost';
$dbname = 'grip';
$username = 'grip';
$password = 'grip';
$port = '3306';
try {
    $dsn = "$type:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}
?> 
<head>
    <link rel="stylesheet" href="/custom/style.css">
    <script src="/custom/script.js" defer></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/bootstrap-italia/dist/css/bootstrap-italia.min.css">
    <link rel="stylesheet" href="/custom/style.css">
</head>
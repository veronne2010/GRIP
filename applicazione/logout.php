<?php
session_start();

// Pulisce tutte le variabili di sessione
$_SESSION = [];

// Distrugge la sessione
session_destroy();

// Reindirizza alla pagina di login
header("Location: /");
exit;

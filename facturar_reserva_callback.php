<?php
include_once("config/db.php");

// Leer JSON recibido
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Loguear todo el contenido primero
$logPath = "./logs/callback_" . date('Ymd_His') . ".log";
file_put_contents($logPath, date('Y-m-d H:i:s') . " | Callback recibido: " . $input . "\n", FILE_APPEND);




<?php
include_once("config/db.php");

// Leer JSON recibido
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Loguear todo el contenido primero
$logPath = "./logs/callback_" . date('Ymd_His') . ".log";
file_put_contents($logPath, date('Y-m-d H:i:s') . " | Callback recibido: " . $input . "\n", FILE_APPEND);


// Validar JSON
if (!$data) {
    http_response_code(400);
    file_put_contents($logPath, date('Y-m-d H:i:s') . " | JSON inválido\n", FILE_APPEND);
    die(json_encode(['error' => 'JSON inválido']));
}

// Procesar cada comprobante recibido
foreach ($data['comprobantes'] as $comp) {
    $idReserva = intval(str_replace('RES-', '', $comp['external_reference']));
    $numero = $comp['numero'] ?? '';
    $cae = $comp['cae'] ?? '';
    $pdfUrl = $comp['pdfUrl'] ?? '';
    $total = $comp['total'] ?? 0;

    // Log individual de cada comprobante
    file_put_contents($logPath, date('Y-m-d H:i:s') . " | Procesando Reserva ID: $idReserva | Número: $numero | CAE: $cae | Total: $total\n", FILE_APPEND);

    // INSERT/UPDATE en la BD
    /*$sqlInsert = "INSERT INTO reserva_facturas
        (punto_venta_id, tipoDoc, tipo, titular, fecha_emision, numero, monto, reserva_id, agregada_por)
        VALUES (
            3, 1, 'C', 'TITULAR', NOW(), '$numero', $total, $idReserva, 1
        )
        ON DUPLICATE KEY UPDATE numero='$numero', monto=$total, cae='$cae', pdf='$pdfUrl'";
    mysqli_query($conn, $sqlInsert);*/
}

// Responder OK a la API
http_response_code(200);
echo json_encode(['status' => 'ok']);
